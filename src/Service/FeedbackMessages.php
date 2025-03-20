<?php

namespace App\Service;

use App\Repository\DonationRepository;

class FeedbackMessages
{
    private DonationRepository $donationRepository;

    public function __construct(DonationRepository $donationRepository)
    {
        $this->donationRepository = $donationRepository;
    }

    public function getRandomFeedbackMessage(): array
    {
        $totalWeightToday = $this->donationRepository->getTotalWeightForToday();
        $recordWeight = $this->donationRepository->getRecordWeightDay();
        // dump("le record", $recordWeight);
        // dump("le poids total du jour", $totalWeightToday);

        if ($totalWeightToday >= $recordWeight['totalWeight']) {
            $messages = [
                "Vous avez pulvérisé le record ! <span class='highlighted'>$totalWeightToday kg</span> collectés aujourd’hui ! 🎉",
                "Incroyable ! <span class='highlighted'>$totalWeightToday kg</span> aujourd’hui : un nouveau record 🚀 !",
                "Les fourmis sont en feu 🔥! Nouveau record battu avec <span class='highlighted'>$totalWeightToday kg</span> !",
            ];
        } elseif ($totalWeightToday > 400) {
            $messages = [
                "💪 Woah ! Déjà <span class='highlighted'>$totalWeightToday kg</span> collectés ! On va manquer de place 😆 !",
                "Les stocks explosent ! <span class='highlighted'>$totalWeightToday kg</span> aujourd’hui, vous êtes incroyables !",
                "🔥 Une collecte MASSIVE de <span class='highlighted'>$totalWeightToday kg</span> en une journée !",
            ];
        } elseif ($totalWeightToday > 200) {
            $messages = [
                "🚀 On avance bien ! Déjà <span class='highlighted'>$totalWeightToday kg</span> aujourd’hui !",
                "Les fourmis s’activent : <span class='highlighted'>$totalWeightToday kg</span> récoltés, bravo !",
                "Super collecte : <span class='highlighted'>$totalWeightToday kg</span> aujourd’hui !",
            ];
        } else {
            $messages = [
                "Petit à petit, on remplit : <span class='highlighted'>$totalWeightToday kg</span> pour l’instant !",
                "Chaque kilo compte ! Déjà <span class='highlighted'>$totalWeightToday kg</span> aujourd’hui !",
                "C’est un bon début ! <span class='highlighted'>$totalWeightToday kg</span> au compteur !",
            ];
        }

        return [
            'message' => $messages[array_rand($messages)],
            'totalWeight' => $totalWeightToday
        ];
    }
}
