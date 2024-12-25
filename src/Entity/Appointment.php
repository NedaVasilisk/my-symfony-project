<?php

namespace App\Entity;

use App\Repository\AppointmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AppointmentRepository::class)]
#[ORM\Table(name: "appointments")]
class Appointment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['appointment_list', 'appointment_detail'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Groups(['appointment_detail'])]
    private ?Customer $customer = null;

    #[ORM\ManyToOne(targetEntity: Vehicle::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Groups(['appointment_detail'])]
    private ?Vehicle $vehicle = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['appointment_detail'])]
    private ?\datetime $scheduledDate = null;

    #[ORM\Column(length: 20)]
    #[Groups(['appointment_detail'])]
    private ?string $status = null;

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

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicle $vehicle): self
    {
        $this->vehicle = $vehicle;
        return $this;
    }

    public function getScheduledDate(): ?\datetime
    {
        return $this->scheduledDate;
    }

    public function setScheduledDate(\datetime $scheduledDate): self
    {
        $this->scheduledDate = $scheduledDate;
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
}
