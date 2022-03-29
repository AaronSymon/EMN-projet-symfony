<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieFormType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use http\Client\Curl\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/creer-sortie", name="app_sortie_afficher")
     */
    public function creerSortie(SortieRepository $sortieRepo,Request $request): Response
    {

        //Creation d'un objet Sortie
        $sortie = new Sortie();

        //Creation du formulaire
        $sortieForm = $this->createForm(SortieFormType::class,$sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()){
            $sortie->setOrganisateur();
            $sortieRepo->add($sortie,true);

            return $this->redirectToRoute('app_sortie_afficher');
        }

        return $this->render('sortie/creerSortie.html.twig',
        ["sortieForm"=>$sortieForm->createView()]
        );
    }
}
