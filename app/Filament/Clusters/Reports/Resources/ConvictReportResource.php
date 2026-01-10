<?php

namespace App\Filament\Clusters\Reports\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Inmate;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ConvictReport;
use Filament\Resources\Resource;
use App\Filament\Clusters\Reports;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clusters\Reports\Resources\ConvictReportResource\Pages;
use App\Filament\Clusters\Reports\Resources\ConvictReportResource\RelationManagers;

class ConvictReportResource extends Resource
{
    protected static ?string $model = Inmate::class;

    protected static ?string $navigationLabel = 'Convict Reports';


   // protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $cluster = Reports::class;

   

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('station.name')
                ->label('Station')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('serial_number')
                ->label('Serial Number')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label("Name of Prisoner")
                    ->searchable()
                    ->sortable(),
            Tables\Columns\TextColumn::make('admission_date')
                ->label("Date of Admission")
                ->date()
                ->searchable()
                ->sortable(),
           
        Tables\Columns\TextColumn::make('age_on_admission')
                    ->label('Age on Admission')
                    ->sortable(),
            Tables\Columns\TextColumn::make('court')
                ->label('Court of Committal')

                ->sortable(),
            ])
            ->filters([
           
            Filter::make('admission_date')->columnSpanFull()
                ->form([

                DatePicker::make('admitted_from')->label('Admitted From'),
                DatePicker::make('admitted_until')->label('Admitted From'),
                ])->columns(2)
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                    $data['admitted_from'],
                    fn(Builder $query, $date): Builder => $query->whereDate('admission_date', '>=', $date),
                        )
                        ->when(
                    $data['admitted_until'],
                    fn(Builder $query, $date): Builder => $query->whereDate('admission_date', '<=', $date),
                        );
                })
            ], layout: FiltersLayout::AboveContent)
                        ->headerActions([
                           
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
            ])->bulkActions([
               
        ]);
    }

   

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConvictReports::route('/'),
        ];
    }
}
