<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Album;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface
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
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $permi;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cpf;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rg;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $datNasc;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datCad;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $photo = null;

    /**
     * @ORM\OneToOne(targetEntity=Album::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $album;

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

    public function getUserIdentifier(): string
    {
        return (string) $this->email;  // email é o identificador do login
    }

    public function getUsername(): string
    {
        return $this->getUserIdentifier(); // compatibilidade com interfaces antigas
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
    /**
     * @return array|string[]
     */
    public function getRoles(): array
    {
        return array('ROLE_USER');
    }

    public function eraseCredentials() {}

    public function getPermi(): ?string
    {
        return $this->permi;
    }

    public function setPermi(?string $permi): self
    {
        $this->permi = $permi;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCpf(): ?string
    {
        return $this->cpf;
    }

    public function setCpf(?string $cpf): self
    {
        $this->cpf = $cpf;

        return $this;
    }

    public function getRg(): ?string
    {
        return $this->rg;
    }

    public function setRg(?string $rg): self
    {
        $this->rg = $rg;

        return $this;
    }

    public function getDatNasc(): ?string
    {
        return $this->datNasc;
    }

    public function setDatNasc(?string $datNasc): self
    {
        $this->datNasc = $datNasc;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getDatCad(): ?\DateTimeInterface
    {
        return $this->datCad;
    }

    public function setDatCad(\DateTimeInterface $datCad): self
    {
        $this->datCad = $datCad;
        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getAlbum(): ?Album
    {
        return $this->album;
    }

    public function setAlbum(?Album $album): self
    {
        $this->album = $album;
        if ($album && $album->getUser() !== $this) {
            $album->setUser($this);
        }
        return $this;
    }
}
