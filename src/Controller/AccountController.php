<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\UpdateAccountFormType;
use App\Form\UpdateProfilePictureFormType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AccountController extends AbstractController
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    #[Route('/account/edit', name: 'app_account_edit', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);

        $form = $this->createForm(UpdateAccountFormType::class, $user);

        $form->handleRequest($request);
        if ($request->isMethod('POST') && $form->isSubmitted() && $form->isValid()) {
            $resource = $form->getData();

            // Upload idCard
            $idCard = $request->files->get('update_account_form')['idCard'];
            if ($idCard instanceof UploadedFile) {
                $fileName = md5(uniqid()).'.'.$idCard->guessExtension();
                $destination = $this->getParameter('kernel.project_dir') . '/public/uploads';
                $idCard->move($destination, $fileName);

                $user->setIdCard($fileName);
            }

            $this->userRepository->save($resource, true);

            $this->addFlash('success', "Votre profil a bien été modifié");
        }

        return $this->render('account/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/account/profile-picture/edit', name: 'app_account_update_profile_picture', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function updateProfilePicture(Request $request): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);

        $form = $this->createForm(UpdateProfilePictureFormType::class, $user);

        $form->handleRequest($request);
        if ($request->isMethod('POST') && $form->isSubmitted() && $form->isValid()) {
            $profilePictureUrl = $form->get('profilePicture')->getData();

            $profilePictureBase64 = base64_encode(file_get_contents($profilePictureUrl));
            $user->setProfilePictureBase64($profilePictureBase64);

            $this->userRepository->save($user, true);

            $this->addFlash('success', "Votre photo de profil a bien été modifié");
        }

        return $this->render('account/edit_profile_picture.html.twig', [
            'form' => $form->createView(),
            'profilePictureBase64' => $user->getProfilePictureBase64(),
        ]);
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

        return $this->render('account/become_vip.html.twig');
    }

    #[Route('/account/pay-vip', name: 'app_account_pay_vip', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function payVip(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $amount = $content['amount'] ?? 0;

        if (! is_numeric($amount)) {
            return new JsonResponse(['success' => false, 'message' => "Le montant est invalide"]);
        }

        if ($amount < 0) {
            return new JsonResponse(['success' => false, 'message' => "Le montant ne peut pas être inférieur à 0"]);
        }

        return new JsonResponse(['success' => true, 'message' => "ok"]);
    }
}
