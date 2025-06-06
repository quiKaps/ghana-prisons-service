<?php

namespace App\Filament\Station\Resources;

use Filament\Forms;
use App\Models\Cell;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Station\Resources\CellResource\Pages;
use App\Filament\Station\Resources\CellResource\RelationManagers;

class CellResource extends Resource
{
    protected static ?string $model = Cell::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $navigationGroup = 'Cell Management';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Group::make()
                ->schema(
                    [
                        Forms\Components\Section::make('Add Cell Information')
                            ->schema([
                        Forms\Components\TextInput::make('cell_number')
                            ->label('Cell Number')
                            ->required()
                            ->numeric()
                            ->placeholder('Enter Cell Number')
                            ->minValue(1)
                            ->unique(Cell::class, 'cell_number', ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('block')
                            ->label('Identifier (Block/Floor etc.)')
                            ->required()
                            ->placeholder('Enter Block/Floor etc. eg. Block A, Floor 1')
                            ->maxLength(255)
                            ->required(),
                            ])
                    ]
                )

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('No Cells Found')
            ->emptyStateIcon('heroicon-o-table-cells')
            ->emptyStateDescription('You can create a add cell by clicking the button below.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make('Add A Cell')
                    ->label('Add A Cell')
                    ->icon('heroicon-o-plus-circle')
                    ->color('primary'),
            ])
            ->columns([
            Tables\Columns\TextColumn::make('cell_number')
                ->label('Cell Number')
                ->searchable()
                ->formatStateUsing(fn($record) => 'CELL' . $record->cell_number)
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('block')
                ->label('Identifier (Block/Floor etc.)')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('station.name')
                ->label('Station')
                ->searchable()
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
            'index' => Pages\ListCells::route('/'),
            'create' => Pages\CreateCell::route('/create'),
            'view' => Pages\ViewCell::route('/{record}'),
            'edit' => Pages\EditCell::route('/{record}/edit'),
        ];
    }
}
