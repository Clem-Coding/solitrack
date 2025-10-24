<?php

namespace App\Enum;

enum OutgoingWeighingType: string
{
    case Waste = 'waste';
    case FreeZone = 'free_zone';

    public function getLabel(): string
    {
        return match ($this) {
            self::Waste => 'Déchets',
            self::FreeZone => 'Zone de gratuité',
        };
    }
}
