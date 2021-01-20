<?php


namespace App\Controller;


use App\database\EntityManager;
use App\database\exceptions\UserNotFoundException;
use App\Entity\AutoEntrepreneur;
use App\Entity\Candidat;
use App\Entity\Entreprise;
use Doctrine\ORM\EntityManagerInterface;
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
     * @Route("/show/{type}", name="show")
     * @param $type
     * @return Response
     */
    public function show($type): Response
    {
        switch ($type) {
            case 'candidat':
                return $this->render('home/candidat.html.twig');

            case 'entreprise':
                return $this->render('home/employeur.html.twig');

            case 'autoEntrepreneur':
                return $this->render('home/autoEntrepreneurs.html.twig');

            default:
                return $this->render('@Twig/Exception/error404.html.twig');
        }
    }

    /**
     * @Route("/connexion", name="connexion")
     * @param Request $request
     * @return Response
     */
    public function connexion(Request $request): Response
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
     */
    public function deconnexion(): RedirectResponse
    {
        $this->session->clear();
        $this->addFlash('success', 'Vous êtes déconnecté !');

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/inscription", name="inscription")
     * @param Request $request
     * @param MailerInterface $mailer
     * @param EntityManagerInterface $em
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function inscription(Request $request, MailerInterface $mailer, EntityManagerInterface $em): Response
    {
        if ($this->session->get('user')) {
            return $this->redirectToRoute('userSpace');
        }

        if ($request->isMethod('POST')) {
            switch ($request->get('tab')) {
                case 'candidat':
                    $nom = $request->get('nom');
                    $nomB = true;
                    if ($nom === '') {
                        $nomB = false;
                        $this->addFlash('nom', 'Merci de renseigner un nom');
                    }

                    $prenom = $request->get('prenom');
                    $prenomB = true;
                    if ($prenom === '') {
                        $prenomB = false;
                        $this->addFlash('prenom', 'Merci de renseigner un prénom');
                    }

                    $telephone = $request->get('telephone');
                    $telephoneB = true;
                    if (!preg_match('/^((([+][0-9]{2})|0)[1-9])([ ]?)([0-9]{2}\\4){3}([0-9]{2})$/', $telephone)) {
                        $telephoneB = false;
                        $this->addFlash('telephone', 'Merci de renseigner un numéro de téléphone valide');
                    }

                    $mail = $request->get('mail');
                    $mailB = true;
                    $validator = new EmailValidator();
                    $multipleValidations = new MultipleValidationWithAnd([
                        new RFCValidation(),
                        new DNSCheckValidation()
                    ]);
                    if (!$validator->isValid($mail, $multipleValidations)) {
                        $mailB = false;
                        $this->addFlash('mail', 'Merci de renseigner une adresse mail valide');
                    } elseif (EntityManager::isMailUsed($mail)) {
                        $mailB = false;
                        $this->addFlash('mail', 'Cet email est déjà utilisé');
                    }

                    $motdepasse = $request->get('motdepasse');
                    $motdepasse2 = $request->get('motdepasse2');
                    $motdepasseB = true;
                    if (!preg_match('/^(?=.{8,}$)(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*?\W).*$/', $motdepasse)) {
                        $motdepasseB = false;
                        $this->addFlash('motdepasse', 'Merci de renseigner un mot de passe valide : <ul><li>1 lettre majuscule</li><li>1 lettre minuscule</li><li>1 chiffre</li><li>1 caractères spécial</li><li>une longueur de 8 caractères</li></ul>');
                    } else if ($motdepasse != $motdepasse2) {
                        $motdepasseB = false;
                        $this->addFlash('motdepasse2', 'Les mots de passe ne concordent pas');
                    }
                    $salt = $this->randomString(16);
                    $motdepasse = password_hash(hash('sha512', $motdepasse . $salt), PASSWORD_BCRYPT, ['cost' => 12]);

                    $conditionsB = true;
                    if (!$request->get('conditions')) {
                        $conditionsB = false;
                        $this->addFlash('condition', 'Vous devez acceptez les conditions d\'utilisation');
                    }

                    if ($nomB && $prenomB && $telephoneB && $mailB && $motdepasseB && $conditionsB) {
                        $candidat = new Candidat();
                        $candidat->setPrenom($prenom)
                            ->setNom($nom)
                            ->setTelephone($telephone);
                        EntityManager::createCandidat($candidat, $em, $motdepasse, $salt, $mail);

                        return $this->sendMailAndWait($mailer, $mail, $candidat->getPrenom(), $candidat->getNom(), $candidat->getIdentity());
                    }
                    break;

                case 'entreprise':
                    $nom = $request->get('nom');
                    $nomB = true;
                    if ($nom === '') {
                        $nomB = false;
                        $this->addFlash('nom', 'Merci de renseigner un nom');
                    }

                    $prenom = $request->get('prenom');
                    $prenomB = true;
                    if ($prenom === '') {
                        $prenomB = false;
                        $this->addFlash('prenom', 'Merci de renseigner un prénom');
                    }

                    $nomEntreprise = $request->get('nomEntreprise');
                    $nomEntrepriseB = true;
                    if ($nomEntreprise === '') {
                        $nomEntrepriseB = false;
                        $this->addFlash('nomEntreprise', 'Merci de renseigner un nom d\'entreprise');
                    }

                    $adresse = $request->get('adresse');
                    $adresseB = true;
                    if ($adresse === '') {
                        $adresseB = false;
                        $this->addFlash('adresse', 'Merci de renseigner une adresse');
                    }

                    $logo = $this->uploadImage();

                    $siret = $request->get('siret');
                    $siretB = true;
                    if ($siret === '') {
                        $siretB = false;
                        $this->addFlash('siret', 'Merci de renseigner le numéro siret');
                    }

                    $activite = $request->get('activite');
                    if (is_string($activite)) {
                        $activite = [$activite];
                    }

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
                        $this->addFlash('mail', 'Merci de renseigner une adresse mail valide');
                    } elseif (EntityManager::isMailUsed($mail)) {
                        $mailB = false;
                        $this->addFlash('mail', 'Cet email est déjà utilisé');
                    }

                    $telephone = $request->get('telephone');
                    $telephoneB = true;
                    if (!preg_match('/^((([+][0-9]{2})|0)[1-9])([ ]?)([0-9]{2}\4){3}([0-9]{2})$/', $telephone)) {
                        $telephoneB = false;
                        $this->addFlash('telephone', 'Merci de renseigner un numéro de téléphone valide');
                    }

                    $motdepasse = $request->get('motdepasse');
                    $motdepasse2 = $request->get('motdepasse2');
                    $motdepasseB = true;
                    if (!preg_match('/^(?=.{8,}$)(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*?\W).*$/', $motdepasse)) {
                        $motdepasseB = false;
                        $this->addFlash('motdepasse', 'Merci de renseigner un mot de passe valide : <ul><li>1 lettre majuscule</li><li>1 lettre minuscule</li><li>1 chiffre</li><li>1 caractères spécial</li><li>une longueur de 8 caractères</li></ul>');
                    } else if ($motdepasse != $motdepasse2) {
                        $motdepasseB = false;
                        $this->addFlash('motdepasse2', 'Les mots de passe ne concordent pas');
                    }
                    $salt = $this->randomString(16);
                    $motdepasse = password_hash(hash('sha512', $motdepasse . $salt), PASSWORD_BCRYPT, ['cost' => 12]);

                    $conditionsB = true;
                    if (!$request->get('conditions')) {
                        $conditionsB = false;
                        $this->addFlash('condition', 'Vous devez acceptez les conditions d\'utilisation');
                    }

                    if ($nomB && $prenomB && $nomEntrepriseB && $adresseB && $siretB && $mailB && $telephoneB && $motdepasseB && $conditionsB) {
                        $entreprise = new Entreprise();
                        $entreprise->setNom($nom)
                            ->setPrenom($prenom)
                            ->setNomEntreprise($nomEntreprise)
                            ->setLogo($logo)
                            ->setSiret($siret)
                            ->setDescription($description)
                            ->setTelephone($telephone);
                        EntityManager::createEntreprise($entreprise, $em, $motdepasse, $salt, $mail, $activite);

                        return $this->sendMailAndWait($mailer, $mail, $entreprise->getPrenom(), $entreprise->getNom(), $entreprise->getIdentity());
                    }
                    break;

                case 'auto':
                    $nom = $request->get('nom');
                    $nomB = true;
                    if ($nom === '') {
                        $nomB = false;
                        $this->addFlash('nom', 'Merci de renseigner un nom');
                    }

                    $prenom = $request->get('prenom');
                    $prenomB = true;
                    if ($prenom === '') {
                        $prenomB = false;
                        $this->addFlash('prenom', 'Merci de renseigner un prénom');
                    }

                    $nomEntreprise = $request->get('nomEntreprise');
                    $nomEntrepriseB = true;
                    if ($nomEntreprise === '') {
                        $nomEntrepriseB = false;
                        $this->addFlash('nomEntreprise', 'Merci de renseigner un nom d\'entreprise');
                    }

                    $adresse = $request->get('adresse');
                    $adresseB = true;
                    if ($adresse === '') {
                        $adresseB = false;
                        $this->addFlash('adresse', 'Merci de renseigner une adresse pour l\'entreprise');
                    }

                    $logo = $this->uploadImage();

                    $siret = $request->get('siret');
                    $siretB = true;
                    if ($siret === '') {
                        $siretB = false;
                        $this->addFlash('siret', 'Merci de renseigner un siret');
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
                        $this->addFlash('mail', 'Merci de renseigner une adresse mail valide');
                    } elseif (EntityManager::isMailUsed($mail)) {
                        $mailB = false;
                        $this->addFlash('mail', 'Cet email est déjà utilisé');
                    }

                    $telephone = $request->get('telephone');
                    $telephoneB = true;
                    if (!preg_match('/^((([+][0-9]{2})|0)[1-9])([ ]?)([0-9]{2}\4){3}([0-9]{2})$/', $telephone)) {
                        $telephoneB = false;
                        $this->addFlash('telephone', 'Merci de renseigner un numéro de téléphone valide');
                    }

                    $motdepasse = $request->get('motdepasse');
                    $motdepasse2 = $request->get('motdepasse2');
                    $motdepasseB = true;
                    if (!preg_match('/^(?=.{8,}$)(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*?\W).*$/', $motdepasse)) {
                        $motdepasseB = false;
                        $this->addFlash('motdepasse', 'Merci de renseigner un mot de passe valide</br>Au minimum : <ul><li>1 lettre majuscule</li><li>1 lettre minuscule</li><li>1 chiffre</li><li>1 caractères spécial</li><li>une longueur de 8 caractères</li></ul>');
                    } else if ($motdepasse != $motdepasse2) {
                        $motdepasseB = false;
                        $this->addFlash('motdepasse2', 'Les mots de passe ne concordent pas');
                    }
                    $salt = $this->randomString(16);
                    $motdepasse = password_hash(hash('sha512', $motdepasse . $salt), PASSWORD_BCRYPT, ['cost' => 12]);

                    $conditionsB = true;
                    if (!$request->get('conditions')) {
                        $conditionsB = false;
                        $this->addFlash('condition', 'Vous devez acceptez les conditions d\'utilisation');
                    }

                    if ($nomB && $prenomB && $nomEntrepriseB && $adresseB && $siretB && $mailB && $telephoneB && $motdepasseB && $conditionsB) {
                        $autoEntrepreneur = new AutoEntrepreneur();
                        $autoEntrepreneur->setPrenom($prenom)
                            ->setNom($nom)
                            ->setTelephone($telephone)
                            ->setDescription($description)
                            ->setSiret($siret)
                            ->setLogo($logo)
                            ->setNomEntreprise($nomEntreprise)
                            ->setAbonne(false);
                        EntityManager::createAutoEntrepreneur($autoEntrepreneur, $em, $motdepasse, $salt, $mail, $activite);

                        return $this->sendMailAndWait($mailer, $mail, $autoEntrepreneur->getPrenom(), $autoEntrepreneur->getNom(), $autoEntrepreneur->getIdentity());
                    }
                    break;

                default:
            }
        }

        return $this->render('home/inscription.html.twig', [
            'secteurActivites' => EntityManager::getAllActivitySectorName()
        ]);
    }

    /**
     * @Route("/verification/{id}", name="waitVerifEmail", defaults={"id"=""})
     * @param $id
     * @param Request $request
     * @param MailerInterface $mailer
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     * @throws TransportExceptionInterface
     * @throws UserNotFoundException
     */
    public function waitVerifEmail($id, Request $request, MailerInterface $mailer, EntityManagerInterface $em)
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
    public function verifEmail($id): RedirectResponse
    {
        if ($id === '') {
            return $this->redirectToRoute('homepage');
        }

        $user = EntityManager::getGenericUserFromId($id);
        if ($user) {
            $user->setVerifie(true);
            $user->flush();

            $this->addFlash('success', 'Votre email est vérifié');
            return $this->redirectToRoute('connexion');
        }

        return $this->redirectToRoute('homepage');
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
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Changement du nom par quelque chose qui ne se répétera pas
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

            // Les extensions autorisées
            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'svg');

            if (!file_exists('./uploads/logos')) {
                mkdir('./uploads/logos');
            }

            if (in_array($fileExtension, $allowedfileExtensions)) {
                $uploadFileDir = './uploads/logos/';
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

    /**
     * @param MailerInterface $mailer
     * @param string $email
     * @param string $prenom
     * @param string $nom
     * @param int $id
     * @return RedirectResponse
     * @throws TransportExceptionInterface
     */
    private function sendMailAndWait(MailerInterface $mailer, string $email, string $prenom, string $nom, int $id): RedirectResponse
    {
        $email = (new TemplatedEmail())
            ->from('no-reply@fealjob.com')
            ->to($email)
            ->htmlTemplate('emails/verification.html.twig')
            ->context([
                'nom' => $nom,
                'prenom' => $prenom,
                'id' => $id
            ]);
        $mailer->send($email);

        $this->addFlash('success', 'Bravo ! Vous avez un nouveau compte !');
        return $this->redirectToRoute('waitVerifEmail', ['id' => $id]);
    }
}