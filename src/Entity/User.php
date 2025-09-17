<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\DBAL\Types\Types;




#[ORM\Table(name: "users")]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'entity.user.email.unique')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Email()]
    #[Assert\NotBlank()]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */


    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotBlank()]
    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[Assert\NotBlank()]
    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    /**
     * @var Collection<int, Donation>
     */
    #[ORM\OneToMany(targetEntity: Donation::class, mappedBy: 'user')]
    private Collection $donations;

    /**
     * @var Collection<int, Sale>
     */
    #[ORM\OneToMany(targetEntity: Sale::class, mappedBy: 'user')]
    private Collection $sales;

    /**
     * @var Collection<int, Visitor>
     */
    #[ORM\OneToMany(targetEntity: Visitor::class, mappedBy: 'user')]
    private Collection $visitors;

    /**
     * @var Collection<int, CashRegisterSession>
     */
    #[ORM\OneToMany(targetEntity: CashRegisterSession::class, mappedBy: 'user')]
    private Collection $cashRegisterSessions;

    /**
     * @var Collection<int, CashRegisterClosure>
     */
    #[ORM\OneToMany(targetEntity: CashRegisterClosure::class, mappedBy: 'user')]
    private Collection $cashRegisterClosures;

    /**
     * @var Collection<int, CashMovement>
     */
    #[ORM\OneToMany(targetEntity: CashMovement::class, mappedBy: 'madeBy')]
    private Collection $cashMovements;

    /**
     * @var Collection<int, VolunteerSession>
     */
    #[ORM\OneToMany(targetEntity: VolunteerSession::class, mappedBy: 'createdBy')]
    private Collection $volunteerSessions;

    /**
     * @var Collection<int, VolunteerRegistration>
     */
    #[ORM\OneToMany(targetEntity: VolunteerRegistration::class, mappedBy: 'user')]
    private Collection $volunteerRegistrations;

    public function __construct()
    {
        $this->donations = new ArrayCollection();
        $this->sales = new ArrayCollection();
        $this->visitors = new ArrayCollection();
        $this->cashRegisterSessions = new ArrayCollection();
        $this->cashRegisterClosures = new ArrayCollection();
        $this->cashMovements = new ArrayCollection();
        $this->volunteerSessions = new ArrayCollection();
        $this->volunteerRegistrations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

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
            $donation->setUser($this);
        }

        return $this;
    }

    public function removeDonation(Donation $donation): static
    {
        if ($this->donations->removeElement($donation)) {
            // set the owning side to null (unless already changed)
            if ($donation->getUser() === $this) {
                $donation->setUser(null);
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
            $sale->setUser($this);
        }

        return $this;
    }

    public function removeSale(Sale $sale): static
    {
        if ($this->sales->removeElement($sale)) {
            // set the owning side to null (unless already changed)
            if ($sale->getUser() === $this) {
                $sale->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Visitor>
     */
    public function getVisitors(): Collection
    {
        return $this->visitors;
    }

    public function addVisitor(Visitor $visitor): static
    {
        if (!$this->visitors->contains($visitor)) {
            $this->visitors->add($visitor);
            $visitor->setUser($this);
        }

        return $this;
    }

    public function removeVisitor(Visitor $visitor): static
    {
        if ($this->visitors->removeElement($visitor)) {
            // set the owning side to null (unless already changed)
            if ($visitor->getUser() === $this) {
                $visitor->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CashRegisterSession>
     */
    public function getCashRegisterSessions(): Collection
    {
        return $this->cashRegisterSessions;
    }

    public function addCashRegisterSession(CashRegisterSession $cashRegisterSession): static
    {
        if (!$this->cashRegisterSessions->contains($cashRegisterSession)) {
            $this->cashRegisterSessions->add($cashRegisterSession);
            $cashRegisterSession->setUser($this);
        }

        return $this;
    }

    public function removeCashRegisterSession(CashRegisterSession $cashRegisterSession): static
    {
        if ($this->cashRegisterSessions->removeElement($cashRegisterSession)) {
            // set the owning side to null (unless already changed)
            if ($cashRegisterSession->getUser() === $this) {
                $cashRegisterSession->setUser(null);
            }
        }

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
            $cashRegisterClosure->setUser($this);
        }

        return $this;
    }

    public function removeCashRegisterClosure(CashRegisterClosure $cashRegisterClosure): static
    {
        if ($this->cashRegisterClosures->removeElement($cashRegisterClosure)) {
            // set the owning side to null (unless already changed)
            if ($cashRegisterClosure->getUser() === $this) {
                $cashRegisterClosure->setUser(null);
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
            $cashMovement->setMadeBy($this);
        }

        return $this;
    }

    public function removeCashMovement(CashMovement $cashMovement): static
    {
        if ($this->cashMovements->removeElement($cashMovement)) {
            // set the owning side to null (unless already changed)
            if ($cashMovement->getMadeBy() === $this) {
                $cashMovement->setMadeBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, VolunteerSession>
     */
    public function getVolunteerSessions(): Collection
    {
        return $this->volunteerSessions;
    }

    public function addVolunteerSession(VolunteerSession $volunteerSession): static
    {
        if (!$this->volunteerSessions->contains($volunteerSession)) {
            $this->volunteerSessions->add($volunteerSession);
            $volunteerSession->setCreatedBy($this);
        }

        return $this;
    }

    public function removeVolunteerSession(VolunteerSession $volunteerSession): static
    {
        if ($this->volunteerSessions->removeElement($volunteerSession)) {
            // set the owning side to null (unless already changed)
            if ($volunteerSession->getCreatedBy() === $this) {
                $volunteerSession->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, VolunteerRegistration>
     */
    public function getVolunteerRegistrations(): Collection
    {
        return $this->volunteerRegistrations;
    }

    public function addVolunteerRegistration(VolunteerRegistration $volunteerRegistration): static
    {
        if (!$this->volunteerRegistrations->contains($volunteerRegistration)) {
            $this->volunteerRegistrations->add($volunteerRegistration);
            $volunteerRegistration->setUser($this);
        }

        return $this;
    }

    public function removeVolunteerRegistration(VolunteerRegistration $volunteerRegistration): static
    {
        if ($this->volunteerRegistrations->removeElement($volunteerRegistration)) {
            // set the owning side to null (unless already changed)
            if ($volunteerRegistration->getUser() === $this) {
                $volunteerRegistration->setUser(null);
            }
        }

        return $this;
    }
}
