<?php

namespace App\Entity;

use App\Repository\SaleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Table(name: "sales")]
#[ORM\Entity(repositoryClass: SaleRepository::class)]
class Sale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'sales')]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?User $user = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalPrice = null;

    #[Assert\Length(
        exactly: 5,
    )]
    #[Assert\Regex(
        pattern: "/^\d{5}$/",
        message: "Le code postal doit contenir exactement 5 chiffres."
    )]
    #[ORM\Column(length: 12, nullable: true)]
    private ?string $zipcodeCustomer = null;

    /**
     * @var Collection<int, SalesItem>
     */
    #[ORM\OneToMany(targetEntity: SalesItem::class, mappedBy: 'sale', cascade: ['persist'])]
    private Collection $salesItems;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $changeAmount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $pwywAmount = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $customer_city = null;

    #[ORM\ManyToOne(inversedBy: 'sales')]
    #[ORM\JoinColumn(nullable: true)]
    private ?CashRegisterSession $CashRegisterSession = null;

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'sale')]
    private Collection $payments;

    public function __construct()
    {
        $this->salesItems = new ArrayCollection();
        $this->payments = new ArrayCollection();
    }



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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getTotalPrice(): ?string
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(string $totalPrice): static
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getZipcodeCustomer(): ?string
    {
        return $this->zipcodeCustomer;
    }

    public function setZipcodeCustomer(?string $zipcodeCustomer): static
    {
        $this->zipcodeCustomer = $zipcodeCustomer;

        return $this;
    }

    /**
     * @return Collection<int, SalesItem>
     */
    public function getSalesItems(): Collection
    {
        return $this->salesItems;
    }

    public function addSalesItem(SalesItem $salesItem): static
    {
        if (!$this->salesItems->contains($salesItem)) {
            $this->salesItems->add($salesItem);
            $salesItem->setSale($this);
        }

        return $this;
    }

    public function removeSalesItem(SalesItem $salesItem): static
    {
        if ($this->salesItems->removeElement($salesItem)) {
            // set the owning side to null (unless already changed)
            if ($salesItem->getSale() === $this) {
                $salesItem->setSale(null);
            }
        }

        return $this;
    }

    public function getChangeAmount(): ?string
    {
        return $this->changeAmount;
    }

    public function setChangeAmount(?string $changeAmount): static
    {
        $this->changeAmount = $changeAmount;

        return $this;
    }

    public function getPwywAmount(): ?string
    {
        return $this->pwywAmount;
    }

    public function setPwywAmount(?string $pwywAmount): static
    {
        $this->pwywAmount = $pwywAmount;

        return $this;
    }

    public function getCustomerCity(): ?string
    {
        return $this->customer_city;
    }

    public function setCustomerCity(?string $customer_city): static
    {
        $this->customer_city = $customer_city;

        return $this;
    }

    public function getCashRegisterSession(): ?CashRegisterSession
    {
        return $this->CashRegisterSession;
    }

    public function setCashRegisterSession(?CashRegisterSession $CashRegisterSession): static
    {
        $this->CashRegisterSession = $CashRegisterSession;

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setSale($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getSale() === $this) {
                $payment->setSale(null);
            }
        }

        return $this;
    }
}
