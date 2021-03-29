<?php


namespace App\database;

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
    public const EST_DANS = 'EST_DANS';

    public const UTILS = 'Utils';
}
