<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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


        $UserProfil = $this->participantRepo->findBy(["pseudo"=>true]);

        return $this->render('mon_profil/index.html.twig',compact($UserProfil));
    }
}
