<?php

namespace App\Filament\Station\Resources\InmateResource\Pages;

use App\Models\Inmate;
use Filament\Tables\Table;
use App\Actions\SecureEditAction;
use Filament\Resources\Pages\Page;
use App\Actions\SecureDeleteAction;
use Filament\Tables\Actions\Action;


use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Station\Resources\InmateResource;

class ConvictedForiegners extends Page implements \Filament\Tables\Contracts\HasTable
{
    use \Filament\Tables\Concerns\InteractsWithTable;

    protected static string $resource = InmateResource::class;

    protected static string $view = 'filament.station.resources.inmate-resource.pages.convicted-foriegners';

    protected static ?string $navigationGroup = 'Convicts';

    protected static ?string $navigationLabel = 'Foreigners - Convicts';

    protected static ?string $title = 'Foreigners - Convicts';

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Inmate::query()
                ->where('nationality', '!=', 'ghana')
                ->with('latestSentenceByDate')
                ->orderByDesc('created_at')
            )
            ->columns([
                TextColumn::make('serial_number')
                    ->label('Serial Number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('full_name')
                ->label("Prisoner's Name")
                    ->searchable()
                    ->sortable(),
                TextColumn::make('gender')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),
                TextColumn::make('age_on_admission')
                    ->label('Age')
                    ->sortable(),
                TextColumn::make('nationality')
                    ->label('Nationality')
                    ->sortable(),

            TextColumn::make('latestSentenceByDate.sentence')
                    ->searchable()
                ->label('Sentence')
                    ->sortable(),
                TextColumn::make('admission_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('cell.cell_number')
                    ->label('Cell')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(
                        fn($record) => $record->cell ? "CELL {$record->cell->cell_number} - {$record->cell->block}" : 'No Cell Assigned'
                    ),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make()
                    ->label('Profile')
                    ->button()
                    ->icon('heroicon-o-user')
                    ->color('blue')
                    ->url(fn(Inmate $record) => route('filament.station.resources.inmates.view', [
                        'record' => $record->getKey(),
                    ]))
            ])

            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
