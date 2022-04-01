<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;




class VilleController extends AbstractController
{
    /**
     * @Route("/ajouter/ville", name="app_ajouter")
     */
    public function ajouter(EntityManagerInterface  $em, Request $request): Response
    {
        $ville = new Ville();
        $formVille = $this->createForm(VilleType::class,$ville);
        $formVille->handleRequest($request);
        if($formVille->isSubmitted() && $formVille->isValid()){
            $em->persist($ville);
            $em->flush();

        }
        return $this->render('ville/ajouter.html.twig',[
            "ajouterVille" =>$formVille->createView()
        ]);
    }


    /**
     * @Route("/list/Ville", name="app_lister")
     */
    public function lister(VilleRepository $villeRepo): Response
    {
       $villesList = $villeRepo->findAll();

       return $this->render('ville/index.html.twig',["villesList" => $villesList]);

    }



    /**
     * @Route("/ville/remove/", name="app_remove")
     */
    public function remove(Request $request,VilleRepository $villeRepo): Response
    {
        $villeDelete = $villeRepo->find($id);
        $ville = $villeRepo->remove($villeDelete);
        return $this->render('ville/supprimer.html.twig', [
            'villeSupprimer' => $ville->createView()]);
    }


    /**
     * @Route("/ville/modifier", name="app_modifier")
     */
    public function modifier(Request $request,VilleRepository $villeRepo,$id): Response
    {
        $villeModifier = $villeRepo->find($id);

        $formVille = $this->createForm(VilleType::class, $villeModifier);

        $formVille->handleRequest($request);


        if($formVille->isSubmitted() && $formVille->isValid()){
            $villeRepo->add($villeModifier,true);
        }

        return $this->render('ville/modifier.html.twig', [
            'formVille' => $formVille->createView()
        ]);

    }


}
