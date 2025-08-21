<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum PrisonerTypeEnum: string implements HasLabel
{
    case REMAND = 'remand';
    case TRIAL = 'trial';
    case CONVICT = 'convict';


    public function getLabel(): string
    {
        return match ($this) {
            self::REMAND => 'Remand',
            self::TRIAL => 'Trial',
            self::CONVICT => 'Convict',
        };
    }
}
