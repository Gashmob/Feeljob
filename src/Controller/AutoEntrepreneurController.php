<?php


namespace App\Controller;


use App\database\EntityManager;
use App\Entity\AutoEntrepreneur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class AutoEntrepreneurController extends AbstractController
{
    /**
     * @var SessionInterface
     */
    private SessionInterface $session;

    /**
     * AutoEntrepreneurController constructor.
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/create/carte", name="create_carte")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function setCarteVisite(Request $request, EntityManagerInterface $em): Response
    {
        if (!($this->session->get('user'))) {
            return $this->redirectToRoute('homepage');
        }

        if (EntityManager::getUserTypeFromId($this->session->get('user')) != 'Freelance') {
            return $this->redirectToRoute('homepage');
        }

        if ($request->isMethod('POST')) {
            $carte = $this->uploadImage();

            $auto = $em->getRepository(AutoEntrepreneur::class)->findOneBy(['identity' => $this->session->get('user')]);
            $auto->setCarte($carte);
            $em->persist($auto);
            $em->flush();

            return $this->redirectToRoute('userSpace');
        }

        return $this->render('autoEntrepreneur/createCarteVisite.html.twig');
    }

    /**
     * @Route("/accept/{id}", name="accept")
     * @param int $id
     * @return RedirectResponse
     */
    public function acceptOffreChantier(int $id): RedirectResponse
    {
        if ($this->session->get('user')) {
            if ($this->session->get('userType') === 'Freelance') {
                EntityManager::acceptOffreChantier($id, $this->session->get('user'));
            }
        }

        return $this->redirectToRoute('contrats');
    }

    /**
     * @Route("/chantiers", name="chantiers")
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function offres(EntityManagerInterface $em): Response
    {
        return $this->render('autoEntrepreneur/showOffresChantier.html.twig', [
            'offres' => EntityManager::getAllOffreChantier($em)
        ]);
    }

    /**
     * @Route("/contrats", name="contrats")
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function contrats(EntityManagerInterface $em): Response
    {
        if (!$this->session->get('user')) {
            return $this->redirectToRoute('homepage');
        }

        return $this->render('autoEntrepreneur/propositionsContrat.html.twig', [
            'contrats' => EntityManager::getAllPropositions($em, $this->session->get('user'))
        ]);
    }

    /**
     * @Route("/contrats/{id}", name="show_contrat")
     * @param $id
     * @return RedirectResponse
     */
    public function showContrat($id): RedirectResponse
    {
        return $this->redirectToRoute('homepage');
    }

    // _.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-.

    /**
     * @return string
     */
    private
    function uploadImage(): string
    {
        if (isset($_FILES['carte']) && $_FILES['carte']['error'] === UPLOAD_ERR_OK) {
            // Infos sur le fichier téléchargé
            $fileTmpPath = $_FILES['carte']['tmp_name'];
            $fileName = $_FILES['carte']['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Changement du nom par quelque chose qui ne se répétera pas
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

            // Les extensions autorisées
            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'svg');

            if (!file_exists('./uploads/cartes')) {
                mkdir('./uploads/cartes');
            }

            if (in_array($fileExtension, $allowedfileExtensions)) {
                $uploadFileDir = './uploads/cartes/';
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