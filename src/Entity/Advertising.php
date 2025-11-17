<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AdvertisingRepository;

/**
 * @ORM\Entity(repositoryClass=AdvertisingRepository::class)
 */
class Advertising
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;


    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * URL que o banner aponta
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * Caminho da imagem
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $advertisingImg;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
    */
    private $approved;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getAdvertisingImg(): ?string
    {
        return $this->advertisingImg;
    }

    public function setAdvertisingImg(?string $advertisingImg): self
    {
        $this->advertisingImg = $advertisingImg;
        return $this;
    }

    public function getApproved(): ?float
    {
        return $this->approved;
    }

    public function setApproved(?float $approved): self
    {
        $this->approved = $approved;
        return $this;
    }
}
