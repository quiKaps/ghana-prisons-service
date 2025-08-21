<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum DetentionTypeEnum: string implements HasLabel
{
    case REMAND = 'remand';
    case TRIAL = 'trial';

    public function getLabel(): string
    {
        return match ($this) {
            self::REMAND => 'Remand',
            self::TRIAL => 'Trial',
        };
    }
}
