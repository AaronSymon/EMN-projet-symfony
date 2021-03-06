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
use Symfony\Component\String\Slugger\SluggerInterface;

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
     * modification  du profil d'un utilisateur connecté
     * @Route("/modifier/profil", name="app_modifier")
     */
    public function ModifierMonProfil(Request $request,UserPasswordEncoderInterface $encoder,SluggerInterface $slugger): Response
    {
        $user = new Participant();
        $user = $this->getUser();
        $form = $this->createForm(ModifierMonProfilType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $photo = $form->get('photo')->getData();
            if($photo){
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photo->guessExtension();

                $photo->move(
                    $this->getParameter('image_directory'),
                    $newFilename

                );
                $user->setPhoto($newFilename);
            }
                $newPassword = $form->get("plainPassword")['first']->getData();
                // Grâce au service, on génère un nouveau hash de notre nouveau mot de passe
                $hashOfNewPassword = $encoder->encodePassword($user, $newPassword);
                // On change l'ancien mot de passe hashé par le nouveau que l'on a généré juste au dessus
                $user->setPassword($hashOfNewPassword);
                $em->flush();
                $this->addFlash('success', 'Profil modifié avec succès.');
                return $this->redirectToRoute('app_mon_profil');
            }
            return $this->render('mon_profil/modifierProfil.html.twig', [
                'editProfilForm' => $form->createView()
            ]);
        }
    }
