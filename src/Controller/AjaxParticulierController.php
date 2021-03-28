<?php


namespace App\Controller;


use App\database\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AjaxParticulierController
 * @package App\Controller
 * @Route("/particulier")
 */
class AjaxParticulierController extends AbstractController
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

        if (EntityManager::getRepository(EntityManager::UTILS)->getUserTypeFromId($this->session->get('user')) != EntityManager::AUTO_ENTREPRENEUR) {
            return $this->json(['result' => false]);
        }

        if ($request->isMethod('POST')) {
            return $this->json([
                'result' => EntityManager::getRepository(EntityManager::ANNONCE)->candidate($id, $this->session->get('user'))
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

        if (EntityManager::getRepository(EntityManager::UTILS)->getUserTypeFromId($this->session->get('user')) != EntityManager::AUTO_ENTREPRENEUR) {
            return $this->json(['result' => false]);
        }

        if ($request->isMethod('POST')) {
            return $this->json([
                'result' => EntityManager::getRepository(EntityManager::ANNONCE)->uncandidate($id, $this->session->get('user'))
            ]);
        }

        return $this->json(['result' => false]);
    }

    /**
     * @Route("/propose/{idAnn}/{idAuto}", requirements={"idAnn": true, "idAuto": true}, methods={"POST"})
     * @param $idAnn
     * @param $idAuto
     * @param Request $request
     * @return JsonResponse
     */
    public function propose($idAnn, $idAuto, Request $request): JsonResponse
    {
        if (!($this->session->get('user'))) {
            return $this->json(['result' => false]);
        }

        if (EntityManager::getRepository(EntityManager::UTILS)->getUserTypeFromId($this->session->get('user')) != EntityManager::PARTICULIER) {
            return $this->json(['result' => false]);
        }

        if ($request->isMethod('POST')) {
            return $this->json([
                'result' => EntityManager::getRepository(EntityManager::ANNONCE)->propose($idAnn, $idAuto)
            ]);
        }

        return $this->json(['result' => false]);
    }

    /**
     * @Route("/remove/proposition/{idAnn}/{idAuto}", requirements={"idAnn": true, "idAuto": true}, methods={"POST"})
     * @param $idAnn
     * @param $idAuto
     * @param Request $request
     * @return JsonResponse
     */
    public function removeProposition($idAnn, $idAuto, Request $request): JsonResponse
    {
        if (!($this->session->get('user'))) {
            return $this->json(['result' => false]);
        }

        if (EntityManager::getRepository(EntityManager::UTILS)->getUserTypeFromId($this->session->get('user')) != EntityManager::PARTICULIER) {
            return $this->json(['result' => false]);
        }

        if ($request->isMethod('POST')) {
            return $this->json([
                'result' => EntityManager::getRepository(EntityManager::ANNONCE)->removeProposition($idAnn, $idAuto)
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

        if (EntityManager::getRepository(EntityManager::UTILS)->getUserTypeFromId($this->session->get('user')) != EntityManager::AUTO_ENTREPRENEUR) {
            return $this->json(['result' => false]);
        }

        if ($request->isMethod('POST')) {
            return $this->json([
                'result' => EntityManager::getRepository(EntityManager::ANNONCE)->acceptProposition($id, $this->session->get('user'))
            ]);
        }

        return $this->json(['result' => false]);
    }

    /**
     * @Route("/accept/candidature/{idAnn}/{idAuto}", requirements={"idAnn": true, "idAuto": true}, methods={"POST"})
     * @param $idAnn
     * @param $idAuto
     * @param Request $request
     * @return JsonResponse
     */
    public function acceptCandidature($idAnn, $idAuto, Request $request): JsonResponse
    {
        if (!($this->session->get('user'))) {
            return $this->json(['result' => false]);
        }

        if (EntityManager::getRepository(EntityManager::UTILS)->getUserTypeFromId($this->session->get('user')) != EntityManager::PARTICULIER) {
            return $this->json(['result' => false]);
        }

        if ($request->isMethod('POST')) {
            return $this->json([
                'result' => EntityManager::getRepository(EntityManager::ANNONCE)->acceptCandidature($idAnn, $idAuto)
            ]);
        }

        return $this->json(['result' => false]);
    }
}