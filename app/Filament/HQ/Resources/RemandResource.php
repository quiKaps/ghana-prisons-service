<?php

namespace App\Filament\HQ\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Remand;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\RemandTrial;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\HQ\Resources\RemandResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\HQ\Resources\RemandResource\RelationManagers;

class RemandResource extends Resource
{

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $modelLabel = 'Prisoners On Remand';

    protected static ?string $pluralModelLabel = 'All Remands';

    protected static ?string $navigationGroup = 'Remand and Trials';

    protected static ?string $navigationLabel = 'All Remands';

    protected ?string $subheading = "View and manage remand prisoners";

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
            //     ->where('detention_type', 'remand')
            //     ->where('next_court_date', '>=', now())
            //     ->where('is_discharged', false)
            //     ->orderBy('created_at', 'DESC'))
            ->columns([
            Tables\Columns\TextColumn::make('station.name')
                ->label('Station Name')
                ->searchable()
                ->sortable(),
            TextColumn::make('serial_number')
                ->weight(FontWeight::Bold)
                ->label('S.N.'),
            TextColumn::make('full_name')
                ->searchable()
                ->label("Name of Prisoner"),
            TextColumn::make('country_of_origin')
                ->badge()
                ->label('Nationality'),
            TextColumn::make('offense')
                ->badge()
                ->searchable()
                ->label('Offence'),
            TextColumn::make('admission_date')
                ->date()
                ->searchable()
                ->label('Date of Admission'),
            TextColumn::make('next_court_date')
                ->badge()
                ->color(
                    fn($state) =>
                    Carbon::parse($state)->isFuture() ? 'success' : 'danger'
                )
                ->label('Next Court Date')
                ->date(),
            TextColumn::make('court')
                ->searchable()
                ->label('Court of Committal'),
            ])
            ->filters([

            SelectFilter::make('station_id')
                ->label('Station')
                ->placeholder('Type Station Name')
                ->searchable()
                ->preload()
                ->options(fn() => \App\Models\Station::all()->pluck('name', 'id'))

        ], layout: FiltersLayout::AboveContent)

            ->actions([

                //dischrge start
                Action::make('Profile')
                    ->color('gray')
                    ->icon('heroicon-o-user')
                    ->label('Profile')
                    ->button()
                    ->color('blue')
                    ->url(fn(RemandTrial $record) => RemandResource::getUrl('view', ['record' => $record])),

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
            'index' => Pages\ListRemands::route('/'),
            'view' => Pages\ViewRemand::route('/{record}'),
        ];
    }
}
