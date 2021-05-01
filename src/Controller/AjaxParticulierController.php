<?php


namespace App\Controller;


use App\database\EntityManager;
use App\database\manager\AnnonceManager;
use App\database\manager\AutoEntrepreneurManager;
use App\Entity\Annonce;
use App\Entity\AutoEntrepreneur;
use App\Entity\CarteVisite;
use App\Entity\Particulier;
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

        if ($this->session->get('userType') != EntityManager::AUTO_ENTREPRENEUR) {
            return $this->json(['result' => false]);
        }

        if ($request->isMethod('POST')) {
            return $this->json([
                'result' => (new AnnonceManager())->candidate($id, $this->session->get('user'))
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

        if ($this->session->get('userType') != EntityManager::AUTO_ENTREPRENEUR) {
            return $this->json(['result' => false]);
        }

        if ($request->isMethod('POST')) {
            (new AnnonceManager())->uncandidate($id, $this->session->get('user'));
            return $this->json(['result' => true]);
        }

        return $this->json(['result' => false]);
    }

    /**
     * @Route("/propose/{idAnn}/{idAuto}", methods={"POST"})
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
                'result' => (new AnnonceManager())->propose($idAnn, $idAuto)
            ]);
        }

        return $this->json(['result' => false]);
    }

    /**
     * @Route("/remove/proposition/{idAnn}/{idAuto}", methods={"POST"})
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
            (new AnnonceManager())->removeProposition($idAnn, $idAuto);
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

        if ($this->session->get('userType') != EntityManager::AUTO_ENTREPRENEUR) {
            return $this->json(['result' => false]);
        }

        if ($request->isMethod('POST')) {
            (new AnnonceManager())->removeProposition($id, $this->session->get('user'));
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

        if ($this->session->get('userType') != EntityManager::AUTO_ENTREPRENEUR) {
            return $this->json(['result' => false]);
        }

        if ($request->isMethod('POST')) {
            return $this->json([
                'result' => (new AnnonceManager())->acceptProposition($id, $this->session->get('user'))
            ]);
        }

        return $this->json(['result' => false]);
    }

    /**
     * @Route("/accept/candidature/{idAnn}/{idAuto}", methods={"POST"})
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
                'result' => (new AnnonceManager())->acceptCandidature($idAnn, $idAuto)
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
                'candidatures' => (new AnnonceManager())->getCandidature($em, $this->session->get('user'))
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
                'propositions' => (new AnnonceManager())->getPropositions($em, $this->session->get('user'))
            ]);
        }

        return $this->json([]);
    }

    /**
     * @Route("/get/annonces/{secteur}/{distanceMax}/{limit}/{offset}", defaults={"secteur":"none", "distanceMax":"-1", "limit":25, "offset":0})
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
            $results = (new AnnonceManager())->getAnnoncesBySecteurActiviteFromPreResult(
                $em->getRepository(Annonce::class)->findByDistanceMax($distanceMax, $adresse->getRue() . ' ' . $adresse->getCodePostal() . ' ' . $adresse->getVille()),
                $secteur
            );

            return $this->json([
                'annonces' => array_slice(
                    $results,
                    $offset,
                    $limit
                ),
                'quantity' => count($results)
            ]);
        }

        return $this->json([]);
    }

    /**
     * @Route("/get/cartes/{nom}/{secteur}/{distanceMax}/{limit}/{offset}", methods={"POST"}, defaults={"nom":"none", "secteur":"none", "distanceMax":"none", "limit":"25", "offset":"0"})
     * @param $nom
     * @param $secteur
     * @param $distanceMax
     * @param $limit
     * @param $offset
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function getCartesVisite($nom, $secteur, $distanceMax, $limit, $offset, Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!($this->session->get('user'))) {
            return $this->json([]);
        }

        if ($this->session->get('userType') != EntityManager::PARTICULIER) {
            return $this->json([]);
        }

        if ($request->isMethod('POST')) {
            $filterNom = $nom == 'none' ? $em->getRepository(AutoEntrepreneur::class)->findAll() :
                $em->getRepository(AutoEntrepreneur::class)->findBy(['nomEntreprise' => $nom]);

            $filterSecteur = $secteur == 'none' ? $filterNom :
                (new AutoEntrepreneurManager())->findBySecteurActiviteFromPreResult($filterNom, $secteur);

            $particulier = $em->getRepository(Particulier::class)->findOneBy(['identity' => $this->session->get('user')]);
            $adresse = $particulier->getAdresse();
            $filterDistance = $distanceMax == 'none' || is_null($adresse) ? $filterSecteur :
                $em->getRepository(AutoEntrepreneur::class)->findByDistanceMaxFromPreResult($filterSecteur, $distanceMax, $adresse->getRue() . ' ' . $adresse->getCodePostal() . ' ' . $adresse->getVille());

            $results = array_slice(
                $em->getRepository(CarteVisite::class)->findByAutoEntrepreneur($filterDistance),
                $offset,
                $limit
            );

            foreach ($results as $result) {
                $result->getAutoEntrepreneur()->setCarteVisite(null);
                foreach ($result->getRealisations() as $realisation) {
                    $realisation->setCarteVisite(null);
                }
            }

            return $this->json([
                'cartes' => $results
            ]);
        }

        return $this->json([]);
    }
}