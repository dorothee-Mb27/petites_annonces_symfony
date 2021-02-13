<?php

namespace App\Controller;

use App\Form\AnnoncesType;
use App\Form\EditProfileType;
use App\Entity\Annonces;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsersController extends AbstractController
{
    /**
     * @Route("/users", name="users")
     */
    public function index(): Response
    {
        return $this->render('users/index.html.twig');
    }

    /**
     * @Route("/users/annonces/ajout", name="users_annonces_ajout")
     */
    public function ajoutAnnonce(Request $request): Response
    {
        $annonce = new Annonces;

        $form = $this->createForm(AnnoncesType::class, $annonce);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $annonce->setUsers($this->getUser());
            $annonce->setActive(false);

            $em = $this->getDoctrine()->getManager();
            $em->persist($annonce);
            $em->flush();

            return $this->redirectToRoute('users');
        }

        return $this->render('users/annonces/ajout.html.twig', [

            'form' => $form->createView(),
        ]);
    }


    // modifier profil

    
    /**
     * @Route("/users/profil/modifier", name="users_profil_modifier")
     */
    public function editProfile(Request $request): Response
    {
        $user = $this->getUser();
    //    utilisateur connecter $this->getUser()
        $form = $this->createForm(EditProfileType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('message', 'Profil mis à jour');

            return $this->redirectToRoute('users');
        }

        return $this->render('users/editprofile.html.twig', [

            'form' => $form->createView(),
        ]);
    }

    
    // modifier mot de passe

    
    /**
     * @Route("/users/pass/modifier", name="users_pass_modifier")
     */
    public function editPass(Request $request, UserPasswordEncoderInterface
    $passwordEncoder ): Response
    {
        if($request->isMethod('POST'))
        {
            // eNTITY DOCTRINE
            $em = $this->getDoctrine()->getManager();

            // on recupere le user
            $user = $this->getUser();
            // on vérifie si les 2 mots de passe sont identiques
            if($request->request->get('pass') == $request->request->get('pass2'))
            {
                // encoder mot de passe
                $user->setPassword($passwordEncoder->encodePassword($user,
                $request->request->get('pass')));
                $em->flush();
                $this->addFlash('message', 'Mot de passe mis à jour avec success');

                // redirection 
                return $this->redirectToRoute('users');
            }
            else
            {
                $this->addFlash('error' , 'Les deux mots de passe ne sont 
                pas identiques' );
            }
        }

        return $this->render('users/editmotdepasse.html.twig');
    }


} // fin de la class UsersController
