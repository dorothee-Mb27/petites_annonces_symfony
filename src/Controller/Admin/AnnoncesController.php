<?php

namespace App\Controller\Admin;

use App\Entity\Annonces;
use App\Repository\AnnoncesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/annonces", name="admin_annonces_")
 *@package App\Controller\Admin
 */
class AnnoncesController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(AnnoncesRepository $annoncesRepo): Response
    {
        return $this->render('admin/annonces/index.html.twig', [
            'annonces' => $annoncesRepo->findAll(),
        ]);
    }
// activer une annonce

 /**
     * @Route("/activer/{id}", name="activer")
     */
    public function activer(Annonces $annonce)
    {
        // ternaire si l'annonce est active ou non
       $annonce->setActive(($annonce->getActive())?false:true);

       $em = $this->getDoctrine()->getManager();
       $em->persist($annonce);
       $em->flush();

       return new Response("true");
    }
    

    // supprimer une annonce

 /**
     * @Route("/supprimer/{id}", name="supprimer")
     */
    public function supprimer(Annonces $annonce)
    {
     
       $em = $this->getDoctrine()->getManager();
       $em->remove($annonce);
       $em->flush();

       $this->addFlash('message', 'Annonce supprimée avec succès');

       return $this->redirectToRoute('admin_annones_home');
    }
    

}
