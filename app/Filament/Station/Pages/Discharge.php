<?php

namespace App\Filament\Station\Pages;

use App\Models\Inmate;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class Discharge extends Page implements \Filament\Tables\Contracts\HasTable
{
    use \Filament\Tables\Concerns\InteractsWithTable;

    use InteractsWithInfolists;




    public function table(Table $table): Table
    {
        return $table
            ->query(Inmate::scheduledForDischargeTomorrow()->orderByDesc('created_at'))
            ->emptyStateHeading('No Prisoners Available for Discharge')
            ->emptyStateDescription('There are currently no prisoners available for discharge today.')
            ->emptyStateIcon('heroicon-s-user')
            ->columns([
                TextColumn::make('serial_number')
                    ->weight(FontWeight::Bold)
                    ->label('S.N.'),
                TextColumn::make('full_name')
                    ->searchable()
                ->label('Prisoner Name'),
                TextColumn::make('admission_date')
                    ->label('Admission Date')
                    ->date(),

            ])
            ->filters([
                // Define any filters here if needed
            ])
            ->actions([]);
    }
}
