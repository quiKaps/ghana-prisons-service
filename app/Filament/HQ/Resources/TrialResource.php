<?php

namespace App\Filament\HQ\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Trial;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\RemandTrial;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\HQ\Resources\TrialResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\HQ\Resources\TrialResource\RelationManagers;

class TrialResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $modelLabel = 'Prisoners On Trial';

    protected static ?string $pluralModelLabel = 'All Trials';

    protected static ?string $navigationGroup = 'Remand and Trials';

    protected static ?string $navigationLabel = 'All Trials';

    protected ?string $subheading = "View and manage trial prisoners";

    protected static ?string $model = RemandTrial::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Group::make()->columnSpan(3)
                    ->schema([
                        Section::make('Prisoner Details')
                            ->columns(2)
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                FileUpload::make("picture")
                                                    ->label("Prisoner's Picture")
                                                    ->acceptedFileTypes(['image/jpeg', 'image/png'])
                                                    ->helperText('Only jpeg and png files are allowed for upload.')
                                                    ->previewable()
                                                    ->downloadable()
                                                    ->openable()
                                                    ->columnSpan(1)
                                            ])->columns(2)
                                    ])->columnSpan(2),
                                Forms\Components\TextInput::make('serial_number')

                                    ->unique(ignoreRecord: true)
                                    ->placeholder('e.g. NSW/06/25')
                                    ->label('Serial Number'),
                                Forms\Components\TextInput::make('full_name')
                                    ->required()
                                    ->placeholder('e.g. Nana Kwame')
                                    ->label("Name of Prisoner"),
                                Forms\Components\TextInput::make('offense')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g. Theft')
                                    ->label('Offence'),
                                Forms\Components\DatePicker::make('admission_date')
                                    ->required()
                                    ->default(now())
                                    ->label('Date of Admission'),
                                Forms\Components\DatePicker::make('next_court_date')
                                    ->required()
                                    ->minDate('now')
                                    ->label('Next Court Date'),
                                Forms\Components\TextInput::make('court')
                                    ->required()
                                    ->placeholder('e.g. Kumasi Circuit Court')
                                    ->label('Court of Committal'),
                                Group::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('age_on_admission')
                                            ->numeric()
                                            ->minValue(15)
                                            ->placeholder('e.g. 30')
                                            ->required()
                                            ->label('Age on Admission'),
                                        Forms\Components\Radio::make('gender')
                                            ->label('Gender')
                                            ->inline()
                                            ->disabled()
                                            ->default(Auth::user()->station?->category)
                                            ->dehydrated()
                                            ->inlineLabel(false)
                                            ->options([
                                                'male' => "Male",
                                                'female' => 'Female'
                                            ])
                                            ->required()
                                    ])->columns(2),
                                Forms\Components\Select::make('detention_type')
                                    ->options([
                                        RemandTrial::TYPE_REMAND => 'Remand',
                                        RemandTrial::TYPE_TRIAL => 'Trial',
                                    ])
                                    ->placeholder('Select remand or trial')
                                    ->required()
                                    ->label('Detention Type'),
                                Forms\Components\Select::make('country_of_origin')
                                    ->options(config('countries'))
                                    ->searchable()
                                    ->placeholder('Select Country of Origin')
                                    ->required()
                                    ->label('Country of Origin'),
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
                                FileUpload::make('warrant')
                                    ->label("Upload Warrant")
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->helperText('Only PDF files are allowed for upload.')
                                    ->previewable()
                                    ->downloadable()
                                    ->openable()
                            ]),
                        Section::make('Police Information')
                            ->columns(3)
                            ->schema([
                                Forms\Components\TextInput::make('police_officer')
                                    ->label('Police Officer')
                                    ->placeholder('e.g. Inspector Kwesi Nyarko'),
                                Forms\Components\TextInput::make('police_contact')
                                    ->label('Police Contact')
                                    ->placeholder('e.g. 0241234567')
                                    ->tel(),
                                Forms\Components\TextInput::make('police_station')
                                    ->placeholder('e.g. Central Police Station')
                                    ->label('Police Station'),
                            ]),
                    ]),
            ])->columns(
                3
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            // ->query(RemandTrial::query()
            //     ->where('detention_type', 'trial')
            //     //->where('next_court_date', '>=', now())
            //     ->where('is_discharged', false)
            //     ->orderBy('created_at', 'DESC'))
            ->emptyStateHeading('No prisoner On Trial Found')
            ->emptyStateDescription('Station has no prisoners on trial yet...')
            ->emptyStateIcon('heroicon-s-user')
            ->columns([
            TextColumn::make('serial_number')
                ->weight(FontWeight::Bold)
                ->label('S.N.'),
            TextColumn::make('full_name')
                ->searchable()
                ->label("Prisoner's Name"),
            TextColumn::make('offense')
                ->badge()
                ->label('Offense'),
            TextColumn::make('admission_date')
                ->label('Admitted On')
                ->date(),
            TextColumn::make('next_court_date')
                ->label('Next Court Date')
                ->badge()
                ->color('success')
                ->date(),
            TextColumn::make('court')
                ->label('Court of Committal'),
            ])
            ->filters([
            // Define any filters here if needed
        ])
            ->actions([

                //profile starts
                Action::make('Profile')
                    ->color('gray')
                    ->icon('heroicon-o-user')
                    ->label('Profile')
                    ->button()
                    ->color('blue')
                    ->url(fn(RemandTrial $record) => TrialResource::getUrl('view', ['record' => $record])),

                //profile ends
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
            'index' => Pages\ListTrials::route('/'),
            'view' => Pages\ViewTrial::route('/{record}'),
        ];
    }
}
