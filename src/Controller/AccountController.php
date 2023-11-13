<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{

    public function __construct()
    {
    }

    #[Route('/account', name: 'app_account', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(): Response
    {
        $form = $this->createForm(AccountType::class);

        return $this->render('account/edit-account.html.twig', [
            'user' => $this->getUser(),
            'form' => $form->createView()
        ]);
    }

    #[Route('/account', name: 'app_account_submit', methods: ['POST'])]
    public function save(Request $request): Response
    {
        $user = new User();

        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Should persist, but not needed for the exercise
        } else {
            foreach ($form->getErrors(true) as $violation) {
                $this->addFlash('error', $violation->getMessage());
            }
        }

        return $this->redirectToRoute('app_account');
    }
}
