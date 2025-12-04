<?php

namespace App\Enums;

enum StatusTransactionEnum: int
{
    case EM_ABERTO  = 1;
    case AGUARDANDO = 2;
    case PAGO       = 3;
    case NEGADO     = 4;
    case INCLUIDO   = 5;
    case ESTORNADO  = 6;

    public function label(): string
    {
        return match ($this) {
            self::EM_ABERTO  => 'Em aberto',
            self::AGUARDANDO => 'Aguardando',
            self::PAGO       => 'Pago',
            self::NEGADO     => 'Negado',
            self::INCLUIDO   => 'Incluído',
            self::ESTORNADO  => 'Estornado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::EM_ABERTO  => 'primary',
            self::AGUARDANDO => 'warning',
            self::PAGO       => 'success',
            self::NEGADO     => 'danger',
            self::INCLUIDO   => 'primary',
            self::ESTORNADO  => 'danger',
        };
    }

    public static function labelEnum($state): string
    {
        return match ($state) {
            self::EM_ABERTO->value  => self::EM_ABERTO->label(),
            self::AGUARDANDO->value => self::AGUARDANDO->label(),
            self::PAGO->value       => self::PAGO->label(),
            self::NEGADO->value     => self::NEGADO->label(),
            self::INCLUIDO->value   => self::INCLUIDO->label(),
            self::ESTORNADO->value  => self::ESTORNADO->label(),
        };
    }

    public static function colorEnum($state): string
    {
        return match ($state) {
            self::EM_ABERTO->value  => self::EM_ABERTO->color(),
            self::AGUARDANDO->value => self::AGUARDANDO->color(),
            self::PAGO->value       => self::PAGO->color(),
            self::NEGADO->value     => self::NEGADO->color(),
            self::INCLUIDO->value   => self::INCLUIDO->color(),
            self::ESTORNADO->value  => self::ESTORNADO->color(),
        };
    }
}
