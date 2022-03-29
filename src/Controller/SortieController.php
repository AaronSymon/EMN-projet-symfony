<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/afficher-sortie", name="app_sortie_afficher")
     */
    public function afficherSortie(SortieRepository $sortieRepo): Response
    {

        $sorties = $sortieRepo->findAll();

        return $this->render('sortie/listeSorties.html.twig', compact("sorties"));
    }
}
