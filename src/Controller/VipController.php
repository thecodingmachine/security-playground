<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\CouponRepository;
use App\Repository\UserRepository;
use App\Service\StripeServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Stripe\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class VipController extends AbstractController
{
    public function __construct(private readonly UserRepository $userRepository, private readonly CouponRepository $couponRepository,private readonly StripeServiceInterface $stripeService)
    {
    }

    #[Route('/account/become-vip', name: 'app_account_become_vip', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function becomeVip(): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);

        if ($user->isVIP()) {
            throw new AccessDeniedException();
        }

        $coupon = $this->couponRepository->findOneBy(['code' => 'VIP10']);

        return $this->render('account/become_vip.html.twig', ['coupon' => $coupon]);
    }

    #[Route('/account/stripe/payment/checkout', name: 'app_account_stripe_payment_checkout', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function startPayment(Request $request): JsonResponse
    {
        $user = $this->getUser();
        assert($user instanceof User);

        $content = json_decode($request->getContent(), true);
        $amount = 100;
        $discount = $content['discount'] ?? 0;

        if (! is_numeric($discount)) {
            return new JsonResponse(['success' => false, 'message' => "Le montant est invalide"]);
        }

        $amount = $amount - $amount * $discount / 100;

        if ($amount <= 0) {
            return new JsonResponse(['success' => false, 'message' => "Le montant ne peut pas être inférieur à 0"]);
        }

        $session = $this->stripeService->createSession($amount);

        $user->setSessionStripeId($session->id);

        $this->userRepository->save($user, true);

        return new JsonResponse(['success' => true, 'url' => $session->url]);
    }

    #[Route('/account/stripe/payment/success', name: 'app_account_stripe_payment_success', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function successPayment(): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);

        if ($user->getSessionStripeId() === null) {
            throw new AccessDeniedException();
        }

        try {
            $session = $this->stripeService->findSessionById($user->getSessionStripeId());
        } catch (ApiErrorException) {
            throw new AccessDeniedException();
        }

        if ($session->payment_status !== 'paid') {
            throw new AccessDeniedException();
        }

        $user->addRole('ROLE_VIP');

        $this->userRepository->save($user, true);

        $this->addFlash('success', "Merci pour votre paiement");

        return $this->render('home/index.html.twig');
    }

    #[Route('/account/stripe/payment/cancel', name: 'app_account_stripe_payment_cancel', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function cancelPayment(): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);

        $this->addFlash('error', "Le paiement a été annulé");

        return $this->render('account/become_vip.html.twig');
    }
}