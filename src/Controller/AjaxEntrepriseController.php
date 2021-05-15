<?php


namespace App\Controller;


use App\database\EntityManager;
use App\database\manager\OffreEmploiManager;
use App\Entity\CV;
use App\Entity\OffreEmploi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AjaxEntrepriseController
 * @package App\Controller
 * @Route("/entreprise")
 */
class AjaxEntrepriseController extends AbstractController
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
     * @Route("/candidate/{id}", methods={"POST"})
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function candidate($id, Request $request): JsonResponse
    {
        if (!($this->session->get('user'))) {
            return $this->json(['result' => false]);
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYE) {
            return $this->json(['result' => false]);
        }

        if ($request->isMethod('POST')) {
            return $this->json([
                'result' => (new OffreEmploiManager())->candidate($id, $this->session->get('user'))
            ]);
        }

        return $this->json(['result' => false]);
    }

    /**
     * @Route("/uncandidate/{id}", methods={"POST"})
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function uncandidate($id, Request $request): JsonResponse
    {
        if (!($this->session->get('user'))) {
            return $this->json(['result' => false]);
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYE) {
            return $this->json(['result' => false]);
        }

        if ($request->isMethod('POST')) {
            (new OffreEmploiManager())->uncandidate($id, $this->session->get('user'));
            return $this->json(['result' => true]);
        }

        return $this->json(['result' => false]);
    }

    /**
     * @Route("/propose/{idO}/{idE}", methods={"POST"})
     * @param $idO
     * @param $idE
     * @param Request $request
     * @return JsonResponse
     */
    public function propose($idO, $idE, Request $request): JsonResponse
    {
        if (!($this->session->get('user'))) {
            return $this->json(['result' => false]);
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYEUR) {
            return $this->json(['result' => false]);
        }

        if ($request->isMethod('POST')) {
            return $this->json([
                'result' => (new OffreEmploiManager())->propose($idO, $idE)
            ]);
        }

        return $this->json(['result' => false]);
    }

    /**
     * @Route("/remove/proposition/{idO}/{idE}", methods={"POST"})
     * @param $idO
     * @param $idE
     * @param Request $request
     * @return JsonResponse
     */
    public function removeProposition($idO, $idE, Request $request): JsonResponse
    {
        if (!($this->session->get('user'))) {
            return $this->json(['result' => false]);
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYEUR) {
            return $this->json(['result' => false]);
        }

        if ($request->isMethod('POST')) {
            (new OffreEmploiManager())->removeProposition($idO, $idE);
            return $this->json(['result' => true]);
        }

        return $this->json(['result' => false]);
    }

    /**
     * @Route("/refuse/proposition/{id}", methods={"POST"})
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function rejectProposition($id, Request $request): JsonResponse
    {
        if (!($this->session->get('user'))) {
            return $this->json(['result' => false]);
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYE) {
            return $this->json(['result' => false]);
        }

        if ($request->isMethod('POST')) {
            (new OffreEmploiManager())->removeProposition($id, $this->session->get('user'));
            return $this->json(['result' => true]);
        }

        return $this->json(['result' => false]);
    }

    /**
     * @Route("/add/favoris/{id}", methods={"POST"})
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function addFavoris($id, Request $request): JsonResponse
    {
        if (!($this->session->get('user'))) {
            return $this->json(['result' => false]);
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYE) {
            return $this->json(['result' => false]);
        }

        if ($request->isMethod('POST')) {
            return $this->json(['result' => (new OffreEmploiManager())->addToFavoris($id, $this->session->get('user'))]);
        }

        return $this->json(['result' => false]);
    }

    /**
     * @Route("/remove/favoris/{id}", methods={"POST"})
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function removeFavoris($id, Request $request): JsonResponse
    {
        if (!($this->session->get('user'))) {
            return $this->json(['result' => false]);
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYE) {
            return $this->json(['result' => false]);
        }

        if ($request->isMethod('POST')) {
            (new OffreEmploiManager())->removeFavoris($id, $this->session->get('user'));
            return $this->json(['result' => true]);
        }

        return $this->json(['result' => false]);
    }

    /**
     * @Route("/accept/proposition/{id}", methods={"POST"})
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function acceptProposition($id, Request $request): JsonResponse
    {
        if (!($this->session->get('user'))) {
            return $this->json(['result' => false]);
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYE) {
            return $this->json(['result' => false]);
        }

        if ($request->isMethod('POST')) {
            return $this->json([
                'result' => (new OffreEmploiManager())->acceptProposition($id, $this->session->get('user'))
            ]);
        }

        return $this->json(['result' => false]);
    }

    /**
     * @Route("/accept/candidature/{idO}/{idE}", methods={"POST"})
     * @param $idO
     * @param $idE
     * @param Request $request
     * @return JsonResponse
     */
    public function acceptCandidature($idO, $idE, Request $request): JsonResponse
    {
        if (!($this->session->get('user'))) {
            return $this->json(['result' => false]);
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYEUR) {
            return $this->json(['result' => false]);
        }

        if ($request->isMethod('POST')) {
            return $this->json([
                'result' => (new OffreEmploiManager())->acceptCandidature($idO, $idE)
            ]);
        }

        return $this->json(['result' => false]);
    }

    /**
     * @Route("/get/candidatures", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function getCandidatures(Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!($this->session->get('user'))) {
            return $this->json([]);
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYE) {
            return $this->json([]);
        }

        if ($request->isMethod('POST')) {
            return $this->json([
                'candidatures' => (new OffreEmploiManager())->getCandidature($em, $this->session->get('user'))
            ]);
        }

        return $this->json([]);
    }

    /**
     * @Route("/get/my/candidatures", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function getMyCandidatures(Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!($this->session->get('user'))) {
            return $this->json([]);
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYEUR) {
            return $this->json([]);
        }

        if ($request->isMethod('POST')) {
            return $this->json([
                'candidatures' => (new OffreEmploiManager())->getMyCandidature($em, $this->session->get('user'))
            ]);
        }

        return $this->json([]);
    }

    /**
     * @Route("/get/accepted/candidatures", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function getAcceptedCandidatures(Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!($this->session->get('user'))) {
            return $this->json([]);
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYE) {
            return $this->json([]);
        }

        if ($request->isMethod('POST')) {
            return $this->json([
                'candidatures' => (new OffreEmploiManager())->getAcceptedCandidature($em, $this->session->get('user'))
            ]);
        }

        return $this->json([]);
    }

    /**
     * @Route("/get/my/accepted/candidatures", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function getMyAcceptedCandidatures(Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!($this->session->get('user'))) {
            return $this->json([]);
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYEUR) {
            return $this->json([]);
        }

        if ($request->isMethod('POST')) {
            return $this->json([
                'candidatures' => (new OffreEmploiManager())->getMyAcceptedCandidature($em, $this->session->get('user'))
            ]);
        }

        return $this->json([]);
    }

    /**
     * @Route("/get/propositions", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function getPropositions(Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!($this->session->get('user'))) {
            return $this->json([]);
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYE) {
            return $this->json([]);
        }

        if ($request->isMethod('POST')) {
            return $this->json([
                'propositions' => (new OffreEmploiManager())->getPropositions($em, $this->session->get('user'))
            ]);
        }

        return $this->json([]);
    }

    /**
     * @Route("/get/my/propositions", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function getMyPropositions(Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!($this->session->get('user'))) {
            return $this->json([]);
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYEUR) {
            return $this->json([]);
        }

        if ($request->isMethod('POST')) {
            return $this->json([
                'propositions' => (new OffreEmploiManager())->getMyPropositions($em, $this->session->get('user'))
            ]);
        }

        return $this->json([]);
    }

    /**
     * @Route("/get/accepted/propositions", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function getAcceptedPropositions(Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!($this->session->get('user'))) {
            return $this->json([]);
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYE) {
            return $this->json([]);
        }

        if ($request->isMethod('POST')) {
            return $this->json([
                'propositions' => (new OffreEmploiManager())->getAcceptedPropositions($em, $this->session->get('user'))
            ]);
        }

        return $this->json([]);
    }

    /**
     * @Route("/get/my/accepted/propositions", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function getMyAcceptedPropositions(Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!($this->session->get('user'))) {
            return $this->json([]);
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYEUR) {
            return $this->json([]);
        }

        if ($request->isMethod('POST')) {
            return $this->json([
                'propositions' => (new OffreEmploiManager())->getMyAcceptedPropositions($em, $this->session->get('user'))
            ]);
        }

        return $this->json([]);
    }

    /**
     * @Route("/get/favoris", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function getFavoris(Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!($this->session->get('user'))) {
            return $this->json([]);
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYE) {
            return $this->json([]);
        }

        if ($request->isMethod('POST')) {
            return $this->json([
                'favoris' => (new OffreEmploiManager())->getFavoris($em, $this->session->get('user'))
            ]);
        }

        return $this->json([]);
    }

    /**
     * @Route("/get/cvs/{nom}/{competences}/{langues}/{permis}/{limit}/{offset}", defaults={"nom":"none", "competences":"none", "langues":"none", "permis":"none", "limit":"25", "offset":"0"})
     * @param $nom
     * @param $competences
     * @param $langues
     * @param $permis
     * @param $limit
     * @param $offset
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function getCVs($nom, $competences, $langues, $permis, $limit, $offset, Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!($this->session->get('user'))) {
            return $this->json([]);
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYEUR) {
            return $this->json([]);
        }

        $separator = '_';

        $comps = [];
        if ($competences != 'none') {
            $comps = explode($separator, $competences);
        }

        $langs = [];
        if ($langues != 'none') {
            $langs = explode($separator, $langues);
        }

        $perm = $permis;
        if ($permis != 'none') {
            $perm = $permis == 'on';
        }

        $results = array_slice($em->getRepository(CV::class)->findByNomCompetencesLanguesPermis($nom, $comps, $langs, $perm), $offset, $limit);
        foreach ($results as $result) {
            if (!is_null($result->getEmploye())) {
                $result->getEmploye()->setCV(null);
            }
            foreach ($result->getCompetences() as $competence) {
                $competence->setCV(null);
            }
            foreach ($result->getMetiers() as $metier) {
                $metier->setCV(null);
            }
            foreach ($result->getDiplomes() as $diplome) {
                $diplome->setCV(null);
            }
            foreach ($result->getLangues() as $langue) {
                $langue->setCV(null);
            }
        }

        return $this->json([
            'cvs' => $results,
            'quantity' => count($results)
        ]);
    }

    /**
     * @Route("/get/offres_emploi/{nom}/{metier}/{typeContrat}/{salaire}/{heures}/{loge}/{deplacement}/{teletravail}/{limit}/{offset}", defaults={"nom":"none", "metier":"none", "typeContrat":"none", "salaire":"none", "heures":"none", "loge":"none", "deplacement":"none", "teletravail":"none", "limit":"25", "offset":"0"}, methods={"POST"})
     * @param $nom
     * @param $metier
     * @param $typeContrat
     * @param $salaire
     * @param $heures
     * @param $loge
     * @param $deplacement
     * @param $teletravail
     * @param $limit
     * @param $offset
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function getOffresEmploi($nom, $metier, $typeContrat, $salaire, $heures, $loge, $deplacement, $teletravail, $limit, $offset, Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!($this->session->get('user'))) {
            return $this->json([]);
        }

        if ($this->session->get('userType') != EntityManager::EMPLOYE) {
            return $this->json([]);
        }

        if ($request->isMethod('POST')) {
            $results = (new OffreEmploiManager())->findOffreEmploiByTypeContratMetierFromPreResult(
                $em->getRepository(OffreEmploi::class)->findBySalaireHeuresLogeDeplacementTeletravailNom($nom, $salaire, $heures, $loge, $deplacement, $teletravail),
                $typeContrat,
                $metier);

            return $this->json([
                'offres' => array_slice(
                    $results,
                    $offset,
                    $limit),
                'quantity' => count($results)
            ]);
        }

        return $this->json([]);
    }
}