<?php

namespace App\Filament\HQ\Resources;

use App\Filament\HQ\Resources\RemandResource\Pages;
use App\Filament\HQ\Resources\RemandResource\RelationManagers;
use App\Models\Remand;
use App\Models\RemandTrial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RemandResource extends Resource
{
    protected static ?string $model = RemandTrial::class;

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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRemands::route('/'),
            'create' => Pages\CreateRemand::route('/create'),
            'view' => Pages\ViewRemand::route('/{record}'),
            'edit' => Pages\EditRemand::route('/{record}/edit'),
        ];
    }
}
