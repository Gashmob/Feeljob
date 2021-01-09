<?php


namespace App\Controller;


use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use Egulias\EmailValidator\Validation\RFCValidation;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/inscription/{tab}", defaults={"tab"="chercheur"}, name="inscription")
     * @param string $tab
     * @param Request $request
     * @return Response
     */
    public function inscription(string $tab, Request $request): Response
    {
        if ($this->session->get('user')) {
            return $this->redirectToRoute('homepage'); // TODO : Changer plus tard pour mettre vers l'espace utilisateur
        }

        if ($request->isMethod('POST')) {
            switch ($tab) {
                case 'chercheur':
                    break;

                case 'entreprise':
                    $nom = $request->get('nom');
                    $nomB = true;
                    if ($nom === '') {
                        $nomB = false;
                        $this->addFlash('fail', 'Merci de renseigner un nom');
                    }

                    $prenom = $request->get('prenom');
                    $prenomB = true;
                    if ($prenom === '') {
                        $prenomB = false;
                        $this->addFlash('fail', 'Merci de renseigner un prénom');
                    }

                    $nomEntreprise = $request->get('nomEntreprise');
                    $nomEntrepriseB = true;
                    if ($nomEntreprise === '') {
                        $nomEntrepriseB = false;
                        $this->addFlash('fail', 'Merci de renseigner un nom d\'entreprise');
                    }

                    $adresse = $request->get('adresse');
                    $adresseB = true;
                    if ($adresse === '') {
                        $adresseB = false;
                        $this->addFlash('fail', 'Merci de renseigner une adresse');
                    }

                    // TODO : logo

                    $siret = $request->get('siret');
                    $siretB = true;
                    if ($siret === '') {
                        $siretB = false;
                        $this->addFlash('fail', 'Merci de renseigner le numéro siret');
                    }

                    $activite = $request->get('activite');

                    $description = $request->get('description');

                    $mail = $request->get('mail');
                    $mailB = true;
                    $validator = new EmailValidator();
                    $multipleValidations = new MultipleValidationWithAnd([
                        new RFCValidation(),
                        new DNSCheckValidation()
                    ]);
                    if (!$validator->isValid($mail, $multipleValidations)) {
                        $mailB = false;
                        $this->addFlash('fail', 'Merci de renseigner une adresse mail valide');
                    } // TODO : Vérifier si mail déjà utilisé
                    // TODO : Envoyer un mail de validation

                    $telephone = $request->get('telephone');
                    $telephoneB = true;
                    if (!preg_match('^((([+][0-9]{2})|0)[1-9])([ ]?)([0-9]{2}\4){3}([0-9]{2})$', $telephone)) {
                        $telephoneB = false;
                        $this->addFlash('fail', 'Merci de renseigner un numéro de téléphone valide');
                    }

                    $motdepasse = $request->get('motdepasse');
                    $motdepasseB = true;
                    if (!preg_match('^(?=.{8,}$)(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*?\W).*$', $motdepasse)) {
                        $motdepasseB = false;
                        $this->addFlash('fail', 'Merci de renseigner un mot de passe valide');
                    }
                    $salt = $this->randomString(16);
                    $motdepasse = password_hash(hash('sha512', hash('sha512', $motdepasse . $salt)), PASSWORD_DEFAULT, ['cost' => 12]);

                    if ($nomB && $prenomB && $telephoneB && $mailB && $motdepasseB && $adresseB) {
                        // TODO : Create Employeur and flush
                        $this->addFlash('success', 'Bravo ! Vous avez un nouveau compte !');
                        $this->redirectToRoute('homepage'); // TODO : Vers page vérification mail
                    }
                    break;

                case 'auto':
                    // TODO
                    break;

                default:
            }
        }

        return $this->render('home/inscription.html.twig', [
            'tab' => $tab,
            'activites' => ['example1', 'example2'] // TODO : récupérer toutes les activités
        ]);
    }

    // _.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-.

    /**
     * @param int $n
     * @return string
     */
    function randomString(int $n): string
    {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $charactersLength = strlen($characters);
        $randomString = "";
        for ($i = 0; $i < $n; $i++) {
            try {
                $randomString .= $characters[random_int(0, $charactersLength - 1)];
            } catch (Exception $e) {
            }
        }
        return $randomString;
    }
}