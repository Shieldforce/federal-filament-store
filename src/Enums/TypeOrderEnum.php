<?php

namespace App\Enums;

enum TypeOrderEnum: int
{
    case AVULSO                = 1;
    case MENSAL                = 2;

    public function label(): string
    {
        return match ($this) {
            self::AVULSO                => 'Avulso',
            self::MENSAL                => 'Mensal',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::AVULSO                => 'primary',
            self::MENSAL                => 'success',
        };
    }

    public static function labelEnum($state): string
    {
        return match ($state) {
            self::AVULSO->value                => self::AVULSO->label(),
            self::MENSAL->value                => self::MENSAL->label(),
        };
    }

    public static function colorEnum($state): string
    {
        return match ($state) {
            self::AVULSO->value                => self::AVULSO->color(),
            self::MENSAL->value                => self::MENSAL->color(),
        };
    }
}
