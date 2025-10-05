<?php

namespace App\Enum;

enum CryptoPair: string
{
    case BTCEUR = 'BTCEUR';
    case ETHEUR = 'ETHEUR';
    case LTCEUR = 'LTCEUR';

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
