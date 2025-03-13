<?php

namespace App\Entity;

use App\Repository\SaleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


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
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalPrice = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $cashAmount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $cardAmount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $tip = null;

    #[ORM\Column(length: 12, nullable: true)]
    private ?string $zipcodeCustomer = null;

    /**
     * @var Collection<int, SalesItem>
     */
    #[ORM\OneToMany(targetEntity: SalesItem::class, mappedBy: 'sale', cascade: ['persist'])]
    private Collection $salesItems;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $keep_change = null;

    public function __construct()
    {
        $this->salesItems = new ArrayCollection();
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

    public function getCashAmount(): ?string
    {
        return $this->cashAmount;
    }

    public function setCashAmount(?string $cashAmount): static
    {
        $this->cashAmount = $cashAmount;

        return $this;
    }

    public function getCardAmount(): ?string
    {
        return $this->cardAmount;
    }

    public function setCardAmount(?string $cardAmount): static
    {
        $this->cardAmount = $cardAmount;

        return $this;
    }

    public function getTip(): ?string
    {
        return $this->tip;
    }

    public function setTip(?string $tip): static
    {
        $this->tip = $tip;

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

    public function getKeepChange(): ?string
    {
        return $this->keep_change;
    }

    public function setKeepChange(?string $keep_change): static
    {
        $this->keep_change = $keep_change;

        return $this;
    }
}
