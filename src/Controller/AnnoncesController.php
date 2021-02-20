<?php

namespace App\Controller;


use App\Entity\Annonces;
use App\Form\AnnonceContactType;
use App\Repository\AnnoncesRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;



 /**
 * @Route("/annonces", name="annonces_")
*/
class AnnoncesController extends AbstractController
{
    /**
     * @Route("/details/{slug}", name="details")
     */
    public function details($slug, AnnoncesRepository $annoncesRepo, Request $request,
    MailerInterface $mailer)
    {
        $annonce = $annoncesRepo->findOneBy(['slug' => $slug ]);

        if(!$annonce)
        {
            throw new NotFoundHttpException('Pas d\'annonces trouvées');
        }
        $form = $this->createForm(AnnonceContactType::class);
        // gestion de formulaire
        
        $contact = $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // on crée le mail
            $email = (new TemplatedEmail())
            ->from($contact->get('email')->getData())
            ->to($annonce->getUsers()->getEmail())
            ->subject('contact au sujet de votre annonce"' . $annonce->getTitle() . '"')
            ->htmlTemplate('emails/contact_annonce.html.twig')
            ->context([
                'annonce' => $annonce,
                'e_mail' => $contact->get('email')->getData(),
                'message' =>$contact->get('message')->getData()
            ]);
              // envoyer un message
            $mailer->send($email);

            // On confirme et on redirige  
            $this->addFlash('message', 'Votre e-mail a bien été envoyé');
            return $this->redirectToRoute('annonces_details' , 
            ['slug' => $annonce->getSlug()]);
        }
        //dd($annonce);
        return $this->render('annonces/details.html.twig', 
        [
            'annonce' => $annonce,
            'form' => $form->createView()
        ]);
    }

      /**
     * @Route("/favoris/ajout/{id}", name="ajout_favaoris")
     */
    public function ajoutFavoris(Annonces $annonce)
    {

        if(!$annonce)
        {
            throw new NotFoundHttpException('Pas d\'annonces trouvées');
        }
        $annonce->addFavori($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($annonce);
        $em->flush();
        return $this->redirectToRoute('app_home');
    }
   
     /**
     * @Route("/favoris/retrait/{id}", name="retrait_favoris")
     */
    public function retraitFavoris(Annonces $annonce)
    {
        if(!$annonce){
            throw new NotFoundHttpException('Pas d\'annonce trouvée');
        }
        $annonce->removeFavori($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($annonce);
        $em->flush();
        return $this->redirectToRoute('app_home');
    }
    
}
