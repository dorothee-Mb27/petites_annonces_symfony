<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Repository\AnnoncesRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(AnnoncesRepository $annoncesRepo): Response
    {
        return $this->render('main/index.html.twig', [
            'annonces' => $annoncesRepo->findBy(['active' => true], ['created_at'
            => 'desc']),
        ]);
    }

    /**
     * @Route("/mentions-legales", name="mentions")
     */
    public function mentions(): Response
    {
        return $this->render('main/mentions.html.twig');
    }
     /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request, MailerInterface $mailer)
    {
        // generer form
        $form = $this->createForm(ContactType::class);

        $contact = $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $email = (new TemplatedEmail())
                ->from($contact->get('email')->getData())
                ->to('Vous@domaine.fr')
                ->subject('contact depuis le site PetitesAnnonces')
                ->htmlTemplate('emails/contact.html.twig')
                ->context([
                    'mail' => $contact->get('email')->getData(),
                    'sujet' => $contact->get('sujet')->getData(),
                    'message' => $contact->get('message')->getData(),
                ])
                ;
                $mailer->send($email);
                $this->addFlash('message', 'Mail envoyé');
                return $this->redirectToRoute('contact');
        }

        return $this->render('main/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
