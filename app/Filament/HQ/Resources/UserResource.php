<?php

namespace App\Filament\HQ\Resources;

use App\Filament\HQ\Resources\UserResource\Pages;
use App\Filament\HQ\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            Forms\Components\TextInput::make('name')
                ->label("Officer's Name")
                ->placeholder('e.g. Ohene Adjei Samuel')

                ->required()
                ->maxLength(50),
            Forms\Components\Select::make('rank')
                ->required()
                ->placeholder('Select Officer Rank')
                ->label('Officer Rank')
                ->options([
                    'dg' => 'Director-General',
                    'ddg' => 'Deputy Director-General',
                    'doc' => 'Director of Corrections',
                    'dcp' => 'Deputy Director of Corrections',
                    'acp' => 'Assistant Director of Corrections',
                    'csp' => 'Chief Superintendent',
                    'sp' => 'Superintendent',
                    'dsp' => 'Deputy Superintendent',
                    'asp' => 'Assistant Superintendent',
                    'coi' => 'Chief Officer',
                    'soi' => 'Senior Officer',
                    'oi' => 'Officer',
                    'lo' => 'Lance Officer',
                    'sco' => 'Second Class Officer'
                ]),
            Forms\Components\TextInput::make('serial_number')
                ->label('Officer Serial Number')
                ->placeholder('e.g. 112')
                ->required()
                ->unique(User::class, 'serial_number', ignoreRecord: true)
                ->maxLength(50),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
