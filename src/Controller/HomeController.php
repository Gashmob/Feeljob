<?php


namespace App\Controller;


use App\database\EntityManager;
use App\database\manager\AutoEntrepreneurManager;
use App\database\manager\EmployeManager;
use App\database\manager\EmployeurManager;
use App\database\manager\ParticulierManager;
use App\database\manager\UtilsManager;
use App\Entity\AutoEntrepreneur;
use App\Entity\Employe;
use App\Entity\Employeur;
use App\Entity\Particulier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 * @package App\Controller
 */
class HomeController extends AbstractController
{
    /**
     * @var SessionInterface
     */
    private SessionInterface $session;

    /**
     * HomeController constructor.
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/", name="homepage")
     * @return Response
     */
    public function homepage(): Response
    {
        return $this->render('home/homepage.html.twig');
    }

    /**
     * @Route("/connexion", name="connexion")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function connexion(Request $request, EntityManagerInterface $em): Response
    {
        if ($this->session->get('user')) {
            return $this->redirectToRoute('userSpace');
        }

        if ($request->isMethod('POST')) {
            $mail = $request->get('mail');

            $user = (new UtilsManager())->getUserFromMail($em, $mail);
            if ($user) {
                if ($user->getVerifie()) {
                    $motdepasse = $request->get('motdepasse');

                    if (password_verify(hash('sha512', $motdepasse . $user->getSel()), $user->getMotdepasse())) {
                        $this->session->set('user', $user->getIdentity());
                        $this->session->set('userType', (new UtilsManager())->getUserTypeFromId($user->getIdentity()));
                        $this->session->set('userName', $user->getPrenom());
                        if ($this->session->get('userType') == EntityManager::EMPLOYEUR ||
                            $this->session->get('userType') == EntityManager::AUTO_ENTREPRENEUR) {
                            $this->session->set('userImage', $user->getLogo());
                        } elseif ($this->session->get('userType') == EntityManager::EMPLOYE) {
                            $this->session->set('userImage', $user->getPhoto());
                        }

                        $this->addFlash('success', 'Vous êtes connecté !');

                        return $this->redirectToRoute('userSpace');
                    } else {
                        $this->addFlash('form', 'Identifiants incorrects');
                    }
                } else {
                    $this->addFlash('fail', 'Vous devez vérifier votre mail !');
                }
            } else {
                $this->addFlash('form', 'Identifiants incorrects');
            }
        }

        return $this->render('home/connexion.html.twig');
    }

    /**
     * @Route("/deconnexion", name="deconnexion")
     * @return RedirectResponse
     */
    public function deconnexion(): RedirectResponse
    {
        $this->session->clear();
        $this->addFlash('success', 'Vous êtes déconnecté !');

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/supprimer/compte", name="delete_account")
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function deleteAccount(EntityManagerInterface $em): RedirectResponse
    {
        if (!($this->session->get('user'))) {
            return $this->redirectToRoute('homepage');
        }

        switch ($this->session->get('userType')) {
            case EntityManager::AUTO_ENTREPRENEUR:
                (new AutoEntrepreneurManager())->remove(
                    $em,
                    $em->getRepository(AutoEntrepreneur::class)->findOneBy([
                        'identity' => $this->session->get('user')
                    ])
                );
                break;

            case EntityManager::PARTICULIER:
                (new ParticulierManager())->remove(
                    $em,
                    $em->getRepository(Particulier::class)->findOneBy([
                        'identity' => $this->session->get('user')
                    ])
                );
                break;

            case EntityManager::EMPLOYEUR:
                (new EmployeurManager())->remove(
                    $em,
                    $em->getRepository(Employeur::class)->findOneBy([
                        'identity' => $this->session->get('user')
                    ])
                );
                break;

            case EntityManager::EMPLOYE:
                (new EmployeManager())->remove(
                    $em,
                    $em->getRepository(Employe::class)->findOneBy([
                        'identity' => $this->session->get('user')
                    ])
                );
                break;
        }

        $this->addFlash('success', 'Votre compte a été supprimé !');

        return $this->redirectToRoute('deconnexion');
    }

    /**
     * @Route("/userspace", name="userSpace")
     * @return RedirectResponse
     */
    public function userSpace(): RedirectResponse
    {
        if (!($this->session->get('user'))) {
            return $this->redirectToRoute('homepage');
        }

        if ($this->session->get('userType') == EntityManager::PARTICULIER || $this->session->get('userType') == EntityManager::AUTO_ENTREPRENEUR) {
            return $this->redirectToRoute('particulier_espace');
        } else {
            return $this->redirectToRoute('entreprise_espace');
        }
    }

    /**
     * @Route("/inscription", name="inscription")
     * @return Response
     */
    public function inscription(): Response
    {
        return $this->render('home/choixInscription.html.twig');
    }

    /**
     * @Route("/mail_verifie", name="mailVerified")
     * @return Response
     */
    public function mailVerified(): Response
    {
        return $this->render('home/mailVerified.html.twig');
    }

    /**
     * @Route("/verification/{id}", name="waitVerifEmail", defaults={"id"=""})
     * @param $id
     * @param Request $request
     * @param MailerInterface $mailer
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     * @throws TransportExceptionInterface
     */
    public function waitVerifEmail($id, Request $request, MailerInterface $mailer, EntityManagerInterface $em)
    {
        if ($id === '') {
            return $this->redirectToRoute('homepage');
        }

        $user = (new UtilsManager())->getUserFromId($em, $id);
        if ($user->getVerifie()) {
            return $this->redirectToRoute('userSpace');
        }

        if ($request->isMethod('POST')) {
            if ($user) {
                $email = (new TemplatedEmail())
                    ->from('no-reply@fealjob.com')
                    ->to($user->getEmail())
                    ->htmlTemplate('emails/verification.html.twig')
                    ->context([
                        'nom' => $user->getNom(),
                        'prenom' => $user->getPrenom(),
                        'id' => $id
                    ]);
                $mailer->send($email);
                $this->addFlash('success', 'Email envoyé !');
            }
        }

        return $this->render('home/waitVerifEmail.html.twig', [
            'mail' => $user->getEmail()
        ]);
    }

    /**
     * @Route("/verif/{id}", name="verifEmail", defaults={"id"=""})
     * @param $id
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function verifEmail($id, EntityManagerInterface $em): RedirectResponse
    {
        if ($id === '') {
            return $this->redirectToRoute('homepage');
        }

        $user = (new UtilsManager())->getUserFromId($em, $id);
        if ($user) {
            $user->setVerifie(true);
            $em->flush();

            return $this->redirectToRoute('mailVerified');
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/contact", name="contact")
     * @return Response
     */
    public function contact(): Response
    {
        return $this->render('footer/nousContacter.html.twig');
    }

    /**
     * @Route("/developpeurs", name="developers")
     * @return Response
     */
    public function developers(): Response
    {
        return $this->render('footer/developpeurs.html.twig');
    }

    /**
     * @Route("/cookies", name="cookies")
     * @return Response
     */
    public function cookies(): Response
    {
        return $this->render('footer/cookies.html.twig');
    }

    /**
     * @Route("/confidentialite", name="confidentiality")
     * @return Response
     */
    public function confidentiality(): Response
    {
        return $this->render('footer/confidentialite.html.twig');
    }

    /**
     * @Route("/conditions", name="conditions")
     * @return Response
     */
    public function useConditions(): Response
    {
        return $this->render('footer/conditionsUtilisation.html.twig');
    }

    /**
     * @Route("/voir/{type}", name="show")
     * @param $type
     * @return Response
     */
    public function show($type): Response
    {
        switch ($type) {
            case EntityManager::EMPLOYE:
                return $this->render('home/candidats.html.twig');

            case EntityManager::EMPLOYEUR:
                return $this->render('home/employeurs.html.twig');

            case EntityManager::AUTO_ENTREPRENEUR:
                return $this->render('home/freelances.html.twig');

            case EntityManager::PARTICULIER:
                return $this->render('home/particuliers.html.twig');

            default:
                return $this->render('@Twig/Exception/error404.html.twig');
        }
    }

    /**
     * @Route("/mdp_oublie", name="mdpOublie")
     * @return Response|RedirectResponse
     */
    public function mdpOublie()
    {
        if (!($this->session->get('user'))) {
            return $this->redirectToRoute('homepage');
        }

        return $this->render('home/mdpOublie.html.twig');
    }

    /**
     * @Route("/reinitialiser_mdp", name="mdpReinitialiser")
     * @return Response|RedirectResponse
     */
    public function mdpReinitialiser()
    {
        if (!($this->session->get('user'))) {
            return $this->redirectToRoute('homepage');
        }

        return $this->render('home/mdpReinitialiser.html.twig');
    }

    /**
     * @Route("/ajout/credits", name="ajoutCredits")
     * @return Response|RedirectResponse
     */
    public function ajoutCredit()
    {
        if (!($this->session->get('user'))) {
            return $this->redirectToRoute('homepage');
        }

        if ($this->session->get('userType') != EntityManager::AUTO_ENTREPRENEUR && $this->session->get('userType') != EntityManager::EMPLOYEUR) {
            return $this->redirectToRoute('userSpace');
        }

        return $this->render('utilisateurs/ajoutCredits.html.twig');
    }
}