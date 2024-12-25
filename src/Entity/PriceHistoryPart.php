<?php

namespace App\Entity;

use App\Repository\PriceHistoryPartRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PriceHistoryPartRepository::class)]
#[ORM\Table(name: "price_history_parts")]
class PriceHistoryPart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['price_history_service_list'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Part::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Groups(['price_history_part_list'])]
    private ?Part $part = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['price_history_part_list'])]
    private ?\datetime $effectiveDate = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups(['price_history_part_list'])]
    private ?string $price = null;

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

    public function getEffectiveDate(): ?\datetime
    {
        return $this->effectiveDate;
    }

    public function setEffectiveDate(\datetime $effectiveDate): self
    {
        $this->effectiveDate = $effectiveDate;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;
        return $this;
    }
}
