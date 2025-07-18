<?php

namespace App\Filament\HQ\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Inmate;
use App\Models\Convict;
use App\Models\Station;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\HQ\Resources\ConvictResource\Pages;
use App\Filament\HQ\Resources\ConvictResource\RelationManagers;
use App\Filament\HQ\Resources\ConvictResource\RelationManagers\SentencesRelationManager;

class ConvictResource extends Resource
{
    protected static ?string $model = Inmate::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Convicts';

    protected static ?string $navigationLabel = 'All Convicts';

    protected static ?string $modelLabel = 'All Convict';

    protected static ?string $pluralModelLabel = 'All Convicts';

    public static function form(Form $form): Form
    {

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
                                        FileUpload::make('prisoner_picture')
                                            ->label('Prisoner Photo')
                                            ->placeholder("Upload Prisoner's Picture")
                                            ->visibility('public')
                                            ->previewable()
                                            ->downloadable()
                                            ->image()
                                            ->acceptedFileTypes(['image/png', 'image/jpg', 'image/jpeg'])
                                            ->openable()
                                            ->uploadingMessage('Uploading picture...')->columnSpan(1)
                                    ])->columns(2)
                            ])->columnSpanFull(),
                        Forms\Components\TextInput::make('serial_number')
                            ->label('Serial Number')

                            ->unique(ignoreRecord: true)
                            ->placeholder('Serial Number eg. NSM/01/25')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('full_name')
                            ->label("Name of Prisoner")

                            ->required()
                            ->placeholder('Enter Full Name')
                            ->maxLength(255),
                        Group::make()
                    ->schema([
                                Forms\Components\TextInput::make('age_on_admission')
                                    ->minValue(16)
                                    ->maxValue(100)

                                    ->numeric()
                                    ->label('Age on Admission')
                                    ->required()
                                    ->placeholder('Enter Age eg. 25'),
                                Forms\Components\Select::make('offence')
                                    ->required()
                                    ->label('Offence')
                                    ->live()

                                    ->placeholder('Select an Offence')
                                    ->options([
                                        'assault' => 'Assault',
                                        'causing_harm' => 'Causing Harm',
                                        'defilement' => 'Defilement',
                                        'defrauding' => 'Defrauding by False Pretence',
                                        'manslaughter' => 'Manslaughter',
                                        'murder' => 'Murder',
                                        'narcotics' => 'Narcotics',
                                        'robbery' => 'Robbery',
                                        'stealing' => 'Stealing',
                                        'unlawful_damage' => 'Unlawful Damage',
                                        'unlawful_entry' => 'Unlawful Entry',
                                        'others' => 'Others'
                                    ]),
                                Forms\Components\TextInput::make('other_offence')
                                    ->label('Other Offence')
                                    ->required()
                                    ->hidden(fn(Get $get): bool => !in_array('others', (array) $get('offence')))
                                    ->placeholder('Enter Other Offence')
                                    ->maxLength(255),
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
                                    ->readOnly(fn(string $context) => $context === 'edit')
                                    ->required(fn(string $context) => $context === 'create'),
                                Forms\Components\DatePicker::make('EPD')
                                    ->label('EPD (Earliest Possible Date of Discharge)')
                                    ->minDate(now())
                                    ->placeholder('Select EPD'),
                                Forms\Components\DatePicker::make('LPD')
                                    ->label('LPD (Latest Possible Date of Discharge)')
                                    ->placeholder('Select LPD')
                                    ->minDate(now()),
                                Forms\Components\TextInput::make('court_of_committal')
                                    ->label('Court of Committal')
                                    ->required()

                                    ->placeholder('Enter Court of Committal')
                                    ->maxLength(255),
                                Forms\Components\Select::make('cell_id')
                                    ->label('Block & Cell')
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
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->downloadable()
                                    ->helperText('Only PDF files are allowed for upload.')
                                    ->previewable()
                                    ->uploadingMessage('Uploading warrant document...'),
                            ])->columnSpanFull()
                            ->columns(3),
                    ]),

                Section::make('Transfer-In Information')
                    ->description('Please provide the transfer-in information of the prisoner.')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Radio::make('transferred_in')
                            ->label('Transferred Inmate')
                            ->default(0)
                            ->columns(2)
                            ->live()
                            ->options([
                                1 => 'Yes',
                                0 => 'No',
                            ]),
                        Select::make('station_transferred_from_id')
                            ->label('Station Transferred From')
                            ->placeholder('Select Station Transferred From')
                            ->required(fn(Get $get): bool => $get('transferred_in') == 1)
                            ->hidden(fn(Get $get): bool => $get('transferred_in') != 1)
                            ->options(
                                fn() => Station::withoutGlobalScopes()
                                    ->where('id', '!=', Auth::user()->station_id)
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->searchable(),
                        DatePicker::make('date_transferred_in')
                            ->label('Transferred Date')
                            ->required(fn(Get $get): bool => $get('transferred_in') == 1)
                            ->hidden(fn(Get $get): bool => $get('transferred_in') != 1)
                            ->placeholder('Select Transferred Date')
                            ->maxDate(now()),

                    ]),
                Section::make('Disability Information')
                    ->description('Please provide the disability information of the prisoner.')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Radio::make('disability')
                            ->label('Disability?')
                            ->default(fn($record) => $record?->disability ?? false)
                            ->columns(2)
                            ->live()
                            ->options([
                                true => 'Yes',
                                false => 'No',
                            ]),
                        TagsInput::make('disability_type')
                            ->hidden(fn(Get $get): bool => $get('disability') == false)
                            ->required(fn(Get $get): bool => $get('disability') == true)
                            ->placeholder('Select or Enter Disability Type')
                            ->helperText('Press enter after typing to add a disability type.')
                            ->label('Disability Type')
                            ->suggestions([
                                'hearing impairment' => 'Hearing Impairment',
                                'visual impairment' => 'Visual Impairment',
                                'speaking impairment' => 'Speaking Impairment',
                                'mobility impairment' => 'Mobility Impairment',
                            ]),
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
                                'separated' => 'Separated',
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
                            ->live()
                            ->placeholder('Select a Religion')
                            ->options([
                                'christian' => 'Christian',
                                'muslim' => 'Muslim',
                                'traditionalist' => 'Traditionalist',
                                'other_religion' => 'Other Religion',
                                'no_religion' => 'No Religion',
                            ]),

                        TextInput::make('religion_other')
                            ->label('Other Religion')
                            ->placeholder('Enter Other Religion')
                            ->hidden(fn(Get $get): bool => !in_array('other_religion', (array) $get('religion')))
                            ->required(fn(Get $get): bool => $get('religion') == 'other_religion'),

                        Forms\Components\TextInput::make('occupation')
                            ->label('Occupation')
                            ->placeholder('Enter Occupation')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('next_of_kin_name')
                            ->label('Name of Next of Kin')
                            ->placeholder('eg. Nana Kwame'),
                        Forms\Components\TextInput::make('next_of_kin_relationship')
                            ->label('Next of Kin Relationship')
                            ->placeholder('eg. Brother, Sister, Spouse'),
                        Forms\Components\TextInput::make('next_of_kin_contact')
                            ->label('Contact of Next of Kin')
                            ->placeholder('Enter Contact of Next of Kin')
                            ->tel()
                            ->maxLength(15),
                    ])->columns(3),

                Section::make('Distinctive Body Marks')
                    ->description('Please provide the distinctive body marks information of the prisoner.')
                    ->columns(2)
                    ->schema([

                        TagsInput::make('distinctive_marks')
                            ->helperText('Select all that apply or type and press ENTER when you are done')
                            ->label('Distinctive Marks')
                            ->placeholder('Enter Distinctive Marks')
                            ->suggestions([
                                'scars' => 'Scars',
                                'tribal_marks' => 'Tribal Marks',
                                'birthmarks' => 'Birthmarks',
                                'tattoos' => 'Tattoos',
                                'others' => 'Others',
                            ]),

                        Forms\Components\TextInput::make('part_of_the_body')
                            ->hidden(fn(Get $get): bool => empty($get('distinctive_marks')))
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
                            ->default(0)
                            ->inline()
                            ->live()
                            ->inlineLabel(false)
                            ->options([
                                1 => 'Yes',
                                0 => 'No',
                            ])
                            ->required(),
                        FileUpload::make('goaler_document')
                            ->label('Goaler Document')
                            ->placeholder('Upload Goaler Document')
                            ->visibility('private')
                            ->acceptedFileTypes(['application/pdf', 'png', 'jpg', 'jpeg'])
                            ->openable()
                            ->previewable()
                            ->multiple()
                            ->uploadingMessage('Uploading goaler document...')
                            ->hidden(fn(Get $get): bool => $get('goaler') != 1),
                    ]),
                Section::make('Previous Conviction')
                    ->description('Please provide the previous conviction information of the prisoner.')
                    ->columnSpanFull()
                    ->schema([
                        Radio::make('previously_convicted')
                            ->label('Previous Conviction')
                            ->default(0)
                            ->inline()
                            ->live()
                            ->columnSpanFull()
                            ->inlineLabel(false)
                            ->options([
                                1 => 'Yes',
                                0 => 'No',
                            ])
                            ->required(),


                    ]),
                Repeater::make('previous_convictions')
                    ->addActionLabel('Add Previous Conviction Details')
                    ->defaultItems(1)
                    ->columnSpanFull()
                    ->hidden(fn(Get $get): bool => $get('previously_convicted') != 1)
                    ->schema([
                        Group::make()
                            ->columns(3)
                            ->schema([
                                TextInput::make('previous_sentence')
                                    ->label('Previous Sentence')
                                    ->placeholder('Enter Previous Sentence')
                                    ->maxLength(255)
                                    ->columnSpan(1)
                                    ->required(),
                                TextInput::make('previous_offence')
                                    ->label('Previous Offence')
                                    ->required()
                                    ->columnSpan(1)
                                    ->placeholder('Enter Previous Offence'),
                                Select::make('previous_station_id')
                                    ->label('Previous Station')
                                    ->required()
                                    ->columnSpan(1)
                                    ->placeholder('Select Previous Station')
                                    ->options(
                                        fn() => Station::withoutGlobalScopes()
                                            ->where('id', '!=', (Auth::user()?->station_id ?? null))
                                            ->where('category', (Auth::user()?->station?->category))
                                            ->pluck('name', 'id')
                                            ->toArray()
                                    )
                                    ->searchable(),
                            ])
                    ]),

                Section::make('Police Information')
                    ->description('Please provide the police information of the prisoner.')
                    ->columns(2)
                    ->schema(
                        [
                            Forms\Components\TextInput::make('police_name')
                                ->label('Police Name')
                                ->placeholder('Enter Police Officer Name'),
                            Forms\Components\TextInput::make('police_station')
                                ->label('Police Station')
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


            ->columns([
            Tables\Columns\TextColumn::make('station.name')
                ->label('Station Name')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('serial_number')
                ->label('Serial Number')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('full_name')
                ->label("Name of Prisoner")
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('age_on_admission')
                ->label('Age on Admission')
                ->sortable(),
            Tables\Columns\TextColumn::make('earliestSentenceByDate.offence')
                ->label('Offence')
                ->badge()
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('latestSentenceByDate.sentence')
                ->label('Sentence')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('earliestSentenceByDate.date_of_sentence')
                ->label('Date of Sentence')
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('admission_date')
                ->label('Date of Admission')
                ->date()
                ->sortable(),
            ])
            ->filters([
            SelectFilter::make('station_id')
                ->label('Station')
                ->placeholder('Type Station Name')
                ->searchable()
                ->preload()
                ->options(fn() => \App\Models\Station::all()->pluck('name', 'id')),


        ], layout: FiltersLayout::AboveContent)
            ->actions([
            Tables\Actions\ViewAction::make()
                ->label('Profile')
                ->button()
                ->color('blue')
                ->icon('heroicon-o-user'),
        ])


            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                //Tables\Actions\DeleteBulkAction::make(),
            ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SentencesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConvicts::route('/'),
            'view' => Pages\ViewConvict::route('/{record}'),

        ];
    }
}
