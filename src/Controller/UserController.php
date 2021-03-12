<?php


namespace App\Controller;


use App\database\EntityManager;
use App\database\exceptions\UserNotFoundException;
use App\Entity\AutoEntrepreneur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @var SessionInterface
     */
    private SessionInterface $session;

    /**
     * UserController constructor.
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/my-space", name="userSpace")
     * @param EntityManagerInterface $em
     * @return Response
     * @throws UserNotFoundException
     */
    public function userSpace(EntityManagerInterface $em): Response
    {
        if (!$this->session->get('user'))
            return $this->redirectToRoute('connexion');

        $nomPrenom = EntityManager::getNomPrenomFromId($this->session->get('user'), $em);

        $offres = [];
        $nomEntreprise = '';
        if ($this->session->get('userType') == 'Candidat') {
            $offres = EntityManager::getCVFromUser($em, $this->session->get('user'));
        } elseif ($this->session->get('userType') == 'Entreprise') {
            $offres = EntityManager::getEmploiFromUser($em, $this->session->get('user'));
            $nomEntreprise = EntityManager::getNomEntrepriseFromId($this->session->get('user'), $em);
        } elseif ($this->session->get('userType') == 'Freelance') {
            $offres = $em->getRepository(AutoEntrepreneur::class)->findOneBy(['identity' => $this->session->get('user')])->getCarte();
        }

        return $this->render('home/profil.html.twig', [
            'nom' => $nomPrenom['nom'],
            'prenom' => $nomPrenom['prenom'],
            'publications' => $offres,
            'nomEntreprise' => $nomEntreprise
        ]);
    }

    /**
     * @Route("/preferences", name="preferences")
     * @return Response
     */
    public function preferences(): Response
    {
        return $this->render('candidat/preferences.html.twig');
    }

    /**
     * @Route("/annonceChantier", name="annonceChantier")
     * @return Response
     */
    public function annonceChantier(): Response
    {
        return $this->render('autoEntrepreneur/creerAnnonceChantier.html.twig');
    }

    /**
     * @Route("/create/carte", name="createCarteVisite")
     * @return Response
     */
    public function createCarteVisite(): Response
    {
        return $this->render('autoEntrepreneur/createCarteVisite.html.twig');
    }
}