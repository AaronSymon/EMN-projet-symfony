<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ModifierMonProfilType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Bar\UserInterface;

class MonProfilController extends AbstractController
{

    private $participantRepo ;

    function __construct(ParticipantRepository $participantRepo)
    {
        $this->participantRepo = $participantRepo;
    }


    /**
     * @Route("/mon/profil", name="app_mon_profil")
     */
    public function monProfil(): Response
    {


        $monProfil = $this->participantRepo->find($this->getUser()->getId());
        return $this->render('mon_profil/index.html.twig',compact("monProfil"));

    }
    /**
     * @Route("/modifier/profil", name="app_modifier")
     */
    public function ModifierMonProfil(Request $request,UserPasswordEncoderInterface $encoder): Response
    {

        $user = $this->getUser();

        $form = $this->createForm(ModifierMonProfilType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            $newPassword = $form->get("plainPassword")['first']->getData();

            // Grâce au service, on génère un nouveau hash de notre nouveau mot de passe
            $hashOfNewPassword = $encoder->encodePassword($user, $newPassword);

            // On change l'ancien mot de passe hashé par le nouveau que l'on a généré juste au dessus
            $user->setPassword( $hashOfNewPassword );
            $em->flush();

            $this->addFlash('success', 'Profil modifié avec succès.');
            return $this->redirectToRoute('app_mon_profil');
        }

        // Pour que la vue puisse afficher le formulaire, on doit lui envoyer le formulaire généré, avec $form->createView()
        return $this->render('mon_profil/modifierProfil.html.twig', [
            'editProfilForm' => $form->createView()
        ]);



    }



}
