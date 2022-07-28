<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class EmailController extends AbstractController
{
    #[Route('/email', name: 'app_email')]
    public function index(): Response
    {
        $transport = Transport::fromDsn($_ENV['MAILER_DSN']);

        $email = (new Email())
            ->from('support@example.com')
            ->to('aaa@example.com')
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer = new Mailer($transport);

        $mailer->send($email);

        return new Response('send');
    }
}
