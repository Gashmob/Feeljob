<?php


namespace App\Controller;


use App\database\EntityManager;
use App\database\exceptions\UserNotFoundException;
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

        return $this->render('home/profil.html.twig', [
            'nom' => $nomPrenom['nom'],
            'prenom' => $nomPrenom['prenom']
        ]);
    }
}