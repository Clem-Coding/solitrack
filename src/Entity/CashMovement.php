<?php

namespace App\Entity;

use App\Repository\CashMovementRepository;
use Doctrine\DBAL\Types\Types;
use App\Enum\CashMovementAction;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: "cash_movements")]
#[ORM\Entity(repositoryClass: CashMovementRepository::class)]
class CashMovement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $createdAt = null;

    #[Assert\NotBlank()]
    #[Assert\GreaterThan(value: 0)]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $amount = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne(inversedBy: 'cashMovements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $madeBy = null;

    #[ORM\ManyToOne(inversedBy: 'cashMovements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CashRegisterSession $cashRegisterSession = null;

    #[ORM\Column(length: 10)]
    private ?CashMovementAction $type = null;

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

    public function getType(): ?CashMovementAction
    {
        return $this->type;
    }

    public function setType(CashMovementAction $type): static
    {
        $this->type = $type;

        return $this;
    }
}
