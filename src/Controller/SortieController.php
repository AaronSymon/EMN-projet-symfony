<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\ModifierSortieType;
use App\Form\SortieFormType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
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
     * @Route("/creer-sortie", name="app_sortie_creer")
     */
    public function creerSortie(LieuRepository $lieuRepo,EtatRepository $etatRepo, SiteRepository $siteRepo,SortieRepository $sortieRepo,Request $request): Response
    {


        //Creation d'un objet Sortie
        $sortie = new Sortie();

        //Creation du formulaire
        $sortieForm = $this->createForm(SortieFormType::class,$sortie);
        $sortieForm->handleRequest($request);


        if ($sortieForm->isSubmitted() && $sortieForm->isValid()){

            //definition des valeurs par defauts de Organisateur, site, etat, sortieLieu
            $sortie->setOrganisateur($this->getUser());
            $sortie->setSite($siteRepo->find($this->getUser()->getSiteRatache()));
            $sortie->setEtat($etatRepo->find(1));
            $sortie->setSortieLieu($lieuRepo->find($request->request->get('sortie_form')['SortieLieu']['0']));
            //$sortie->setSortieLieu($lieuRepo->find($_POST["sortie_form"]["SortieLieu"][0]));

            $sortieRepo->add($sortie,true);


            return $this->redirectToRoute('app_sortie_afficher');
        }

        return $this->render('sortie/creerSortie.html.twig',
        ["sortieForm"=>$sortieForm->createView()]
        );
    }

    /**
     * @Route("/mes-sorties", name="app_mes_sorties")
     */
    public function mesSorties(SortieRepository $sortieRepo): Response
    {
        $mesSorties = $sortieRepo->findBy(array('organisateur'=> $this->getUser()->getId()));

        return $this->render('sortie/mesSorties.html.twig', compact("mesSorties"));
    }

    /**
     * @Route("/modifier-sortie/{id}", name="app_modifier_sortie")
     */
    public function modifierSorties(LieuRepository $lieuRepo, SortieRepository $sortieRepo, $id, Request $request): Response
    {

        //recupération de la sortie à modifier
        $maSortieAModif = $sortieRepo->find($id);

        //création du formulaire pour modifier la sortie
        $maSortieAModifForm = $this->createForm(ModifierSortieType::class,$maSortieAModif);
        $maSortieAModifForm->handleRequest($request);


        if ($maSortieAModifForm->isSubmitted() && $maSortieAModifForm->isValid()){

            $sortieRepo->add($maSortieAModif,true);

            return $this->redirectToRoute('app_mes_sorties');
        }


        return $this->render('sortie/modifierSortie.html.twig', [
            "maSortieAModifForm"=>$maSortieAModifForm->createView()
        ]);
    }

    /**
     * @Route("/annuler-sortie/{id}", name="app_annuler_sortie")
     */
    public function annulerSorties(LieuRepository $lieuRepo, SortieRepository $sortieRepo, $id, Request $request): Response
    {
        return $this->render('sortie/annulerSortie.html.twig',[

        ]);
    }

    /**
     * @Route("/sortie-inscription/{id}", name="app_sortie-inscription")
     */
    public function sortieInscription(SortieRepository $sortieRepo, $id): Response
    {
        //Récupération de la sortie où s'inscrire
        $sortieInscription = $sortieRepo->find($id);

        //Ajout du participant à la liste des personnes inscrite
        $sortieInscription->addParticipant($this->getUser());

        //Ajout du participant à la Base de données
        $sortieRepo->add($sortieInscription,true);

        return $this->redirectToRoute('app_sortie_afficher', compact("sortieInscription"));
    }
}
