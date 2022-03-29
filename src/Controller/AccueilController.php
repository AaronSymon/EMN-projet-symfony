<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\FiltreSortieFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AccueilController extends AbstractController
{
    /**
     * @Route("/accueil", name="app_accueil")
     */
    public function index(Request $request): Response
    {
        $sortie = new Sortie();
        $filtreForm = $this->createForm(FiltreSortieFormType::class,$sortie);
        $filtreForm->handleRequest($request);

        return $this->render('accueil/accueil.html.twig', [
            "filtreForm"=>$filtreForm->createView(),
            "dateNow"=>date("d/m/Y")
        ]);
    }
}
