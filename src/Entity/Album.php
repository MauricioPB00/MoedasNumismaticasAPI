<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\AlbumRepository;

/**
 * @ORM\Entity(repositoryClass=AlbumRepository::class)
 */
class Album
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="album")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=AlbumCoin::class, mappedBy="album", cascade={"persist", "remove"})
     */
    private $albumCoins;

    public function __construct()
    {
        $this->albumCoins = new ArrayCollection();
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

    /**
     * @return Collection<int, AlbumCoin>
     */
    public function getAlbumCoins(): Collection
    {
        return $this->albumCoins;
    }

    public function addAlbumCoin(AlbumCoin $albumCoin): self
    {
        if (!$this->albumCoins->contains($albumCoin)) {
            $this->albumCoins[] = $albumCoin;
            $albumCoin->setAlbum($this);
        }
        return $this;
    }

    public function removeAlbumCoin(AlbumCoin $albumCoin): self
    {
        if ($this->albumCoins->removeElement($albumCoin)) {
            if ($albumCoin->getAlbum() === $this) {
                $albumCoin->setAlbum(null);
            }
        }
        return $this;
    }
}
