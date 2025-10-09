<?php

namespace App\Service;

use App\Entity\VolunteerSession;
use Doctrine\ORM\EntityManagerInterface;

class VolunteerSessionGeneratorService
{

    // Limit occurrences to avoid bug performance issues, to do : define type or numer later
    private const MAX_OCCURRENCES = 4;

    public function __construct(private EntityManagerInterface $em) {}

    public function generateOccurrences(VolunteerSession $templateSession): array
    {
        $recurrence = $templateSession->getRecurrence();
        if (!$recurrence) return ['count' => 0, 'warning' => null];

        $start     = $templateSession->getStartDatetime();
        $end       = $templateSession->getEndDatetime();
        $frequency = $recurrence->getFrequency();
        $until     = $recurrence->getUntilDate() ?: $start;

        $intervalSpec = match ($frequency) {
            'daily'   => 'P1D',
            'weekly'  => 'P1W',
            'monthly' => 'P1M',
            default   => throw new \Exception("Frequency not supported: $frequency"),
        };

        // Start from the next occurrence
        $currentStart = (clone $start)->add(new \DateInterval($intervalSpec));
        $currentEnd   = (clone $end)->add(new \DateInterval($intervalSpec));

        $count = 0;
        while ($currentStart <= $until && $count < self::MAX_OCCURRENCES) {
            $occurrence = new VolunteerSession();
            $occurrence->setTitle($templateSession->getTitle());
            $occurrence->setDescription($templateSession->getDescription());
            $occurrence->setLocation($templateSession->getLocation());
            $occurrence->setRequiredVolunteers($templateSession->getRequiredVolunteers());
            $occurrence->setCreatedBy($templateSession->getCreatedBy());
            $occurrence->setCreatedAt(new \DateTimeImmutable());
            $occurrence->setUpdatedAt(new \DateTimeImmutable());
            $occurrence->setIsCancelled(false);
            $occurrence->setRecurrence($recurrence);
            $occurrence->setStartDatetime(clone $currentStart);
            $occurrence->setEndDatetime(clone $currentEnd);

            $this->em->persist($occurrence);

            $currentStart = (clone $currentStart)->add(new \DateInterval($intervalSpec));
            $currentEnd   = (clone $currentEnd)->add(new \DateInterval($intervalSpec));

            $count++;
        }

        $this->em->flush();

        $warning = ($count >= self::MAX_OCCURRENCES)
            ? "Attention : nombre maximum d'occurrences autorisées ({self::MAX_OCCURRENCES}) atteint."
            : null;

        return ['count' => $count, 'warning' => $warning];
    }
}
