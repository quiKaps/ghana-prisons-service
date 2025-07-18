<?php

namespace App\Filament\HQ\Resources\ConvictResource\Pages;

use Filament\Actions;
use App\Models\Sentence;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\HQ\Resources\ConvictResource;

class ViewConvict extends ViewRecord
{
    protected static string $resource = ConvictResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //back to convicts actions
            Action::make('back')
                ->label('Back to Convicts')
                ->color('success')
                ->icon('heroicon-o-arrow-left')
                ->url(ConvictResource::getUrl('index')),
            //back to all convicts actions

            //print action starts
            Action::make('print')
                ->label('Print Profile')
                ->color('warning')
                ->icon('heroicon-o-printer'),
            //print action ends


        ];
    }

    public function getHeading(): string
    {
        return "{$this->record->full_name}'s Profile";
    }


    protected function mutateFormDataBeforeFill(array $data): array
    {
        $sentences = Sentence::where('inmate_id', $data['id'])->first();

        $latestSentence = Sentence::where('inmate_id', $data['id'])->latest()->first();


        if ($latestSentence != null) {
            $data['offence'] = $sentences?->offence;
            $data['sentence'] = $latestSentence?->sentence;
            $data['date_sentenced'] = $sentences?->date_of_sentence;
            $data['EPD'] = $latestSentence?->EPD;
            $data['LPD'] = $latestSentence?->LPD;
            $data['court_of_committal'] = $sentences?->court_of_committal;
            $data['warrant_document'] = $sentences?->warrant_document;
        }


        return $data;
    }
}
