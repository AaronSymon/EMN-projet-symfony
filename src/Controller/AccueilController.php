<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\FiltreSortieFormType;
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
     * @Route("/accueil", name="app_accueil")
     */
    public function index(Request $request, SiteRepository $siteRepo, SortieRepository $sortieRepo, ParticipantRepository $userRepo): Response
    {
        $siteC=""; $dateD=""; $dateF=""; $ckOrg="on"; $ckIns="on"; $ckNon="on"; $ckPast=""; $mot=""; $sorties=array();
        $data = array();
        $sites = $siteRepo->findAll();
        $filtre = $this->createFormBuilder($data)->getForm();
        $filtre->handleRequest($request);


        if ($request->isMethod('POST')) {
            $siteC = $request->request->get('Site');
            $mot = $request->request->get('nameSearch');
            $dateD = $request->request->get("dateDebut");
            $dateF = $request->request->get("dateFin");
            $ckOrg = $request->request->get('SortOrg');
            $ckIns = $request->request->get('SortIns');
            $ckNon = $request->request->get('SortNon');
            $ckPast = $request->request->get('SortPast');
        }

        $user = $userRepo->find($this->getUser()->getId());
        $sorties = $sortieRepo->filtrer($siteC, $mot, $dateD, $dateF, $ckOrg, $ckIns, $ckNon, $ckPast, $user);
        //$sorties = $sortieRepo->findAll();
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
}
