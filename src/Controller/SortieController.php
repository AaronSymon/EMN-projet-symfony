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



        //Pour chaque sorties en BDD, à chaque fois que l'on affiche la liste, l'on verifie

        for ($sortie = 0;  $sortie <= count($sorties)-1; $sortie++){

            // ---si la datetime de maintenant est supérieur ou égale à la date limite des inscriptions,
            //si l'état de la sortie est différent de "Annulee".
            //Si ces conditions sont réunis, l'état de la sortie est égale à "Fermee"
            if ( ($datenow  >= $sorties[$sortie]->getDateLimiteInscription())
                and $sorties[$sortie]->getEtat() != $etatRepo->findOneBy(["libelle"=>"Annulee"])){
                $sorties[$sortie]->setEtat($etatRepo->findOneBy(["libelle"=>"Fermee"]));
            };

            //---si le nombre de partipant est égale au nombre maximum d"inscriptions,
            //et si l'état de la sortie est différent d'annuler
            // l'état de la sortie est égale à "Cloturee"
            if (count($sorties[$sortie]->getParticipants()) == $sorties[$sortie]->getNbInscriptionMax()
            and $sorties[$sortie]->getEtat() != $etatRepo->findOneBy(["libelle"=>"Annulee"])){
                $sorties[$sortie]->setEtat($etatRepo->findOneBy(["libelle"=>"Cloturee"]));
            }

            //---si l'état de la sortie est égale à "Cloturee" et si le nombre de participant est inférieur au nombre maximum
            //d'inscription, l'état de la sortie est égale "Ouverte".

            if ((count($sorties[$sortie]->getParticipants()) < $sorties[$sortie]->getNbInscriptionMax())
            and ($sorties[$sortie]->getEtat() == $etatRepo->findOneBy(["libelle"=>"Cloturee"]))){
                $sorties[$sortie]->setEtat($etatRepo->findOneBy(["libelle"=>"Ouverte"]));
            }

            //---Si la dateTime now est inclut dans date début activité et date début activité plus durée
            // l'état de la sortie est égale à "Activité en cours"

            //La duree est *60 pour être traitée en tant que minutes

            if (($datenow->getTimestamp() >= $sorties[$sortie]->getDateHeureDebut()->getTimestamp())
            and ($datenow->getTimestamp() <= $sorties[$sortie]->getDateHeureDebut()->getTimestamp() + ($sorties[$sortie]->getDuree()*60))){
                $sorties[$sortie]->setEtat($etatRepo->findOneBy(["libelle"=>"Activite en cours"]));
            }

            //---Si le dateTime now est supérieur à date début évenement + durée,
            //si l'état actuel de la sortie est différent de annulée alors l'état de la sortie est égale
            // à "Passee"

            //La duree est *60 pour être traitée en tant que minutes

            if ($datenow->getTimestamp() >= $sorties[$sortie]->getDateHeureDebut()->getTimestamp() + ($sorties[$sortie]->getDuree()*60)
            and $sorties[$sortie]->getEtat() != $etatRepo->findOneBy(["libelle"=>"Annulee"]) ){
                $sorties[$sortie]->setEtat($etatRepo->findOneBy(["libelle"=>"Passee"]));
            }

            //L'on applique les changements d'état en base de données
            $sortieRepo->add($sorties[$sortie],true);

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

            //Si date limite inscription > date debut sortie alors on renvoit vers la page et l'on indique que
            //que la date ne peut pas être plus grande

            if ($sortie->getDateLimiteInscription() > $sortie->getDateHeureDebut()){
                $this->addFlash('error', "La date d'inscription ne peut être plus grande que celle du début de l'événement");
                return $this->redirectToRoute("app_sortie_creer");

            } else {
                //definition des valeurs par defauts de Organisateur, site, etat, sortieLieu
                $sortie->setOrganisateur($this->getUser());
                $sortie->setSite($siteRepo->find($this->getUser()->getSiteRatache()));
                $sortie->setEtat($etatRepo->find(1));
                $sortie->setSortieLieu($lieuRepo->find($request->request->get('sortie_form')['SortieLieu']['0']));
                //$sortie->setSortieLieu($lieuRepo->find($_POST["sortie_form"]["SortieLieu"][0]));

                $sortieRepo->add($sortie,true);


                return $this->redirectToRoute('app_sortie_afficher');
            }

        }

        return $this->render('sortie/creerSortie.html.twig',
        ["sortieForm"=>$sortieForm->createView(),
            "lieux"=>$lieuRepo->findAll()]
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
     * @Route("/publier-sortie/{id}", name="app_sortie-publication")
     */
    public function publierSorties(EtatRepository $etatRepo, SortieRepository $sortieRepo, $id): Response
    {
        //Récupération de la sortie à publier
        $sortieAPublier = $sortieRepo->find($id);

        //Modification de l'état de la sortie à Publier. Passe de "Creee" à "Ouverte"
        $sortieAPublier->setEtat($etatRepo->findOneBy(["libelle"=>"Ouverte"]));

        //Modification de l'état de la sortie en base de données
        $sortieRepo->add($sortieAPublier,true);

        return $this->redirectToRoute('app_sortie_afficher', compact("sortieAPublier"));
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

            $sortieAAnnuler->setEtat($etatRepo->findOneBy(["libelle"=>"Annulee"]));

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
