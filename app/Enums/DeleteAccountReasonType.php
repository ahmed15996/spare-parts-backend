<?php

namespace App\Enums;

enum DeleteAccountReasonType: int
{
    case Client = 1;
    case Provider = 2;

    public function label(): string
    {
        return match ($this) {
            self::Client => __('Client'),
            self::Provider => __('Provider'),
        };
    }

    public function value(): int
    {
        return $this->value;
    }

    public function values(){
        return array_column(self::cases(), 'value');
    }
}
