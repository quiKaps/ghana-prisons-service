<?php

namespace App\Filament\Station\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Models\DischargedInmates;
use Filament\Tables\Actions\Action;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Notifications\Notification;

class EscapeesOnTrial extends Page implements HasTable
{
    use \Filament\Tables\Concerns\InteractsWithTable;

    protected static string $view = 'filament.station.pages.escapees-on-trial';

    protected static ?string $navigationGroup = 'Escapees';

    protected static ?string $navigationLabel = 'Escaped List - Trial';

    protected ?string $heading = 'Escape List - Trial';

    protected ?string $subheading = 'List of inmates who were on trial but have escaped';


    public function table(Table $table): Table
    {
        return $table
            ->query(DischargedInmates::query()
                ->where('inmate_type', 'trial')
                ->where('mode_of_discharge', 'escape')
                ->orderBy('created_at', 'DESC'))
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
