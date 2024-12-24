<?php

namespace App\Entity;

use App\Repository\PartRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PartRepository::class)]
#[ORM\Table(name: "parts")]
class Part
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['part_list', 'part_detail'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['part_detail'])]
    private ?string $name = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['part_detail'])]
    private ?string $manufacturer = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Groups(['part_detail'])]
    private ?string $partNumber = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups(['part_detail'])]
    private ?string $currentPrice = null;

    #[ORM\Column]
    #[Groups(['part_detail'])]
    private int $quantityInStock = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    public function setManufacturer(?string $manufacturer): self
    {
        $this->manufacturer = $manufacturer;
        return $this;
    }

    public function getPartNumber(): ?string
    {
        return $this->partNumber;
    }

    public function setPartNumber(string $partNumber): self
    {
        $this->partNumber = $partNumber;
        return $this;
    }

    public function getCurrentPrice(): ?string
    {
        return $this->currentPrice;
    }

    public function setCurrentPrice(string $currentPrice): self
    {
        $this->currentPrice = $currentPrice;
        return $this;
    }

    public function getQuantityInStock(): int
    {
        return $this->quantityInStock;
    }

    public function setQuantityInStock(int $quantityInStock): self
    {
        $this->quantityInStock = $quantityInStock;
        return $this;
    }
}
