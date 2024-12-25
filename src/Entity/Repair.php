<?php

namespace App\Entity;

use App\Repository\RepairRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RepairRepository::class)]
#[ORM\Table(name: "repairs")]
class Repair
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['repair_list', 'repair_detail'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Vehicle::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Groups(['repair_detail'])]
    private ?Vehicle $vehicle = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['repair_detail'])]
    private ?\datetime $dateIn = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['repair_detail'])]
    private ?\datetime $dateOut = null;

    #[ORM\Column(length: 20)]
    #[Groups(['repair_detail'])]
    private ?string $status = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups(['repair_detail'])]
    private ?string $totalCost = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicle $vehicle): self
    {
        $this->vehicle = $vehicle;
        return $this;
    }

    public function getDateIn(): ?\datetime
    {
        return $this->dateIn;
    }

    public function setDateIn(\datetime $dateIn): self
    {
        $this->dateIn = $dateIn;
        return $this;
    }

    public function getDateOut(): ?\datetime
    {
        return $this->dateOut;
    }

    public function setDateOut(?\datetime $dateOut): self
    {
        $this->dateOut = $dateOut;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getTotalCost(): ?string
    {
        return $this->totalCost;
    }

    public function setTotalCost(string $totalCost): self
    {
        $this->totalCost = $totalCost;
        return $this;
    }
}
