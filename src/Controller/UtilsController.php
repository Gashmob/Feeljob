<?php


namespace App\Controller;


use App\database\EntityManager;
use App\database\manager\MetierManager;
use App\database\PreparedQuery;
use App\database\Query;
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
     * All Metier
     */
    private const METIERS = [
        'Agriculture' => [
            'Agriculteur',
            'Agronome',
            'Berger',
            'Conseiller de gestion agricole',
            'Chargé d\'affaires agricoles',
            'Conducteur de machines agricoles',
            'Fermier',
            'Conchyliculteur',
            'Horticulteur',
            'Juriste agricole',
            'Cueilleur',
            'Cultivateur',
            'Paysan',
            'Maraîcher',
            'Employé',
            'Entreposeur',
            'Exécutant',
            'Ingénieur d\'étude',
            'Viticulteur',
            'Vigneron',
            'Métayer',
            'Ouvrier',
            'Responsable',
            'Technicien',
            'Responsable d\'élevage',
        ],
        'Agro-alimentaire/Alimentation' => [
            'Boucher',
            'Boulanger',
            'Brasseur malteur',
            'Agent de distribution',
            'Agent de manutention',
            'Céréalier',
            'Charcutier',
            'Conseiller de programme',
            'Conseiller viticole',
            'Crémier',
            'Crêpier',
            'Directeur de supermarché',
            'Frigoriste',
            'Laitier',
            'Meunier',
            'Pizzaïolo',
            'Poissonnier',
            'Primeur',
            'Écailler',
            'Mareyeur',
            'Directeur',
            'Employé',
            'Entreposeur',
            'Exécutant',
            'Fabricant',
            'Garde-manger',
            'Ingénieur d\'étude',
            'Ingénieur de fabrication',
            'Ouvrier',
            'Technicien',
            'Œnologue',
            'Rôtisseur',
            'Saunier',
            'Testeur culinaire',
            'Maître ouvrier',
            'Manager de l\'innovation',
            'Opérateur',
            'Préparateur méthodes',
            'Responsable',
            'Testeur',
        ],
        'Animaux' => [
            'Assistant vétérinaire',
            'Auxiliaire de santé animale (vétérinaire)',
            'Animalier en laboratoire',
            'Aquaculteur',
            'Chargé de recherche animalier',
            'Cadre d\'écurie',
            'Dompteur',
            'Dresseur',
            'Éducateur canin',
            'Éleveur',
            'Enseignant animateur équestre',
            'Fauconnier',
            'Maître chien',
            'Ostéopathe animalier',
            'Pet sitter',
            'Maréchal-ferrant',
            'Palefrenier',
            'Directeur',
            'Employé',
            'Exécutant',
            'Herpétologue',
            'Ingénieur d\'étude',
            'Soigneur animalier',
            'Toiletteur',
            'Vendeur en animalerie',
            'Vétérinaire',
            'Zoologiste',
            'Zoologue',
            'Zoothérapeute',
            'Lad-driver',
            'Lad-jockey',
            'Loueur d\'équidés',
            'Ouvrier',
            'Responsable',
            'Technicien',
        ],
        'Architecture/Aménagement intérieur' => [
            'Aménageur d\'espace intérieur',
            'Animateur 2D',
            'Animateur 3D',
            'BIM Manager',
            'Architecte',
            'Architecte d\'intérieur',
            'Architecte du patrimoine',
            'Architecte paysagiste',
            'Chargé du développement',
            'Concepteur',
            'Chef décorateur',
            'Cuisiniste',
            'Designer',
            'Designer mobilier',
            'Dessinateur',
            'Designer produit',
            'Décorateur',
            'Décorateur d\'intérieur',
            'Conseiller de programme',
            'Développeur de produits',
            'Designer manager',
            'Développeur d\'enseigne',
            'Directeur',
            'Employé',
            'Exécutant',
            'Ingénieur d\'étude',
            'Ergonome',
            'Modélisateur',
            'Métreur',
            'Manager de l\'innovation',
            'Urbaniste',
            'Product manager',
            'Product owner',
            'Responsable',
            'Technicien',
            'Testeur',
        ],
        'Artisanat/Métier d\'art' => [
            'Acheteur d\'art',
            'Agent artistique',
            'Apiculteur',
            'Apprêteur',
            'Bijoutier',
            'Ardoisier',
            'Armurier',
            'Archetier',
            'Argenteur',
            'Artificier',
            'Artisan fromager',
            'Artiste peintre',
            'Chausseur',
            'Âtrier',
            'Batteur d\'or',
            'Chocolatier-confiseur',
            'Chocolatier',
            'Boutonnier',
            'Briquetier',
            'Brodeur',
            'Cirier',
            'Créateur de parfum',
            'Décorateur-étalagiste',
            'Dentellier',
            'Designer céramique',
            'Dinandier',
            'Employé',
            'Encadreur',
            'Entreposeur',
            'Facteur d\'instruments',
            'Bronzier',
            'Calligraphe',
            'Campaniste',
            'Chaudronnier',
            'Canneur-rempailleur',
            'Céramiste',
            'Chaîniste',
            'Chapelier',
            'Charron',
            'Chaumier',
            'Cordonnier',
            'Costumier',
            'Corsetier',
            'Coutelier',
            'Créateur',
            'Créateur de mode',
            'Ébéniste',
            'Ferronnier d\'art',
            'Coffreur-boiseur',
            'Confiseur',
            'Ciseleur',
            'Décorateur en résine',
            'Diamantaire',
            'Exécutant',
            'Fabricant',
            'Féron',
            'Feutrier',
            'Fondeur',
            'Fontainier',
            'Formier',
            'Forgeron',
            'Fromager',
            'Glacier',
            'Graveur sur pierre',
            'Joaillier',
            'Ferrailleur',
            'Graveur',
            'Horloger',
            'Ferronnier',
            'Maître tailleur',
            'Maître verrier',
            'Maître-ouvrier d\'art',
            'Maquettiste',
            'Maquettiste automobile',
            'Menuisier',
            'Menuisier en sièges',
            'Métallier',
            'Mosaïste',
            'Orfèvre',
            'Pâtissier',
            'Graveur de poinçons',
            'Ivoirier',
            'Laqueur',
            'Ouvrier',
            'Parfumeur',
            'Perruquier',
            'Piqueur',
            'Plisseur',
            'Raffineur',
            'Technicien',
            'Peintre en décor',
            'Peintre en lettres',
            'Peintre solier',
            'Peintre sur mobilier',
            'Peintre',
            'Escaliéteur',
            'Enlumineur',
            'Émailleur sur lave',
            'Émailleur sur métal',
            'Doreur',
            'Doreur sur cuir',
            'Doreur sur tranche',
            'Dominotier',
            'Éventailliste',
            'Fresquiste',
            'Gemmologue',
            'Glypticien',
            'Guillocheur',
            'Imagier au pochoir',
            'Maroquinier',
            'Rappeur',
            'Testeur',
            'Marqueteur de pailles',
            'Marqueteur de pierres dures',
            'Mégissier',
            'Modiste',
            'Nacrier',
            'Ostréiculteur',
            'Parqueteur',
            'Passementier',
            'Pipier',
            'Gantier',
            'Fourreur',
            'Lapidaire',
            'Maître de chai',
            'Marbrier',
            'Marqueteur',
            'Mytiliculteur',
            'Luthier',
            'Restaurateur',
            'Restaurateur d\'art',
            'Sculpteur',
            'Sculpteur sur bois',
            'Sculpteur sur métal',
            'Sculpteur sur pierre',
            'Sellier',
            'Sellier bourrelier',
            'Sellier d\'ameublement',
            'Sellier maroquinier',
            'Serrurier',
            'Serrurier métallier',
            'Shaper',
            'Soudeur',
            'Souffleur de verre',
            'Staffeur ornemaniste',
            'Stratifieur',
            'Tailleur',
            'Tailleur de pierre',
            'Tanneur',
            'Tapissier',
            'Tapissier d\'ameublement',
            'Tapissier garnisseur',
            'Tatoueur',
            'Taillandier',
            'Taxidermiste',
            'Tisserand',
            'Tôlier',
            'Tonnelier',
            'Tuilier',
            'Vernisseur',
            'Zingueur',
            'Plasticien',
            'Plumassier',
            'Porcelainier',
            'Potier',
            'Potier d\'étain',
            'Quincaillier',
            'Ramoneur',
            'Relieur',
            'Relieur-doreur',
            'Sertisseur',
            'Product manager',
            'Product owner',
            'Responsable',
            'Solier-moquettiste',
            'Tabletier',
            'Voilier-sellier',
            'Tourneur sur bois',
            'Tourneur sur métal',
            'Vitrailliste',
            'Vannier',
            'Verrier',
        ],
        'Audiovisuel/Numérique/Multimédia' => [
            'Accordeur (instrument)',
            'Acousticien',
            'Animateur radio',
            'Agent de distribution',
            'Animateur de télévision',
            'Character designer',
            'Chargé de programmation',
            'Chargé du développement',
            'Caméraman',
            'Cadreur',
            'Chanteur',
            'Chargé de production vidéo',
            'Compositeur',
            'Compositeur de musique',
            'Conseiller en image',
            'Critique d\'art',
            'Critique de cinéma',
            'Designer graphique',
            'Conseiller de programme',
            'Directeur',
            'Employé',
            'Étalonneur numérique',
            'Exécutant',
            'Infographe',
            'Ingénieur d\'étude',
            'Designer graphiste',
            'Développeur multimédia',
            'Designer de réalité virtuelle',
            'Graphiste',
            'Illustrateur',
            'Illustrateur 3D',
            'Infographiste',
            'Ingénieur du son',
            'Journaliste radio',
            'Designer sonore',
            'E-sportif',
            'Game designer',
            'Musicien',
            'Présentateur radio',
            'Présentateur télé',
            'Photographe',
            'Programmeur',
            'Programmeur de jeux vidéo',
            'Layout artist',
            'Habilleur',
            'Harpiste',
            'Immersive designer',
            'Influenceur',
            'Ingénieur télépilote',
            'Lead game designer',
            'Technicien des effets spéciaux',
            'Technicien vidéo',
            'Testeur de jeux vidéo',
            'Scénariste',
            'Level designer',
            'Mixeur',
            'Monteur incorporateur',
            'Pilote de drone',
            'Pro gamer',
            'Photocompositeur',
            'Professionnels du dessin animé',
            'Projectionniste',
            'Réalisateur',
            'Réalisateur de cinéma',
            'Réalisateur de plateau',
            'Réalisateur de radio',
            'Réalisateur VR',
            'Réalisateur de téléfilms',
            'Manager de l\'innovation',
            'Media planner',
            'Modérateur',
            'Monteur',
            'Opérateur',
            'Perchman',
            'Producteur',
            'Sound designer',
            'Télépilote audiovisuel',
            'UX designer',
            'UI designer',
            'Product manager',
            'Product owner',
            'Tools programmer',
            'Responsable',
            'Story-boarder',
            'Streamer',
            'Technical artist',
            'Technicien d\'antenne',
            'Testeur',
            'Violoniste',
            'Youtuber',
        ],
        'Banque/Finance/Assurance' => [
            'Agent d\'assurance',
            'Analyste crédit',
            'Analyste financier',
            'Chargé de conformité',
            'Agent général d\'assurances',
            'Banquier',
            'Auditeur financier',
            'Actuaire',
            'Agent de change',
            'Analyste',
            'Animateur économique',
            'Auditeur externe',
            'Auditeur interne',
            'Chargé d\'affaire',
            'Chargé du développement',
            'Conseiller financier',
            'Conseiller fiscal',
            'Contrôleur financier',
            'Courtier',
            'Courtier en assurances',
            'Conseiller',
            'Conseiller en assurances',
            'Consolideur',
            'Convoyeur de fonds',
            'Credit manager',
            'Data scientist',
            'Directeur',
            'Employé',
            'Exécutant',
            'Courtier en bourse',
            'Économe',
            'Économiste',
            'Expert comptable',
            'Employé d\'assurance',
            'Employé de banque',
            'Expert assurance',
            'Expert-comptable',
            'Conseiller de programme',
            'Ingénieur financier',
            'Juriste dans les assurances',
            'Directeur financier',
            'Éco-conseiller',
            'Mandataire liquidateur',
            'Fiscaliste',
            'Gérant de portefeuille',
            'Gestionnaire d\'actifs',
            'Trader',
            'Risk manager',
            'Souscripteur',
            'Rédacteur de contrats',
            'Responsable',
            'Trésorier',
            'Technicien du patrimoine',
            'Technicien de paie',
            'Conseiller en économie sociale',
        ],
        'Bâtiment/Travaux public' => [
            'Carreleur',
            'Agent de distribution',
            'Agent de manutention',
            'Assistant chef de chantier',
            'Charpentier',
            'Charpentier bois',
            'Chef de chantier',
            'Chef de travaux',
            'Conducteur d\'engin de chantier',
            'Chauffagiste',
            'Chargé d\'affaire',
            'Conducteur de travaux',
            'Contrôleur de travaux',
            'Couvreur',
            'Échafaudeur',
            'Éclairagiste',
            'Économiste de la construction',
            'Électricien',
            'Étancheur',
            'Façadier',
            'Conseiller de programme',
            'Contremaître',
            'Directeur',
            'Employé',
            'Exécutant',
            'Planificateur OPC',
            'Démolisseur',
            'Grutier',
            'Ingénieur électricien',
            'Maçon',
            'Dalleur',
            'Électricien d\'équipement',
            'Géomètre',
            'Géomètre expert',
            'Géomètre topographe',
            'Ingénieur du BTP',
            'Monteur électricien',
            'Monteur en isolation thermique',
            'Monteur-câbleur',
            'Paveur-dalleur',
            'Peintre en bâtiment',
            'Plaquiste',
            'Plâtrier',
            'Plombier/Chauffagiste',
            'Faïencier',
            'Entreposeur',
            'Ingénieur d\'étude',
            'Ingénieur de fabrication',
            'Maître ouvrier',
            'Manage de l\'innovation',
            'Ouvrier',
            'Terrassier',
            'Photogrammètre',
            'Vitrier',
            'Tireur de cable',
            'Responsable',
            'Technicien',
        ],
        'Biologie/Chimie/Recherche' => [
            'Biochimiste',
            'Biologiste',
            'Biologiste médical',
            'Agronome',
            'Biomathématicien',
            'Aromaticien',
            'Aromaticien parfumeur',
            'Chef de laboratoire',
            'Chimiste',
            'Chercheur',
            'Biophysicien',
            'Biotechnologue',
            'Chargé du développement',
            'Exobiologiste',
            'Ethnologue',
            'Géochimiste',
            'Géographe',
            'Géologue',
            'Ingénieur chimiste',
            'Ingénieur de recherche',
            'Directeur',
            'Employé',
            'Exécutant',
            'Logisticien nucléaire',
            'Ingénieur biomécanique',
            'Hydrobiologiste',
            'Hydrogéologue',
            'Océanographe',
            'Océanologue',
            'Éthologue',
            'Laborantin',
            'Formulateur',
            'Glaciologue',
            'Goûteur d\'eau',
            'Ingénieur d\'étude',
            'Ingénieur de fabrication',
            'Statisticien',
            'Technicien biologiste',
            'Technicien de laboratoire',
            'Qualiticien',
            'Manager de l\'innovation',
            'Opérateur',
            'Préparateur',
            'Responsable conseil brevets',
            'Responsable',
            'Volcanologue',
            'Ingénieur civil',
            'Ingénieur en aéronautique',
            'Technicien',
            'Testeur',
        ],
        'Commerce (Vendeur/Commercial)' => [

        ],
        'Communication/Information' => [

        ],
        'Culture/Spectacle' => [

        ],
        'Défense/Sécurité/Secours' => [

        ],
        'Droit' => [

        ],
        'Edition/Littérature/Imprimerie' => [

        ],
        'Enseignement/Formation' => [

        ],
        'Esthétique/Coiffure/Soins' => [

        ],
        'Environnement/Nature/Nettoyage' => [

        ],
        'Gestion/RH' => [

        ],
        'Histoire/Histoire de l\'art' => [

        ],
        'Hôtellerie/Restauration/Tourisme' => [

        ],
        'Humanitaire' => [

        ],
        'Informatique/Electronique' => [

        ],
        'Industrie/Usine' => [

        ],
        'Mécanique/Maintenance' => [

        ],
        'Maths/Sciences/Physique' => [

        ],
        'Santé' => [

        ],
        'Secrétariat/Accueil' => [

        ],
        'Service à la personne/Social' => [

        ],
        'Sport/Animation' => [

        ],
        'Transport/Logistique' => [

        ]
    ];
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

        // Fill Metier
        foreach (self::METIERS as $secteurActivite => $metiers) {
            if ((new PreparedQuery('MATCH (s:' . EntityManager::SECTEUR_ACTIVITE . ' {nom:$nom}) RETURN s'))
                    ->setString('nom', $secteurActivite)
                    ->run()
                    ->getOneOrNullResult() == null) { // If not exist
                $alreadyFilled = false;
                (new PreparedQuery('CREATE (:' . EntityManager::SECTEUR_ACTIVITE . ' {nom:$nom})'))
                    ->setString('nom', $secteurActivite)
                    ->run(); // Create
            }

            foreach ($metiers as $metier) {
                if ((new PreparedQuery('MATCH (m:' . EntityManager::METIER . ' {nom:$nom}) RETURN m'))
                        ->setString('nom', $secteurActivite)
                        ->run()
                        ->getOneOrNullResult() == null) { // If not exist
                    $alreadyFilled = false;
                    (new MetierManager())->create($metier, $secteurActivite);
                }
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