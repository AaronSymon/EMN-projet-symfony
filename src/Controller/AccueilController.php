<?php

namespace App\Controller;

use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AccueilController extends AbstractController
{

    /**
     * @Route("/", name="app_accueil")
     */
    public function index(Request $request, SiteRepository $siteRepo, SortieRepository $sortieRepo, ParticipantRepository $userRepo, EtatRepository $etatRepo): Response
    {
        date_default_timezone_set('Europe/Paris');
        $user=""; $datenow = new \DateTime();
        if($this->isGranted("ROLE_USER")){
            $user = $userRepo->find($this->getUser()->getId());
            $siteC=$user->getSiteRatache()->getNom();
        } else {
            $siteC="Angers";
        }
        $dateD=""; $dateF=""; $ckOrg="on"; $ckIns="on"; $ckNon="on"; $ckPast=""; $mot="";
        $data = array();
        $sites = $siteRepo->findAll();
        $filtre = $this->createFormBuilder($data)->getForm();
        $filtre->handleRequest($request);


        if ($request->isMethod('POST')) {
            $siteC = $request->request->get('Site');
            $mot = $request->request->get('search');
            $dateD = $request->request->get("dateDebut");
            $dateF = $request->request->get("dateFin");
            $ckOrg = $request->request->get('SortOrg');
            $ckIns = $request->request->get('SortIns');
            $ckNon = $request->request->get('SortNon');
            $ckPast = $request->request->get('SortPast');
            if(!$this->isGranted("ROLE_USER")){ $ckNon="on"; } //liste toutes les sorties non passees quand non connecté
        }

        //vérification des états
        $all = $sortieRepo->findAll();
        foreach ($all as $sortie){

            // ---si la datetime de maintenant est supérieur ou égale à la date limite des inscriptions,
            //si l'état de la sortie est différent de "Annulee".
            //Si ces conditions sont réunis, l'état de la sortie est égale à "Fermee"
            if ( ($datenow  >= $sortie->getDateLimiteInscription())
                and $sortie->getEtat() != $etatRepo->findOneBy(["libelle"=>"Annulee"])){
                $sortie->setEtat($etatRepo->findOneBy(["libelle"=>"Cloturee"]));
            };

            //---si le nombre de partipant est égale au nombre maximum d"inscriptions,
            //et si l'état de la sortie est différent d'annuler
            // l'état de la sortie est égale à "Cloturee"
            if (count($sortie->getParticipants()) == $sortie->getNbInscriptionMax()
                and $sortie->getEtat() != $etatRepo->findOneBy(["libelle"=>"Annulee"])){
                $sortie->setEtat($etatRepo->findOneBy(["libelle"=>"Fermee"]));
            }

            //---si l'état de la sortie est égale à "Cloturee" et si le nombre de participant est inférieur au nombre maximum
            //d'inscription, l'état de la sortie est égale "Ouverte".

            if ((count($sortie->getParticipants()) < $sortie->getNbInscriptionMax())
                and ($sortie->getEtat() == $etatRepo->findOneBy(["libelle"=>"Cloturee"]))){
                $sortie->setEtat($etatRepo->findOneBy(["libelle"=>"Ouverte"]));
            }

            //---Si la dateTime now est inclut dans date début activité et date début activité plus durée
            // l'état de la sortie est égale à "Activité en cours"

            //La duree est *60 pour être traitée en tant que minutes

            if (($datenow->getTimestamp() >= $sortie->getDateHeureDebut()->getTimestamp())
                and ($datenow->getTimestamp() <= ($sortie->getDateHeureDebut()->getTimestamp() + ($sortie->getDuree()*60)))){
                $sortie->setEtat($etatRepo->findOneBy(["libelle"=>"En cours"]));

            }

            //---Si le dateTime now est supérieur à date début évenement + durée,
            //si l'état actuel de la sortie est différent de annulée alors l'état de la sortie est égale
            // à "Passee"

            //La duree est *60 pour être traitée en tant que minutes

            if ($datenow->getTimestamp() >= $sortie->getDateHeureDebut()->getTimestamp() + ($sortie->getDuree()*60)
                and $sortie->getEtat() != $etatRepo->findOneBy(["libelle"=>"Annulee"]) ){
                $sortie->setEtat($etatRepo->findOneBy(["libelle"=>"Passee"]));
            }

            $sortieRepo->add($sortie);

        }

        //affichage
        $sorties = $sortieRepo->filtrer($siteC, $mot, $dateD, $dateF, $ckOrg, $ckIns, $ckNon, $ckPast, $user);

        return $this->render('accueil/accueil.html.twig', [
            "sites" => $sites,
            "dateNow" => date("d/m/Y"),
            "filtreForm" => $filtre->createView(),
            "siteC" => $siteC,
            "dateD" => $dateD,
            "dateF" => $dateF,
            "ckOrg" => $ckOrg,
            "ckIns" => $ckIns,
            "ckNon" => $ckNon,
            "ckPast" => $ckPast,
            "sorties" => $sorties
        ]);
    }

    /**
     * @Route("/sortie/{id}", name="app_accueil_sortie")
     */
    public function afficherSortie($id, SortieRepository $sortieRepo){
        $sortie = $sortieRepo->find($id);

        return $this->render('accueil/detail.html.twig', [
            "sortie"=>$sortie
        ]);
    }
}
