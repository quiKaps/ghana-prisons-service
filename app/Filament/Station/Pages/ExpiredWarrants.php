<?php

namespace App\Filament\Station\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Models\RemandTrial;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ActionGroup;

class ExpiredWarrants extends Page implements \Filament\Tables\Contracts\HasTable
{

    use \Filament\Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.station.pages.expired-warrants';

    protected static ?string $navigationGroup = 'Remand';

    protected static ?string $navigationLabel = 'Expired Warrants';

    protected static ?string $title = 'Expired Warrants';

    protected ?string $subheading = 'List of inmates with expired remand warrants';

    public function table(Table $table): Table
    {
        return $table
            ->query(RemandTrial::query()
                ->where('detention_type', 'remand')
                ->where('next_court_date', '<', now()))
            ->columns([
                TextColumn::make('serial_number')
                    ->label('Serial Number'),
                TextColumn::make('name')
                    ->searchable()
                    ->label('Inmate Name'),
                TextColumn::make('admission_date')
                    ->label('Admission Date')
                    ->date(),

                TextColumn::make('court')
                    ->label('Court'),
                TextColumn::make('next_court_date')
                    ->label('Next Court Date')
                    ->badge()
                    ->color('danger')
                    ->date(),
                TextColumn::make('country_of_origin')
                    ->label('Country of Origin'),

            TextColumn::make('police_officer')
                    ->label('Police Officer'),
                TextColumn::make('police_contact')
                    ->label('Police Contact'),

            ])
            ->filters([
                // Define any filters here if needed
            ])
            ->actions([
                ActionGroup::make([
                    // // Transfer Inmate Action
                    // EditAction::make()
                    //     ->successNotification(
                    //         Notification::make()
                    //             ->success()
                    //             ->title('Remand Updated')
                    //             ->body('The inmates remand has been updated successfully.'),
                    //     )

                    //     ->color('info')
                    //     ->icon('heroicon-m-arrow-right-start-on-rectangle')
                    //     ->modalHeading('Edit Remand Details')
                    //     ->label('Transfer Inmate')
                    //     ->form([
                    //         Section::make('Inmate Details')
                    //             ->columns(2)
                    //             ->schema([
                    //                 TextInput::make('serial_number')
                    //                     ->required()
                    //                     ->unique(ignoreRecord: true)
                    //                     ->placeholder('e.g. NSW/06/25')
                    //                     ->label('Serial Number'),
                    //                 TextInput::make('name')
                    //                     ->required()
                    //                     ->placeholder('e.g. Nana Kwame')
                    //                     ->label('Inmate Name'),
                    //                 TextInput::make('age_on_admission')
                    //                     ->numeric()
                    //                     ->minValue(15)
                    //                     ->placeholder('e.g. 30')
                    //                     ->required()
                    //                     ->label('Age on Admission'),
                    //                 Select::make('country_of_origin')
                    //                     ->options(config('countries'))
                    //                     ->searchable()
                    //                     ->required()
                    //                     ->label('Country of Origin'),
                    //                 DatePicker::make('admission_date')
                    //                     ->required()
                    //                     ->default(now())
                    //                     ->label('Admission Date'),

                    //                 Select::make('detention_type')
                    //                     ->options([
                    //                         'remand' => 'Remand',
                    //                         'trial' => 'Trial',
                    //                     ])
                    //                     ->required()
                    //                     ->label('Detention Type'),
                    //             ])->columns(2),
                    //         Section::make('Legal Details')
                    //             ->columns(2)
                    //             ->schema([
                    //                 TextInput::make('offense')
                    //                     ->required()
                    //                     ->maxLength(255)
                    //                     ->placeholder('e.g. Theft')
                    //                     ->label('Offense'),
                    //                 TextInput::make('court')
                    //                     ->required()
                    //                     ->placeholder('e.g. Kumasi Circuit Court')
                    //                     ->label('Court'),
                    //                 DatePicker::make('next_court_date')
                    //                     ->required()
                    //                     ->label('Next Court Date'),
                    //                 TextInput::make('police_station')
                    //                     ->required()
                    //                     ->placeholder('e.g. Central Police Station')
                    //                     ->label('Police Station'),
                    //                 TextInput::make('police_officer')
                    //                     ->label('Police Officer')
                    //                     ->placeholder('e.g. Inspector Kwesi Nyarko'),
                    //                 TextInput::make('police_contact')
                    //                     ->label('Police Contact')
                    //                     ->placeholder('e.g. 0241234567')
                    //                     ->tel(),
                    //             ]),
                    //         Section::make('Discharge Details')
                    //             ->columns(2)
                    //             ->schema([
                    //                 TextInput::make('date_of_discharge')
                    //                     ->required()
                    //                     ->maxLength(255)
                    //                     ->placeholder('e.g. 2023-12-31')
                    //                     ->label('Date of Discharge'),
                    //                 Select::make('mode_of_discharge')
                    //                     ->required()
                    //                     ->options([
                    //                         'discharged' => 'Discharged',
                    //                         'acquitted_and_discharged' => 'Acquitted and Discharged',
                    //                         'bail_bond' => 'Bail Bond',
                    //                         'escape' => 'Escape',
                    //                         'death' => 'Death',
                    //                         'other' => 'Other',
                    //                     ])
                    //                     ->label('Mode of Discharge'),
                    //             ])->columns(2),

                    //     ]),
                    // //Dischage Action
                    // EditAction::make()
                    //     ->successNotification(
                    //         Notification::make()
                    //             ->success()
                    //             ->title('Remand Updated')
                    //             ->body('The inmates remand has been updated successfully.'),
                    //     )

                    //     ->color('success')
                    //     ->icon('heroicon-m-arrow-right-start-on-rectangle')
                    //     ->modalHeading('Edit Remand Details')
                    //     ->label('Discharge')
                    //     ->form([
                    //         Section::make('Inmate Details')
                    //             ->columns(2)
                    //             ->schema([
                    //                 TextInput::make('serial_number')
                    //                     ->required()
                    //                     ->unique(ignoreRecord: true)
                    //                     ->placeholder('e.g. NSW/06/25')
                    //                     ->label('Serial Number'),
                    //                 TextInput::make('name')
                    //                     ->required()
                    //                     ->placeholder('e.g. Nana Kwame')
                    //                     ->label('Inmate Name'),
                    //                 TextInput::make('age_on_admission')
                    //                     ->numeric()
                    //                     ->minValue(15)
                    //                     ->placeholder('e.g. 30')
                    //                     ->required()
                    //                     ->label('Age on Admission'),
                    //                 Select::make('country_of_origin')
                    //                     ->options(config('countries'))
                    //                     ->searchable()
                    //                     ->required()
                    //                     ->label('Country of Origin'),
                    //                 DatePicker::make('admission_date')
                    //                     ->required()
                    //                     ->default(now())
                    //                     ->label('Admission Date'),

                    //                 Select::make('detention_type')
                    //                     ->options([
                    //                         'remand' => 'Remand',
                    //                         'trial' => 'Trial',
                    //                     ])
                    //                     ->required()
                    //                     ->label('Detention Type'),
                    //             ])->columns(2),
                    //         Section::make('Legal Details')
                    //             ->columns(2)
                    //             ->schema([
                    //                 TextInput::make('offense')
                    //                     ->required()
                    //                     ->maxLength(255)
                    //                     ->placeholder('e.g. Theft')
                    //                     ->label('Offense'),
                    //                 TextInput::make('court')
                    //                     ->required()
                    //                     ->placeholder('e.g. Kumasi Circuit Court')
                    //                     ->label('Court'),
                    //                 DatePicker::make('next_court_date')
                    //                     ->required()
                    //                     ->label('Next Court Date'),
                    //                 TextInput::make('police_station')
                    //                     ->required()
                    //                     ->placeholder('e.g. Central Police Station')
                    //                     ->label('Police Station'),
                    //                 TextInput::make('police_officer')
                    //                     ->label('Police Officer')
                    //                     ->placeholder('e.g. Inspector Kwesi Nyarko'),
                    //                 TextInput::make('police_contact')
                    //                     ->label('Police Contact')
                    //                     ->placeholder('e.g. 0241234567')
                    //                     ->tel(),
                    //             ]),
                    //         Section::make('Discharge Details')
                    //             ->columns(2)
                    //             ->schema([
                    //                 TextInput::make('date_of_discharge')
                    //                     ->required()
                    //                     ->maxLength(255)
                    //                     ->placeholder('e.g. 2023-12-31')
                    //                     ->label('Date of Discharge'),
                    //                 Select::make('mode_of_discharge')
                    //                     ->required()
                    //                     ->options([
                    //                         'discharged' => 'Discharged',
                    //                         'acquitted_and_discharged' => 'Acquitted and Discharged',
                    //                         'bail_bond' => 'Bail Bond',
                    //                         'escape' => 'Escape',
                    //                         'death' => 'Death',
                    //                         'other' => 'Other',
                    //                     ])
                    //                     ->label('Mode of Discharge'),
                    //             ])->columns(2),

                    //     ]),
                    // //Edit remand action
                ])
                ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(ActionSize::Small)
                ->color('green')
                    ->button()
            ]);
    }
}
