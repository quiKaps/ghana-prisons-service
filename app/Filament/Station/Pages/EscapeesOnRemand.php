<?php

namespace App\Filament\Station\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Models\DischargedInmates;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;

class EscapeesOnRemand extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'filament.station.pages.escapees-on-remand';

    protected static ?string $navigationGroup = 'Escapees';

    protected static ?string $navigationLabel = 'Escaped List - Remand';


    public function table(Table $table): Table
    {
        return $table
            ->query(DischargedInmates::query()
                ->where('inmate_type', 'remand')
                ->where('mode_of_discharge', 'escape')
                ->orderBy('created_at', 'DESC'))
            ->emptyStateHeading('No Escapee Remands')
            ->emptyStateDescription('No recorded escaped remand inmates')
            ->columns([
                TextColumn::make('serial_number')
                    ->weight(FontWeight::Bold)
                    ->label('S.N.'),
                TextColumn::make('full_name')
                    ->searchable()
                    ->label('Inmate Name'),
                TextColumn::make('admission_date')
                    ->label('Admission Date')
                    ->date(),
                TextColumn::make('court')
                    ->label('Court'),
                TextColumn::make('next_court_date')
                    ->badge()
                    ->color('success')
                    ->label('Next Court Date')
                    ->date(),
                TextColumn::make('police_station')
                    ->label('Police Station'),
                TextColumn::make('police_contact')
                    ->label('Police Contact'),

            ])
            ->filters([
                // Define any filters here if needed
            ])
            ->actions([]);
    }
}
