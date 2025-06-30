<?php

namespace App\Filament\Station\Resources;

use Filament\Forms;
use Faker\Core\File;
use Filament\Tables;
use App\Models\Inmate;
use App\Models\Station;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\RemandTrial;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use App\Actions\SecureEditAction;
use App\Actions\SecureDeleteAction;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Illuminate\Support\Facades\Date;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\Session;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\CheckboxList;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Station\Resources\InmateResource\Pages;
use App\Filament\Station\Resources\InmateResource\RelationManagers;
use Dom\Text;
use Filament\Forms\Components\TextInput;

class InmateResource extends Resource

{
    protected static ?string $model = Inmate::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Inmate Management';

    protected static ?string $navigationLabel = 'Convicts List';

    protected static ?string $modelLabel = 'Convict List';

    protected static ?string $pluralModelLabel = 'Convicts List';

    public static function form(Form $form): Form
    {

        $remand = null;

        if (Session::has('remand_id')) {
            $remand = RemandTrial::find(Session::pull('remand_id')); // 👈 pull instead of get
            if ($remand) {
                Session::put('used_remand_id', $remand->id); // temporarily store it again for afterCreate
            }
        }
        return $form
            ->schema([
            Section::make("Penal Record")
                ->description('Please provide the penal information of the prisoner.')
                ->columns(2)
                ->schema([
                Group::make()
                    ->schema([
                    Group::make()
                    ->schema([
                        FileUpload::make('photo')
                            ->label('Prisoner Photo')
                                    ->placeholder("Upload Prisoner's Photo")
                                    ->visibility('public')
                                    ->image()
                                    ->openable()
                            ->uploadingMessage('Uploading photo...')->columnSpan(1)
                            ])->columns(2)
                    ])->columnSpanFull(),
                Forms\Components\TextInput::make('serial_number')
                    ->label('Serial Number')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->placeholder('Serial Number eg. NSM/01/25')
                    ->maxLength(255),
                Forms\Components\TextInput::make('full_name')
                    ->label("Name of Prisoner")
                    ->default($remand?->full_name)
                    ->required()
                    ->placeholder('Enter Full Name')
                    ->maxLength(255),
                Group::make()
                    ->schema([
                    Forms\Components\TextInput::make('age_on_admission')
                        ->minValue(16)
                        ->maxValue(100)
                        ->default($remand?->age_on_admission)
                        ->numeric()
                        ->label('Age on Admission')
                        ->required()
                        ->placeholder('Enter Age eg. 25'),
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
                    Forms\Components\DatePicker::make('EPD')
                        ->label('EPD (Earliest Possible Date of Discharge)')
                        ->minDate(now())
                        ->placeholder('Select EPD')
                        ->required(),
                    Forms\Components\DatePicker::make('LPD')
                        ->label('LPD (Latest Possible Date of Discharge)')
                        ->placeholder('Select LPD')
                        ->minDate(now())
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
                    Forms\Components\FileUpload::make('warrant_document')
                        ->label('Warrant Document')
                        ->columnSpan(2)
                        ->placeholder('Upload Warrant Document')
                        ->visibility('private')
                        ->multiple()
                        ->acceptedFileTypes(['application/pdf', 'png', 'jpg', 'jpeg'])
                        ->openable()
                                ->uploadingMessage('Uploading warrant document...'),
                        ])->columnSpanFull()
                        ->columns(3),
                ]),

            Section::make('Transfer-In Information')
                ->description('Please provide the transfer-in information of the prisoner.')
                ->columns(3)
                ->schema([
                    Forms\Components\Radio::make('transferred_inmate')
                        ->label('Transferred Inmate')
                        ->default('0')
                        ->columns(2)
                        ->live()
                        ->options([
                            '1' => 'Yes',
                            '0' => 'No',
                        ]),
                    Select::make('transferred_from_station_id')
                    ->label('Station Transferred From')
                    ->placeholder('Select Station Transferred From')
                        ->required(fn(Get $get): bool => $get('transferred_inmate') === '1')
                        ->hidden(fn(Get $get): bool => $get('transferred_inmate') !== '1')
                        ->options(
                            fn() => Station::withoutGlobalScopes()
                                ->where('id', '!=', auth()->user()->station_id)
                                ->pluck('name', 'id')
                                ->toArray()
                        )
                        ->searchable(),
                    DatePicker::make('date_of_transfer')
                        ->label('Transferred Date')
                        ->required(fn(Get $get): bool => $get('transferred_inmate') === '1')
                        ->hidden(fn(Get $get): bool => $get('transferred_inmate') !== '1')
                        ->placeholder('Select Transferred Date')
                        ->maxDate(now()),

                ]),
            Section::make('Disability Information')
                ->description('Please provide the disability information of the prisoner.')
                ->columns(3)
                ->schema([
                Forms\Components\Radio::make('disability')
                    ->label('Disability?')
                    ->live()
                    ->default('0')
                    ->options([
                        '1' => 'Yes',
                        '0' => 'No',
                    ])
                    ->inline(),
                Forms\Components\CheckboxList::make('disability_type')
                    ->label('Disability Type')
                    ->columns(2)
                    ->required(fn(Get $get): bool => $get('disability') === '1')
                    ->live()
                    ->hidden(fn(Get $get): bool => $get('disability') === '0')
                    ->options([
                        'hearing impairment' => 'Hearing Impairment',
                        'visual impairment' => 'Visual Impairment',
                        'speaking impairment' => 'Speaking Impairment',
                        'mobility impairment' => 'Mobility Impairment',
                        'others' => 'Others',
                    ]),
                Forms\Components\TextInput::make('disability_type_other')
                    ->label('Other Disability Type')
                    ->placeholder('Enter Other Disability Type')
                    ->hidden(fn(Get $get): bool => !in_array('others', (array) $get('disability_type')))
                    ->required(fn(Get $get): bool => $get('disability') === '1'),

                ]),
            Section::make('Social Background')
                ->description('Please provide the social background information of the prisoner.')
                ->schema([
                Forms\Components\TextInput::make('tribe')
                    ->label('Tribe')
                    ->placeholder('Enter Tribe (e.g. Ewe)'),
                Forms\Components\TagsInput::make('languages_spoken')
                    ->label('Languages Spoken')
                    ->placeholder('Enter Languages Spoken (optional)'),
                Forms\Components\TextInput::make('hometown')
                    ->label('Hometown')
                    ->placeholder('Enter Hometown (e.g. Kyebi)'),
                Forms\Components\Select::make('nationality')
                    ->options(config('countries'))
                    ->searchable()
                    ->default($remand?->country_of_origin)
                    ->placeholder('Select Nationality')
                    ->required()
                    ->label('Country of Origin'),
                Forms\Components\Select::make('married_status')
                    ->label('Marital Status')
                    ->options([
                        'single' => 'Single',
                        'married' => 'Married',
                        'divorced' => 'Divorced',
                        'widowed' => 'Widowed',
                    ]),

                Forms\Components\Select::make('education_level')
                    ->label('Education Background')
                    ->options([
                        'primary' => 'Primary',
                        'middle' => 'Middle Sch/JSS/JHS',
                        'secondary' => 'Sec./SSS/SHS/Tech/Vocation',
                        'tertiary' => 'Tertiary',
                        'no_formal' => 'No Formal',
                        'others' => 'Others'
                    ])
                    ->placeholder('Select Education Background'),

                Forms\Components\Select::make('religion')
                    ->label('Religious Background')
                    ->placeholder('Select a Religion')
                    ->options([
                        'christian' => 'Christian',
                        'muslim' => 'Muslim',
                        'traditionalist' => 'Traditionalist',
                        'other_religion' => 'Other Religion',
                        'no_religion' => 'No Religion',
                    ]),

                Forms\Components\TextInput::make('occupation')
                    ->label('Occupation')
                    ->placeholder('Enter Occupation')
                    ->maxLength(255),
                Forms\Components\TextInput::make('next_of_kin_name')
                    ->label('Name of Next of Kin')
                    ->placeholder('eg. Nana Kwame'),
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
                    ->label('Contact of Next of Kin')
                    ->placeholder('Enter Contact of Next of Kin')
                    ->tel()
                    ->maxLength(15),
                ])->columns(3),

            Section::make('Distinctive Body Marks')
                ->description('Please provide the distinctive body marks information of the prisoner.')
                ->columns(3)
                ->schema([
                CheckboxList::make('distinctive_marks')
                    ->label('Distinctive Marks')
                    ->columns(3)
                    ->live()
                    ->options([
                        'scars' => 'Scars',
                    'tattoos' => 'Tattoos',
                        'others' => 'Others',
                    ]),
                Forms\Components\TextInput::make('distinctive_marks_other')
                    ->label('Other Distinctive Marks')
                    ->placeholder('Enter Other Distinctive Marks')
                        ->hidden(fn(Get $get): bool => !in_array('others', (array) $get('distinctive_marks'))),
                    Forms\Components\TextInput::make('part_of_the_body')
                        ->label('Part of the Body')
                        ->placeholder('Enter Part of the Body'),
                ]),

            Section::make('Goaler Information')
                ->columns(2)
                ->label('Goaler Information')
                ->description('Please provide the goaler information of the prisoner.')
                ->schema([
                    Radio::make('goaler')
                        ->label('Goaler')
                        ->default('no')
                        ->inline()
                        ->live()
                        ->inlineLabel(false)
                        ->options([
                            'yes' => 'Yes',
                            'no' => 'No',
                        ])
                        ->required(),
                    FileUpload::make('goaler_document')
                        ->label('Goaler Document')
                        ->placeholder('Upload Goaler Document')
                        ->visibility('private')
                        ->acceptedFileTypes(['application/pdf', 'png', 'jpg', 'jpeg'])
                        ->openable()
                        ->uploadingMessage('Uploading goaler document...')
                        ->hidden(fn(Get $get): bool => $get('goaler') !== 'yes'),
                    ]),

            Section::make('Previous Conviction')
                ->description('Please provide the previous conviction information of the prisoner.')
                ->columns(3)
                ->schema([
                Radio::make('previous_conviction')
                    ->label('Previous Conviction')
                    ->default('no')
                    ->inline()
                    ->live()
                    ->columnSpanFull()
                    ->inlineLabel(false)
                    ->options([
                        'yes' => 'Yes',
                        'no' => 'No',
                    ])
                    ->required(),
                TextInput::make('previous_sentence')
                        ->label('Previous Sentence')
                        ->placeholder('Enter Previous Sentence')
                        ->maxLength(255)
                    ->hidden(fn(Get $get): bool => $get('previous_conviction') !== 'yes')
                    ->required(fn(Get $get): bool => $get('previous_conviction') === 'yes'),
                    TextInput::make('previous_conviction_id')
                        ->label('Previous Offence')
                    ->required(fn(Get $get): bool => $get('previous_conviction') === 'yes')
                    ->hidden(fn(Get $get): bool => $get('previous_conviction') !== 'yes')
                    ->placeholder('Enter Previous Offence'),
                    Select::make('previous_station_id')
                    ->label('Previous Station')
                    ->hidden(fn(Get $get): bool => $get('previous_conviction') !== 'yes')
                    ->required(fn(Get $get): bool => $get('previous_conviction') === 'yes')
                    ->placeholder('Select Previous Station')
                        ->options(
                            fn() => Station::withoutGlobalScopes()
                                ->where('id', '!=', auth()->user()->station_id)
                                ->pluck('name', 'id')
                                ->toArray()
                        )
                        ->searchable(),
                ]),

            Section::make('Police Information')
                    ->description('Please provide the police information of the prisoner.')
                    ->columns(2)
                    ->schema(
                [
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
                        ]
                    )->columns(3),

            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Inmate::query()
                    ->whereDate('LPD', '!=', now()->toDateString())
                    ->orderByDesc('created_at')
            )
            ->columns([
            Tables\Columns\TextColumn::make('serial_number')
                ->label('Serial Number')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('full_name')
                ->label("Prisoner's Name")
                ->searchable()
                ->sortable(),
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

            Tables\Actions\ActionGroup::make([
                Tables\Actions\ViewAction::make()
                    ->label('Profile')
                    ->icon('heroicon-o-user'),
                Action::make('Transfer')->icon('heroicon-o-arrow-right-on-rectangle'),
                Action::make('Additional Sentence')->icon('heroicon-o-plus-circle'),
                Action::make('Amnesty')->icon('heroicon-o-sparkles'),
                Action::make('Sentence Reduction')->icon('heroicon-o-arrow-trending-down'),
                SecureEditAction::make('edit', 'filament.admin.resources.inmates.edit')
                    ->modalWidth('md')
                    ->modalHeading('Protected Data Access')
                    ->modalDescription('This is a secure area of the application. Please confirm your password before continuing.')
                    ->label('Edit'),
                SecureDeleteAction::make('delete')
                    ->label('Delete'),
            ])
                ->button()
                ->label('More Actions'),
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
            'convicted-foriegners' => Pages\ConvictedForiegners::route('convicted-foriegners'),
            'index' => Pages\ListInmates::route('/'),
            'create' => Pages\CreateInmate::route('/create'),
            'view' => Pages\ViewInmate::route('/{record}'),
            'edit' => Pages\EditInmate::route('/{record}/edit'),
        ];
    }
}
