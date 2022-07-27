<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{


    #[Route('/', name: 'main_index')]
    public function index(): Response
    {
        try {
            $this->denyAccessUnlessGranted('ROLE_USER');
            return $this->redirectToRoute('task_index');
        } catch (Exception ){
            return $this->redirectToRoute('security_login');
        }
    }

}