<?php

namespace Shieldforce\FederalFilamentStore\Enums;

enum TypeContractEnum: int
{
    case contrato_1 = 1;
    case contrato_2 = 2;
    case contrato_3 = 3;

    public function label(): string
    {
        return match ($this) {
            self::contrato_1 => 'Contrato 1',
            self::contrato_2 => 'Contrato 2',
            self::contrato_3 => 'Sem contrato',
        };
    }

    public function template()
    {
         $contrato1 = env("CLICKSIGN_AMBIENT") == "PRODUCTION"
             ? env("CLICKSIGN_TEMPLATE_PRODUCTION_1")
             : env("CLICKSIGN_TEMPLATE_SANDBOX_1");

        $contrato2 = env("CLICKSIGN_AMBIENT") == "PRODUCTION"
            ? env("CLICKSIGN_TEMPLATE_PRODUCTION_2")
            : env("CLICKSIGN_TEMPLATE_SANDBOX_2");

        return match ($this) {
            self::contrato_1 => $contrato1,
            self::contrato_2 => $contrato2,
            self::contrato_3 => null,
        };
    }
}
