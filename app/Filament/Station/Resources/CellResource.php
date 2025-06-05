<?php

namespace App\Filament\Station\Resources;

use App\Filament\Station\Resources\CellResource\Pages;
use App\Filament\Station\Resources\CellResource\RelationManagers;
use App\Models\Cell;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CellResource extends Resource
{
    protected static ?string $model = Cell::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
