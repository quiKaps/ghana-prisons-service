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
use Filament\Tables\Contracts\HasTable;

class ConvictedEscapees extends Page implements HasTable
{
    use \Filament\Tables\Concerns\InteractsWithTable;

    protected static string $view = 'filament.station.pages.convicted-escapees';

    protected static ?string $navigationGroup = 'Convicts';

    protected static ?string $navigationLabel = 'Escape List - Convict';

    public function table(Table $table): Table
    {
        return $table
            ->query(DischargedInmates::query()
                ->where('inmate_type', 'convict')
                ->where('mode_of_discharge', 'escape')
                ->orderBy('created_at', 'DESC'))
            ->emptyStateHeading('No Escapee Remands')
            ->emptyStateDescription('No recorded escaped remand prisoners')
            ->columns([
                TextColumn::make('serial_number')
                    ->weight(FontWeight::Bold)
                    ->label('S.N.'),
                TextColumn::make('full_name')
                    ->searchable()
                ->label('Prisoner Name'),
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
                                    ->readOnly()
                                    ->required(),
                                TextInput::make('full_name')
                        ->label("Prisoner's Name")
                                    ->readonly()
                                    ->required(),
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
