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
                TextColumn::make('admission_date')
                    ->label('Admission Date')
                    ->date(),
                TextColumn::make('court')
                    ->label('Court'),
                TextColumn::make('next_court_date')
                    ->label('Next Court Date')
                    ->badge()
                    ->color('success')
                    ->date(),
                TextColumn::make('police_name')
                    ->label('Police Officer'),
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
                                    ->required(),
                                TextInput::make('full_name')
                                    ->label("Prisoner's Full Name")
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
                    ->modalHeading('Re-Admit InmatePrisoner')
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
