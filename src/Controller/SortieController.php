<?php

namespace App\Controller;


use App\Entity\Sortie;
use App\Form\AnnulerSortieType;
use App\Form\ModifierSortieType;
use App\Form\SortieFormType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
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
    public function afficherSortie(EtatRepository $etatRepo, SortieRepository $sortieRepo): Response
    {

        //L'on récupère la data et l'heure de maintenant
        date_default_timezone_set('Europe/Paris');
        $datenow = new \DateTime();

        //L'on récupere le tableau de toutes les sorties qui ne sont pas archivées
        $sorties = $sortieRepo->findAll();



        //Pour chaque sorties en BDD, à chaque fois que l'on affiche la liste, l'on verifie :
        //
        // ---si la datetime de maintenant est supérieur ou égale à la date limite des inscriptions,
        //si l'état de la sortie est différent de "Annulee".
        //Si ces conditions sont réunis, l'état de la sortie est égale à "cloturée"

        //---si le nombre de partipant est égale au nombre maximum d"inscription, l'état de la sortie est égale à fermee

        for ($sortie = 0;  $sortie <= count($sorties)-1; $sortie++){

            if ( ($datenow  >= $sorties[$sortie]->getDateLimiteInscription())
                and $sorties[$sortie]->getEtat() != $etatRepo->find(6)){
                $sorties[$sortie]->setEtat($etatRepo->find(8));
            };

            if (count($sorties[$sortie]->getParticipants()) == $sorties[$sortie]->getNbInscriptionMax()){
                $sorties[$sortie]->setEtat($etatRepo->find(3));
            }
        }

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
    public function annulerSorties(EtatRepository $etatRepo, SortieRepository $sortieRepo, $id, Request $request): Response
    {

        //Recuperation de la sortie à annuler
        $sortieAAnnuler = $sortieRepo->find($id);

        //Creation d'un formulaire pour saisir motif annulation sortie

        $sortieAAnnulerForm = $this->createForm(AnnulerSortieType::class,$sortieAAnnuler);
        $sortieAAnnulerForm->handleRequest($request);

        if ($sortieAAnnulerForm->isSubmitted() && $sortieAAnnulerForm->isValid()){

            $sortieAAnnuler->setEtat($etatRepo->find(6));

            $sortieRepo->add($sortieAAnnuler);

            return $this->redirectToRoute('app_mes_sorties');
        }

        return $this->render('sortie/annulerSortie.html.twig',[
            "sortieAAnnulerForm"=>$sortieAAnnulerForm->createView(),
            "sortieAAnnuler"=>$sortieAAnnuler
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

    /**
     * @Route("/annuler-sortie-inscription/{id}", name="app_annuler-sortie-inscription")
     */
    public function annuelerSortieInscription(SortieRepository $sortieRepo, $id): Response
    {
        //Récupération de la sortie où s'inscrire
        $annulerSortieInscription = $sortieRepo->find($id);

        //Suppression du participant

        $annulerSortieInscription->removeParticipant($this->getUser());

        //Suppression du participant dans la  base de données

        $sortieRepo->add($annulerSortieInscription,true);

        return $this->redirectToRoute('app_sortie_afficher', compact("annulerSortieInscription"));
    }
}
