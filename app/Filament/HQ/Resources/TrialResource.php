<?php

namespace App\Filament\HQ\Resources;

use App\Filament\HQ\Resources\TrialResource\Pages;
use App\Filament\HQ\Resources\TrialResource\RelationManagers;
use App\Models\RemandTrial;
use App\Models\Trial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TrialResource extends Resource
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
            'index' => Pages\ListTrials::route('/'),
            'create' => Pages\CreateTrial::route('/create'),
            'view' => Pages\ViewTrial::route('/{record}'),
            'edit' => Pages\EditTrial::route('/{record}/edit'),
        ];
    }
}
