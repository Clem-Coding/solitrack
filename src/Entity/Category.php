<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;



#[ORM\Table(name: "categories")]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Donation>
     */
    #[ORM\OneToMany(targetEntity: Donation::class, mappedBy: 'category')]
    private Collection $donations;

    /**
     * @var Collection<int, SalesItem>
     */
    #[ORM\OneToMany(targetEntity: SalesItem::class, mappedBy: 'category')]
    private Collection $salesItems;

    public function __construct()
    {
        $this->donations = new ArrayCollection();
        $this->salesItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Donation>
     */
    public function getDonations(): Collection
    {
        return $this->donations;
    }

    public function addDonation(Donation $donation): static
    {
        if (!$this->donations->contains($donation)) {
            $this->donations->add($donation);
            $donation->setCategory($this);
        }

        return $this;
    }

    public function removeDonation(Donation $donation): static
    {
        if ($this->donations->removeElement($donation)) {
            // set the owning side to null (unless already changed)
            if ($donation->getCategory() === $this) {
                $donation->setCategory(null);
            }
        }

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
            $salesItem->setCategory($this);
        }

        return $this;
    }

    public function removeSalesItem(SalesItem $salesItem): static
    {
        if ($this->salesItems->removeElement($salesItem)) {
            // set the owning side to null (unless already changed)
            if ($salesItem->getCategory() === $this) {
                $salesItem->setCategory(null);
            }
        }

        return $this;
    }
}
