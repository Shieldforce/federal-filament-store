<?php

namespace App\Enums;

enum TypeTransactionEnum: int
{
    case input  = 1;
    case output = 2;

    public function preLabel(): string
    {
        return match ($this) {
            self::input  => 'Entrada',
            self::output => 'Saída',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::input  => 'Entrada (Pagamentos, Faturas)',
            self::output => 'Saída (Contas, Prestações, Salários)',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::input  => 'success',
            self::output => 'danger',
        };
    }
}
