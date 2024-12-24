<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
#[ORM\Table(name: "invoices")]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['invoices_list', 'invoices_detail'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Repair::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Groups(['invoices_detail'])]
    private ?Repair $repair = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['invoices_detail'])]
    private ?\Datetime $dateIssued = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups(['invoices_detail'])]
    private ?string $totalAmount = null;

    #[ORM\Column]
    #[Groups(['invoices_detail'])]
    private bool $isPaid = false;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateIssued(): ?\datetime
    {
        return $this->dateIssued;
    }

    public function setDateIssued(\datetime $dateIssued): self
    {
        $this->dateIssued = $dateIssued;
        return $this;
    }

    public function getTotalAmount(): ?string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(string $totalAmount): self
    {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    public function isPaid(): bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): self
    {
        $this->isPaid = $isPaid;
        return $this;
    }
}
