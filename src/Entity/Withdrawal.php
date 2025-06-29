<?php

namespace App\Entity;

use App\Repository\WithdrawalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "withdrawals")]
#[ORM\Entity(repositoryClass: WithdrawalRepository::class)]
class Withdrawal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $amount = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne(inversedBy: 'withdrawals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $madeBy = null;

    #[ORM\ManyToOne(inversedBy: 'withdrawals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CashRegisterSession $cashRegisterSession = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getMadeBy(): ?User
    {
        return $this->madeBy;
    }

    public function setMadeBy(?User $madeBy): static
    {
        $this->madeBy = $madeBy;

        return $this;
    }

    public function getCashRegisterSession(): ?CashRegisterSession
    {
        return $this->cashRegisterSession;
    }

    public function setCashRegisterSession(?CashRegisterSession $cashRegisterSession): static
    {
        $this->cashRegisterSession = $cashRegisterSession;

        return $this;
    }
}
