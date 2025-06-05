<?php

namespace App\Filament\Station\Resources;

use App\Filament\Station\Resources\InmateResource\Pages;
use App\Filament\Station\Resources\InmateResource\RelationManagers;
use App\Models\Inmate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InmateResource extends Resource
{
    protected static ?string $model = Inmate::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Inmate Management';

    protected static ?string $navigationLabel = 'All Inmates';



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
            'index' => Pages\ListInmates::route('/'),
            'create' => Pages\CreateInmate::route('/create'),
            'view' => Pages\ViewInmate::route('/{record}'),
            'edit' => Pages\EditInmate::route('/{record}/edit'),
        ];
    }
}
