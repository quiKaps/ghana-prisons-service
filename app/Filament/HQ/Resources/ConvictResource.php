<?php

namespace App\Filament\HQ\Resources;

use App\Filament\HQ\Resources\ConvictResource\Pages;
use App\Filament\HQ\Resources\ConvictResource\RelationManagers;
use App\Models\Convict;
use App\Models\Inmate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConvictResource extends Resource
{
    protected static ?string $model = Inmate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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

    public static function getEloquentQuery(): Builder
    {
        return parent::withoutGlobalScopes();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConvicts::route('/'),
            'create' => Pages\CreateConvict::route('/create'),
            'view' => Pages\ViewConvict::route('/{record}'),
            'edit' => Pages\EditConvict::route('/{record}/edit'),
        ];
    }
}
