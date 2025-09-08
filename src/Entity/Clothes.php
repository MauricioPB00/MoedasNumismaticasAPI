<?php

namespace App\Entity;

use App\Repository\ClothesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClothesRepository::class)
 * @ORM\Table(name="`clothes`")
 */
class Clothes
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $size;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $resale;              //revenda

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $bought;              //comprado

    /**
     * @ORM\Column(type="integer")
     */
    private $suppliers;      

    /**
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getname(): ?string
    {
        return $this->name;
    }

    public function setname(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getsize(): ?string
    {
        return $this->size;
    }

    public function setsize(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getresale(): ?string
    {
        return $this->resale;
    }

    public function setresale(string $resale): self
    {
        $this->resale = $resale;

        return $this;
    }

    public function getbought(): ?string
    {
        return $this->bought;
    }

    public function setbought(string $bought): self
    {
        $this->bought = $bought;

        return $this;
    }

    public function getsuppliers(): ?string
    {
        return $this->suppliers;
    }

    public function setsuppliers(string $suppliers): self
    {
        $this->suppliers = $suppliers;

        return $this;
    }
}
