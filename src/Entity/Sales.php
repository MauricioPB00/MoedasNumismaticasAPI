<?php

namespace App\Entity;

use App\Repository\SalesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SalesRepository::class)
 * @ORM\Table(name="`sales`")
 */
class Sales
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
    private $card;

    /**
     * @ORM\Column(type="integer")
     */
    private $flag;

    /**
     * @ORM\Column(type="integer")
     */
    private $discount;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $totalWithDiscount;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $combinedTotalText;  

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

    public function getcard(): ?int
    {
        return $this->card;
    }

    public function setcard(int $card): self
    {
        $this->card = $card;

        return $this;
    }

    public function getflag(): ?int
    {
        return $this->flag;
    }

    public function setflag(int $flag): self
    {
        $this->flag = $flag;

        return $this;
    }

    public function getdiscount(): ?int
    {
        return $this->discount;
    }

    public function setdiscount(int $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    public function gettotalWithDiscount(): ?string
    {
        return $this->totalWithDiscount;
    }

    public function settotalWithDiscount(string $totalWithDiscount): self
    {
        $this->totalWithDiscount = $totalWithDiscount;

        return $this;
    }

    public function getcombinedTotalText(): ?string
    {
        return $this->combinedTotalText;
    }

    public function setcombinedTotalText(string $combinedTotalText): self
    {
        $this->combinedTotalText = $combinedTotalText;

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

    public function gettype(): ?string
    {
        return $this->type;
    }

    public function settype(string $type): self
    {
        $this->type = $type;

        return $this;
    }

}
