<?php

namespace App\Entity;

use App\Repository\SuppliersRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SuppliersRepository::class)
 * @ORM\Table(name="`suppliers`")
 */
class Suppliers
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
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phone;



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

    public function getcity(): ?string
    {
        return $this->city;
    }

    public function setcity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getphone(): ?string
    {
        return $this->phone;
    }

    public function setphone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

}
