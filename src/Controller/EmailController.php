<?php

namespace App\Controller;

use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmailController extends AbstractController
{
    #[Route('/email', name: 'app_email')]
    public function index(EmailService $emailService): Response
    {
        $emailService->sendEmail(
            'emetteur@lrr.fr',
            'destinataire@lrr.fr',
            "Essaie envoie de mail",
            'email/model_1.html.twig',
            [
                'prenom' => 'paul',
                'nom' => 'gravinese',
            ]
        );
        return $this->redirectToRoute('app_accueil');
    }
}
