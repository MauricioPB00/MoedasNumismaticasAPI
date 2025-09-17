<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="coins")
 */
class Coins
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /** @ORM\Column(type="string", length=255) */
    private $title;

    /** @ORM\Column(type="string", length=100) */
    private $category;

    /** @ORM\Column(type="string", length=255) */
    private $issuer;

    /** @ORM\Column(type="integer", nullable=true) */
    private $minYear;

    /** @ORM\Column(type="integer", nullable=true) */
    private $maxYear;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $valueFullName;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $rulerName;

    /** @ORM\Column(type="string", length=100, nullable=true) */
    private $technique;

    /** @ORM\Column(type="text", nullable=true) */
    private $obverseDescription;

    /** @ORM\Column(type="text", nullable=true) */
    private $reverseDescription;

    /** @ORM\Column(type="json", nullable=true) */
    private $mints;

    /** @ORM\Column(type="decimal", precision=10, scale=2, nullable=true) */
    private $weight;

    /** @ORM\Column(type="decimal", precision=10, scale=2, nullable=true) */
    private $size;

    /** @ORM\Column(type="decimal", precision=10, scale=2, nullable=true) */
    private $thickness;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $shape;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $compositionText;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $edge;

    /** @ORM\Column(type="date", nullable=true) */
    private $demonetizationDate;

    /** 
     * @ORM\Column(type="string", length=255, nullable=true) 
     */
    private $obverse; // nome da imagem frente

    /** 
     * @ORM\Column(type="string", length=255, nullable=true) 
     */
    private $reverse; // nome da imagem verso

    public function getId(): int
    {
        return $this->id;
    }
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }
    public function setCategory(string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getIssuer(): string
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

    public function getValueFullName(): ?string
    {
        return $this->valueFullName;
    }
    public function setValueFullName(?string $valueFullName): self
    {
        $this->valueFullName = $valueFullName;
        return $this;
    }

    public function getRulerName(): ?string
    {
        return $this->rulerName;
    }
    public function setRulerName(?string $rulerName): self
    {
        $this->rulerName = $rulerName;
        return $this;
    }

    public function getTechnique(): ?string
    {
        return $this->technique;
    }
    public function setTechnique(?string $technique): self
    {
        $this->technique = $technique;
        return $this;
    }

    public function getObverseDescription(): ?string
    {
        return $this->obverseDescription;
    }
    public function setObverseDescription(?string $obverseDescription): self
    {
        $this->obverseDescription = $obverseDescription;
        return $this;
    }

    public function getReverseDescription(): ?string
    {
        return $this->reverseDescription;
    }
    public function setReverseDescription(?string $reverseDescription): self
    {
        $this->reverseDescription = $reverseDescription;
        return $this;
    }

    public function getMints(): ?array
    {
        return $this->mints;
    }
    public function setMints(?array $mints): self
    {
        $this->mints = $mints;
        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }
    public function setWeight(?float $weight): self
    {
        $this->weight = $weight;
        return $this;
    }

    public function getSize(): ?float
    {
        return $this->size;
    }
    public function setSize(?float $size): self
    {
        $this->size = $size;
        return $this;
    }

    public function getThickness(): ?float
    {
        return $this->thickness;
    }
    public function setThickness(?float $thickness): self
    {
        $this->thickness = $thickness;
        return $this;
    }

    public function getShape(): ?string
    {
        return $this->shape;
    }
    public function setShape(?string $shape): self
    {
        $this->shape = $shape;
        return $this;
    }

    public function getCompositionText(): ?string
    {
        return $this->compositionText;
    }
    public function setCompositionText(?string $compositionText): self
    {
        $this->compositionText = $compositionText;
        return $this;
    }

    public function getEdge(): ?string
    {
        return $this->edge;
    }
    public function setEdge(?string $edge): self
    {
        $this->edge = $edge;
        return $this;
    }

    public function getDemonetizationDate(): ?\DateTimeInterface
    {
        return $this->demonetizationDate;
    }
    public function setDemonetizationDate(?\DateTimeInterface $demonetizationDate): self
    {
        $this->demonetizationDate = $demonetizationDate;
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
