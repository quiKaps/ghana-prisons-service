<?php

namespace App\Filament\Station\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Inmate;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ConvictDischarge;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Station\Resources\ConvictDischargeResource\Pages;
use App\Filament\Station\Resources\ConvictDischargeResource\RelationManagers;

class ConvictDischargeResource extends Resource
{

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Convicts Discharged';

    protected static ?string $navigationGroup = 'Convicts';

    //protected static ?string $modelLabel = 'Convicts Discharged';

    protected ?string $subheading = 'List of inmates scheduled for discharge tomorrow';

    protected static ?string $model = Inmate::class;

    public static function getLabel(): string
    {
        return class_basename(static::$model);
    }


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
            ->query(Inmate::discharged()->orderByDesc('created_at'))
            ->emptyStateHeading('No Prisoners Available for Discharge')
            ->emptyStateDescription('There are currently no prisoners available for discharge today.')
            ->emptyStateIcon('heroicon-s-user')
            ->columns([
                TextColumn::make('serial_number')
                    ->weight(FontWeight::Bold)
                    ->label('S.N.'),
                TextColumn::make('full_name')
                    ->searchable()
                ->label("Prisoner's Name"),
                TextColumn::make('admission_date')
                    ->label('Admission Date')
                ->date(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->button()
                    ->label('Profile')
                    ->icon('heroicon-o-user')
                ->color('primary'),
        ])
            ->bulkActions([

                Tables\Actions\DeleteBulkAction::make(),

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
            'index' => Pages\ListConvictDischarges::route('/'),
            'create' => Pages\CreateConvictDischarge::route('/create'),
            'view' => Pages\ViewConvictDischarge::route('/{record}'),
            'edit' => Pages\EditConvictDischarge::route('/{record}/edit'),
        ];
    }
}
