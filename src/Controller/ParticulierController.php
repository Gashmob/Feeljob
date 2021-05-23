<?php


namespace App\Controller;


use App\database\EntityManager;
use App\database\manager\AnnonceManager;
use App\database\manager\AutoEntrepreneurManager;
use App\database\manager\MetierManager;
use App\database\manager\ParticulierManager;
use App\database\manager\SecteurActiviteManager;
use App\database\manager\UtilsManager;
use App\Entity\Adresse;
use App\Entity\Annonce;
use App\Entity\AutoEntrepreneur;
use App\Entity\CarteVisite;
use App\Entity\Particulier;
use App\Entity\Realisation;
use App\Utils;
use DateTime;
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
     * @Route("/inscription/particulier", name="particulier_inscription_particulier")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function inscriptionParticulier(Request $request, EntityManagerInterface $em)
    {
        if ($this->session->get('user')) {
            return $this->redirectToRoute('userSpace');
        }

        if ($request->isMethod('POST')) {
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
                    ->setVerifie(true) // TODO : add verif email
                    ->setMotdepasse($data['motdepasse'])
                    ->setSel($data['sel'])
                    ->setAdresse($adresse);

                (new ParticulierManager())->create($em, $particulier);

                //Utils::sendMailAndWait($mailer, $particulier->getEmail(), $particulier->getPrenom(), $particulier->getNom(), $particulier->getIdentity());
                $this->addFlash('success', 'Bravo ! Vous avez un nouveau compte !');

                return $this->redirectToRoute('waitVerifEmail', ['id' => $particulier->getIdentity()]);
            }
        }

        return $this->render('home/inscriptionParticulier.html.twig', [
            'secteurActivites' => (new SecteurActiviteManager())->findAllNames(),
        ]);
    }

    /**
     * @Route("/inscription/auto", name="particulier_inscription_auto")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function inscriptionAutoEntrepreneur(Request $request, EntityManagerInterface $em)
    {
        if ($this->session->get('user')) {
            return $this->redirectToRoute('userSpace');
        }

        if ($request->isMethod('POST')) {
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
                    ->setVerifie(true) // TODO : add verif email
                    ->setMotdepasse($data['motdepasse'])
                    ->setSel($data['sel'])
                    ->setLogo($logo)
                    ->setSiret($siret)
                    ->setDescription($description);

                (new AutoEntrepreneurManager())->create($em, $auto_entrepreneur, $secteurActivite);

                //Utils::sendMailAndWait($mailer, $auto_entrepreneur->getEmail(), $auto_entrepreneur->getPrenom(), $auto_entrepreneur->getNom(), $auto_entrepreneur->getIdentity());
                $this->addFlash('success', 'Bravo ! Vous avez un nouveau compte !');

                return $this->redirectToRoute('waitVerifEmail', ['id' => $auto_entrepreneur->getIdentity()]);
            }
        }

        return $this->render('home/inscriptionFreelance.html.twig', [
            'secteurActivites' => (new SecteurActiviteManager())->findAllNames(),
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

        $type = $this->session->get('userType');
        if ($type == EntityManager::EMPLOYEUR || $type == EntityManager::EMPLOYE) {
            return $this->redirectToRoute('entreprise_espace');
        }

        $user = (new UtilsManager())->getUserFromId($em, $this->session->get('user'));

        switch ($type) {
            case EntityManager::AUTO_ENTREPRENEUR:
                return $this->render('autoEntrepreneur/profilFreelance.html.twig', [
                    'nom' => $user->getNom(),
                    'prenom' => $user->getPrenom(),
                    'publications' => $user->getCarteVisite()
                ]);

            case EntityManager::PARTICULIER:
                $publications = (new AnnonceManager())->findAnnoncesByParticulier($em, $user->getIdentity());
                return $this->render('particulier/profilParticulier.html.twig', [
                    'nom' => $user->getNom(),
                    'prenom' => $user->getPrenom(),
                    'publications' => $publications
                ]);
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/creer/carte", name="particulier_create_carte")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response|RedirectResponse
     * @throws Exception
     */
    public function createCarteVisite(Request $request, EntityManagerInterface $em)
    {
        if (!($this->session->get('user'))) {
            return $this->redirectToRoute('homepage');
        }

        if ($this->session->get('userType') != EntityManager::AUTO_ENTREPRENEUR) {
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

                for ($i = 0; $i < $request->get('nbRealisations'); $i++) {
                    $image = Utils::uploadImage('realisations', 'image' . $i);
                    $descriptionR = $request->get('description' . $i);

                    if ($image != '' && $descriptionR != '') {
                        $r = (new Realisation())
                            ->setImage($image)
                            ->setDescription($descriptionR)
                            ->setCarteVisite($carte);
                        $em->persist($r);
                        $em->flush();
                    }
                }

                $auto_entrepreneur->setCarteVisite($carte);
                $em->flush();

                return $this->redirectToRoute('userSpace');
            }
        }

        return $this->render('autoEntrepreneur/createCarteVisite.html.twig', [
            'metiers' => (new MetierManager())->findAllNamesWithSecteurActivite()
        ]);
    }

    /**
     * @Route("/modifier/carte/{id}", name="particulier_modifier_carte_visite")
     * @param $id
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function modifyCarteVisite($id, Request $request, EntityManagerInterface $em)
    {
        if (!($this->session->get('user'))) {
            return $this->redirectToRoute('homepage');
        }

        if ($this->session->get('userType') != EntityManager::AUTO_ENTREPRENEUR) {
            return $this->redirectToRoute('userSpace');
        }

        $carte = $em->getRepository(CarteVisite::class)->find($id);
        if (!$em->getRepository(CarteVisite::class)->isOwner($carte, $this->session->get('user'))) {
            return $this->redirectToRoute('userSpace');
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

                $carte->setDescription($description)
                    ->clearRealisation();
                $em->flush();

                for ($i = 0; $i < $request->get('nbRealisations'); $i++) {
                    $image = $request->get('change' . $i);
                    if ($image == 'none') {
                        $image = Utils::uploadImage('realisations', 'image' . $i);
                    }
                    $descriptionR = $request->get('description' . $i);
                    if ($image != '' && $descriptionR != '') {
                        $r = (new Realisation())
                            ->setImage($image)
                            ->setDescription($descriptionR)
                            ->setCarteVisite($carte);
                        $em->persist($r);
                        $em->flush();
                    }
                }

                $auto_entrepreneur->setCarteVisite($carte);
                $em->flush();

                return $this->redirectToRoute('userSpace');
            }
        }

        return $this->render('autoEntrepreneur/editCarteVisite.html.twig', [
            'carte' => $carte
        ]);
    }

    /**
     * @Route("/supprimer/carte/{id}", name="particulier_delete_carte")
     * @param $id
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function deleteCarteVisite($id, EntityManagerInterface $em): RedirectResponse
    {
        if (!($this->session->get('user'))) {
            return $this->redirectToRoute('homepage');
        }

        if ($this->session->get('userType') != EntityManager::AUTO_ENTREPRENEUR) {
            return $this->redirectToRoute('userSpace');
        }

        $carte = $em->getRepository(CarteVisite::class)->find($id);
        if (!$em->getRepository(CarteVisite::class)->isOwner($carte, $this->session->get('user'))) {
            return $this->redirectToRoute('userSpace');
        }

        $carte->getAutoEntrepreneur()->setCarteVisite(null);
        $em->remove($carte);
        $em->flush();

        return $this->redirectToRoute('userSpace');
    }

    /**
     * @Route("/creer/annonce", name="particulier_create_annonce")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response|RedirectResponse
     * @throws Exception
     */
    public function createAnnonce(Request $request, EntityManagerInterface $em)
    {
        if (!($this->session->get('user'))) {
            return $this->redirectToRoute('homepage');
        }

        if ($this->session->get('userType') != EntityManager::PARTICULIER) {
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

            $ville = $request->get('ville');
            $rue = $request->get('rue');
            $codePostal = $request->get('codePostal');

            $date = $request->get('date');

            $metier = $request->get('metier');

            if ($nomB && $descriptionB) {
                $adresse = (new Adresse())
                    ->setRue($rue)
                    ->setCodePostal($codePostal)
                    ->setVille($ville);
                $em->persist($adresse);
                $em->flush();

                $annonce = (new Annonce())
                    ->setNom($nom)
                    ->setDescription($description)
                    ->setAdresse($adresse)
                    ->setDate(new DateTime($date));

                (new AnnonceManager())->create($em, $annonce, $this->session->get('user'), $metier);
                $this->addFlash('success', 'Votre annonce a été publiée !');

                return $this->redirectToRoute('userSpace');
            }
        }

        return $this->render('autoEntrepreneur/creerAnnonceChantier.html.twig', [
            'metiers' => (new MetierManager())->findAllNamesWithSecteurActivite()
        ]);
    }

    /**
     * @Route("/modifier/annonce/{id}", name="particulier_modifier_annonce")
     * @param $id
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function modifyAnnonce($id, Request $request, EntityManagerInterface $em)
    {
        if (!($this->session->get('user'))) {
            return $this->redirectToRoute('homepage');
        }

        if ($this->session->get('userType') != EntityManager::PARTICULIER) {
            return $this->redirectToRoute('userSpace');
        }

        if (!(new AnnonceManager())->isOwner($id, $this->session->get('user'))) {
            return $this->redirectToRoute('userSpace');
        }

        $annonce = $em->getRepository(Annonce::class)->findOneBy(['identity' => $id]);

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

            $ville = $request->get('ville');

            $date = $request->get('date');

            $metier = $request->get('metier');

            if ($nomB && $descriptionB) {
                $adresse = (new Adresse())
                    ->setRue('')
                    ->setCodePostal('')
                    ->setVille($ville);
                $em->persist($adresse);
                $em->flush();

                $annonce->setNom($nom)
                    ->setDescription($description)
                    ->setAdresse($adresse)
                    ->setDate(new DateTime($date));

                (new AnnonceManager())->update($em, $annonce->getIdentity(), $metier);
                $this->addFlash('success', 'Votre annonce a été modifiée !');

                return $this->redirectToRoute('userSpace');
            }
        }

        return $this->render('particulier/editAnnonce.html.twig', [
            'annonce' => $annonce,
            'metiers' => (new MetierManager())->findAllNamesWithSecteurActivite(),
            'metier' => (new AnnonceManager())->getMetier($annonce->getIdentity())
        ]);
    }

    /**
     * @Route("/supprimer/annonce/{id}", name="particulier_delete_annonce")
     * @param $id
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function deleteAnnonce($id, EntityManagerInterface $em): RedirectResponse
    {
        if (!($this->session->get('user'))) {
            return $this->redirectToRoute('homepage');
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYE) {
            return $this->redirectToRoute('userSpace');
        }

        if (!(new AnnonceManager())->isOwner($id, $this->session->get('user'))) {
            return $this->redirectToRoute('userSpace');
        }

        (new AnnonceManager())->remove($em, $em->getRepository(Annonce::class)->findOneBy(['identity' => $id]));

        $this->addFlash('success', 'Votre annonce a été supprimée !');
        return $this->redirectToRoute('userSpace');
    }

    /**
     * @Route("/annonces", name="particulier_annonces")
     * @return Response
     */
    public function annonces(): Response
    {
        return $this->render('autoEntrepreneur/showAnnonces.html.twig', [
            'secteurs' => (new SecteurActiviteManager())->findAllNames(),
            'connected' => ($this->session->get('user')),
        ]);
    }

    /**
     * @Route("/annonce/{id}", name="particulier_show_annonce")
     * @param $id
     * @param EntityManagerInterface $em
     * @return Response|RedirectResponse
     */
    public function voirAnnonce($id, EntityManagerInterface $em)
    {
        if (!($this->session->get('user'))) {
            return $this->redirectToRoute('homepage');
        }

        $owner = (new AnnonceManager())->isOwner($id, $this->session->get('user'));
        if ($this->session->get('userType') != EntityManager::AUTO_ENTREPRENEUR && !$owner) {
            return $this->redirectToRoute('userSpace');
        }

        return $this->render('autoEntrepreneur/showAnnonce.html.twig', [
            'annonce' => $em->getRepository(Annonce::class)->findOneBy(['identity' => $id]),
            'owner' => $owner,
            'metier' => (new AnnonceManager())->getMetier($id)
        ]);
    }

    /**
     * @Route("/cartes", name="particulier_cartes")
     * @return Response
     */
    public function listCarteVisite(): Response
    {
        return $this->render('particulier/showCartesVisite.html.twig', [
            'secteurs' => (new SecteurActiviteManager())->findAllNames(),
            'connected' => ($this->session->get('user')),
        ]);
    }

    /**
     * @Route("/carte/{id}", name="particulier_show_carte")
     * @param $id
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     */
    public function voirCarteVisite($id, EntityManagerInterface $em)
    {
        if (!($this->session->get('user'))) {
            return $this->redirectToRoute('homepage');
        }

        $carte = $em->getRepository(CarteVisite::class)->findOneBy(['id' => $id]);

        if (is_null($carte)) {
            return $this->redirectToRoute('userSpace');
        }

        $owner = $em->getRepository(CarteVisite::class)->isOwner($carte, $this->session->get('user'));
        if ($this->session->get('userType') != EntityManager::PARTICULIER && !$owner) {
            return $this->redirectToRoute('userSpace');
        }

        $annonces = [];
        if (!$owner) { // Is Particulier
            $annonces = (new AnnonceManager())->findAnnoncesByParticulier($em, $this->session->get('user'));
        }

        return $this->render('autoEntrepreneur/showCarteVisite.html.twig', [
            'carte' => $carte,
            'annonces' => $annonces,
            'owner' => $owner,
            'secteur' => (new AutoEntrepreneurManager())->getSecteurActivite($carte->getAutoEntrepreneur()->getIdentity()),
        ]);
    }

    /**
     * @Route("/contrats", name="particulier_contrats")
     * @return Response|RedirectResponse
     */
    public function contracts(): Response
    {
        if ($this->session->get('user')) {
            if ($this->session->get('userType') == EntityManager::AUTO_ENTREPRENEUR) {
                return $this->render('autoEntrepreneur/contratsFreelance.html.twig');
            } elseif ($this->session->get('userType') == EntityManager::PARTICULIER) {
                return $this->render('particulier/contratsParticulier.html.twig');
            }
        }

        return $this->redirectToRoute('homepage');
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
        } elseif (!(new UtilsManager())->isMailNotUsed($em, $mail)) {
            $mailB = false;
            $this->addFlash('email', 'Cet email est déjà utilisé');
        }
        $res['email'] = $mail;

        $telephone = $request->get('telephone');
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

        $res['rue'] = $request->get('rue') == null ? '' : $request->get('rue');
        $res['code_postal'] = $request->get('code_postal') == null ? '' : $request->get('code_postal');
        $res['ville'] = $request->get('ville') == null ? '' : $request->get('ville');

        $res['ok'] = $prenomB && $nomB && $mailB && $motdepasseB && $conditionsB;

        return $res;
    }
}