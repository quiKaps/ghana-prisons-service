<?php

namespace App\Filament\Station\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\RemandTrial;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Station\Resources\RemandTrialResource\Pages;
use App\Filament\Station\Resources\RemandTrialResource\RelationManagers;

class RemandTrialResource extends Resource
{
    protected static ?string $model = RemandTrial::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                                ->optimize('webp')
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
                    Forms\Components\TextInput::make('cell_id')
                        ->label('Block & Cell')
                        ->placeholder('e.g. A-101')
                        ,
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
            ->columns([
                Tables\Columns\TextColumn::make('serial_number')
                    ->sortable()
                ->label("S.N.")
                ->searchable(),
            Tables\Columns\TextColumn::make('full_name')
                    ->sortable()
                ->label("Prisoner's Name")
                ->searchable(),
                Tables\Columns\TextColumn::make('offense')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('admission_date')
                    ->date()
                ->sortable(),
                Tables\Columns\TextColumn::make('age_on_admission')
                    ->sortable(),
                Tables\Columns\TextColumn::make('court')
                    ->sortable()
                    ->searchable(),
            Tables\Columns\TextColumn::make('detention_type')
                    ->sortable(),
                Tables\Columns\TextColumn::make('next_court_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
            ]);
        // ->bulkActions([
        //     Tables\Actions\BulkActionGroup::make([
        //         Tables\Actions\DeleteBulkAction::make(),
        //     ]),
        // ]);
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
            'index' => Pages\ListRemandTrials::route('/'),
            'create' => Pages\CreateRemandTrial::route('/create'),
            'view' => Pages\ViewRemandTrial::route('/{record}'),
            'edit' => Pages\EditRemandTrial::route('/{record}/edit'),
            // 'discharged-remand' => Pages\RemandDischarge::route('/discharged-remand/{record}'),
            // 'discharged-trial' => Pages\TrialDischarge::route('/discharged-trial/{record}'),
        ];
    }
}
