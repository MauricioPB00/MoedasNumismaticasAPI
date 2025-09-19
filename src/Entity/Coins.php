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

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $url;

    /** @ORM\Column(type="string", length=100) */
    private $category;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $type;

    /** @ORM\Column(type="json", nullable=true) */
    private $issuer;

    /** @ORM\Column(type="integer", nullable=true) */
    private $minYear;

    /** @ORM\Column(type="integer", nullable=true) */
    private $maxYear;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $valueText;

    /** @ORM\Column(type="float", nullable=true) */
    private $valueNumeric;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $currencyName;

    /** @ORM\Column(type="json", nullable=true) */
    private $ruler;

    /** @ORM\Column(type="boolean", nullable=true) */
    private $isDemonetized;

    /** @ORM\Column(type="json", nullable=true) */
    private $composition;

    /** @ORM\Column(type="json", nullable=true) */
    private $technique;

    /** @ORM\Column(type="json", nullable=true) */
    private $obverse;

    /** @ORM\Column(type="json", nullable=true) */
    private $reverse;

    /** @ORM\Column(type="json", nullable=true) */
    private $edge;

    /** @ORM\Column(type="text", nullable=true) */
    private $comments;

    /** @ORM\Column(type="json", nullable=true) */
    private $relatedTypes;

    /** @ORM\Column(type="json", nullable=true) */
    private $tags;

    /** @ORM\Column(type="json", nullable=true) */
    private $referenceCode;

    /** @ORM\Column(type="decimal", precision=10, scale=2, nullable=true) */
    private $size;

    /** @ORM\Column(type="decimal", precision=10, scale=2, nullable=true) */
    private $thickness;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $shape;

    /** @ORM\Column(type="json", nullable=true) */
    private $coinGroup;

    /** @ORM\Column(type="json", nullable=true) */
    private $currency;

    /** @ORM\Column(type="date", nullable=true) */
    private $demonetizationDate;

    /** ✅ NOVO: Peso da moeda */
    /** @ORM\Column(type="decimal", precision=10, scale=2, nullable=true) */
    private $weight;

    /** ✅ NOVO: Orientação da moeda (coin, medal, etc.) */
    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $orientation;

    /** ✅ NOVO: Casas da moeda */
    /** @ORM\Column(type="json", nullable=true) */
    private $mints;

    /** 
     * @ORM\Column(type="string", length=255, nullable=true) 
     */
    private $obverse_img; // nome da imagem frente

    /** 
     * @ORM\Column(type="string", length=255, nullable=true) 
     */
    private $reverse_img; // nome da imagem verso

    /** 
     * @ORM\Column(type="string", length=255, nullable=true) 
     */
    private $edge_img; // nome da imagem frente




    // ========= GETTERS & SETTERS =========

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

    public function getUrl(): ?string
    {
        return $this->url;
    }
    public function setUrl(?string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getValueNumeric(): ?float
    {
        return $this->valueNumeric;
    }
    public function setValueNumeric(?float $valueNumeric): self
    {
        $this->valueNumeric = $valueNumeric;
        return $this;
    }

    public function getCurrencyName(): ?string
    {
        return $this->currencyName;
    }
    public function setCurrencyName(?string $currencyName): self
    {
        $this->currencyName = $currencyName;
        return $this;
    }

    public function getRuler(): ?array
    {
        return $this->ruler;
    }
    public function setRuler(?array $ruler): self
    {
        $this->ruler = $ruler;
        return $this;
    }

    public function getIsDemonetized(): ?bool
    {
        return $this->isDemonetized;
    }
    public function setIsDemonetized(?bool $isDemonetized): self
    {
        $this->isDemonetized = $isDemonetized;
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

    public function getIssuer(): ?array
    {
        return $this->issuer;
    }
    public function setIssuer(?array $issuer): self
    {
        $this->issuer = $issuer;
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

      public function getMinYear(): ?int
    {
        return $this->minYear;
    }
    public function setMinYear(?int $minYear): self
    {
        $this->minYear = $minYear;
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

    public function getCoinGroup(): ?array
    {
        return $this->coinGroup;
    }
    public function setCoinGroup(?array $coinGroup): self
    {
        $this->coinGroup = $coinGroup;
        return $this;
    }

    public function getCurrency(): ?array
    {
        return $this->currency;
    }
    public function setCurrency(?array $currency): self
    {
        $this->currency = $currency;
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

    public function getObverse(): ?array
    {
        return $this->obverse;
    }
    public function setObverse(?array $obverse): self
    {
        $this->obverse = $obverse;
        return $this;
    }

    public function getReverse(): ?array
    {
        return $this->reverse;
    }
    public function setReverse(?array $reverse): self
    {
        $this->reverse = $reverse;
        return $this;
    }

    public function getEdge(): ?array
    {
        return $this->edge;
    }
    public function setEdge(?array $edge): self
    {
        $this->edge = $edge;
        return $this;
    }

    /** ✅ Getters/Setters novos */
    public function getWeight(): ?float
    {
        return $this->weight;
    }
    public function setWeight(?float $weight): self
    {
        $this->weight = $weight;
        return $this;
    }

    public function getOrientation(): ?string
    {
        return $this->orientation;
    }
    public function setOrientation(?string $orientation): self
    {
        $this->orientation = $orientation;
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
    public function getType(): ?string
    {
        return $this->type;
    }
    public function setType(?string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getValueText(): ?string
    {
        return $this->valueText;
    }
    public function setValueText(?string $valueText): self
    {
        $this->valueText = $valueText;
        return $this;
    }

    public function getComposition(): ?array
    {
        return $this->composition;
    }
    public function setComposition(?array $composition): self
    {
        $this->composition = $composition;
        return $this;
    }

    public function getTechnique(): ?array
    {
        return $this->technique;
    }
    public function setTechnique(?array $technique): self
    {
        $this->technique = $technique;
        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }
    public function setComments(?string $comments): self
    {
        $this->comments = $comments;
        return $this;
    }

    public function getRelatedTypes(): ?array
    {
        return $this->relatedTypes;
    }
    public function setRelatedTypes(?array $relatedTypes): self
    {
        $this->relatedTypes = $relatedTypes;
        return $this;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }
    public function setTags(?array $tags): self
    {
        $this->tags = $tags;
        return $this;
    }

    public function getReferenceCode (): ?array
    {
        return $this->referenceCode;
    }
    public function setReferenceCode (?array $referenceCode): self
    {
        $this->referenceCode  = $referenceCode ;
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

    public function getObverseImg(): ?string
    {
        return $this->obverse_img;
    }
    public function setObverseImg(?string $obverse_img): self
    {
        $this->obverse_img = $obverse_img;
        return $this;
    }

    public function getReverseImg(): ?string
    {
        return $this->reverse_img;
    }
    public function setReverseImg(?string $reverse_img): self
    {
        $this->reverse_img = $reverse_img;
        return $this;
    }

    public function getEdgeImg(): ?string
    {
        return $this->edge_img;
    }
    public function setEdgeImg(?string $edge_img): self
    {
        $this->edge_img = $edge_img;
        return $this;
    }
}
