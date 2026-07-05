<?php

namespace App\Enums;

enum AdminBannerType: int
{
    case Admin = 4;

    public function label(): string
    {
        return match ($this) {
            self::Admin => __('Admin'),
        };
    }

    public function value(): int
    {
        return $this->value;
    }
}
