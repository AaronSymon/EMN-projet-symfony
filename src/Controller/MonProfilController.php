<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ModifierMonProfilType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class MonProfilController extends AbstractController
{

    private $participantRepo ;

    function __construct(ParticipantRepository $participantRepo)// injection de dépendances
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
     * @Route("/modifier-profil", name="app_modifier")
     */
    public function ModifierMonProfil(Request $request): Response
    {
        $profil = $this->participantRepo->find($this->getUser()->getId());
        $profilForm = $this->createForm(ModifierMonProfilType::class,$profil);
        $profilForm->handleRequest($request);

        if($profilForm->isSubmitted() && $profilForm->isValid()){
            $profil->setPseudo()->add('pseudo');
            $profil->setEmail()->add('email');
            $profil->setNom()->add('nom');
            $profil->setPrenom()->add('prenom');
            $profil->setTelephone()->add('telephone');
            $profil->setPassword()->add('password');
            $profil->setSiteRatache()->add('siteRatache');
        }

        return $this->render('mon_profil/modifierProfil.html.twig',["profilForm"=>$profilForm->createView()]);

    }


}
