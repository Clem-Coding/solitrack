<?php

namespace App\Entity;

use App\Enum\OutgoingWeighingType;
use App\Repository\OutgoingWeighingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: "outgoing_weighings")]
#[ORM\Entity(repositoryClass: OutgoingWeighingRepository::class)]
class OutgoingWeighing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank()]
    #[Assert\GreaterThan(value: 0)]
    private ?float $weight = null;

    #[ORM\Column]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'outgoingWeighings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?OutgoingWeighingType $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): static
    {
        $this->weight = $weight;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getType(): ?OutgoingWeighingType
    {
        return $this->type;
    }

    public function setType(OutgoingWeighingType $type): static
    {
        $this->type = $type;

        return $this;
    }
}
