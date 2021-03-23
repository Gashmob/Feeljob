<?php


namespace App\Controller;


use Doctrine\ORM\EntityManagerInterface;
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
    public function connexion(Request $request, EntityManagerInterface $em): Response // TODO : wait for dev-bdd
    {
        if ($this->session->get('user')) {
            return $this->redirectToRoute('userSpace');
        }

        if ($request->isMethod('POST')) {
            $mail = $request->get('mail');

            $user = EntityManager::getGenericUserFromMail($mail);
            if ($user) {
                if ($user->isVerifie()) {
                    $motdepasse = $request->get('motdepasse');

                    if (password_verify(hash('sha512', $motdepasse . $user->getSel()), $user->getMotdepasse())) {
                        $this->session->set('user', $user->getId());
                        $this->session->set('userType', EntityManager::getUserTypeFromId($user->getId()));
                        $this->session->set('userName', EntityManager::getNomPrenomFromId($user->getId(), $em)['prenom']);

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
    public function waitVerifEmail($id, Request $request, MailerInterface $mailer, EntityManagerInterface $em) // TODO : wait for dev-bdd
    {
        if ($id === '') {
            return $this->redirectToRoute('homepage');
        }

        $user = EntityManager::getGenericUserFromId($id);
        if ($user->isVerifie()) {
            return $this->redirectToRoute('userSpace');
        }

        if ($request->isMethod('POST')) {
            $nomPrenom = EntityManager::getNomPrenomFromId($id, $em);
            if ($user) {
                $email = (new TemplatedEmail())
                    ->from('no-reply@fealjob.com')
                    ->to($user->getEmail())
                    ->htmlTemplate('emails/verification.html.twig')
                    ->context([
                        'nom' => $nomPrenom['nom'],
                        'prenom' => $nomPrenom['prenom'],
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
     * @return RedirectResponse
     */
    public function verifEmail($id): RedirectResponse // TODO : wait for dev-bdd
    {
        if ($id === '') {
            return $this->redirectToRoute('homepage');
        }

        $user = EntityManager::getGenericUserFromId($id);
        if ($user) {
            $user->setVerifie(true);
            $user->flush();

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
     * @Route("/confidentiality", name="confidentiality")
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
     * @Route("/show/{type}", name="show")
     * @param $type
     * @return Response
     */
    public function show($type): Response
    {
        switch ($type) {
            case 'candidats':
                return $this->render('home/candidats.html.twig');

            case 'entreprises':
                return $this->render('home/employeurs.html.twig');

            case 'freelances':
                return $this->render('home/freelances.html.twig');

            case 'particuliers':
                return $this->render('home/particuliers.html.twig');

            default:
                return $this->render('@Twig/Exception/error404.html.twig');
        }
    }
}