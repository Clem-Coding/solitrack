<?php

namespace App\Entity;

use App\Repository\VolunteerRecurrenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'volunteer_recurrences')]
#[ORM\Entity(repositoryClass: VolunteerRecurrenceRepository::class)]
class VolunteerRecurrence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $frequency = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $untilDate = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, VolunteerSession>
     */
    #[ORM\OneToMany(targetEntity: VolunteerSession::class, mappedBy: 'recurrence')]
    private Collection $volunteerSessions;

    public function __construct()
    {
        $this->volunteerSessions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFrequency(): ?string
    {
        return $this->frequency;
    }

    public function setFrequency(string $frequency): static
    {
        $this->frequency = $frequency;

        return $this;
    }

    public function getUntilDate(): ?\DateTimeImmutable
    {
        return $this->untilDate;
    }

    public function setUntilDate(\DateTimeImmutable $untilDate): static
    {
        $this->untilDate = $untilDate;

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
            $volunteerSession->setRecurrence($this);
        }

        return $this;
    }

    public function removeVolunteerSession(VolunteerSession $volunteerSession): static
    {
        if ($this->volunteerSessions->removeElement($volunteerSession)) {
            // set the owning side to null (unless already changed)
            if ($volunteerSession->getRecurrence() === $this) {
                $volunteerSession->setRecurrence(null);
            }
        }

        return $this;
    }
}
