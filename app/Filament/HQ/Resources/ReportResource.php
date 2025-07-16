<?php

namespace App\Filament\HQ\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Inmate;
use App\Models\Report;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use App\Filament\HQ\Resources\ReportResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\HQ\Resources\ReportResource\RelationManagers;
use App\Models\InmateRemandUnion;

class ReportResource extends Resource
{
    protected static ?string $model = InmateRemandUnion::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';

    protected static ?string $navigationLabel = 'Reports';

    protected static ?string $navigationGroup = 'Convicts';

    protected static ?string $modelLabel = 'Reports';

    protected ?string $subheading = 'Access and download all prisoner data';

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
            Tables\Columns\TextColumn::make('station.name')
                    ->label('Sation')
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
            Tables\Columns\TextColumn::make('gender')
                ->label("Gender")
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('admission_date')
                ->label("Date of Admission")
                ->date()
                ->searchable()
                ->sortable(),
            TextColumn::make('detention_type')
                ->label('Detention Type')
                ->sortable()
                ->badge()
                ->color(fn($state) => match (trim($state ?? '') ?: 'convict') {
                    'remand' => 'info',
                    'trial' => 'warning',
                    'convict' => 'gray',
                }),
            Tables\Columns\TextColumn::make('age_on_admission')
                ->label('Age on Admission')
                ->sortable(),
            Tables\Columns\TextColumn::make('court')
                ->label('Court of Committal')

                ->sortable(),
            ])
            ->filters([
            SelectFilter::make('detention_type')
                ->label('Source')
                ->options([
                    null => 'Inmate',
                    'remand' => 'Remand',
                    'trial' => 'Trial',
                ]),

            TernaryFilter::make('is_discharged')
                ->label('Discharged')
                ->trueLabel('Discharged')
                ->falseLabel('Not Discharged')
                ->nullable(),
                Filter::make('created_at')->columnSpanFull()
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])->columns(2)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Action::make('Profile')
                    ->color('gray')
                    ->icon('heroicon-o-user')
                    ->label('Profile')
                    ->button()
                    ->color('blue')
                ->url(function (InmateRemandUnion $record) {
                    if ($record->detention_type === 'convict') {
                        // It's an inmate → ConvictResource
                        return \App\Filament\HQ\Resources\ConvictResource::getUrl('view', ['record' => $record->unique_id]);
                    }

                    // It's a remand/trial → TrialResource
                    return \App\Filament\HQ\Resources\TrialResource::getUrl('view', ['record' => $record->unique_id]);
                })

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
            'index' => Pages\ListReports::route('/'),

        ];
    }
}