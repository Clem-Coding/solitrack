<?php

namespace App\Entity;

use App\Repository\CashRegisterSessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: "cash_register_sessions")]
#[ORM\Entity(repositoryClass: CashRegisterSessionRepository::class)]
class CashRegisterSession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $openingAt = null;

    #[ORM\Column]
    #[Assert\NotBlank()]
    #[Assert\GreaterThan(value: 0)]
    private ?float $cashFloat = null;

    #[ORM\ManyToOne(inversedBy: 'cashRegisterSessions')]
    #[ORM\JoinColumn(onDelete: 'SET NULL', nullable: true)]
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

    /**
     * @var Collection<int, CashMovement>
     */
    #[ORM\OneToMany(targetEntity: CashMovement::class, mappedBy: 'cashRegisterSession')]
    private Collection $cashMovements;

    public function __construct()
    {
        $this->cashRegisterClosures = new ArrayCollection();
        $this->sales = new ArrayCollection();
        $this->cashMovements = new ArrayCollection();
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

    /**
     * @return Collection<int, CashMovement>
     */
    public function getCashMovements(): Collection
    {
        return $this->cashMovements;
    }

    public function addCashMovement(CashMovement $cashMovement): static
    {
        if (!$this->cashMovements->contains($cashMovement)) {
            $this->cashMovements->add($cashMovement);
            $cashMovement->setCashRegisterSession($this);
        }

        return $this;
    }

    public function removeCashMovement(CashMovement $cashMovement): static
    {
        if ($this->cashMovements->removeElement($cashMovement)) {
            // set the owning side to null (unless already changed)
            if ($cashMovement->getCashRegisterSession() === $this) {
                $cashMovement->setCashRegisterSession(null);
            }
        }

        return $this;
    }
}
