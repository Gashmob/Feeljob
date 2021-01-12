<?php


namespace App\Controller;


use App\Entity\Employeur;
use App\Entity\EntityManager;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use Egulias\EmailValidator\Validation\RFCValidation;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use function Sodium\add;

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
     * @param MailerInterface $mailer
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function inscription(string $tab, Request $request, MailerInterface $mailer): Response
    {
        if ($this->session->get('user')) {
            return $this->redirectToRoute('homepage'); // TODO : Changer plus tard pour mettre vers l'espace utilisateur
        }

        if ($request->isMethod('POST')) {
            switch ($tab) {
                case 'chercheur':
                    // TODO : formulaire candidat
                    break;

                case 'entreprise':
                    $nom = $request->get('nom');
                    $nomB = true;
                    if ($nom === '') {
                        $nomB = false;
                        $this->addFlash('form', 'Merci de renseigner un nom');
                    }

                    $prenom = $request->get('prenom');
                    $prenomB = true;
                    if ($prenom === '') {
                        $prenomB = false;
                        $this->addFlash('form', 'Merci de renseigner un prénom');
                    }

                    $nomEntreprise = $request->get('nomEntreprise');
                    $nomEntrepriseB = true;
                    if ($nomEntreprise === '') {
                        $nomEntrepriseB = false;
                        $this->addFlash('form', 'Merci de renseigner un nom d\'entreprise');
                    }

                    $adresse = $request->get('adresse');
                    $adresseB = true;
                    if ($adresse === '') {
                        $adresseB = false;
                        $this->addFlash('form', 'Merci de renseigner une adresse');
                    }

                    $logo = $this->uploadImage();

                    $siret = $request->get('siret');
                    $siretB = true;
                    if ($siret === '') {
                        $siretB = false;
                        $this->addFlash('form', 'Merci de renseigner le numéro siret');
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
                        $this->addFlash('form', 'Merci de renseigner une adresse mail valide');
                    } elseif (EntityManager::isMailUsed($mail)) {
                        $mailB = false;
                        $this->addFlash('form', 'Cet email est déjà utilisé');
                    }

                    $telephone = $request->get('telephone');
                    $telephoneB = true;
                    if (!preg_match('^((([+][0-9]{2})|0)[1-9])([ ]?)([0-9]{2}\4){3}([0-9]{2})$', $telephone)) {
                        $telephoneB = false;
                        $this->addFlash('form', 'Merci de renseigner un numéro de téléphone valide');
                    }

                    $motdepasse = $request->get('motdepasse');
                    $motdepasseB = true;
                    if (!preg_match('^(?=.{8,}$)(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*?\W).*$', $motdepasse)) {
                        $motdepasseB = false;
                        $this->addFlash('form', 'Merci de renseigner un mot de passe valide');
                    }
                    $salt = $this->randomString(16);
                    $motdepasse = password_hash(hash('sha512', hash('sha512', $motdepasse . $salt)), PASSWORD_DEFAULT, ['cost' => 12]);

                    if ($nomB && $prenomB && $nomEntrepriseB && $adresseB && $siretB && $mailB && $telephoneB && $motdepasseB) {
                        $employeur = new Employeur($nom, $prenom, $nomEntreprise, $adresse, $logo, $siret,
                            $description, $mail, $telephone, false, $motdepasse, $salt);
                        $employeur->flush();

                        $email = (new TemplatedEmail())
                            ->from('no-reply@fealjob.com')
                            ->to($mail)
                            ->htmlTemplate('emails/verification.html.twig')
                            ->context([
                                'nom' => $nom,
                                'prenom' => $prenom
                            ]);
                        $mailer->send($email);

                        $this->addFlash('success', 'Bravo ! Vous avez un nouveau compte !');
                        return $this->redirectToRoute('waitVerifEmail', ['id' => $employeur->getId()]);
                    }
                    break;

                case 'auto':
                    // TODO : formulaire auto-entrepreneur
                    break;

                default:
            }
        }

        return $this->render('home/inscription.html.twig', [
            'tab' => $tab,
            'secteurActivites' => EntityManager::getAllActivitySectorName()
        ]);
    }

    /**
     * @Route("/verification/{id}", name="waitVerifEmail", defaults={"id"=""})
     * @param $id
     * @param Request $request
     * @param MailerInterface $mailer
     * @return RedirectResponse|Response
     */
    public function waitVerifEmail($id, Request $request, MailerInterface $mailer)
    {
        if ($id === '') {
            return $this->redirectToRoute('homepage');
        }

        if ($request->isMethod('POST')) {
            // TODO : Récupérer l'utilisateur
            /*$email = (new TemplatedEmail())
                ->from('no-reply@fealjob.com')
                ->to($mail)
                ->htmlTemplate('emails/verification.html.twig')
                ->context([
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'nomEntreprise' => $nomEntreprise
                ]);
            $mailer->send($email);*/
            $this->addFlash('success', 'Email envoyé !');
        }

        return $this->render('home/waitVerifEmail.html.twig');
    }

    /**
     * @Route("/verif/{id}", name="verifEmail", defaults={"id"=""})
     * @param $id
     * @return RedirectResponse
     */
    public function verifEmail($id): RedirectResponse
    {
        if ($id === '') {
            return $this->redirectToRoute('homepage');
        }

        // TODO : Récupérer l'utilisateur et mettre son compte en vérifier et flush

        return $this->redirectToRoute('homepage'); // TODO : rediriger vers la connexion
    }

    // _.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-.

    /**
     * @param int $n
     * @return string
     */
    private function randomString(int $n): string
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

    /**
     * @return string
     */
    private function uploadImage(): string
    {
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            // Infos sur le fichier téléchargé
            $fileTmpPath = $_FILES['logo']['tmp_name'];
            $fileName = $_FILES['logo']['name'];
            $fileSize = $_FILES['logo']['size'];
            $fileType = $_FILES['logo']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Changement du nom par quelque chose qui ne se répétera pas
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

            // Les extensions autorisées
            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'svg');

            if (in_array($fileExtension, $allowedfileExtensions)) {
                $uploadFileDir = './uploads/';
                $dest_path = $uploadFileDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    return $newFileName;
                } else {
                    $this->addFlash('fail', 'L\'image n\'a pas pu être téléchargée, les droits d\'écriture ne sont pas accordés');
                }
            } else {
                $this->addFlash('fail', 'L\'image n\'a pas pu être téléchargée, l\'extension doit être : ' . implode(',', $allowedfileExtensions));
            }
        } else {
            $this->addFlash('fail', 'Il y eu a une erreur lors du téléchargement : ' . $_FILES['uploadedFile']['error']);
        }

        return "";
    }
}