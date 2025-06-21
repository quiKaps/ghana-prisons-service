<?php

namespace App\Filament\Station\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Inmate;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Station\Resources\InmateResource\Pages;
use App\Filament\Station\Resources\InmateResource\RelationManagers;

class InmateResource extends Resource
{
    protected static ?string $model = Inmate::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Inmate Management';

    protected static ?string $navigationLabel = 'All Inmates';

    protected static string | array $routeMiddleware = ['password.confirm'];



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Forms\Components\Section::make("Inmate's Personal Information")
                    ->schema([
                    Forms\Components\TextInput::make('serial_number')
                        ->label('Serial Number')
                        ->required()
                        ->placeholder('Serial Number eg. NSM/01/25')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('full_name')
                        ->label('Full Name')
                        ->required()
                        ->placeholder('Enter Full Name')
                        ->columnSpan(2)
                        ->maxLength(255),
                    Forms\Components\Select::make('gender')
                                    ->label('Gender')
                                    ->required()
                                    ->options([
                                        'male' => 'Male',
                                        'female' => 'Female',
                                    ]),
                                Forms\Components\Select::make('married_status')
                                    ->label('Marital Status')
                                    ->options([
                                        'single' => 'Single',
                                        'married' => 'Married',
                                        'divorced' => 'Divorced',
                                        'widowed' => 'Widowed',
                                    ]),
                                Forms\Components\TextInput::make('age_on_admission')
                                    ->minValue(16)
                                    ->maxValue(100)
                                    ->numeric()
                                    ->label('Age on Admission')
                                    ->required()
                        ->placeholder('Enter Age eg. 25'),
                                Forms\Components\DatePicker::make('date_of_birth')
                                    ->label('Date of Birth')
                                    ->required()
                                    ->minDate(now()->subYears(100))
                                    ->maxDate(now()->subYears(16)),
                    Forms\Components\Select::make('religion')
                        ->label('Religion')
                                    ->placeholder('Select a Religion')
                                    ->options([
                                        'christian' => 'Christian',
                                        'muslim' => 'Muslim',
                                        'traditionalist' => 'Traditionalist',
                                        'other_religion' => 'Other Religion',
                                        'no_religion' => 'No Religion',
                                    ]),
                                Forms\Components\TextInput::make('nationality')
                                    ->label('Nationality')
                                    ->required()
                                    ->placeholder('Enter Nationality (e.g. Ghanaian)'),
                                Forms\Components\TextInput::make('tribe')
                                    ->label('Tribe')

                        ->placeholder('Enter Tribe (e.g. Ewe)'),
                                Forms\Components\TextInput::make('hometown')
                                    ->label('Hometown')

                        ->placeholder('Enter Hometown (e.g. Kyebi)'),
                            ])->columns(3),
                        Forms\Components\Section::make('Legal & Offence Details')
                            ->schema([
                                Forms\Components\Select::make('offence')
                                    ->required()
                                    ->label('Offence')
                                    ->placeholder('Select an Offence')
                                    ->options([
                                        'assault' => 'Assault',
                                        'causing_harm' => 'Causing Harm',
                                        'defilement' => 'Defilement',
                                        'defrauding' => 'Defrauding by False Pretence',
                                        'manslaughter' => 'Manslaughter',
                                        'murder' => 'Murder',
                                        'robbery' => 'Robbery',
                                        'stealing' => 'Stealing',
                                        'unlawful_damage' => 'Unlawful Damage',
                                        'unlawful_entry' => 'Unlawful Entry',
                                        'others' => 'Others'
                                    ]),
                                Forms\Components\TextInput::make('sentence')
                                    ->label('Sentence')
                                    ->required()
                                    ->placeholder('Enter Sentence')
                                    ->maxLength(255),
                                Forms\Components\DatePicker::make('admission_date')
                                    ->label('Date of Admission')
                        ->required()
                                    ->default(now()),
                                Forms\Components\DatePicker::make('date_sentenced')
                                    ->label('Date of Sentence')
                        ->required(),
                                Forms\Components\TextInput::make('court_of_committal')
                                    ->label('Court of Committal')
                                    ->required()
                                    ->placeholder('Enter Court of Committal')
                                    ->maxLength(255)
                                    ->columnSpan(2),
                                Forms\Components\Select::make('cell_id')
                                    ->label('Select Inmate Cell')
                                    ->required()
                                    ->relationship(
                                        'cell',
                                        'id',
                                        fn($query) => $query->orderBy('block')->orderBy('cell_number')
                                    )
                                    ->getOptionLabelFromRecordUsing(fn($record) => "CELL {$record->cell_number} - {$record->block}")
                                    ->searchable(['cell_number', 'block']),
                    Forms\Components\DatePicker::make('EPD')
                                    ->label('EPD (Estimated Prison Discharge)')
                        ->minDate(now())
                        ->placeholder('Enter EPD (optional)'),
                    Forms\Components\DatePicker::make('LPD')
                                    ->label('LPD (Legal Prison Discharge)')
                        ->minDate(now())
                                    ->required(),
                            ])->columns(3),
                        Forms\Components\Section::make('Police & Legal Authorities')
                            ->schema([
                                Forms\Components\TextInput::make('police_name')
                                    ->required()
                                    ->label('Police Name')
                                    ->placeholder('Enter Police Officer Name'),
                                Forms\Components\TextInput::make('police_station')
                                    ->label('Police Station')
                                    ->required()
                                    ->placeholder('Enter Police Station')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('police_contact')
                                    ->label('Police Contact')
                                    ->placeholder('Enter Police Contact'),
                                Radio::make('goalder')
                                    ->label('Goaler?')
                        ->live()
                                    ->default('no')
                                    ->options([
                                        'yes' => "Yes",
                                        'no' => "No"
                                    ])
                                    ->inline(),
                                Forms\Components\FileUpload::make('goaler_document')
                                    ->label('Goaler Document')
                        ->hidden(fn(Get $get) => $get('goalder') === 'no')
                                    ->placeholder('Upload Goaler Document')
                                    ->visibility('private')
                                    ->multiple()
                                    ->acceptedFileTypes(['application/pdf', 'png', 'jpg', 'jpeg'])
                                    ->openable()
                                    ->uploadingMessage('Uploading goaler document...')
                                    ->columnSpan(3),
                                Forms\Components\FileUpload::make('warrant_document')
                                    ->label('Warrant Document')
                                    ->placeholder('Upload Warrant Document')
                                    ->visibility('private')
                                    ->multiple()
                                    ->acceptedFileTypes(['application/pdf', 'png', 'jpg', 'jpeg'])
                                    ->openable()
                                    ->uploadingMessage('Uploading warrant document...')
                                    ->columnSpan(3),
                            ])->columns(3)
                    ])->columnSpan(2),
                Group::make()
                    ->schema([
                        Forms\Components\Section::make('Medical & Health Information')
                            ->schema([

                                Forms\Components\TagsInput::make('medical_conditions')
                                    ->label('Medical Conditions')
                                    ->placeholder('Enter Medical Conditions (optional)'),
                                Forms\Components\TagsInput::make('allergies')
                                    ->label('Allergies')
                                    ->placeholder('Enter Allergies (optional)'),
                    Forms\Components\Radio::make('disability')
                        ->label('Disability?')
                        ->live()
                        ->default('0')
                        ->options([
                            '1' => 'Yes',
                            '0' => 'No',
                        ])
                        ->inline(),
                    Forms\Components\Select::make('disability_type')
                        ->label('Disability Type')
                        ->required()
                        ->hidden(fn(Get $get): bool => $get('disability') === '0')
                                    ->options([
                                        'hearing impairment' => 'Hearing Impairment',
                                        'visual impairment' => 'Visual Impairment',
                                        'speaking impairment' => 'Speaking Impairment',
                                        'mobility impairment' => 'Mobility Impairment',
                                        'others' => 'Others',
                                    ]),
                            ]),
                        Forms\Components\Section::make('Identification & Biometric Data')
                            ->schema([
                                Forms\Components\FileUpload::make('photo')
                                    ->label('Inmate Photo')
                                    ->placeholder('Upload Inmate Photo')
                                    ->visibility('private')
                        ->image()
                                    ->openable()
                                    ->uploadingMessage('Uploading inmate photo...'),
                                Forms\Components\FileUpload::make('fingerprint')
                                    ->label('Inmate Fingerprint')
                                    ->placeholder('Upload Inmate Fingerprint')
                                    ->visibility('private')
                        ->image()
                                    ->openable()
                                    ->uploadingMessage('Uploading inmate fingerprint...'),
                                Forms\Components\FileUpload::make('signature')
                                    ->label('Inmate Signature')
                                    ->placeholder('Upload Inmate Signature')
                                    ->visibility('private')
                        ->image()
                                    ->openable()
                                    ->uploadingMessage('Uploading inmate signature...'),
                                Forms\Components\TextInput::make('distinctive_marks')
                                    ->label('Distinctive Marks')
                                    ->required()
                                    ->placeholder('Enter Distinctive Marks')
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Section::make('Educational & Occupational Information')
                            ->schema([
                                Forms\Components\Select::make('education_level')
                                    ->label('Education Level')
                                    ->options([
                                        'primary' => 'Primary',
                                        'middle' => 'Middle Sch/JSS/JHS',
                                        'secondary' => 'Sec./SSS/SHS/Tech/Vocation',
                                        'tertiary' => 'Tertiary',
                                        'no_formal' => 'No Formal',
                                        'others' => 'Others'
                                    ])
                                    ->placeholder('Select Education Level (optional)'),
                                Forms\Components\TextInput::make('occupation')
                                    ->label('Occupation')
                                    ->placeholder('Enter Occupation (optional)'),
                                Forms\Components\TagsInput::make('languages_spoken')
                                    ->label('Languages Spoken')
                                    ->placeholder('Enter Languages Spoken (optional)'),
                            ]),
                        Forms\Components\Section::make('Next of Kin Information')
                            ->schema([
                                Forms\Components\TextInput::make('next_of_kin_name')
                                    ->label('Next of Kin Name')
                                    ->placeholder('Enter Next of Kin Name'),
                                Forms\Components\Select::make('next_of_kin_relationship')
                                    ->label('Next of Kin Relationship')
                                    ->placeholder('Select Relationship')
                                    ->options([
                                        'father' => 'Father',
                                        'mother' => 'Mother',
                                        'brother' => 'Brother',
                                        'sister' => 'Sister',
                                        'spouse' => 'Spouse',
                                        'child' => 'Child',
                                        'friend' => 'Friend',
                                        'other' => 'Other',
                                    ]),
                                Forms\Components\TextInput::make('next_of_kin_contact')
                                    ->label('Next of Kin Contact')
                                    ->placeholder('Enter Next of Kin Contact')
                                    ->tel()
                                    ->maxLength(15)
                            ]),
                    ]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('serial_number')
                ->label('Serial Number')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('surname')
                ->label('Full Name')
                ->searchable()
                ->sortable()
                ->formatStateUsing(
                    fn($record) =>
                    $record->surname . ', ' . $record->first_name . ' ' . $record->middle_name
                ),
            Tables\Columns\TextColumn::make('gender')
                ->searchable()
                ->sortable()
                ->formatStateUsing(fn(string $state): string => ucfirst($state)),
            Tables\Columns\TextColumn::make('age_on_admission')
                ->label('Age')
                ->sortable(),
            Tables\Columns\TextColumn::make('sentence')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('admission_date')
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('cell.cell_number')
                ->label('Cell')
                ->searchable()
                ->sortable()
                ->formatStateUsing(
                    fn($record) => $record->cell ? "CELL {$record->cell->cell_number} - {$record->cell->block}" : 'No Cell Assigned'
                ),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListInmates::route('/'),
            'create' => Pages\CreateInmate::route('/create'),
            'view' => Pages\ViewInmate::route('/{record}'),
            'edit' => Pages\EditInmate::route('/{record}/edit'),
        ];
    }
}
