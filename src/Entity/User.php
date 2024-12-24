<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "users")]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user_list', 'user_detail'])]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Groups(['user_detail'])]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user_detail'])]
    private ?string $passwordHash = null;

    #[ORM\Column(length: 50)]
    #[Groups(['user_detail'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 50)]
    #[Groups(['user_detail'])]
    private ?string $lastName = null;

    #[ORM\Column(length: 100, unique: true, nullable: true)]
    #[Groups(['user_detail'])]
    private ?string $email = null;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['user_detail'])]
    private ?Role $role = null;

    #[ORM\Column(options: ['default' => true])]
    #[Groups(['user_detail'])]
    private bool $isActive = true;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['user_detail'])]
    private ?\datetime $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): self
    {
        $this->passwordHash = $passwordHash;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\datetime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
