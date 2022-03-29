<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\FiltreSortieFormType;
use App\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AccueilController extends AbstractController
{
    /**
     * @Route("/accueil", name="app_accueil")
     */
    public function index(Request $request, SiteRepository $siteRepo): Response
    {

        $sites = $siteRepo->findAll();
        return $this->render('accueil/accueil.html.twig', [
            "sites"=>$sites,
            "dateNow"=>date("d/m/Y")
        ]);
    }
}
