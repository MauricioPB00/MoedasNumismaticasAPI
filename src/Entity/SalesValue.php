<?php

namespace App\Entity;

use App\Repository\SalesValueRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SalesValueRepository::class)
 * @ORM\Table(name="`salesValue`")
 */
class SalesValue
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $resale;

        /**
     * @ORM\Column(type="integer")
     */
    private $bought;

    /**
     * @ORM\Column(type="integer")
     */
    private $idClothes;

    /**
     * @ORM\Column(type="integer")
     */
    private $idSales;
   
    /**
     * @ORM\Column(type="date")
     */
    private $daysale;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getresale(): ?int
    {
        return $this->resale;
    }

    public function setresale(int $resale): self
    {
        $this->resale = $resale;

        return $this;
    }

    public function getbought(): ?int
    {
        return $this->bought;
    }

    public function setbought(int $bought): self
    {
        $this->bought = $bought;

        return $this;
    }

    public function geidClothes(): ?int
    {
        return $this->idClothes;
    }

    public function setidClothes(int $idClothes): self
    {
        $this->idClothes = $idClothes;

        return $this;
    }

    public function geidSales(): ?int
    {
        return $this->idSales;
    }

    public function setidSales(int $idSales): self
    {
        $this->idSales = $idSales;

        return $this;
    }
    
    public function getDaysale(): ?\DateTimeInterface
    {
        return $this->daysale;
    }

    public function setDaysale(\DateTimeInterface $daysale): self
    {
        $this->daysale = $daysale;

        return $this;
    }

   

}
