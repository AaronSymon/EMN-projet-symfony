<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    /**
     * @Route("/profil/{id}", name="app_profil")
     */
    public function index($id, ParticipantRepository $partRepo): Response
    {
        $profil = $partRepo->find($id);

        return $this->render('profil/index.html.twig', [
            "profil"=>$profil
        ]);
    }
}
