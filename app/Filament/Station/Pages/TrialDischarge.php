<?php

namespace App\Filament\Station\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Models\DischargedInmates;
use Filament\Tables\Actions\Action;
use App\Services\ReAdmissionService;
use Filament\Forms\Components\Group;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;

class TrialDischarge extends Page implements \Filament\Tables\Contracts\HasTable
{
    use \Filament\Tables\Concerns\InteractsWithTable;

    protected static string $view = 'filament.station.pages.trial-discharge';

    protected static ?string $navigationLabel = 'Discharged Trials';

    protected static ?string $title = 'Trials Discharge';

    protected ?string $subheading = 'Manage and track inmates discharged from trials';

    protected static ?string $model = DischargedInmates::class;

    protected static ?string $navigationGroup = 'Remand and Trials';

    public function table(Table $table): Table
    {
        return $table
            ->query(DischargedInmates::query()
                ->where('inmate_type', 'trial')
                ->orderBy('created_at', 'DESC'))
            ->emptyStateHeading('Station has no disharged prisoners')
            ->emptyStateIcon('heroicon-s-user')
            ->columns([
                TextColumn::make('serial_number')
                    ->weight(FontWeight::Bold)
                    ->label('S.N.'),
                TextColumn::make('full_name')
                    ->searchable()
                    ->label("Prisoner's Name"),
            TextColumn::make('country_of_origin')
                ->label('Nationality'),
                TextColumn::make('court')
                ->label('Court of Committal'),
            TextColumn::make('offense')
                ->badge()
                ->label('Offence'),
            TextColumn::make('mode_of_discharge')
                ->label('Mode of Discharge')
                ->badge()
                ->color(fn($state) => match ($state) {
                    'discharged' => 'success',
                    'acquitted_and_discharged' => 'primary',
                    'bail_bond' => 'info',
                    'escape' => 'danger',
                    'death' => 'gray',
                    'other' => 'secondary',
                    default => 'secondary',
                })
                ->formatStateUsing(fn($state) => match ($state) {
                    'discharged' => 'Discharged',
                    'acquitted_and_discharged' => 'Acquitted and Discharged',
                    'bail_bond' => 'Bail Bond',
                    'escape' => 'Escape',
                    'death' => 'Death',
                    'other' => 'Other',
                    default => ucfirst($state),
                }),
            TextColumn::make('date_of_discharge')
                ->label('Date of Discharge')
                    ->badge()
                    ->color('success')
                ->date(),
            ])
            ->filters([
                // Define any filters here if needed
            ])
            ->actions([]);
    }
}
