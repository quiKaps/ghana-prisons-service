<?php

namespace App\Filament\HQ\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Station;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\HQ\Resources\StationResource\Pages;
use App\Filament\HQ\Resources\StationResource\RelationManagers;

class StationResource extends Resource
{
    protected static ?string $model = Station::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Prisons Facility Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->placeholder('e.g. Nsawam Medium Security Prison')
                            ->label('Prison Facility Name')
                            ->unique(Station::class, 'name', ignoreRecord: true)
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $state, Forms\Set $set) {
                                $set('slug', Str::slug($state));
                            }),
                        Forms\Components\TextInput::make('slug')
                            ->placeholder('This will be auto-generated from the name')
                            ->unique(Station::class, 'slug', ignoreRecord: true)
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('code')
                            ->label('Prison Code')
                            ->placeholder('e.g. NSW')
                            ->required()
                            ->unique(Station::class, 'code', ignoreRecord: true)
                            ->maxLength(50),
                        Forms\Components\Select::make('category')
                            ->required()
                            ->placeholder('Select Prison Category')
                            ->label('Prison Category')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                            ]),
                        Forms\Components\Select::make('region')

                            ->placeholder('Select Region')
                            ->label('Prison Region')
                            ->searchable()
                            ->options([
                                'ahafo' => 'Ahafo',
                                'ashanti' => 'Ashanti',
                                'bono' => 'Bono',
                                'bono east' => 'Bono East',
                                'central' => 'Central',
                                'eastern' => 'Eastern',
                                'greater accra' => 'Greater Accra',
                                'north east' => 'North East',
                                'northern' => 'Northern',
                                'oti' => 'Oti',
                                'savannah' => 'Savannah',
                                'upper east' => 'Upper East',
                                'upper west' => 'Upper West',
                                'volta' => 'Volta',
                                'western' => 'Western',
                                'western north' => 'Western North',
                            ]),
                        Forms\Components\TextInput::make('city')
                            ->label('City/Town')
                            ->placeholder('e.g. Nsawam')
                            ->helperText('The city or town where the prison facility is located')
                            ->required()
                            ->maxLength(50),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('No Stations Found')
            ->emptyStateIcon('heroicon-o-building-library')
            ->emptyStateDescription('You can create a new station by clicking the button below.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make('Create Station')
                    ->label('Create New Station')
                    ->icon('heroicon-o-plus-circle')
                    ->color('primary'),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Prison Facility Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->label('Prison Code')
                    ->searchable()
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Category')
                    ->searchable()
                ->badge()
                ->color(fn(string $state): string => match ($state) {
                    'male' => 'success',
                    'female' => 'danger',
                    default => 'primary',
                })
                    ->sortable(),
                Tables\Columns\TextColumn::make('region')
                    ->label('Region')
                    ->searchable()
                    ->sortable(),

            ])
            ->filters([
            Filter::make('male_prisons')
                ->label('Male Prisons')
                ->query(fn(Builder $query): Builder => $query->where('category', 'male')),
            Filter::make('female_prisons')
                ->label('Female Prisons')
                ->query(fn(Builder $query): Builder => $query->where('category', 'female')),
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
            'index' => Pages\ListStations::route('/'),
            'create' => Pages\CreateStation::route('/create'),
            'view' => Pages\ViewStation::route('/{record}'),
            'edit' => Pages\EditStation::route('/{record}/edit'),
        ];
    }
}
