<?php


namespace App\database;

use App\database\manager\AnnonceManager;
use App\database\manager\AutoEntrepreneurManager;
use App\database\manager\EmployeManager;
use App\database\manager\EmployeurManager;
use App\database\manager\Manager;
use App\database\manager\OffreEmploiManager;
use App\database\manager\ParticulierManager;
use App\database\manager\SecteurActiviteManager;
use App\database\manager\TypeContratManager;
use App\database\manager\UtilsManager;

/**
 * Class EntityManager
 * @package App\database
 */
abstract class EntityManager
{
    public const TYPE_CONTRAT = 'TypeContrat';
    public const OFFRE_EMPLOI = 'OffreEmploi';
    public const EMPLOYEUR = 'Employeur';
    public const EMPLOYE = 'Employe';
    public const SECTEUR_ACTIVITE = 'SecteurActivite';
    public const AUTO_ENTREPRENEUR = 'AutoEntrepreneur';
    public const PARTICULIER = 'Particulier';
    public const ANNONCE = 'Annonce';

    public const PUBLIE = 'PUBLIE';
    public const CANDIDATURE = 'CANDIDATURE';
    public const PROPOSITION = 'PROPOSITION';
    public const FAVORI = 'FAVORI';
    public const TYPE = 'TYPE';

    public const UTILS = 'Utils';

    /**
     * @param string $entityClass
     * @return Manager|null
     */
    public static function getRepository(string $entityClass): ?Manager
    {
        switch ($entityClass) {
            case self::TYPE_CONTRAT:
                return new TypeContratManager();

            case self::OFFRE_EMPLOI:
                return new OffreEmploiManager();

            case self::EMPLOYEUR:
                return new EmployeurManager();

            case self::EMPLOYE:
                return new EmployeManager();

            case self::SECTEUR_ACTIVITE:
                return new SecteurActiviteManager();

            case self::AUTO_ENTREPRENEUR:
                return new AutoEntrepreneurManager();

            case self::PARTICULIER:
                return new ParticulierManager();

            case self::ANNONCE:
                return new AnnonceManager();

            case self::UTILS:
                return new UtilsManager();

            default:
                return null;
        }
    }

}
