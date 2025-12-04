<?php

namespace Shieldforce\FederalFilamentStore\Enums;

enum TypePeopleEnum: int
{
    case F = 1;
    case J = 2;

    public function label(): string
    {
        return match ($this) {
            self::F => 'Física',
            self::J => 'Jurídica',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
