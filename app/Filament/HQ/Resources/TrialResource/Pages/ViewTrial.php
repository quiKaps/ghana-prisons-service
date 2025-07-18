<?php

namespace App\Filament\HQ\Resources\TrialResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\HQ\Resources\TrialResource;

class ViewTrial extends ViewRecord
{
    protected static string $resource = TrialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Show "Back to Remands" if detention_type is 'remand'
            Actions\Action::make('back-to-remands')
                ->label('Back to Remands')
                ->icon('heroicon-o-arrow-left')
                ->color('success')
                ->url(TrialResource::getUrl('index')),


            //print action starts
            Actions\Action::make('Print')
                ->color('warning')
                ->icon('heroicon-s-printer'),
            //print action ends


        ];
    }

    public function getHeading(): string
    {
        return "{$this->record->full_name}'s Profile";
    }

    public function getSubheading(): string|Htmlable|null
    {
        if ($this->record->is_discharged) {
            return "Prisoner has been discharged";
        } else {
            return '';
        }
    }
}
