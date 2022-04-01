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
     * @Route("/ville", name="app_ville")
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
        return $this->render('ville/index.html.twig',[
            "formVille" =>$formVille->createView()
        ]);
    }

    /**
     * @Route("/ville/list", name="app_lister")
     */
    public function lister(VilleRepository $villeRepo): Response
    {
       $villes = $villeRepo->findAll();

       return $this->render('ville/index.html.twig',["villes" => $villes]);

    }



    /**
     * @Route("/ville/remove", name="app_remove")
     */
    public function remove(Request $request,VilleRepository $villeRepo): Response
    {
        $token = $request->request->get('_token');
        if($this->isCsrfTokenValid('delete-item',  $token)){
            $id = $request->request->get('id');
            $ville =$this->villeRepo->find($id);
            $this->villeRepo ->remove($ville);
        }
        return $this->redirectToRoute(app_lister);
    }


}
