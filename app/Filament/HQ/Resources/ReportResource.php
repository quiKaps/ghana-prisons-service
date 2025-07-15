<?php

namespace App\Filament\HQ\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Inmate;
use App\Models\Report;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\HQ\Resources\ReportResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\HQ\Resources\ReportResource\RelationManagers;

class ReportResource extends Resource
{
    protected static ?string $model = Inmate::class;

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
                Tables\Columns\TextColumn::make('age_on_admission')
                    ->label('Age on Admission')
                    ->sortable(),
                Tables\Columns\TextColumn::make('latestSentenceByDate.offence')
                    ->label('Offence')
                    ->sortable()
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('latestSentenceByDate.sentence')
                    ->label('Sentence')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('earliestSentenceByDate.date_of_sentence')
                    ->label('Date of Sentence')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('admission_date')
                    ->label('Date of Admission')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
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
                    ->url(fn(Inmate $record) => ConvictResource::getUrl('view', ['record' => $record])),

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
