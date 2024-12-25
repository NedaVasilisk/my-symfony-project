<?php

namespace App\Entity;

use App\Repository\VehicleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VehicleRepository::class)]
#[ORM\Table(name: "vehicles")]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(length: 17, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 17, max: 17)]
    #[Groups(['vehicle_list', 'vehicle_detail', 'vehicle_max'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Groups([ 'vehicle_detail', 'vehicle_max'])]
    private ?Customer $customer = null;

    #[ORM\Column(length: 17, unique: true)]
    #[Groups(['vehicle_detail', 'vehicle_max'])]
    private ?string $vin = null;

    #[ORM\Column(length: 10, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 10)]
    #[Groups(['vehicle_detail', 'vehicle_max'])]
    private ?string $licensePlate = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    #[Groups(['vehicle_detail', 'vehicle_max'])]
    private ?string $make = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    #[Groups(['vehicle_detail', 'vehicle_max'])]
    private ?string $model = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Range(min: 1886, max: 2100)]
    #[Groups(['vehicle_detail', 'vehicle_max'])]
    private ?int $year = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    #[Groups(['vehicle_detail', 'vehicle_max'])]
    private ?string $engineType = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    #[Assert\PositiveOrZero]
    #[Groups(['vehicle_detail', 'vehicle_max'])]
    private ?string $batteryCapacity = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\Type(\DateTime::class)]
    #[Groups(['vehicle_detail', 'vehicle_max'])]
    private ?\datetime $lastIotUpdate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;
        return $this;
    }

    public function getVin(): ?string
    {
        return $this->vin;
    }

    public function setVin(string $vin): self
    {
        $this->vin = $vin;
        return $this;
    }

    public function getLicensePlate(): ?string
    {
        return $this->licensePlate;
    }

    public function setLicensePlate(string $licensePlate): self
    {
        $this->licensePlate = $licensePlate;
        return $this;
    }

    public function getMake(): ?string
    {
        return $this->make;
    }

    public function setMake(string $make): self
    {
        $this->make = $make;
        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;
        return $this;
    }

    public function getEngineType(): ?string
    {
        return $this->engineType;
    }

    public function setEngineType(string $engineType): self
    {
        $this->engineType = $engineType;
        return $this;
    }

    public function getBatteryCapacity(): ?string
    {
        return $this->batteryCapacity;
    }

    public function setBatteryCapacity(?string $batteryCapacity): self
    {
        $this->batteryCapacity = $batteryCapacity;
        return $this;
    }

    public function getLastIotUpdate(): ?\datetime
    {
        return $this->lastIotUpdate;
    }

    public function setLastIotUpdate(?\datetime $lastIotUpdate): self
    {
        $this->lastIotUpdate = $lastIotUpdate;
        return $this;
    }
}
