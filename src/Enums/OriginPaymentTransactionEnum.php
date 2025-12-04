<?php

namespace Shieldforce\FederalFilamentStore\Enums;

enum OriginPaymentTransactionEnum: int
{
    case colaboradores = 1;
    case fornecedores  = 2;
    case avulso        = 3;


    public function preLabel(): string
    {
        return match ($this) {
            self::colaboradores => 'Colaboradores',
            self::fornecedores  => 'Fornecedores',
            self::avulso        => 'Avulso',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::colaboradores => 'Colaboradores (Salários e Benefícios)',
            self::fornecedores  => 'Fornecedores (Produtos e Serviços)',
            self::avulso        => 'Avulso (Saídas em geral)',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::colaboradores => 'success',
            self::fornecedores  => 'info',
            self::avulso        => 'danger',
        };
    }
}
