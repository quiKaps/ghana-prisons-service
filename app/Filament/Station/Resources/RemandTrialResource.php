<?php

namespace App\Filament\Station\Resources;

use App\Filament\Station\Resources\RemandTrialResource\Pages;
use App\Filament\Station\Resources\RemandTrialResource\RelationManagers;
use App\Models\RemandTrial;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RemandTrialResource extends Resource
{
    protected static ?string $model = RemandTrial::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->columnSpan(2)
                    ->schema([
                        Section::make('Inmate Details')
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('serial_number')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('e.g. NSW/06/25')
                                    ->label('Serial Number'),
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->placeholder('e.g. Nana Kwame')
                                    ->label('Inmate Name'),
                                Forms\Components\TextInput::make('age_on_admission')
                                    ->numeric()
                                    ->minValue(15)
                                    ->placeholder('e.g. 30')
                                    ->required()
                                    ->label('Age on Admission'),
                                Forms\Components\Select::make('country_of_origin')
                                    ->options(config('countries'))
                                    ->searchable()
                                    ->required()
                                    ->label('Country of Origin'),
                                Forms\Components\DatePicker::make('admission_date')
                                    ->required()
                                    ->default(now())
                                    ->label('Admission Date'),

                                Forms\Components\Select::make('detention_type')
                                    ->options([
                                        'remand' => 'Remand',
                                        'trial' => 'Trial',
                                    ])
                                    ->required()
                                    ->label('Detention Type'),
                            ]),

                        Section::make('Legal Details')
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('offense')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g. Theft')
                                    ->label('Offense'),
                                Forms\Components\TextInput::make('court')
                                    ->required()
                                    ->placeholder('e.g. Kumasi Circuit Court')
                                    ->label('Court'),
                                Forms\Components\DatePicker::make('next_court_date')
                                    ->required()
                                    ->label('Next Court Date'),
                                Forms\Components\TextInput::make('police_station')
                                    ->required()
                                    ->placeholder('e.g. Central Police Station')
                                    ->label('Police Station'),
                                Forms\Components\TextInput::make('police_officer')
                                    ->label('Police Officer')
                                    ->placeholder('e.g. Inspector Kwesi Nyarko'),
                                Forms\Components\TextInput::make('police_contact')
                                    ->label('Police Contact')
                                    ->placeholder('e.g. 0241234567')
                                    ->tel(),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('serial_number')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
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
            'index' => Pages\ListRemandTrials::route('/'),
            'create' => Pages\CreateRemandTrial::route('/create'),
            'view' => Pages\ViewRemandTrial::route('/{record}'),
            'edit' => Pages\EditRemandTrial::route('/{record}/edit'),
        ];
    }
}
