<?php

namespace App\Filament\Station\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Inmate;
use Filament\Forms\Form;
use App\Models\Transfers;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use App\Actions\SecureEditAction;
use App\Actions\SecureDeleteAction;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Station\Resources\TransfersResource\Pages;
use App\Filament\Station\Resources\TransfersResource\RelationManagers;

class TransfersResource extends Resource
{
    protected static ?string $model = Inmate::class;

    protected static ?string $navigationGroup = 'Convicts';

    protected static ?string $navigationLabel = 'Transfers';

    protected static ?string $modelLabel = 'Transfers';

    protected static ?string $pluralModelLabel = 'Transfers';

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

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
                Tables\Columns\TextColumn::make('serial_number')
                    ->label('Serial Number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label("Name of Prisoner")
                    ->searchable()
                    ->sortable(),
            Tables\Columns\TextColumn::make('date_transferred_out')
                ->label("Date of Transfer")
                ->formatStateUsing(fn($state) => Carbon::parse($state)->format('Y-m-d'))
                ->date()
                ->searchable()
                ->sortable(),
            TextColumn::make('transferred_in')
                ->label('Transferred In')
                ->state(fn(Inmate $record) => $record->transferred_in) // this is the "column state"
                ->formatStateUsing(fn($state) => $state ? 'Yes' : 'No')
                ->visible(fn(?Inmate $record) => $record?->transferred_in ?? false), // recheck the value manually

            // Tables\Columns\TextColumn::make('station_transferred_from_id')
            //     ->label('Station Transfered From')
            //     ->formatStateUsing(fn($state, Inmate $record) => $record->transferred_in)
            //     ->visible(fn($state, Inmate $record) => $record == true)
            //     ->sortable(),
            // Tables\Columns\TextColumn::make('station_transferred_to_id')
            //     ->label('Station Transfered To')
            //     ->formatStateUsing(fn($state, Inmate $record) => $record->transferred_out)
            //     ->visible(fn($state) => $state == true)
            //     ->sortable(),
            Tables\Columns\TextColumn::make('latestSentenceByDate.sentence')
                    ->label('Sentence')
                    ->sortable()
                ->searchable(),
            // Tables\Columns\TextColumn::make('latestSentenceByDate.date_of_sentence')
            //         ->label('Date of Sentence')
            //         ->date()
            //         ->sortable(),
            Tables\Columns\TextColumn::make('admission_date')
                    ->label('Date of Admission')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([


            Action::make('Profile')
                ->color('gray')
                ->icon('heroicon-o-user')
                ->button()
                ->label('Profile')
                ->color('blue')
                ->url(fn(Inmate $record) => route('filament.station.resources.inmates.view', [
                    'record' => $record->getKey(),
                ])),


            ]);

        // ->bulkActions([
        //     Tables\Actions\BulkActionGroup::make([
        //         Tables\Actions\DeleteBulkAction::make(),
        //     ]),
        // ]);
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
            'index' => Pages\ListTransfers::route('/'),
        ];
    }

    //show resource navigation to only prison_admin
    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        return $user?->user_type === 'prison_admin';
    }
}
