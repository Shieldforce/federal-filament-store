<?php

namespace Shieldforce\FederalFilamentStore\Enums;

enum StatusClientEnum: int
{
    case ativo   = 1;
    case inativo = 2;

    public function label(): string
    {
        return match ($this) {
            self::ativo   => 'Ativo',
            self::inativo => 'Inativo',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ativo   => 'success',
            self::inativo => 'danger',
        };
    }

    public static function labelEnum($state): string
    {
        return match ($state) {
            self::ativo->value   => self::ativo->label(),
            self::inativo->value => self::inativo->label(),
        };
    }

    public static function colorEnum($state): string
    {
        return match ($state) {
            self::ativo->value   => self::ativo->color(),
            self::inativo->value => self::inativo->color(),
        };
    }
}
