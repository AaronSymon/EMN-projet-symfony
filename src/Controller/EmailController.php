<?php

namespace App\Controller;

use App\Form\ResetPassType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;


class EmailController extends AbstractController
{



        /**
         * @Route("/oubli-pass", name="app_forgotten_password")
         */
        public function oubliPass(Request $request, ParticipantRepository $users, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator): Response
        {
            // On initialise le formulaire
            $form = $this->createForm(ResetPassType::class);

            // On traite le formulaire
            $form->handleRequest($request);

            // Si le formulaire est valide
            if ($form->isSubmitted() && $form->isValid()) {
                // On récupère les données
                $donnees = $form->getData();

                // On cherche un utilisateur ayant cet e-mail
                $user = $users->findOneByEmail($donnees['email']);

                // Si l'utilisateur n'existe pas
                if ($user === null) {
                    // On envoie une alerte disant que l'adresse e-mail est inconnue
                    $this->addFlash('danger', 'Cette adresse e-mail est inconnue');

                    // On retourne sur la page de connexion
                    return $this->redirectToRoute('app_login');
                }

                // On génère un token
                $token = $tokenGenerator->generateToken();

                // On essaie d'écrire le token en base de données
                try {
                    $user->setResetToken($token);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($user);
                    $entityManager->flush();
                } catch (\Exception $e) {
                    $this->addFlash('warning', $e->getMessage());
                    return $this->redirectToRoute('app_login');
                }

                // On génère l'URL de réinitialisation de mot de passe
                $url = $this->generateUrl('app_reset_password1', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

                // On génère l'e-mail
                $mail = (new Email())
                    ->from(new Address('expediteur@demo.test', 'Mon nom'))
                    ->to('destinataire@demo.test')
                    ->subject('edz')
                    ->html($url);

                $mailer->send($mail);
                // On crée le message flash de confirmation
                $this->addFlash('message', 'E-mail de réinitialisation du mot de passe envoyé !');

                // On redirige vers la page de login
                return $this->redirectToRoute('app_login');
            }

            // On envoie le formulaire à la vue
            return $this->render('reset_password/check_email.html.twig', ['emailForm' => $form->createView()]);
        }


    }
