<?php

namespace App\Filament\Station\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Inmate;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ConvictDischarge;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Station\Resources\ConvictDischargeResource\Pages;
use App\Filament\Station\Resources\ConvictDischargeResource\RelationManagers;

class ConvictDischargeResource extends Resource
{

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Convicts Discharge';

    protected static ?string $navigationGroup = 'Convicts';

    protected static ?string $modelLabel = 'Convicts Discharge';

    protected ?string $subheading = 'List of inmates scheduled for discharge tomorrow';

    protected static ?string $model = Inmate::class;

    public static function getLabel(): string
    {
        return class_basename(static::$model);
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Inmate::scheduledForDischargeToday()->orderByDesc('created_at'))
            ->emptyStateHeading('No Prisoners Available for Discharge')
            ->emptyStateDescription('There are currently no prisoners available for discharge today.')
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
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->button()
                    ->label('Profile')
                    ->icon('heroicon-o-user')
                    ->color('primary'),
                Action::make('Discharge')
                    ->color('green')
                    ->button()
                    ->icon('heroicon-m-arrow-right-start-on-rectangle')
                    ->modalHeading('Convict Discharge')
                    ->modalSubmitActionLabel("Discharge Convict")
                    ->action(function (array $data, $record) {
                        app(\App\Services\DischargeService::class)
                            ->dischargeInmate($record, $data);
                        Notification::make()
                            ->success()
                            ->title('Inmate Discharged')
                            ->body("{$record->full_name} has been discharged successfully.")
                            ->send();
                    })
                    ->label('Discharge')
                    ->fillForm(fn(Inmate $record): array => [
                        'serial_number' => $record->serial_number,
                        'full_name' => $record->full_name,
                        'age_on_admission' => $record->age_on_admission,
                        'detention_type' => $record->detention_type,
                        'country_of_origin' => $record->country_of_origin,
                        'offense' => $record->offense,
                        'court' => $record->court,
                        'next_court_date' => $record->next_court_date,
                        'police_station' => $record->police_station,
                        'police_officer' => $record->police_officer,
                        'police_contact' => $record->police_contact,
                        //'date_of_discharge' => $record->date_of_discharge,
                    ])
                    ->form([
                        Section::make('Convict Details')
                            ->columns(2)
                            ->schema([
                                TextInput::make('serial_number')
                                    ->readOnly()
                                    ->label('Serial Number'),
                                TextInput::make('full_name')
                                    ->readOnly()
                                    ->label('Convict Name'),
                            ])->columns(2),
                        Section::make('Legal Details')
                            ->columns(2)
                            ->schema([
                                TextInput::make('offense')
                                    ->readOnly()
                                    ->label('Offense'),
                                TextInput::make('court')
                                    ->placeholder('e.g. Kumasi Circuit Court')
                                    ->label('Court'),
                                DatePicker::make('lpd')
                                    ->readOnly()
                                    ->label('LPD'),
                                TextInput::make('police_station')
                                    ->readOnly()
                                    ->label('Police Station'),
                                TextInput::make('police_officer')
                                    ->label('Police Officer')
                                    ->readOnly(),
                                TextInput::make('police_contact')
                                    ->label('Police Contact')
                                    ->readOnly(),
                            ]),
                        Section::make('Discharge Details')
                            ->columns(2)
                            ->schema([
                                DatePicker::make('date_of_discharge')
                                    ->required()
                                    ->default(now())
                                    ->placeholder('e.g. 2023-12-31')
                                    ->label('Date of Discharge'),
                                Select::make('mode_of_discharge')
                                    ->required()
                                    ->options([
                                        'discharged' => 'Discharged',
                                        'acquitted_and_discharged' => 'Acquitted and Discharged',
                                        'bail_bond' => 'Bail Bond',
                                        'escape' => 'Escape',
                                        'death' => 'Death',
                                        'other' => 'Other',
                                    ])
                                    ->label('Mode of Discharge'),
                            ])->columns(2),
                    ]),
            ])
            ->bulkActions([

                Tables\Actions\DeleteBulkAction::make(),

            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConvictDischarges::route('/'),
            'create' => Pages\CreateConvictDischarge::route('/create'),
            'view' => Pages\ViewConvictDischarge::route('/{record}'),
            'edit' => Pages\EditConvictDischarge::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return Inmate::query()
            ->whereDate('lpd', now()->addDay()->toDateString())
            ->count();
    }



    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'The number of inmates available for discharge tomorrow';
    }
}
