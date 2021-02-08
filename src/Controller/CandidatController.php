<?php


namespace App\Controller;


use App\database\entity\CV;
use App\database\EntityManager;
use App\database\exceptions\UserNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CandidatController extends AbstractController
{
    /**
     * @var SessionInterface
     */
    private SessionInterface $session;

    /**
     * CandidatController constructor.
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/cv/{id}", name="show_cv")
     * @param $id
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function showCV($id, EntityManagerInterface $em): Response
    {
        return $this->render('candidat/showCV.html.twig', [
            'cv' => EntityManager::getCVArrayFromId($id, $em)
        ]);
    }

    /**
     * @Route("/create/CV", name="create_cv")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     * @throws UserNotFoundException
     */
    public function createCV(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->session->get('user')) {
            return $this->redirectToRoute('userSpace');
        }

        if (!$this->session->get('userType') === 'Candidat') {
            return $this->redirectToRoute('userSpace');
        }

        if ($request->isMethod('POST')) {
            $nom = $request->get('nom');
            $nomB = true;
            if ($nom === '') {
                $nomB = false;
                $this->addFlash('nom', 'Merci de renseigner un nom');
            }

            $photo = $this->uploadImage();

            $metier = $request->get('metier');

            $famille = $request->get('famille');

            $diplomes = $request->get('diplomes');
            if (is_null($diplomes)) {
                $diplomes = [];
            }
            if (!is_array($diplomes)) {
                $diplomes = [$diplomes];
            }
            $dates = $request->get('dates');
            if (is_null($dates)) {
                $dates = [];
            }
            if (!is_array($dates)) {
                $dates = [$dates];
            }
            $diplomesB = true;
            if (sizeof($diplomes) != sizeof($dates)) {
                $diplomesB = false;
                $this->addFlash('form', 'Merci de renseigner tout vos diplomes et formations avec dates d\'obtention');
            }

            $nomEntreprises = $request->get('nomEntreprises');
            if (is_null($nomEntreprises)) {
                $nomEntreprises = [];
            }
            if (!is_array($nomEntreprises)) {
                $nomEntreprises = [$nomEntreprises];
            }
            $postes = $request->get('postes');
            if (is_null($postes)) {
                $postes = [];
            }
            if (!is_array($postes)) {
                $postes = [$postes];
            }
            $durees = $request->get('durees');
            if (is_null($durees)) {
                $durees = [];
            }
            if (!is_array($durees)) {
                $durees = [$durees];
            }
            $experiencesB = true;
            if (sizeof($nomEntreprises) != sizeof($postes) && sizeof($nomEntreprises) != $durees) {
                $experiencesB = false;
                $this->addFlash('nomEntreprises', 'Merci de renseigner toutes les données pour vos expériences professionnelles');
            }

            $langues = $request->get('langues');
            if (is_null($langues)) {
                $langues = [];
            }
            if (!is_array($langues)) {
                $langues = [$langues];
            }

            $deplacements = $request->get('deplacements');
            if (is_null($deplacements)) {
                $deplacements = [];
            }
            if (!is_array($deplacements)) {
                $deplacements = [$deplacements];
            }

            $typeContrat = $request->get('typeContrat');

            if ($nomB && $diplomesB && $experiencesB) {
                $cv = new CV();
                $cv->setNom($nom)
                    ->setPhoto($photo);
                EntityManager::createCV($cv, $metier, $famille, $diplomes, $dates, $nomEntreprises, $postes, $durees, $langues, $deplacements, $typeContrat, $this->session->get('user'));

                $this->addFlash('success', 'Votre CV a été créé');
                return $this->redirectToRoute('userSpace');
            }
        }

        $nomPrenom = EntityManager::getNomPrenomFromId($this->session->get('user'), $em);

        return $this->render('candidat/createCV.html.twig', [
            'situations' => EntityManager::getAllSituationFamilleName(),
            'langues' => EntityManager::getAllLangueName(),
            'deplacements' => EntityManager::getAllDeplacementName(),
            'typeContrats' => EntityManager::getAllTypeContratName(),
            'metiers' => EntityManager::getAllMetierName(),
            'entreprises' => EntityManager::getAllExperienceName(),
            'nom' => $nomPrenom['nom'],
            'prenom' => $nomPrenom['prenom'],
            'telephone' => EntityManager::getUserPhoneFromId($this->session->get('user'), $em),
            'email' => EntityManager::getGenericUserFromId($this->session->get('user'))->getEmail(),
            'cv' => $request->get('id') ? EntityManager::getCVArrayFromId($request->get('id'), $em) : []
        ]);
    }

    /**
     * @Route("/annonces", name="showAnnonces")
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function offres(EntityManagerInterface $em): Response
    {
        $offres = EntityManager::getAllOffreEmploi($em);

        return $this->render('candidat/showAnnonces.html.twig', [
            'offres' => $offres
        ]);
    }

    /**
     * @Route("/annonces/{nom}", defaults={"nom"=""})
     * @param $nom
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function offresFilters($nom, Request $request, EntityManagerInterface $em): JsonResponse
    {
        if ($request->isMethod('GET')) {
            $secteur = $request->get('secteur');
            $contrat = $request->get('contrat');
            $salaire = $request->get('salaire');
            $heures = $request->get('heures');
            $deplacement = $request->get('deplacement');

            $offres = EntityManager::getOffreEmploiWithFilter($em, $secteur, $contrat, $salaire, $heures, $deplacement, $nom);
        } else {
            $offres = EntityManager::getAllOffreEmploi($em, $nom);
        }

        return new JsonResponse($offres);
    }

    // _.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-.

    /**
     * @return string
     */
    private function uploadImage(): string
    {
        if (count($_FILES) == 0) {
            return '';
        }

        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            // Infos sur le fichier téléchargé
            $fileTmpPath = $_FILES['photo']['tmp_name'];
            $fileName = $_FILES['photo']['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Changement du nom par quelque chose qui ne se répétera pas
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

            // Les extensions autorisées
            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'svg');

            if (!file_exists('./uploads/photos')) {
                mkdir('./uploads/photos');
            }

            if (in_array($fileExtension, $allowedfileExtensions)) {
                $uploadFileDir = './uploads/photos/';
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