<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
}
