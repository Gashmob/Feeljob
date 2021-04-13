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
    private const SECTEURS_ACTIVITE = ['Agriculture', 'Agro-alimentaire/Alimentation', 'Animaux',
        'Architecture/Aménagement intérieur', 'Artisanat/Métier d\'art', 'Audiovisuel/Numérique/Multimédia',
        'Banque/Finance/Assurance', 'Bâtiment/Travaux public', 'Biologie/Chimie/Recherche',
        'Commerce (Vendeur/Commercial)', 'Communication/Information', 'Culture/Spectacle', 'Défense/Sécurité/Secours',
        'Droit', 'Edition/Littérature/Imprimerie', 'Enseignement/Formation', 'Esthétique/Coiffure/Soins',
        'Environnement/Nature/Nettoyage', 'Gestion/RH', 'Histoire/Histoire de l\'art',
        'Hôtellerie/Restauration/Tourisme', 'Humanitaire', 'Informatique/Electronique', 'Industrie/Usine',
        'Mécanique/Maintenance', 'Maths/Sciences/Physique', 'Santé', 'Secrétariat/Accueil',
        'Service à la personne/Social', 'Sport/Animation', 'Transport/Logistique'];
    /**
     * All Langue
     */
    private const LANGUES = ['Albanais', 'Allemand', 'Anglais', 'Arabe', 'Arménien', 'Basque', 'Bengali', 'Birman',
        'Bulgare', 'Catalan', 'Chinois', 'Cingalais', 'Coréen', 'Corse', 'Croate', 'Danois', 'Espagnol', 'Espéranto',
        'Estonien', 'Finnois', 'Français', 'Gaélique', 'Galicien', 'Gallois', 'Géorgien', 'Grec', 'Hébreu', 'Hindi',
        'Indonésien', 'Italien', 'Japonais', 'Javanais', 'Khmer', 'Latin', 'Letton', 'Lituanien', 'Malaisien',
        'Néerlandais', 'Népalais', 'Norvégien', 'Polonais', 'Portugais', 'Roumain', 'Russe', 'Serbe', 'Slovaque',
        'Slovène', 'Suédois', 'Tchèque', 'Turc', 'Ukrainien', 'Vietnamien'];
    /**
     * All SituationFamille
     */
    private const SITUATIONS_FAMILLE = ['Célibataire', 'En couple'];
    /**
     * All AbonnementEntreprise
     */
    private const ABONNEMENTS_ENTREPRISE = [
        ['nom' => '1 Annonce', 'description' => '1 seule annonce', 'montant' => 20],
        ['nom' => '5 Annonces', 'description' => 'Jusqu\'à 5 annonces', 'montant' => 16],
        ['nom' => '20 Annonces', 'description' => 'Jusqu\'à 20 annonces', 'montant' => 14],
        ['nom' => '30 Annonces', 'description' => 'Jusqu\'à 30 annonces', 'montant' => 12],
        ['nom' => '40 Annonces', 'description' => 'Jusqu\'à 40 annonces', 'montant' => 10],
        ['nom' => 'Abonnement', 'description' => 'Autant d\'annonces que vous voulez', 'montant' => 10],
    ];

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
            if ((new PreparedQuery('MATCH (s:' . EntityManager::SECTEUR_ACTIVITE . ' {nom:$nom}) RETURN s'))
                    ->setString('nom', $secteurActivite)
                    ->run()
                    ->getOneOrNullResult() == null) { // If not exist
                $alreadyFilled = false;
                (new PreparedQuery('CREATE (:' . EntityManager::SECTEUR_ACTIVITE . ' {nom:$nom})'))
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