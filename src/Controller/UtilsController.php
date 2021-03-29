<?php


namespace App\Controller;


use App\database\EntityManager;
use App\database\PreparedQuery;
use App\Entity\AbonnementEntreprise;
use App\Entity\Langue;
use App\Entity\SituationFamille;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UtilsController
 * @package App\Controller
 * @Route("/utils")
 */
final class UtilsController extends AbstractController
{
    /**
     * All TypeContrat
     */
    private const TYPES_CONTRAT = ['CDD', 'CDI', 'Saisonnier', 'Job d\'appoint'];
    /**
     * All SecteurActivite
     */
    private const SECTEURS_ACTIVITE = [];
    /**
     * All Langue
     */
    private const LANGUES = [];
    /**
     * All SituationFamille
     */
    private const SITUATIONS_FAMILLE = [];
    /**
     * All AbonnementEntreprise
     */
    private const ABONNEMENTS_ENTREPRISE = [];

    /**
     * @Route("/fill")
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function fillBdd(EntityManagerInterface $em): JsonResponse
    {
        $alreadyFilled = true;

        // Fill TypeContrat
        foreach (self::TYPES_CONTRAT as $typeContrat) {
            if ((new PreparedQuery('MATCH (t:' . EntityManager::TYPE_CONTRAT . ' {nom:$nom}) RETURN t'))
                    ->setString('nom', $typeContrat)
                    ->run()
                    ->getOneOrNullResult() == null) { // If not exist
                $alreadyFilled = false;
                (new PreparedQuery('CREATE (:' . EntityManager::TYPE_CONTRAT . ' {nom:$nom})'))
                    ->setString('nom', $typeContrat)
                    ->run(); // Create
            }
        }

        // Fill SecteurActivite
        foreach (self::SECTEURS_ACTIVITE as $secteurActivite) {
            if ((new PreparedQuery('MATCH (s:' . EntityManager::SECTEUR_ACTIVITE . ' {nom:$nom) RETURN s'))
                    ->setString('nom', $secteurActivite)
                    ->run()
                    ->getOneOrNullResult() == null) { // If not exist
                $alreadyFilled = false;
                (new PreparedQuery('MATCH (:' . EntityManager::SECTEUR_ACTIVITE . ' {nom:$nom)'))
                    ->setString('nom', $secteurActivite)
                    ->run(); // Create
            }
        }

        // Fill Langue
        foreach (self::LANGUES as $langue) {
            if ($em->getRepository(Langue::class)->findOneBy(['nom' => $langue]) == null) {
                $alreadyFilled = false;
                $l = (new Langue())
                    ->setNom($langue);
                $em->persist($l);
            }
        }
        $em->flush();

        // Fill SituationFamille
        foreach (self::SITUATIONS_FAMILLE as $situationFamille) {
            if ($em->getRepository(SituationFamille::class)->findOneBy(['nom' => $situationFamille]) == null) {
                $alreadyFilled = false;
                $s = (new SituationFamille())
                    ->setNom($situationFamille);
                $em->persist($s);
            }
        }
        $em->flush();

        // Fill AbonnementEntreprise
        foreach (self::ABONNEMENTS_ENTREPRISE as $abonnementEntreprise) {
            if ($em->getRepository(AbonnementEntreprise::class)->findOneBy(['nom' => $abonnementEntreprise['nom']]) == null) {
                $alreadyFilled = false;
                $a = (new AbonnementEntreprise())
                    ->setNom($abonnementEntreprise['nom'])
                    ->setDescription($abonnementEntreprise['description'])
                    ->setMontant($abonnementEntreprise['montant']);
                $em->persist($a);
            }
        }
        $em->flush();

        if ($alreadyFilled)
            return $this->json(['result' => 'already_filled']);
        else
            return $this->json(['result' => 'filled']);
    }
}