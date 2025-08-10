<?php

namespace App\Filament\Station\Resources;

use Dom\Text;
use Filament\Forms;
use Faker\Core\File;
use Filament\Tables;
use App\Models\Inmate;
use App\Models\Station;
use Filament\Forms\Get;
use Filament\Forms\Form;
use App\Models\Discharge;
use Filament\Tables\Table;
use App\Models\RemandTrial;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use App\Actions\SecureEditAction;
use App\Actions\SecureDeleteAction;
use Filament\Tables\Actions\Action;
use Illuminate\Validation\Rules\In;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Illuminate\Support\Facades\Session;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Station\Resources\InmateResource\Pages;
use App\Filament\Station\Resources\InmateResource\RelationManagers\SentencesRelationManager;

class InmateResource extends Resource

{
    protected static ?string $model = Inmate::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Convicts';

    protected static ?string $navigationLabel = 'All Convicts';

    protected static ?string $modelLabel = 'Convict List';

    protected static ?string $pluralModelLabel = 'Convicts List';

    public static function form(Form $form): Form
    {

        $remand = null;

        if (Session::has('remand_id')) {
            $remand = RemandTrial::find(Session::pull('remand_id')); // 👈 pull prisoner on trial id form sessiin
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
                        FileUpload::make('prisoner_picture')
                            ->label('Prisoner Photo')
                            ->optimize('webp')
                            ->placeholder("Upload Prisoner's Picture")
                                    ->visibility('public')
                            ->previewable()
                            ->default($remand?->picture)
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
                        ->live()
                        ->default($remand?->offense)
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

                        ->placeholder('Select EPD'),
                    Forms\Components\DatePicker::make('LPD')
                        ->label('LPD (Latest Possible Date of Discharge)')
                        ->placeholder('Select LPD'),
                    Forms\Components\TextInput::make('court_of_committal')
                        ->label('Court of Committal')
                        ->required()
                        ->default($remand?->court)
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
                        ->default($remand?->police_officer)
                        ->label('Police Name')
                                ->placeholder('Enter Police Officer Name'),
                    Forms\Components\TextInput::make('police_station')
                        ->label('Police Station')
                        ->default($remand?->police_station)
                        ->placeholder('Enter Police Station')
                                ->maxLength(255),
                    Forms\Components\TextInput::make('police_contact')
                                ->label('Police Contact')
                        ->default($remand?->police_contact)
                        ->placeholder('Enter Police Contact'),
                        ]
                    )->columns(3),

            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('latestSentenceByDate.total_sentence')
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
            //
        ])
            ->actions([

            Tables\Actions\ActionGroup::make([
                Tables\Actions\ViewAction::make()
                    ->label('Profile')
                    ->icon('heroicon-o-user'),

                //transfer action
                Action::make('transfer')
                    ->label('Transfer')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('success')
                    ->fillForm(fn(Inmate $record): array => [
                        'serial_number' => $record->serial_number,
                        'full_name' => $record->full_name,
                    'sentence' => $record->latestSentenceByDate->sentence,
                    'offence' => $record->latestSentenceByDate->offence,
                    ])->form([
                        Group::make()
                            ->columns(2)
                            ->schema([
                                TextInput::make('serial_number')
                                    ->readOnly(),
                                TextInput::make('full_name')
                                    ->label("Prisoner's Name")
                                    ->readonly(),
                                TextInput::make('sentence')
                                    ->label('Sentence')
                                    ->readonly(),
                                TextInput::make('offence')
                                    ->label('Offence')
                                    ->readonly(),
                                DatePicker::make('date_of_transfer')
                                    ->label('Date of Transfer')
                                    ->default(now())
                                    ->maxDate(now())
                                    ->required(),
                                Select::make('station_transferred_to_id')
                                    ->label('Transfer To: (Station)')
                                    ->required()
                                    ->options(
                                        fn() => Station::withoutGlobalScopes()
                                ->where('id', '!=', Auth::user()->station_id)
                                            ->pluck('name', 'id')
                                            ->toArray()
                                    )
                                    ->searchable(),
                        TextInput::make('reason')
                            ->label('Reason for Transfer')
                            ->placeholder('Enter reason for transfer'),
                    ])
                ])
                    ->modalHeading('Prisoner Transfer')
                    ->modalSubmitActionLabel('Transfer Prisoner')
                    ->action(function (array $data, Inmate $record): void {
                    try {
                        \Illuminate\Support\Facades\DB::transaction(function () use ($data, $record) {
                            \App\Models\Transfer::create([
                                'inmate_id' => $record->id,
                                'from_station_id' => Auth::user()->station_id,
                                'to_station_id' => $data['station_transferred_to_id'],
                                'transfer_date' => $data['date_of_transfer'],
                                'status' => 'completed',
                                'reason' => $data['reason'],
                                'requested_by' => Auth::id(),
                                'approved_by' => null,
                                'rejected_by' => null,
                            ]);
                            $record->update([
                                'transferred_out' => true,
                                'station_transferred_to_id' => $data['station_transferred_to_id'],
                                'date_transferred_out' => $data['date_of_transfer'],
                            ]);
                            //if use online, i will have to set inmate transfered in as 1 and station transfered from

                        });

                        Notification::make()
                            ->success()
                            ->title('Transfer Request Submitted')
                            ->body("The transfer request for {$record->full_name} has been submitted.")
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->danger()
                            ->title('Transfer Failed')
                            ->body('An error occurred: ' . $e->getMessage())
                            ->send();
                    }
                    }),
                //transfer action end

                // special discharge action
                Action::make('special_discharge')
                    ->label('Special Discharge')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('info')
                    ->fillForm(fn(Inmate $record): array => [
                    'serial_number' => $record->serial_number,
                    'full_name' => $record->full_name,
                    'sentence' => $record->latestSentenceByDate->sentence,
                    'offence' => $record->latestSentenceByDate->offence,
                    ])->form([
                        Group::make()
                            ->columns(2)
                            ->schema([
                                TextInput::make('serial_number')
                                    ->readOnly(),
                                TextInput::make('full_name')
                                    ->label("Prisoner's Name")
                                    ->readonly(),
                                TextInput::make('sentence')
                                    ->label('Sentence')
                                    ->readonly(),
                                TextInput::make('offence')
                                    ->label('Offence')
                                    ->readonly(),
                                Select::make('mode_of_discharge')
                                    ->label('Mode of Discharge')
                                    ->options([
                                        'amnesty' => 'Amnesty',
                                        'fine_paid' => 'Fine Paid',
                                        'presidential_pardon' => 'Presidential Pardon',
                                        'acquitted_and_discharged' => 'Acquitted and Discharged',
                                        'bail_bond' => 'Bail Bond',
                                        'reduction_of_sentence' => 'Reduction of Sentence',
                                        'escape' => 'Escape',
                                        'death' => 'Death',
                                        'one_third_remission' => '1/3 Remission',
                                        'other' => 'Other',
                                    ])
                                    ->required(),
                                DatePicker::make('date_of_discharge')
                                    ->label('Date of Discharge')
                                    ->default(now())
                                    ->maxDate(now())
                                    ->required(),
                                FileUpload::make('discharge_document')
                                    ->label('Discharge Document')
                                    ->placeholder('Upload Discharge Document')
                                    ->visibility('private')
                                    ->acceptedFileTypes(['application/pdf', 'png', 'jpg', 'jpeg'])
                                    ->openable()
                                    ->previewable()
                                    ->uploadingMessage('Uploading discharge document...'),

                            ])

                    ])

                    ->modalHeading('Special Discharge')
                    ->modalSubmitActionLabel('Discharge Prisoner')
                    ->action(function (array $data, Inmate $record): void {
                    try {
                        \Illuminate\Support\Facades\DB::transaction(function () use ($data, $record) {
                            \App\Models\Discharge::create([
                                'station_id' => $record->station_id,
                                'inmate_id' => $record->id,
                                'discharge_type' => $data['mode_of_discharge'],
                                'discharge_date' => $data['date_of_discharge'],
                                //'reason' => $data['reason'],
                                'discharge_document' => $data['discharge_document'],
                                'discharged_by' => Auth::id(),
                            ]);

                            $record->update([
                                'is_discharged' => true,
                                'mode_of_discharge' => $data['mode_of_discharge'],
                                'date_of_discharge' => $data['date_of_discharge'],
                            ]);

                            //if use online, i will have to set inmate transfered in as 1 and station transfered from
                        });

                        Notification::make()
                            ->success()
                            ->title('Discharge Successful')
                            ->body("{$record->full_name} has been discharged successfully.")
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->danger()
                            ->title('Discharge Failed')
                            ->body('An error occurred: ' . $e->getMessage())
                            ->send();
                    }
                    }),
                // special discharge action end

                //sentence reduction action
                Action::make('sentence_reduction')
                    ->label('Sentence Reduction')
                    ->icon('heroicon-o-arrow-trending-down')
                    ->color('success')
                    ->fillForm(fn(Inmate $record): array => [
                        'serial_number' => $record->serial_number,
                        'full_name' => $record->full_name,
                    'sentence' => $record->latestSentenceByDate->sentence,
                    'offence' => $record->latestSentenceByDate->offence,
                    'date_of_sentence' => Carbon::parse($record->sentences->first()->date_of_sentence)->format('Y-m-d')
                    ])->form([
                        Group::make()
                            ->columns(2)
                            ->schema([
                                TextInput::make('serial_number')
                                    ->readOnly(),
                                TextInput::make('full_name')
                                    ->label("Prisoner's Name")
                                    ->readonly(),
                                TextInput::make('sentence')
                                    ->label('Sentence')
                                    ->readonly(),
                                TextInput::make('offence')
                                    ->label('Offence')
                                    ->readonly(),
                                TextInput::make('reduced_sentence')
                                    ->label('Reduced Sentence')
                                    ->placeholder('Enter Reduced Sentence')
                                    ->required(),
                        TextInput::make('date_of_sentence')
                            ->label('Date of Sentence')
                            ->placeholder('Enter Date Sentence')
                            ->readOnly(),
                        TextInput::make('court_of_committal')
                            ->label('Appellate Court')
                            ->placeholder('Enter Appellate Court')
                            ->required(),
                                DatePicker::make('EPD')
                                    ->label('EPD (Earliest Possible Date of Discharge)')
                                    ->required(),
                                DatePicker::make('LPD')
                                    ->label('LPD (Latest Possible Date of Discharge)')
                                    ->required(),
                        FileUpload::make('warrant_document')
                                    ->label('Upload Document')
                            ->placeholder('Upload Warrant Document')
                                    ->visibility('private')
                                    ->acceptedFileTypes(['application/pdf', 'png', 'jpg', 'jpeg'])
                                    ->openable()
                                    ->previewable()
                            ->uploadingMessage('Uploading warrant document...'),
                            ])

                    ])

                    ->modalHeading('Sentence Reduction')
                    ->modalSubmitActionLabel('Reduce Sentence')
                    ->action(function (array $data, Inmate $record): void {
                    try {
                        \Illuminate\Support\Facades\DB::transaction(function () use ($data, $record) {
                            \App\Models\Sentence::create([
                                'inmate_id' => $record->id,
                                'sentence' => $data['reduced_sentence'],
                                'offence' => $data['offence'],
                                'date_of_sentence' => $data['date_of_sentence'],
                                'total_sentence' => $data['reduced_sentence'], //this is redundant
                                'court_of_committal' => $data['court_of_committal'],
                                'EPD' =>  $data['EPD'],
                                'LPD' => $data['LPD'],
                                'warrant_document' => $data['warrant_document'],
                            ]);
                        });

                        //check if EPD is today or past after sentence was reduced

                        if ($data['EPD'] <= today()) {

                            $record->update([
                                'mode_of_discharge' => 'Reduced Sentence',
                                'date_of_discharge' => today(),
                                'is_discharged' => true,
                            ]);

                            Discharge::create([
                                'station_id' => $record->station_id,
                                'inmate_id' => $record->id,
                                'discharge_type' => 'reduction_of_sentence',
                                'discharge_date' => today(),
                                //'reason' => $data['reason'],
                            ]);
                        }

                        Notification::make()
                            ->success()
                            ->title('Reduced Sentence Success')
                            ->body("The reduced sentence for {$record->full_name} has been completed.")
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->danger()
                            ->title('Reduced Sentence Failed')
                            ->body('An error occurred: ' . $e->getMessage()) // edit the error message
                            ->send();
                    }
                    }),
                //sentence reduction action end

                // additional sentence action
                Action::make('additional_sentence')
                    ->label('Additional Sentence')
                    ->icon('heroicon-o-plus-circle')
                    ->color('warning')
                    ->fillForm(fn(Inmate $record): array => [
                    'serial_number' => $record->serial_number,
                    'full_name' => $record->full_name,
                    'date_of_sentence' => $record->sentences->first()->date_of_sentence
                    ])->form([
                        Group::make()
                            ->columns(2)
                            ->schema([
                                TextInput::make('serial_number')
                                    ->readOnly(),
                                TextInput::make('full_name')
                                    ->label("Prisoner's Name")
                                    ->readonly(),
                                TextInput::make('sentence')
                                    ->label('Sentence')
                            ->placeholder('Enter Sentence')
                                    ->required(),
                                TextInput::make('offence')
                                    ->label('Offence')
                            ->placeholder('Enter Offence')
                                    ->required(),
                        TextInput::make('date_of_sentence')
                            ->label('Date of Sentence')
                            ->placeholder('Enter Date Sentence')
                            ->readOnly(),
                        //rectify 
                        TextInput::make('total_sentence')
                            ->label('Total Sentence')
                            ->placeholder('Enter Total Sentence') //should be the sum of the current sentence and the additional sentence
                            ->required(),
                        TextInput::make('court_of_committal')
                            ->label('Court of Committal')
                            ->placeholder('Enter Court of Committal')
                            ->required(),
                                DatePicker::make('EPD')
                            ->label('EPD (Earliest Possible Date of Discharge)'),
                                DatePicker::make('LPD')
                            ->label('LPD (Latest Possible Date of Discharge)'),
                        FileUpload::make('warrant_document')
                                    ->label('Upload Document')
                            ->placeholder('Upload Warrant Document')
                                    ->visibility('private')
                                    ->acceptedFileTypes(['application/pdf', 'png', 'jpg', 'jpeg'])
                                    ->openable()
                                    ->previewable()
                            ->uploadingMessage('Uploading warrant document...'),
                            ])

                    ])

                    ->modalHeading('Additional Sentence')
                    ->modalSubmitActionLabel('Add Sentence')
                    ->action(function (array $data, Inmate $record): void {
                    try {
                        \Illuminate\Support\Facades\DB::transaction(function () use ($data, $record) {
                            \App\Models\Sentence::create([
                                'inmate_id' => $record->id,
                                'sentence' => $data['sentence'],
                                'offence' => $data['offence'],
                                'total_sentence' => $data['total_sentence'], //this is redundant
                                'court_of_committal' => $data['court_of_committal'],
                                'date_of_sentence' => $record->sentences->first()->date_of_sentence,
                                'EPD' =>  $data['EPD'],
                                'LPD' => $data['LPD'],
                                'warrant_document' => $data['warrant_document'],
                            ]);
                        });

                        Notification::make()
                            ->success()
                            ->title('Additional Sentence Success')
                            ->body("The additional sentence for {$record->full_name} has been completed.")
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->danger()
                            ->title('Additional Sentence Failed')
                            ->body('An error occurred: ' . $e->getMessage()) // edit the error message
                            ->send();
                    }
                    }),
                // additional sentence action end

                // amnesty action
                //this form is for convicts who are comdemned to death or life imprisonment only show for those inmates)

                Action::make('amnesty')
                    ->label('Amnesty')
                    ->icon('heroicon-o-sparkles')
                    ->color('danger')
                    ->fillForm(fn(Inmate $record): array => [
                        'serial_number' => $record->serial_number,
                        'full_name' => $record->full_name,
                    'sentence' => $record->latestSentenceByDate->sentence,
                    'offence' => $record->latestSentenceByDate->offence,

                    ])->form([
                        Group::make()
                            ->columns(2)
                            ->schema([
                                TextInput::make('serial_number')
                                    ->readOnly(),
                                TextInput::make('full_name')
                                    ->label("Prisoner's Name")
                                    ->readonly(),
                                TextInput::make('sentence')
                                    ->label('Sentence')
                            ->readOnly(),
                                TextInput::make('offence')
                                    ->label('Offence')
                            ->readOnly(),
                                Select::make('commutted_sentence')
                                    ->label('Commutted Sentence')
                                    ->live()
                            ->options([
                                'life' => 'Life',
                                '20yrs_ihl' => '20 Years',
                                'others' => 'Others',
                            ])
                            ->required(),
                        Select::make('commutted_by')
                            ->label('Commuted By')
                            ->options([
                                'amnesty' => 'Amnesty',
                                'others' => 'Others',
                            ]),
                        DatePicker::make('EPD')
                            ->label('EPD (Earliest Possible Date of Discharge)')
                            ->visible(fn(Get $get): bool => $get('commutted_sentence') == '20yrs_ihl')
                            // ->dehydrated(fn(Get $get) => $get('commutted_sentence') == '20yrs_ihl')
                            ->required(fn(Get $get): bool => $get('commutted_sentence') == '20yrs_ihl'),
                        DatePicker::make('LPD')
                            ->label('LPD (Latest Possible Date of Discharge)')
                            ->visible(fn(Get $get): bool => $get('commutted_sentence') == '20yrs_ihl')
                            //->dehydrated(fn(Get $get) => $get('commutted_sentence') == '20yrs_ihl')
                            ->required(fn(Get $get): bool => $get('commutted_sentence') == '20yrs_ihl'),
                                DatePicker::make('date_of_amnesty')
                                    ->label('Date of Amnesty')
                                    ->required()
                                    ->default(now()),
                        FileUpload::make('amnesty_document')
                                    ->label('Upload Document')
                            ->placeholder('Upload Document')
                                    ->visibility('private')
                                    ->acceptedFileTypes(['application/pdf', 'png', 'jpg', 'jpeg'])
                                    ->openable()
                                    ->previewable()
                            ->uploadingMessage('Uploading document...'),
                            ])

                ])
                    ->modalHeading('Convict Amnesty')
                    ->modalSubmitActionLabel('Grant Amnesty')
                    ->action(function (array $data, Inmate $record): void {
                    try {
                        \Illuminate\Support\Facades\DB::transaction(function () use ($data, $record) {
                            \App\Models\Sentence::create([
                                'inmate_id' => $record->id,
                                'sentence' => $data['commutted_sentence'],
                                'offence' => $data['offence'],
                                'commutted_sentence' => $data['commutted_sentence'],
                                'total_sentence' => $data['commutted_sentence'],
                                'commutted_by' => $data['commutted_by'],
                                'EPD' => array_key_exists('EPD', $data) && $data['EPD'] !== '' ? $data['EPD'] : null,
                                'LPD' => array_key_exists('LPD', $data) && $data['LPD'] !== '' ? $data['LPD'] : null,
                                'date_of_amnesty' => $data['date_of_amnesty'],
                                'amnesty_document' => $data['amnesty_document'],
                            ]);
                        });

                        Notification::make()
                            ->success()
                            ->title('Amnesty Success')
                            ->body("The amnesty for {$record->full_name} has been completed.")
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->danger()
                            ->title('Amnesty Failed')
                            ->body('An error occurred: ' . $e->getMessage()) // edit the error message
                            ->send();
                    }
                    }),

                // amnesty action end
                SecureEditAction::make('edit', 'filament.station.resources.inmates.edit')
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
            SentencesRelationManager::class
        ];
    }



    protected function getTableQuery(): Builder
    {
        return Inmate::query()
            ->with('latestSentenceByDate') // Eager load for display
            ->where('is_discharged', false)
            ->whereHas('latestSentenceByDate', function (Builder $query) {
                $query->whereNull('EPD')
                    ->orWhere('EPD', '>', now()->toDateString());
            })
            ->orderByDesc('created_at');
    }

    public static function getPages(): array
    {
        return [
            //'convicted-foriegners' => Pages\ConvictedForiegners::route('convicted-foriegners'),
            'index' => Pages\ListInmates::route('/'),
            'create' => Pages\CreateInmate::route('/create'),
            'view' => Pages\ViewInmate::route('/{record}'),
            'edit' => Pages\EditInmate::route('/{record}/edit'),
            'upcoming-discharge' => Pages\ListUpcomingDischarge::route('upcoming')
        ];
    }

    //show resource navigation to only prison_admin
    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        return $user?->user_type === 'prison_admin';
    }
}
