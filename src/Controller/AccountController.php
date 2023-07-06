<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class AccountController extends AbstractController
{

    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    #[Route('/account', name: 'app_account', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(): Response
    {
        return $this->render('account/edit-account.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/account', name: 'app_account_submit', methods: ['POST'])]
    public function save(Request $request): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);
        $email = $request->request->get('email');
        if ($email === null){
            throw new BadRequestHttpException('missing email');
        }
        $user->setEmail($email);
        $this->userRepository->save($user, true);

        $this->addFlash('account','Account updated');
        return $this->redirectToRoute('app_account');
    }
}
