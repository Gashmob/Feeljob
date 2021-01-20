<?php


namespace App\Controller;


use App\database\entity\CV;
use App\database\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @return Response
     */
    public function createCV(Request $request): Response
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
            $dates = $request->get('dates');
            $diplomesB = true;
            if (sizeof($diplomes) != sizeof($dates)) {
                $diplomesB = false;
                $this->addFlash('form', 'Merci de renseigner tout vos diplomes et formations avec dates d\'obtention');
            }

            $nomEntreprises = $request->get('nomEntreprises');
            $postes = $request->get('postes');
            $durees = $request->get('durees');
            $experiencesB = true;
            if (sizeof($nomEntreprises) != sizeof($postes) && sizeof($nomEntreprises) != $durees) {
                $experiencesB = false;
                $this->addFlash('nomEntreprises', 'Merci de renseigner toutes les données pour vos expériences professionnelles');
            }

            $langues = $request->get('langues');

            $deplacements = $request->get('deplacements');

            $typeContrat = $request->get('typeContrat');

            if ($nomB && $diplomesB && $experiencesB) {
                $cv = new CV();
                $cv->setNom($nom)
                    ->setPhoto($photo);
                EntityManager::createCV($cv, $metier, $famille, $diplomes, $dates, $nomEntreprises, $postes, $durees, $langues, $deplacements, $typeContrat);

                $this->addFlash('success', 'Votre CV a été créé');
                return $this->redirectToRoute('userSpace');
            }
        }

        return $this->render('candidat/createCV.html.twig', [
            'situations' => EntityManager::getAllSituationFamilleName(),
            'langues' => EntityManager::getAllLangueName(),
            'deplacements' => EntityManager::getAllDeplacementName(),
            'typeContrats' => EntityManager::getAllTypeContratName(),
            'metiers' => EntityManager::getAllMetierName(),
            'entreprises' => EntityManager::getAllExperienceName()
        ]);
    }

    // _.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-.

    /**
     * @return string
     */
    private function uploadImage(): string
    {
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