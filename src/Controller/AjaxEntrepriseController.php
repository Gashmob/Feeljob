<?php


namespace App\Controller;


use App\database\EntityManager;
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
     * @Route("/candidate/{id}", requirements={"id": true}, methods={"POST"})
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
                'result' => EntityManager::getRepository(EntityManager::OFFRE_EMPLOI)->candidate($id, $this->session->get('user'))
            ]);
        }

        return $this->json(['result' => false]);
    }

    /**
     * @Route("/uncandidate/{id}", requirements={"id": true}, methods={"POST"})
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
            return $this->json([
                'result' => EntityManager::getRepository(EntityManager::OFFRE_EMPLOI)->uncandidate($id, $this->session->get('user'))
            ]);
        }

        return $this->json(['result' => false]);
    }

    /**
     * @Route("/propose/{idO}/{idE}", requirements={"idO": true, "idE": true}, methods={"POST"})
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
                'result' => EntityManager::getRepository(EntityManager::OFFRE_EMPLOI)->propose($idO, $idE)
            ]);
        }

        return $this->json(['result' => false]);
    }

    /**
     * @Route("/remove/proposition/{idO}/{idE}", requirements={"idO": true, "idE": true}, methods={"POST"})
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

        if ($this->session->get('userType') != EntityManager::PARTICULIER) {
            return $this->json(['result' => false]);
        }

        if ($request->isMethod('POST')) {
            return $this->json([
                'result' => EntityManager::getRepository(EntityManager::OFFRE_EMPLOI)->removeProposition($idO, $idE)
            ]);
        }

        return $this->json(['result' => false]);
    }

    /**
     * @Route("/accept/proposition/{id}", requirements={"id": true}, methods={"POST"})
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
                'result' => EntityManager::getRepository(EntityManager::OFFRE_EMPLOI)->acceptProposition($id, $this->session->get('user'))
            ]);
        }

        return $this->json(['result' => false]);
    }

    /**
     * @Route("/accept/candidature/{idO}/{idE}", requirements={"idO": true, "idE": true}, methods={"POST"})
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
                'result' => EntityManager::getRepository(EntityManager::OFFRE_EMPLOI)->acceptCandidature($idO, $idE)
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
                'candidatures' => EntityManager::getRepository(EntityManager::OFFRE_EMPLOI)->getCandidature($em, $this->session->get('user'))
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
                'propositions' => EntityManager::getRepository(EntityManager::OFFRE_EMPLOI)->getPropositions($em, $this->session->get('user'))
            ]);
        }

        return $this->json([]);
    }
}