<?php

namespace App\Entity;

use App\Repository\VolunteerSessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'volunteer_sessions')]
#[ORM\Entity(repositoryClass: VolunteerSessionRepository::class)]
class VolunteerSession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $location = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startDatetime = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $endDatetime = null;

    #[ORM\Column(nullable: true)]
    private ?int $required_volunteers = null;

    #[ORM\ManyToOne(inversedBy: 'volunteerSessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private ?bool $isCancelled = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'volunteerSessions')]
    private ?VolunteerRecurrence $recurrence = null;

    /**
     * @var Collection<int, VolunteerRegistration>
     */
    #[ORM\OneToMany(targetEntity: VolunteerRegistration::class, mappedBy: 'session')]
    private Collection $volunteerRegistrations;

    public function __construct()
    {
        $this->volunteerRegistrations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getStartDatetime(): ?\DateTimeImmutable
    {
        return $this->startDatetime;
    }

    public function setStartDatetime(\DateTimeImmutable $startDatetime): static
    {
        $this->startDatetime = $startDatetime;

        return $this;
    }

    public function getEndDatetime(): ?\DateTimeImmutable
    {
        return $this->endDatetime;
    }

    public function setEndDatetime(\DateTimeImmutable $endDatetime): static
    {
        $this->endDatetime = $endDatetime;

        return $this;
    }

    public function getRequiredVolunteers(): ?int
    {
        return $this->required_volunteers;
    }

    public function setRequiredVolunteers(?int $required_volunteers): static
    {
        $this->required_volunteers = $required_volunteers;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function isCancelled(): ?bool
    {
        return $this->isCancelled;
    }

    public function setIsCancelled(bool $isCancelled): static
    {
        $this->isCancelled = $isCancelled;

        return $this;
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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getRecurrence(): ?VolunteerRecurrence
    {
        return $this->recurrence;
    }

    public function setRecurrence(?VolunteerRecurrence $recurrence): static
    {
        $this->recurrence = $recurrence;

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
            $volunteerRegistration->setSession($this);
        }

        return $this;
    }

    public function removeVolunteerRegistration(VolunteerRegistration $volunteerRegistration): static
    {
        if ($this->volunteerRegistrations->removeElement($volunteerRegistration)) {
            // set the owning side to null (unless already changed)
            if ($volunteerRegistration->getSession() === $this) {
                $volunteerRegistration->setSession(null);
            }
        }

        return $this;
    }
}
