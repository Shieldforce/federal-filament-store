<?php

namespace Shieldforce\FederalFilamentStore\Enums;

enum StatusCartEnum: int
{
    case comprando  = 1;
    case abandonado = 2;
    case finalizado = 3;
    case cancelado  = 4;

    public function label(): string
    {
        return match ($this) {
            self::comprando  => 'Comprando',
            self::abandonado => 'Abandonado',
            self::finalizado => 'Finalizado',
            self::cancelado  => 'Cancelado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::comprando  => 'info',
            self::abandonado => 'warning',
            self::finalizado => 'success',
            self::cancelado  => 'danger',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
