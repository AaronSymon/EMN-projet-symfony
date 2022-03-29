<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VillesController extends AbstractController
{
    /**
     * @Route("/villes", name="app_villes")
     */
    public function index(): Response
    {
        return $this->render('villes/index.html.twig', [
            'controller_name' => 'VillesController',
        ]);
    }
}
