<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    private ContactRepository $contactRepository;

    /**
     * @param ContactRepository $contactRepository
     */
    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function index(Request $request, EmailService $emailService): Response
    {
        $contact = new Contact();
        $formContact = $this->createForm(ContactType::class, $contact);

        // reconnait si le form à été soumis
        $formContact->handleRequest($request);
        if($formContact->isSubmitted() && $formContact->isValid()){
            $contact->setCreatedAt(new \DateTime());
            $this->contactRepository->add($contact, true);
            $emailService->sendEmail(
                $contact->getEmail(),
                'admin@lrr.fr',
                $contact->getSujet(),
                'email/model_2.html.twig',
                [
                    'contenu' => $contact->getContenu(),
                    'nom' => $contact->getNom(),
                    'prenom' => $contact->getPrenom(),
            ]);
            return $this->redirectToRoute('app_accueil');
        }

        return $this->renderForm('contact/index.html.twig', [
            'formContact' => $formContact,
        ]);
    }
}
