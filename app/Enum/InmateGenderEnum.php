<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum InmateGenderEnum: string implements HasLabel
{
    case MALE = 'male';
    case FEMALE = 'female';

    public function getLabel(): string
    {
        return match ($this) {
            self::MALE => 'Male',
            self::FEMALE => 'Female',
        };
    }
}
