<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/md5', name: 'app_md5')]
    public function md5test(): Response
    {
        $carImg = 'images/car.jpg';
        $planeImg = 'images/plane.jpg';
        $shipImg = 'images/ship.jpg';

        $images = [
            'plane' => [
                'url' => $planeImg,
                'md5' => md5_file($planeImg),
            ],
            'car' => [
                'url' => $carImg,
                'md5' => md5_file($carImg),
            ],
            'ship' => [
                'url' => $shipImg,
                'md5' => md5_file($shipImg),
            ],
        ];

        return $this->render('security/md5.html.twig', ['images' => $images]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
