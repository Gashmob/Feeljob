<?php


namespace App\database\entity;


use App\database\PreparedQuery;

class CV
{
    /**
     * @var int
     */
    private int $id;
    /**
     * @var string
     */
    private string $nom;
    /**
     * @var string
     */
    private string $photo;
    /**
     * @var bool
     */
    private bool $permis;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return CV
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getNom(): string
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     * @return CV
     */
    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhoto(): string
    {
        return $this->photo;
    }

    /**
     * @param string $photo
     * @return CV
     */
    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPermis(): bool
    {
        return $this->permis;
    }

    /**
     * @param bool $permis
     * @return CV
     */
    public function setPermis(bool $permis): self
    {
        $this->permis = $permis;

        return $this;
    }

    public function flush()
    {
        $result = (new PreparedQuery('CREATE (c:CV {nom:$nom, photo:$photo, permis:$permis}) RETURN id(c) AS id'))
            ->setString('nom', $this->nom)
            ->setString('photo', $this->photo)
            ->setBoolean('permis', $this->permis)
            ->run()
            ->getOneOrNullResult();

        $this->id = $result['id'][0];
    }
}