<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ORM\Table(name: "notifications")]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['notifications_list', 'notifications_detail'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Groups(['notifications_detail'])]
    private ?User $user = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['notifications_detail'])]
    private ?string $message = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['notifications_detail'])]
    private ?\datetime $sentAt = null;

    #[ORM\Column]
    #[Groups(['notifications_detail'])]
    private bool $isRead = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getSentAt(): ?\datetime
    {
        return $this->sentAt;
    }

    public function setSentAt(\datetime $sentAt): self
    {
        $this->sentAt = $sentAt;
        return $this;
    }

    public function isRead(): bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;
        return $this;
    }
}
