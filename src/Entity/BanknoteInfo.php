<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BanknoteInfoRepository")
 * @ORM\Table(name="BanknoteInfo")
 */
class BanknoteInfo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $year;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $min_year;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $max_year;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mintage;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $type_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $issue_id;

    /** 
     * @ORM\Column(type="json", nullable=true) 
     */
    private $prices;

    // ================== Getters e Setters ==================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }
    public function setYear(int $year): self
    {
        $this->year = $year;
        return $this;
    }
    public function getMinYear(): ?int
    {
        return $this->min_year;
    }
    public function setMinYear(int $min_year): self
    {
        $this->min_year = $min_year;
        return $this;
    }
    public function getMaxYear(): ?int
    {
        return $this->max_year;
    }
    public function setMaxYear(int $max_year): self
    {
        $this->max_year = $max_year;
        return $this;
    }

    public function getMintage(): ?int
    {
        return $this->mintage;
    }

    public function setMintage(int $mintage): self
    {
        $this->mintage = $mintage;
        return $this;
    }
    public function getTypeId(): ?int
    {
        return $this->type_id;
    }
    public function setTypeId(int $type_id): self
    {
        $this->type_id = $type_id;
        return $this;
    }

    public function getIssueId(): ?int
    {
        return $this->issue_id;
    }
    public function setIssueId(int $issue_id): self
    {
        $this->issue_id = $issue_id;
        return $this;
    }

    public function getPrices(): ?array
    {
        return $this->prices;
    }

    public function setPrices(?array $prices): self
    {
        $this->prices = $prices;
        return $this;
    }
}
