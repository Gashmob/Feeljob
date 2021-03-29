<?php


namespace App\Controller;


use App\database\EntityManager;
use App\Entity\Adresse;
use App\Entity\CV;
use App\Entity\CVLangue;
use App\Entity\Employe;
use App\Entity\Employeur;
use App\Entity\Langue;
use App\Entity\OffreEmploi;
use App\Entity\SituationFamille;
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

/**
 * Class EntrepriseController
 * @package App\Controller
 * @Route("/entreprise")
 */
class EntrepriseController extends AbstractController
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
     * @Route("/inscription", name="entreprise_inscription")
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
                case EntityManager::EMPLOYEUR:
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

                        $employeur = (new Employeur())
                            ->setPrenom($data['prenom'])
                            ->setNom($data['nom'])
                            ->setNomEntreprise($nomEntreprise)
                            ->setTelephone($data['telephone'])
                            ->setEmail($data['email'])
                            ->setMotdepasse($data['motdepasse'])
                            ->setSel($data['sel'])
                            ->setAdresse($adresse)
                            ->setLogo($logo)
                            ->setSiret($siret)
                            ->setDescription($description);

                        EntityManager::getRepository(EntityManager::EMPLOYEUR)->create($em, $employeur, $secteurActivite);

                        Utils::sendMailAndWait($mailer, $employeur->getEmail(), $employeur->getPrenom(), $employeur->getNom(), $employeur->getIdentity());
                        $this->addFlash('success', 'Bravo ! Vous avez un nouveau compte !');

                        return $this->redirectToRoute('waitVerifEmail', ['id' => $employeur->getIdentity()]);
                    }
                    break;

                case EntityManager::EMPLOYE:
                    $data = $this->getInscriptionData($request, $em);

                    if ($data['ok']) {
                        $adresse = (new Adresse())
                            ->setRue($data['rue'])
                            ->setCodePostal($data['code_postal'])
                            ->setVille($data['ville']);
                        $em->persist($adresse);
                        $em->flush();

                        $employe = (new Employe())
                            ->setPrenom($data['prenom'])
                            ->setNom($data['nom'])
                            ->setTelephone($data['telephone'])
                            ->setEmail($data['email'])
                            ->setMotdepasse($data['motdepasse'])
                            ->setSel($data['sel'])
                            ->setAdresse($adresse);

                        EntityManager::getRepository(EntityManager::EMPLOYE)->create($em, $employe);

                        Utils::sendMailAndWait($mailer, $employe->getEmail(), $employe->getPrenom(), $employe->getNom(), $employe->getIdentity());
                        $this->addFlash('success', 'Bravo ! Vous avez un nouveau compte !');

                        return $this->redirectToRoute('waitVerifEmail', ['id' => $employe->getIdentity()]);
                    }
                    break;
            }
        }

        return $this->render('home/inscriptionEntrepriseCandidat.html.twig', [
            'secteurActivites' => EntityManager::getRepository(EntityManager::SECTEUR_ACTIVITE)->findAll(),
            'employeur' => EntityManager::EMPLOYEUR,
            'employe' => EntityManager::EMPLOYE
        ]);
    }

    /**
     * @Route("/mon_espace", name="entreprise_espace")
     * @param EntityManagerInterface $em
     * @return Response|RedirectResponse
     */
    public function userSpace(EntityManagerInterface $em)
    {
        if (!($this->session->get('user'))) {
            return $this->redirectToRoute('homepage');
        }

        $type = $this->session->get('userType');
        if ($type == EntityManager::AUTO_ENTREPRENEUR || $type == EntityManager::PARTICULIER) {
            return $this->redirectToRoute('particulier_espace');
        }

        $user = EntityManager::getRepository(EntityManager::UTILS)->getUserFromId($em, $this->session->get('user'));

        return $this->render('home/profil.html.twig', [
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom()
        ]);
    }

    /**
     * @Route("/cree/CV", name="entreprise_create_cv")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     */
    public function createCV(Request $request, EntityManagerInterface $em)
    {
        if (!($this->session->get('user'))) {
            return $this->redirectToRoute('homepage');
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYE) {
            return $this->redirectToRoute('userSpace');
        }

        if ($request->isMethod('POST')) {
            $naissance = $request->get('naissance');
            $naissanceB = true;
            if ($naissance == '') {
                $naissanceB = false;
                $this->addFlash('naissance', 'Merci de renseigner une date de naissance');
            }

            $permis = $request->get('permis');

            $situationFamille = $request->get('situationFamille');
            $situationFamilleB = true;
            if ($situationFamille == '') {
                $situationFamilleB = false;
                $this->addFlash('situationFamille', 'Merci de renseigner votre situation familiale');
            }

            // TODO : récup langues, metiers, diplomes, competences
            // Exemple how to do for langues
            $langues = [];
            for ($i = 0; $i < $request->get('nbLangues'); $i++) {
                $langue = $request->get('langue' . $i);
                $niveau = $request->get('niveau' . $i);

                $l = $em->getRepository(Langue::class)->findOneBy(['nom' => $langue]);
                if (is_null($l)) {
                    $l = (new Langue())
                        ->setNom($langue);
                    $em->persist($langue);
                    $em->flush();
                }

                $n = (new CVLangue())
                    ->setNiveau($niveau)
                    ->setLangue($l);
                $langues[] = $n;
            }

            $description = $request->get('description');

            if ($naissanceB && $situationFamilleB) {
                $cv = (new CV())
                    ->setNaissance($naissance)
                    ->setPermis($permis)
                    ->setDescription($description)
                    ->setSituationFamille($em->getRepository(SituationFamille::class)->findOneBy(['nom' => $situationFamille]));
                $em->persist($cv);
                $em->flush();

                // TODO : push langues, metiers, diplomes, competences
                // Exemple how to do for langues
                foreach ($langues as $langue) {
                    $langue->setCV($cv);
                    $em->persist($langue);
                }
                $em->flush();

                $employe = $em->getRepository(Employe::class)->findOneBy(['identity' => $this->session->get('user')]);
                $employe->setCV($cv);
                $em->flush();
            }
        }

        return $this->render('candidat/createCV.html.twig');
    }

    /**
     * @Route("/cv/{id}", requirements={"id": true}, name="entreprise_show_cv")
     * @param $id
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     */
    public function showCV($id, EntityManagerInterface $em)
    {
        if (!($this->session->get('user'))) {
            return $this->redirectToRoute('homepage');
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYEUR) {
            return $this->redirectToRoute('userSpace');
        }

        return $this->render('candidat/showCV.html.twig', [
            'cv' => $em->getRepository(CV::class)->findOneBy(['id' => $id])
        ]);
    }

    /**
     * @Route("/cvs", name="entreprise_cvs")
     */
    public function listCVs()
    {
        if (!($this->session->get('user'))) {
            return $this->redirectToRoute('homepage');
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYEUR) {
            return $this->redirectToRoute('userSpace');
        }

        return $this->render('entreprise/showProfiles.html.twig');
    }

    /**
     * @Route("/cree/offre_emploi", name="entreprise_create_offre_emploi")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     */
    public function createOffreEmploi(Request $request, EntityManagerInterface $em)
    {
        if (!($this->session->get('user'))) {
            return $this->redirectToRoute('homepage');
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYEUR) {
            return $this->redirectToRoute('userSpace');
        }

        if ($request->isMethod('POST')) {
            $nom = $request->get('nom');
            $nomB = true;
            if ($nom == '') {
                $nomB = false;
                $this->addFlash('nom', 'Merci de renseigner un nom');
            }

            $debut = $request->get('debut');
            $debutB = true;
            if ($debut == '') {
                $debutB = false;
                $this->addFlash('debut', 'Merci de renseigner une date de début de contrat');
            }
            $fin = $request->get('fin');

            $loge = $request->get('loge') == null;

            $heures = $request->get('heures');
            $heuresB = true;
            if ($heures == '') {
                $heuresB = false;
                $this->addFlash('heures', 'Merci de renseigner un nombre d\'heures par semaine');
            } elseif ($heures <= 0) {
                $heuresB = false;
                $this->addFlash('heures', 'Merci de renseigner un nombre d\'heures supérieur à 0');
            }

            $salaire = $request->get('salaire');
            $salaireB = true;
            if ($salaire == '') {
                $salaireB = false;
                $this->addFlash('salaire', 'Merci de renseigner un salaire');
            } elseif ($salaire <= 0) {
                $salaireB = false;
                $this->addFlash('salaire', 'Merci de renseigner un salaire supérieur à 0');
            }

            $deplacement = $request->get('deplacement') == null;

            $rue = $request->get('rue');
            $code_postal = $request->get('code_postal');
            $ville = $request->get('ville');

            $teletravail = $request->get('teletravail') == null;

            $nbPostes = $request->get('nbPostes');
            $nbPostesB = true;
            if ($nbPostes == '') {
                $nbPostesB = false;
                $this->addFlash('nbPostes', 'Merci de renseigner un nombre de postes à pourvoir');
            } elseif ($nbPostes <= 0) {
                $nbPostesB = false;
                $this->addFlash('nbPostes', 'Merci de renseigner un nombre de postes à pourvoir supérieur à 0');
            }

            $typeContrat = $request->get('typeContrat');

            if ($nomB && $debutB && $heuresB && $salaireB && $nbPostesB) {
                $adresse = (new Adresse())
                    ->setRue($rue)
                    ->setCodePostal($code_postal)
                    ->setVille($ville);
                $em->persist($adresse);
                $em->flush();

                $offre = (new OffreEmploi())
                    ->setNom($nom)
                    ->setDebut($debut)
                    ->setFin($fin)
                    ->setLoge($loge)
                    ->setHeures($heures)
                    ->setSalaire($salaire)
                    ->setDeplacement($deplacement)
                    ->setLieu($adresse)
                    ->setTeletravail($teletravail)
                    ->setNbPostes($nbPostes);

                EntityManager::getRepository(EntityManager::OFFRE_EMPLOI)->create($em, $offre, $this->session->get('user'), $typeContrat);
                $this->addFlash('success', 'Votre offre d\'emploi a été publiée');

                return $this->redirectToRoute('userSpace');
            }
        }

        return $this->render('entreprise/createEmploi.html.twig');
    }

    /**
     * @Route("/offre_emploi/{id}", name="entreprise_show_offre_emploi")
     * @param $id
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     */
    public function showOffreEmploi($id, EntityManagerInterface $em)
    {
        if (!($this->session->get('user'))) {
            return $this->redirectToRoute('homepage');
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYE) {
            return $this->redirectToRoute('userSpace');
        }

        return $this->render('entreprise/showEmploi.html.twig', [
            'offre' => $em->getRepository(OffreEmploi::class)->findOneBy(['identity' => $id])
        ]);
    }

    /**
     * @Route("/offres_emploi", name="entreprise_offres_emploi")
     */
    public function listOffreEmplois()
    {
        if (!($this->session->get('user'))) {
            return $this->redirectToRoute('homepage');
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYE) {
            return $this->redirectToRoute('userSpace');
        }

        return $this->render('candidat/showOffresEmploi.html.twig');
    }

    // _.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-.

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