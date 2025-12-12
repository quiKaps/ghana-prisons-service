<?php

namespace App\Filament\Exports;

use App\Models\ConvictDischarges;
use App\Models\Inmate;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ConvictDischargesExporter extends Exporter
{
    protected static ?string $model = Inmate::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('station.name')
                ->label('Station'),
            ExportColumn::make('serial_number')
                ->label('Serial Number'),
            ExportColumn::make('full_name')
                ->label('Name of Prisoner'),
            ExportColumn::make('age_on_admission')
                ->label('Age'),
            ExportColumn::make('latestSentenceByDate.offence')
                ->label('Offence')
                ->getStateUsing(function ($record) {
                    return $record->latestSentenceByDate ? $record->latestSentenceByDate->offence : '';
                }),
            ExportColumn::make('latestSentenceByDate.total_sentence')
                ->label('Sentence')
                ->getStateUsing(function ($record) {
                    return $record->latestSentenceByDate ? $record->latestSentenceByDate->total_sentence : '';
                }),
            ExportColumn::make('admission_date')
                ->label('Date of Admission')
                ->formatStateUsing(fn($state) => date_format($state, 'Y-m-d')),
            ExportColumn::make('latestSentenceByDate.date_of_sentence')
                ->label('Date of Sentence')
                ->getStateUsing(function ($record) {
                    return $record?->latestSentenceByDate?->date_of_sentence ? date_format($record->latestSentenceByDate->date_of_sentence, 'Y-m-d') : '';
                }),
            ExportColumn::make('latestSentenceByDate.EPD')
                ->label('EPD')
                ->getStateUsing(function ($record) {
                    return $record?->latestSentenceByDate?->EPD ? date_format($record->latestSentenceByDate->EPD, 'Y-m-d') : '';
                }),
            ExportColumn::make('latestSentenceByDate.LPD')
                ->label('LPD')
                ->getStateUsing(function ($record) {
                    return $record?->latestSentenceByDate?->LPD ? date_format($record->latestSentenceByDate->LPD, 'Y-m-d') : '';
                }),
            ExportColumn::make('latestSentenceByDate.court_of_committal')
                ->label('Court of Committal')
                ->getStateUsing(function ($record) {
                    return $record->latestSentenceByDate ? $record->latestSentenceByDate->court_of_committal : '';
                }),
            ExportColumn::make('cell_id')
                ->label('Cell Number - Block'),
            ExportColumn::make('latestSentenceByDate.warrant')
                ->label('Warrant')
                ->getStateUsing(function ($record) {
                    return $record->latestSentenceByDate ? $record->latestSentenceByDate->warrant : '';
                }),
            ExportColumn::make('transferred_in')
                ->label('Transferred In')
                ->getStateUsing(function ($record) {
                    return $record->transferred_in == 1 ? 'Yes' : 'No';
                }),
            ExportColumn::make('disability')
                ->label('Disability')
                ->getStateUsing(function ($record) {
                    return $record->disability == 1 ? 'Yes' : 'No';
                }),
            ExportColumn::make('tribe')
                ->label('Tribe'),
            ExportColumn::make('languages_spoken')
                ->label('Languages Spoken')
                ->getStateUsing(function ($record) {
                    if (!empty($record->languages_spoken)) {
                        $languages = is_array($record->languages_spoken) ? $record->languages_spoken : json_decode($record->languages_spoken, true);
                        return is_array($languages) ? implode(', ', $languages) : '';
                    }
                    return '';
                }),
            ExportColumn::make('hometown')
                ->label('Hometown'),
            ExportColumn::make('married_status')
                ->label('Marital Status'),
            ExportColumn::make('nationality')
                ->label('Country of Origin'),
            ExportColumn::make('education_level')
                ->label('Education Background'),
            ExportColumn::make('religion')
                ->label('Religious Background'),
            ExportColumn::make('occupation')
                ->label('Occupation'),
            ExportColumn::make('next_of_kin_name')
                ->label('Next of Kin Name'),
            ExportColumn::make('next_of_kin_relationship')
                ->label('Next of Kin Relationship'),
            ExportColumn::make('next_of_kin_contact')
                ->label('Contact of Next of Kin'),
            ExportColumn::make('distinctive_marks')
                ->label('Distinctive Marks')
                ->getStateUsing(function ($record) {
                    return is_array($record->distinctive_marks) ? implode(', ', $record->distinctive_marks) : '';
                }),
            ExportColumn::make('goaler')
                ->label('Goaler')
                ->getStateUsing(function ($record) {
                    return $record->goaler == 1 ? 'Yes' : 'No';
                }),
            ExportColumn::make('previously_convicted')
                ->label('Previous Conviction')
                ->getStateUsing(function ($record) {
                    return $record->previous_conviction == 1 ? 'Yes' : 'No';
                }),
            ExportColumn::make('police_name')
                ->label('Police Officer'),
            ExportColumn::make('police_station')
                ->label('Police Station'),
            ExportColumn::make('police_contact')
                ->label('Police Contact'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your convict discharges export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
