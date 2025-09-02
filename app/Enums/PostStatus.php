<?php

namespace App\Enums;

enum PostStatus: int
{
    case Pending = 1;
    case Approved = 2;
    case Rejected = 3;

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('Pending'),
            self::Approved => __('Approved'),
            self::Rejected => __('Rejected'),
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
