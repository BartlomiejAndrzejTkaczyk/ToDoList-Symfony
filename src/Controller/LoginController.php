<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        dump($request);

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUser = $authenticationUtils->getLastUsername();
        dump($lastUser, $error);
        return $this->render('login/index.html.twig', [
            'last_username' => $lastUser,
            'error' => $error
        ]);
    }
}
