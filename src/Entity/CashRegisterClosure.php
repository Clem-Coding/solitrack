<?php

namespace App\Entity;

use App\Repository\CashRegisterClosureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: "cash_register_closures")]
#[ORM\Entity(repositoryClass: CashRegisterClosureRepository::class)]
class CashRegisterClosure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $closedAt = null;

    #[ORM\Column]
    // #[Assert\NotBlank()]
    private ?float $closingCashAmount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank()]
    private ?string $discrepancy = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $note = null;

    #[ORM\ManyToOne(inversedBy: 'cashRegisterClosures')]
    #[ORM\JoinColumn(onDelete: 'SET NULL', nullable: true)]
    private ?User $closedBy = null;

    #[ORM\ManyToOne(inversedBy: 'cashRegisterClosures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CashRegisterSession $cashRegisterSession = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClosedAt(): ?\DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function setClosedAt(\DateTimeImmutable $closedAt): static
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    public function getClosingCashAmount(): ?float
    {
        return $this->closingCashAmount;
    }

    public function setClosingCashAmount(float $closingCashAmount): static
    {
        $this->closingCashAmount = $closingCashAmount;

        return $this;
    }

    public function getDiscrepancy(): ?string
    {
        return $this->discrepancy;
    }

    public function setDiscrepancy(string $discrepancy): static
    {
        $this->discrepancy = $discrepancy;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getClosedBy(): ?User
    {
        return $this->closedBy;
    }

    public function setClosedBy(?User $user): static
    {
        $this->closedBy = $user;

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
