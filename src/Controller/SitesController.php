<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitesController extends AbstractController
{
    /**
     * @Route("/sites", name="app_sites")
     */
    public function index(): Response
    {
        return $this->render('sites/index.html.twig', [
            'controller_name' => 'SitesController',
        ]);
    }
}
