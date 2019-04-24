<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HeroRepository")
 */
class Hero
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $marvelId;

    public function __construct($marvelId)
    {
        $this->setMarvelId($marvelId);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMarvelId(): ?string
    {
        return $this->marvelId;
    }

    public function setMarvelId(string $marvelId): self
    {
        $this->marvelId = $marvelId;

        return $this;
    }
}
