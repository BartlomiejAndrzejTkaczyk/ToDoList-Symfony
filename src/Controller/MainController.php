<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{


    #[Route('/', name: 'main_index')]
    public function index()
    {
        try {
            $this->denyAccessUnlessGranted('ROLE_USER');
            return $this->redirectToRoute('task_index');
        } catch (\Exception ){
            return $this->redirectToRoute('security_login');
        }
    }

}