<?php


namespace App\Controller;


use App\database\EntityManager;
use App\Entity\Annonce;
use App\Entity\AutoEntrepreneur;
use Doctrine\ORM\EntityManagerInterface;
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

        if ($this->session->get('userType') != EntityManager::AUTO_ENTREPRENEUR) {
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

        if ($this->session->get('userType') != EntityManager::AUTO_ENTREPRENEUR) {
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

        if ($this->session->get('userType') != EntityManager::PARTICULIER) {
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

        if ($this->session->get('userType') != EntityManager::PARTICULIER) {
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

        if ($this->session->get('userType') != EntityManager::AUTO_ENTREPRENEUR) {
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

        if ($this->session->get('userType') != EntityManager::PARTICULIER) {
            return $this->json(['result' => false]);
        }

        if ($request->isMethod('POST')) {
            return $this->json([
                'result' => EntityManager::getRepository(EntityManager::ANNONCE)->acceptCandidature($idAnn, $idAuto)
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

        if ($this->session->get('userType') != EntityManager::AUTO_ENTREPRENEUR) {
            return $this->json([]);
        }

        if ($request->isMethod('POST')) {
            return $this->json([
                'candidatures' => EntityManager::getRepository(EntityManager::ANNONCE)->getCandidature($em, $this->session->get('user'))
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

        if ($this->session->get('userType') != EntityManager::AUTO_ENTREPRENEUR) {
            return $this->json([]);
        }

        if ($request->isMethod('POST')) {
            return $this->json([
                'propositions' => EntityManager::getRepository(EntityManager::ANNONCE)->getPropositions($em, $this->session->get('user'))
            ]);
        }

        return $this->json([]);
    }

    /**
     * @Route("/get/annonces/{secteur}/{distanceMax}/{limit}/{offset}", methods={"POST"}, defaults={"secteur":"none", "distanceMax":"none", "limit":25, "offset":0})
     * @param $secteur
     * @param $distanceMax
     * @param $limit
     * @param $offset
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function getAnnonces($secteur, $distanceMax, $limit, $offset, Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!($this->session->get('user'))) {
            return $this->json([]);
        }

        if ($this->session->get('userType') != EntityManager::AUTO_ENTREPRENEUR) {
            return $this->json([]);
        }

        if ($request->isMethod('POST')) {
            $auto_entrepreneur = $em->getRepository(AutoEntrepreneur::class)->findOneBy(['identity' => $this->session->get('user')]);
            $adresse = $auto_entrepreneur->getAdresse();
            if ($distanceMax != 'none' && !is_null($adresse)) {
                $addressFrom = $adresse->getRue() . ' ' . $adresse->getCodePostal() . ' ' . $adresse->getVille();
                if ($secteur != 'none') {
                    $ids = EntityManager::getRepository(EntityManager::ANNONCE)->getAnnoncesBySecteurActivite($secteur, $offset, $limit);

                    return $this->json([
                        'annonces' => $em->getRepository(Annonce::class)->findByDistanceMaxFromPreResultIds($ids, $distanceMax, $addressFrom)
                    ]);
                } else { // $secteur == 'none'
                    return $this->json([
                        'annonces' => $em->getRepository(Annonce::class)->findByDistanceMax($distanceMax, $addressFrom, $offset, $limit)
                    ]);
                }
            } else { // $distanceMax == 'none'
                if ($secteur != 'none') {
                    $ids = EntityManager::getRepository(EntityManager::ANNONCE)->getAnnoncesBySecteurActivite($secteur, $offset, $limit);

                    return $this->json([
                        'annonces' => $em->getRepository(Annonce::class)->findByIdentity($ids)
                    ]);
                } else { // $secteur == 'none'
                    return $this->json([
                        'annonces' => $em->getRepository(Annonce::class)->findBy([], null, $limit, $offset)
                    ]);
                }
            }
        }

        return $this->json([]);
    }
}