<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuxController extends AbstractController
{
    /**
     * @Route("/lieux", name="app_lieux")
     */
    public function index(Request $request, LieuRepository $lieuRepo, VilleRepository $villeRepo): Response
    {
        $nom=""; $error=0;
        $lieux = $lieuRepo->findAll();
        $villes = $villeRepo->findAll();

        if($request->isMethod("POST")){
            if($request->request->has("nomL")){
                $nom = $request->request->get("nomL");
                $lieux = $lieuRepo->search($nom);
            } else { //ajouter
                $newLieu = new Lieu();
                $newLieu->setNom($request->request->get("Nom"));
                $newLieu->setRue($request->request->get("Rue"));
                $newLieu->setLatitude($request->request->get("Lat"));
                $newLieu->setLongitude($request->request->get("Long"));
                if($request->request->get("nomV") != ""){ //ajoute une nouvelle ville
                    $villeAjout = new Ville();
                    $villeAjout->setNom($request->request->get("nomV"));
                    $villeAjout->setCodePostal($request->request->get("cp"));
                    $villeRepo->add($villeAjout);
                    $newLieu->setVille($villeAjout);
                    $lieuRepo->add($newLieu);
                } elseif($request->request->get("ville") != "") { //a sélectionné une ville existante
                    $newLieu->setVille($villeRepo->findOneBy(["nom" => $request->request->get("ville")]));
                    $lieuRepo->add($newLieu);
                } else {
                    $error = 1;
                }


                $lieux = $lieuRepo->findAll();
            }
        }

        return $this->render('lieux/index.html.twig', [
            "lieux" => $lieux,
            "villes" => $villes,
            "error" => $error
        ]);
    }
}
