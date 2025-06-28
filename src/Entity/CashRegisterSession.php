<?php

namespace App\Entity;

use App\Repository\CashRegisterSessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "cash_register_sessions")]
#[ORM\Entity(repositoryClass: CashRegisterSessionRepository::class)]
class CashRegisterSession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $openingAt = null;

    #[ORM\Column]
    private ?float $cashFloat = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $cashWithdrawal = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $withdrawalComment = null;

    #[ORM\ManyToOne(inversedBy: 'cashRegisterSessions')]
    #[ORM\JoinColumn(onDelete: 'SET NULL', nullable: true)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $openedBy = null;

    /**
     * @var Collection<int, CashRegisterClosure>
     */
    #[ORM\OneToMany(targetEntity: CashRegisterClosure::class, mappedBy: 'cashRegisterSession')]
    private Collection $cashRegisterClosures;

    /**
     * @var Collection<int, Sale>
     */
    #[ORM\OneToMany(targetEntity: Sale::class, mappedBy: 'CashRegisterSession')]
    private Collection $sales;

    public function __construct()
    {
        $this->cashRegisterClosures = new ArrayCollection();
        $this->sales = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOpeningAt(): ?\DateTimeImmutable
    {
        return $this->openingAt;
    }

    public function setOpeningAt(\DateTimeImmutable $openingAt): static
    {
        $this->openingAt = $openingAt;

        return $this;
    }

    public function getCashFloat(): ?float
    {
        return $this->cashFloat;
    }

    public function setCashFloat(float $cashFloat): static
    {
        $this->cashFloat = $cashFloat;

        return $this;
    }

    public function getCashWithdrawal(): ?float
    {
        return $this->cashWithdrawal;
    }

    public function setCashWithdrawal(float $cashWithdrawal): static
    {
        $this->cashWithdrawal = $cashWithdrawal;

        return $this;
    }

    public function getWithdrawalComment(): ?string
    {
        return $this->withdrawalComment;
    }

    public function setWithdrawalComment(?string $withdrawalComment): static
    {
        $this->withdrawalComment = $withdrawalComment;

        return $this;
    }

    public function getOpenedBy(): ?User
    {
        return $this->openedBy;
    }

    public function setOpenedBy(?User $user): static
    {
        $this->openedBy = $user;

        return $this;
    }

    /**
     * @return Collection<int, CashRegisterClosure>
     */
    public function getCashRegisterClosures(): Collection
    {
        return $this->cashRegisterClosures;
    }

    public function addCashRegisterClosure(CashRegisterClosure $cashRegisterClosure): static
    {
        if (!$this->cashRegisterClosures->contains($cashRegisterClosure)) {
            $this->cashRegisterClosures->add($cashRegisterClosure);
            $cashRegisterClosure->setCashRegisterSession($this);
        }

        return $this;
    }

    public function removeCashRegisterClosure(CashRegisterClosure $cashRegisterClosure): static
    {
        if ($this->cashRegisterClosures->removeElement($cashRegisterClosure)) {
            // set the owning side to null (unless already changed)
            if ($cashRegisterClosure->getCashRegisterSession() === $this) {
                $cashRegisterClosure->setCashRegisterSession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sale>
     */
    public function getSales(): Collection
    {
        return $this->sales;
    }

    public function addSale(Sale $sale): static
    {
        if (!$this->sales->contains($sale)) {
            $this->sales->add($sale);
            $sale->setCashRegisterSession($this);
        }

        return $this;
    }

    public function removeSale(Sale $sale): static
    {
        if ($this->sales->removeElement($sale)) {
            // set the owning side to null (unless already changed)
            if ($sale->getCashRegisterSession() === $this) {
                $sale->setCashRegisterSession(null);
            }
        }

        return $this;
    }
}
