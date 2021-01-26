<?php


namespace App\Controller;


use App\database\EntityManager;
use App\Entity\OffreEmploi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class EntrepriseController extends AbstractController
{
    /**
     * @var SessionInterface
     */
    private SessionInterface $session;

    /**
     * EntrepriseController constructor.
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/offre_emploi/{id}", name="show_emploi")
     * @param $id
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function showEmploi($id, EntityManagerInterface $em): Response
    {
        return $this->render('entreprise/showEmploi.html.twig', [
            'offre' => EntityManager::getEmploiArrayFromId($id, $em)
        ]);
    }

    /**
     * @Route("/create/emploi", name="create_emploi")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function createEmploi(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->session->get('user')) {
            return $this->redirectToRoute('userSpace');
        }

        if (!$this->session->get('userType') === 'Entreprise') {
            return $this->redirectToRoute('userSpace');
        }

        if ($request->isMethod('POST')) {
            $nom = $request->get('nom');
            $nomB = true;
            if ($nom === '') {
                $nomB = false;
                $this->addFlash('nom', 'Merci de renseigner un nom');
            }

            $typeContrat = $request->get('contrat');
            $typeContratB = true;
            if ($typeContrat === '') {
                $typeContratB = false;
                $this->addFlash('contrat', 'Merci de renseigner un type de contrat');
            }

            $dateD = $request->get('dateD');
            $dateDB = true;
            if ($dateD === '') {
                $dateDB = false;
                $this->addFlash('dateD', 'Merci de renseigner une date de début de contrat');
            }

            if ($typeContrat === 'CDD' || $typeContrat === 'Saisonnier') {
                $dateF = $request->get('dateF');
                $dateFB = true;
                if ($dateF === '') {
                    $dateFB = false;
                    $this->addFlash('dateF', 'Merci de renseigner une date de fin de contrat');
                }
            } else {
                $dateF = '';
                $dateFB = true;
            }

            $loge = $request->get('loge') != null;

            $heures = $request->get('heures');
            $heuresB = true;
            if ($heures <= 0) {
                $heuresB = false;
                $this->addFlash('heures', 'Merci de renseigner un nombre d\'heures de travail valide');
            }

            $salaire = $request->get('salaire');
            $salaireB = true;
            if ($salaire < 0) {
                $salaireB = false;
                $this->addFlash('salaire', 'Merci de renseigner un salaire valide');
            }

            $deplacement = $request->get('deplacement') == null;
            if (!$deplacement) {
                $lieu = $request->get('lieu');
                $lieuB = true;
                if ($lieu === '') {
                    $lieuB = false;
                    $this->addFlash('lieu', 'Merci de renseigner un lieu de travail');
                }
            } else {
                $lieu = '';
                $lieuB = true;
            }

            $teletravail = $request->get('teletravail') != null;

            $nbRecrutement = $request->get('nbRecrutement');
            $nbRecrutementB = true;
            if ($nbRecrutement <= 0) {
                $nbRecrutementB = false;
                $this->addFlash('nbRecrutement', 'Merci de renseigner un nombre de recrutement supérieur à 0');
            }

            if ($nomB && $typeContratB && $dateDB && $dateFB && $heuresB && $salaireB && $lieuB && $nbRecrutementB) {
                $offre = new OffreEmploi();
                $offre->setNom($nom)
                    ->setDebut($dateD)
                    ->setFin($dateF)
                    ->setLoge($loge)
                    ->setHeures($heures)
                    ->setSalaire($salaire)
                    ->setDeplacement($deplacement)
                    ->setLieu($lieu)
                    ->setTeletravail($teletravail)
                    ->setNbRecrutement($nbRecrutement);
                EntityManager::createOffreEmploi($offre, $em, $typeContrat, $this->session->get('user'));

                $this->addFlash('success', 'Votre offre d\'emploi a été créé');
                return $this->redirectToRoute('userSpace');
            }
        }

        return $this->render('entreprise/createEmploi.html.twig', [
            'typeContrat' => EntityManager::getAllTypeContratName()
        ]);
    }

    /**
     * @Route("/search/candidat", name="search_profil")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function searchProfil(Request $request)
    {
        if (!$this->session->get('user')) {
            return $this->redirectToRoute('homepage');
        }

        if (!$this->session->get('userType') === 'Entreprise') {
            return $this->redirectToRoute('userSpace');
        }

        // TODO : récupérer les données de tout les profils

        if ($request->isMethod('POST')) {
            // TODO : récupérer les données des profils correspondant aux filtres
        }

        return $this->render('entreprise/showProfiles.html.twig', [
            'profils' => []
        ]);
    }
}