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
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Concerns\InteractsWithTable;

class EscapeesOnTrial extends Page implements HasTable
{
    use \Filament\Tables\Concerns\InteractsWithTable;

    protected static string $view = 'filament.station.pages.escapees-on-trial';

    protected static ?string $navigationGroup = 'Escapees';

    protected static ?string $navigationLabel = 'Escape List - Trial';

    protected ?string $heading = 'Escape List - Trial';

    protected ?string $subheading = 'List of prisoners who were on trial but have escaped';


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
                ->label("Prisoner's Name"),
            TextColumn::make('country_of_origin')
                ->label('Nationality'),
            TextColumn::make('court')
                ->label('Court of Committal'),
            TextColumn::make('offense')
                ->badge()
                ->weight(FontWeight::Bold)
                ->extraAttributes(['style' => 'font-size: 2.25rem;'])
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
            ->actions([
                Action::make('readmit')
                    ->label('Re-Admit')
                    ->button()
                    ->icon('heroicon-o-arrow-uturn-right')
                    ->color('success')
                    ->fillForm(fn(DischargedInmates $record): array => [
                        'serial_number' => $record->serial_number,
                        'full_name' => $record->full_name,
                    ])
                    ->form([
                        Group::make()
                            ->columns(2)
                            ->schema([
                                TextInput::make('serial_number')
                        ->label("Serial Number")
                        ->readonly(),
                    TextInput::make('full_name')
                        ->label("Prisoner's Full Name")
                        ->readonly(),
                                DatePicker::make('readmission_date')
                                    ->label('Re-Admission Date')
                                    ->default(now())
                                    ->maxDate(now())
                                    ->required(),
                                DatePicker::make('next_court_date')
                                    ->label('Next Court Date')
                                    ->default(now())
                                    ->minDate(now())
                                    ->required(),
                            ])
                    ])
                ->modalHeading('Re-Admit Prisoner')
                ->modalSubmitActionLabel('Re-Admit Prisoner')
                    ->action(function ($data, $record) {
                        app(ReAdmissionService::class)->readmitRemandTrial($record->id, $data);
                        Notification::make()
                            ->success()
                            ->title('Re-Admission Successful')
                            ->body("The {$record->full_name} has been re-admitted on {$record->detention_type}.")
                            ->send();
                    })

            ]);
    }
}
