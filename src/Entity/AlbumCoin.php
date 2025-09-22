<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="album_coin")
 */
class AlbumCoin
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Many AlbumCoin belong to one Album
     * @ORM\ManyToOne(targetEntity=Album::class, inversedBy="albumCoins")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $album;

    /**
     * Many AlbumCoin belong to one Coin
     * @ORM\ManyToOne(targetEntity=Coin::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $coin;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $year;

    /**
     * Coin condition (ex: FC, S, MBC, BC, R, UTG)
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $condition;

    /**
     * Quantity of the same coin in the album
     * @ORM\Column(type="integer", nullable=false, options={"default":1})
     */
    private $quantity = 1;

    public function getId(): ?int { return $this->id; }
    public function getAlbum(): ?Album { return $this->album; }
    public function setAlbum(?Album $album): self { $this->album = $album; return $this; }
    public function getCoin(): ?Coin { return $this->coin; }
    public function setCoin(?Coin $coin): self { $this->coin = $coin; return $this; }
    public function getYear(): ?int { return $this->year; }
    public function setYear(?int $year): self { $this->year = $year; return $this; }
    public function getCondition(): ?string { return $this->condition; }
    public function setCondition(?string $condition): self { $this->condition = $condition; return $this; }
    public function getQuantity(): int { return $this->quantity; }
    public function setQuantity(int $quantity): self { $this->quantity = $quantity; return $this; }
}
