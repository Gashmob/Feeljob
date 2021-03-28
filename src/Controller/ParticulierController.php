<?php


namespace App\Controller;


use App\database\EntityManager;
use App\Entity\Adresse;
use App\Entity\Annonce;
use App\Entity\AutoEntrepreneur;
use App\Entity\CarteVisite;
use App\Entity\Particulier;
use App\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use Egulias\EmailValidator\Validation\RFCValidation;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

/**
 * Class ParticulierController
 * @package App\Controller
 * @Route("/particulier")
 */
class ParticulierController extends AbstractController
{
    /**
     * @var SessionInterface
     */
    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/inscription", name="particulier_inscription")
     * @param Request $request
     * @param MailerInterface $mailer
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     * @throws Exception
     * @throws TransportExceptionInterface
     */
    public function inscription(Request $request, MailerInterface $mailer, EntityManagerInterface $em)
    {
        if ($this->session->get('user')) {
            return $this->redirectToRoute('userSpace');
        }

        if ($request->isMethod('POST')) {
            switch ($request->get('tab')) {
                case EntityManager::AUTO_ENTREPRENEUR:
                    $data = $this->getInscriptionData($request, $em);

                    $nomEntreprise = $request->get('nomEntreprise');
                    $nomEntrepriseB = true;
                    if ($nomEntreprise == '') {
                        $nomEntrepriseB = false;
                        $this->addFlash('nomEntreprise', 'Merci de renseigner un nom pour votre entreprise');
                    }

                    $logo = Utils::uploadImage('logo');

                    $siret = $request->get('siret');
                    $siretB = true;
                    if ($siret == '') {
                        $siretB = false;
                        $this->addFlash('siret', 'Merci de renseigner un numéro siret');
                    } elseif (strlen($siret) != 14) {
                        $siretB = false;
                        $this->addFlash('siret', 'Votre numéro siret est invalide');
                    }

                    $description = $request->get('description');

                    $secteurActivite = $request->get('secteurActivite');
                    $secteurActiviteB = true;
                    if ($secteurActivite == '') {
                        $secteurActiviteB = false;
                        $this->addFlash('secteurActivite', 'Merci de renseigner votre secteur d\'activité');
                    }

                    if ($data['ok'] && $nomEntrepriseB && $siretB && $secteurActiviteB) {
                        $adresse = (new Adresse())
                            ->setRue($data['rue'])
                            ->setCodePostal($data['code_postal'])
                            ->setVille($data['ville']);
                        $em->persist($adresse);
                        $em->flush();

                        $auto_entrepreneur = (new AutoEntrepreneur())
                            ->setNom($data['nom'])
                            ->setPrenom($data['prenom'])
                            ->setNomEntreprise($nomEntreprise)
                            ->setAdresse($adresse)
                            ->setTelephone($data['telephone'])
                            ->setEmail($data['email'])
                            ->setMotdepasse($data['motdepasse'])
                            ->setSel($data['sel'])
                            ->setLogo($logo)
                            ->setSiret($siret)
                            ->setDescription($description);

                        EntityManager::getRepository(EntityManager::AUTO_ENTREPRENEUR)->create($em, $auto_entrepreneur, $secteurActivite);

                        Utils::sendMailAndWait($mailer, $auto_entrepreneur->getEmail(), $auto_entrepreneur->getPrenom(), $auto_entrepreneur->getNom(), $auto_entrepreneur->getIdentity());
                        $this->addFlash('success', 'Bravo ! Vous avez un nouveau compte !');

                        return $this->redirectToRoute('waitVerifEmail', ['id' => $auto_entrepreneur->getIdentity()]);
                    }
                    break;

                case EntityManager::PARTICULIER:
                    $data = $this->getInscriptionData($request, $em);

                    if ($data['ok']) {
                        $adresse = (new Adresse())
                            ->setRue($data['rue'])
                            ->setCodePostal($data['code_postal'])
                            ->setVille($data['ville']);
                        $em->persist($adresse);
                        $em->flush();

                        $particulier = (new Particulier())
                            ->setPrenom($data['prenom'])
                            ->setNom($data['nom'])
                            ->setTelephone($data['telephone'])
                            ->setEmail($data['email'])
                            ->setMotdepasse($data['motdepasse'])
                            ->setSel($data['sel'])
                            ->setAdresse($adresse);

                        EntityManager::getRepository(EntityManager::PARTICULIER)->create($em, $particulier);

                        Utils::sendMailAndWait($mailer, $particulier->getEmail(), $particulier->getPrenom(), $particulier->getNom(), $particulier->getIdentity());
                        $this->addFlash('success', 'Bravo ! Vous avez un nouveau compte !');

                        return $this->redirectToRoute('waitVerifEmail', ['id' => $particulier->getIdentity()]);
                    }
                    break;
            }
        }

        return $this->render('particulier/inscription.html.twig', [
            'secteurActivites' => EntityManager::getRepository(EntityManager::SECTEUR_ACTIVITE)->findAll(),
            'particulier' => EntityManager::PARTICULIER,
            'auto_entrepreneur' => EntityManager::AUTO_ENTREPRENEUR
        ]);
    }

    /**
     * @Route("/mon_espace", name="particulier_espace")
     * @param EntityManagerInterface $em
     * @return Response|RedirectResponse
     */
    public function userSpace(EntityManagerInterface $em)
    {
        if (!($this->session->get('user'))) {
            return $this->redirectToRoute('homepage');
        }

        $type = EntityManager::getRepository(EntityManager::UTILS)->getUserTypeFromId($this->session->get('user'));
        if ($type == EntityManager::EMPLOYEUR || $type == EntityManager::EMPLOYE) {
            return $this->redirectToRoute('entreprise_espace');
        }

        $user = EntityManager::getRepository(EntityManager::UTILS)->getUserFromId($em, $this->session->get('user'));

        return $this->render('home/profil.html.twig', [
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom()
        ]);
    }

    /**
     * @Route("/cree/carte", name="particulier_create_carte")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response|RedirectResponse
     */
    public function createCarteVisite(Request $request, EntityManagerInterface $em)
    {
        if (!($this->session->get('user'))) {
            return $this->redirectToRoute('homepage');
        }

        if (EntityManager::getRepository(EntityManager::UTILS)->getUserTypeFromId($this->session->get('user')) != EntityManager::AUTO_ENTREPRENEUR) {
            return $this->redirectToRoute('homepage');
        }

        if ($request->isMethod('POST')) {
            $description = $request->get('description');
            $descriptionB = true;
            if ($description == '') {
                $descriptionB = false;
                $this->addFlash('description', 'Merci de donner une description à votre carte de visite');
            }

            if ($descriptionB) {
                $auto_entrepreneur = $em->getRepository(AutoEntrepreneur::class)->findOneBy(['identity' => $this->session->get('user')]);

                $carte = (new CarteVisite())
                    ->setDescription($description);
                $em->persist($carte);
                $em->flush();

                // TODO : get all realisations

                $auto_entrepreneur->setCarteVisite($carte);
                $em->flush();
            }
        }

        return $this->render('autoEntrepreneur/createCarteVisite.html.twig');
    }

    /**
     * @Route("/cree/annonce", name="particulier_create_annonce")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response|RedirectResponse
     */
    public function createAnnonce(Request $request, EntityManagerInterface $em)
    {
        if (!($this->session->get('user'))) {
            return $this->redirectToRoute('homepage');
        }

        if (EntityManager::getRepository(EntityManager::UTILS)->getUserTypeFromId($this->session->get('user')) != EntityManager::PARTICULIER) {
            return $this->redirectToRoute('homepage');
        }

        if ($request->isMethod('POST')) {
            $nom = $request->get('nom');
            $nomB = true;
            if ($nom == '') {
                $nomB = false;
                $this->addFlash('nom', 'Merci de renseigner un nom');
            }

            $description = $request->get('description');
            $descriptionB = true;
            if ($description == '') {
                $descriptionB = false;
                $this->addFlash('description', 'Merci de renseigner une description');
            }

            $rue = $request->get('rue');
            $code_postal = $request->get('code_postal');
            $ville = $request->get('ville');

            $date = $request->get('date');

            $secteurActivite = $request->get('secteurActivite');

            if ($nomB && $descriptionB) {
                $adresse = (new Adresse())
                    ->setRue($rue)
                    ->setCodePostal($code_postal)
                    ->setVille($ville);
                $em->persist($adresse);
                $em->flush();

                $annonce = (new Annonce())
                    ->setNom($nom)
                    ->setDescription($description)
                    ->setAdresse($adresse)
                    ->setDate($date);

                EntityManager::getRepository(EntityManager::ANNONCE)->create($em, $annonce, $this->session->get('user'), $secteurActivite);
                $this->addFlash('success', 'Votre annonce a été publiée !');

                return $this->redirectToRoute('userSpace');
            }
        }

        return $this->render('autoEntrepreneur/creerAnnonceChantier.html.twig');
    }

    // _.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-.
    // UTILS

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return array
     * @throws Exception
     */
    private function getInscriptionData(Request $request, EntityManagerInterface $em): array
    {
        $res = [];

        $nom = $request->get('nom');
        $nomB = true;
        if ($nom === '') {
            $nomB = false;
            $this->addFlash('nom', 'Merci de renseigner un nom');
        }
        $res['nom'] = $nom;

        $prenom = $request->get('prenom');
        $prenomB = true;
        if ($prenom === '') {
            $prenomB = false;
            $this->addFlash('prenom', 'Merci de renseigner un prénom');
        }
        $res['prenom'] = $prenom;

        $mail = $request->get('email');
        $mailB = true;
        $validator = new EmailValidator();
        $multipleValidations = new MultipleValidationWithAnd([
            new RFCValidation(),
            new DNSCheckValidation()
        ]);
        if (!$validator->isValid($mail, $multipleValidations)) {
            $mailB = false;
            $this->addFlash('email', 'Merci de renseigner une adresse mail valide');
        } elseif (!EntityManager::getRepository(EntityManager::UTILS)->isMailNotUsed($em, $mail)) {
            $mailB = false;
            $this->addFlash('email', 'Cet email est déjà utilisé');
        }
        $res['email'] = $mail;

        $telephone = $request->get('telephone');
        $telephoneB = true;
        if (!preg_match('/^((([+][0-9]{2})|0)[1-9])([ ]?)([0-9]{2}\4){3}([0-9]{2})$/', $telephone)) {
            $telephoneB = false;
            $this->addFlash('telephone', 'Merci de renseigner un numéro de téléphone valide');
        }
        $res['telephone'] = $telephone;

        $motdepasse = $request->get('motdepasse');
        $motdepasse2 = $request->get('motdepasse2');
        $motdepasseB = true;
        if (!preg_match('/^(?=.{8,}$)(?=.*?[a-z])(?=.*?[0-9]).*$/', $motdepasse)) {
            $motdepasseB = false;
            $this->addFlash('motdepasse', 'Merci de renseigner un mot de passe valide</br>Au minimum : <ul><li>1 lettre majuscule</li><li>1 lettre minuscule</li><li>1 chiffre</li><li>1 caractères spécial</li><li>une longueur de 8 caractères</li></ul>');
        } else if ($motdepasse != $motdepasse2) {
            $motdepasseB = false;
            $this->addFlash('motdepasse2', 'Les mots de passe ne concordent pas');
        }
        $passwordE = Utils::passwordEncrypt($motdepasse);
        $res['motdepasse'] = $passwordE['password'];
        $res['sel'] = $passwordE['salt'];

        $conditionsB = true;
        if (!$request->get('conditions')) {
            $conditionsB = false;
            $this->addFlash('condition', 'Vous devez acceptez les conditions d\'utilisation');
        }

        $res['rue'] = $request->get('rue');
        $res['code_postal'] = $request->get('code_postal');
        $res['ville'] = $request->get('ville');


        $res['ok'] = $prenomB && $nomB && $telephoneB & $mailB && $motdepasseB && $conditionsB;

        return $res;
    }
}