<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CoinRepository")
 * @ORM\Table(name="coin")
 */
class Coin
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /** 
     * @ORM\Column(type="string", length=255) 
     */
    private $title;

    /** 
     * @ORM\Column(type="string", length=255) 
     */
    private $category;

    /** 
     * @ORM\Column(type="string", length=255) 
     */
    private $issuer;

    /** 
     * @ORM\Column(type="integer", nullable=true) 
     */
    private $minYear;

    /** 
     * @ORM\Column(type="integer", nullable=true) 
     */
    private $maxYear;

    /** 
     * @ORM\Column(type="string", length=255, nullable=true) 
     */
    private $obverse; // nome da imagem frente

    /** 
     * @ORM\Column(type="string", length=255, nullable=true) 
     */
    private $reverse; // nome da imagem verso

    // ================== Getters e Setters ==================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getIssuer(): ?string
    {
        return $this->issuer;
    }

    public function setIssuer(string $issuer): self
    {
        $this->issuer = $issuer;
        return $this;
    }

    public function getMinYear(): ?int
    {
        return $this->minYear;
    }

    public function setMinYear(?int $minYear): self
    {
        $this->minYear = $minYear;
        return $this;
    }

    public function getMaxYear(): ?int
    {
        return $this->maxYear;
    }

    public function setMaxYear(?int $maxYear): self
    {
        $this->maxYear = $maxYear;
        return $this;
    }

    public function getObverse(): ?string
    {
        return $this->obverse;
    }

    public function setObverse(?string $obverse): self
    {
        $this->obverse = $obverse;
        return $this;
    }

    public function getReverse(): ?string
    {
        return $this->reverse;
    }

    public function setReverse(?string $reverse): self
    {
        $this->reverse = $reverse;
        return $this;
    }
}
