<?php


namespace App\database\manager;


use App\database\EntityManager;
use App\database\PreparedQuery;
use App\Entity\AutoEntrepreneur;
use App\Entity\Employe;
use App\Entity\Employeur;
use App\Entity\Particulier;
use Doctrine\ORM\EntityManagerInterface;

class UtilsManager extends Manager
{

    /**
     * @inheritDoc
     */
    public function find(int $id): ?string
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function findOneBy(array $filters): ?string
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function findBy(array $filters): array
    {
        return [];
    }

    /**
     * @param int $id
     * @return string
     */
    public function getUserTypeFromId(int $id): string
    {
        $result = (new PreparedQuery('MATCH (u) WHERE id(u)=$id RETURN LABELS(u) as label'))
            ->setInteger('id', $id)
            ->run()
            ->getOneOrNullResult();

        return $result['label'][0];
    }

    /**
     * @param EntityManagerInterface $em
     * @param string $mail
     * @return string|null
     */
    public function getUserTypeFromMail(EntityManagerInterface $em, string $mail): ?string
    {
        if ($em->getRepository(Employe::class)->findOneBy(['email' => $mail]) != null)
            return EntityManager::EMPLOYE;
        if ($em->getRepository(Employeur::class)->findOneBy(['email' => $mail]) != null)
            return EntityManager::EMPLOYEUR;
        if ($em->getRepository(Particulier::class)->findOneBy(['email' => $mail]) != null)
            return EntityManager::PARTICULIER;
        if ($em->getRepository(AutoEntrepreneur::class)->findOneBy(['email' => $mail]) != null)
            return EntityManager::AUTO_ENTREPRENEUR;

        return null;
    }

    /**
     * @param EntityManagerInterface $em
     * @param string $mail
     * @return bool
     */
    public function isMailNotUsed(EntityManagerInterface $em, string $mail): bool
    {
        if ($em->getRepository(Employe::class)->findOneBy(['email' => $mail]) != null)
            return false;
        if ($em->getRepository(Employeur::class)->findOneBy(['email' => $mail]) != null)
            return false;
        if ($em->getRepository(Particulier::class)->findOneBy(['email' => $mail]) != null)
            return false;
        if ($em->getRepository(AutoEntrepreneur::class)->findOneBy(['email' => $mail]) != null)
            return false;

        return true;
    }

    /**
     * @param EntityManagerInterface $em
     * @param int $id
     * @return AutoEntrepreneur|Employe|Employeur|Particulier|null
     */
    public function getUserFromId(EntityManagerInterface $em, int $id)
    {
        $type = $this->getUserTypeFromId($id);
        switch ($type) {
            case EntityManager::AUTO_ENTREPRENEUR:
                return $em->getRepository(AutoEntrepreneur::class)->findOneBy(['identity' => $id]);

            case EntityManager::EMPLOYE:
                return $em->getRepository(Employe::class)->findOneBy(['identity' => $id]);

            case EntityManager::EMPLOYEUR:
                return $em->getRepository(Employeur::class)->findOneBy(['identity' => $id]);

            case EntityManager::PARTICULIER:
                return $em->getRepository(Particulier::class)->findOneBy(['identity' => $id]);

            default:
                return null;
        }
    }

    /**
     * @param EntityManagerInterface $em
     * @param string $mail
     * @return AutoEntrepreneur|Employe|Employeur|Particulier|null
     */
    public function getUserFromMail(EntityManagerInterface $em, string $mail)
    {
        $type = $this->getUserTypeFromMail($em, $mail);
        switch ($type) {
            case EntityManager::AUTO_ENTREPRENEUR:
                return $em->getRepository(AutoEntrepreneur::class)->findOneBy(['email' => $mail]);

            case EntityManager::EMPLOYE:
                return $em->getRepository(Employe::class)->findOneBy(['email' => $mail]);

            case EntityManager::EMPLOYEUR:
                return $em->getRepository(Employeur::class)->findOneBy(['email' => $mail]);

            case EntityManager::PARTICULIER:
                return $em->getRepository(Particulier::class)->findOneBy(['email' => $mail]);

            default:
                return null;
        }
    }
}