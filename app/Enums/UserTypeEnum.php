<?php

namespace App\Enums;

enum UserTypeEnum: int
{
    case Provider = 1;
    case Client = 2;
    case Admin = 3;

    public function label(): string
    {
        return match ($this) {
            self::Provider => __('Provider'),
            self::Client => __('Client'),
            self::Admin => __('Admin'),
        };
    }
    
    public function value(): int
    {
        return $this->value;
    }
}
