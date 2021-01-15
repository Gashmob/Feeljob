<?php


namespace App\database\entity;


use App\database\PreparedQuery;

/**
 * Class GenericUser
 * @package App\database\entity
 */
class GenericUser
{
    /**
     * @var int
     */
    private int $id;
    /**
     * @var string
     */
    private string $email;
    /**
     * @var bool
     */
    private bool $verifie;
    /**
     * @var string
     */
    private string $motdepasse;
    /**
     * @var string
     */
    private string $sel;

    /**
     * GenericUser constructor.
     * @param string $email
     * @param bool $verifie
     * @param string $motdepasse
     * @param string $sel
     * @param int $id
     */
    public function __construct(string $email, bool $verifie, string $motdepasse, string $sel, int $id)
    {
        $this->email = $email;
        $this->verifie = $verifie;
        $this->motdepasse = $motdepasse;
        $this->sel = $sel;
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return bool
     */
    public function isVerifie(): bool
    {
        return $this->verifie;
    }

    /**
     * @param bool $verifie
     */
    public function setVerifie(bool $verifie): void
    {
        $this->verifie = $verifie;
    }

    /**
     * @return string
     */
    public function getMotdepasse(): string
    {
        return $this->motdepasse;
    }

    /**
     * @param string $motdepasse
     */
    public function setMotdepasse(string $motdepasse): void
    {
        $this->motdepasse = $motdepasse;
    }

    /**
     * @return string
     */
    public function getSel(): string
    {
        return $this->sel;
    }

    /**
     * @param string $sel
     */
    public function setSel(string $sel): void
    {
        $this->sel = $sel;
    }

    public function flush()
    {
        (new PreparedQuery('MATCH (u) WHERE u.id=$id SET u.motdepasse=$motdepasse, u.sel=$sel, u.email=$email, u.verifie=$verifie'))
            ->setInteger('id', $this->id)
            ->setString('motdepasse', $this->motdepasse)
            ->setString('sel', $this->sel)
            ->setString('email', $this->email)
            ->setBoolean('verifie', $this->verifie)
            ->run();
    }
}