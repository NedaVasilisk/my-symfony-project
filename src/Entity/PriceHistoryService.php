<?php

namespace App\Entity;

use App\Repository\PriceHistoryServiceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PriceHistoryServiceRepository::class)]
#[ORM\Table(name: "price_history_services")]
#[ORM\UniqueConstraint(name: "unique_service_date", columns: ["service_id", "effective_date"])]
class PriceHistoryService
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['price_history_service_list', 'price_history_service_detail'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Service::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Groups(['price_history_service_detail'])]
    private ?Service $service = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['price_history_service_detail'])]
    private ?\DateTime $effectiveDate = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups(['price_history_service_detail'])]
    private ?string $price = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;
        return $this;
    }

    public function getEffectiveDate(): ?\DateTime
    {
        return $this->effectiveDate;
    }

    public function setEffectiveDate(\Datetime $effectiveDate): self
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
