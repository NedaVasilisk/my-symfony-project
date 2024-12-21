<?php

namespace App\Entity;

use App\Repository\RepairPartRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RepairPartRepository::class)]
#[ORM\Table(name: "repair_parts")]
class RepairPart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Part::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Part $part = null;

    #[ORM\ManyToOne(targetEntity: Repair::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Repair $repair = null;

    #[ORM\Column]
    private int $quantity = 1;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $priceAtTime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPart(): ?Part
    {
        return $this->part;
    }

    public function setPart(?Part $part): self
    {
        $this->part = $part;

        return $this;
    }

    public function getRepair(): ?Repair
    {
        return $this->repair;
    }

    public function setRepair(?Repair $repair): self
    {
        $this->repair = $repair;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getPriceAtTime(): ?string
    {
        return $this->priceAtTime;
    }

    public function setPriceAtTime(string $priceAtTime): self
    {
        $this->priceAtTime = $priceAtTime;
        return $this;
    }
}
