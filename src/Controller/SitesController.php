<?php

namespace App\Controller;

use App\Entity\Site;
use App\Repository\SiteRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitesController extends AbstractController
{
    /**
     * @Route("/sites", name="app_sites")
     */
    public function index(Request $request, SiteRepository $siteRepo): Response
    {
        $nom="";
        $sites = $siteRepo->findAll();


        if($request->isMethod("POST")){
            if($request->request->has("nomS")) { //vérifie s'il récupère via le filtre
                $nom = $request->request->get("nomS");
                $sites = $siteRepo->search($nom);
            } elseif($request->request->has("newNom")){ //Modifier
                $site = $siteRepo->findOneBy(["nom" => $request->request->get("siteNom")]);
                $site->setNom($request->request->get("newNom"));
                $siteRepo->modif($site);
                $sites = $siteRepo->findAll();
            } elseif ($request->request->has("siteNom")){ //Supprimer
                $siteSupp=$siteRepo->findOneBy(["nom" => $request->request->get("siteNom")]);
                $siteRepo->remove($siteSupp);
                $sites = $siteRepo->findAll();
            } else { //Ajouter
                $newSite = new Site();
                $add = $request->request->get('nomAdd');
                $newSite->setNom($add);
                $siteRepo->add($newSite);
                $sites = $siteRepo->findAll();
            }
        }

        return $this->render('sites/index.html.twig', [
            'sites' => $sites
        ]);
    }
}
