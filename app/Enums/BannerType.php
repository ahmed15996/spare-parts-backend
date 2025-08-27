<?php

namespace App\Enums;

enum BannerType: int
{
    case Home = 1;
    case Profile = 2;
    case Both = 3;

    public function label(): string
    {
        return match ($this) {
            self::Home => __('Main Section'),
            self::Profile => __('Profile Section'),
            self::Both => __('Main and Profile Section'),
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
