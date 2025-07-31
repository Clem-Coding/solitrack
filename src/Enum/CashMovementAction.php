<?php

namespace App\Enum;

enum CashMovementAction: string
{
    case Deposit = 'deposit';
    case Withdraw = 'withdraw';

    public function getLabel(): string
    {
        return match ($this) {
            self::Deposit => 'Ajout',
            self::Withdraw => 'Retrait',
        };
    }
}
