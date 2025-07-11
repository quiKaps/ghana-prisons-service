<?php

namespace App\Filament\Station\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Inmate;
use App\Models\Report;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Station\Resources\ReportResource\Pages;
use App\Filament\Station\Resources\ReportResource\RelationManagers;

class ReportResource extends Resource
{
    protected static ?string $model = Inmate::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';

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
                Tables\Columns\TextColumn::make('age_on_admission')
                    ->label('Age on Admission')
                    ->sortable(),
                Tables\Columns\TextColumn::make('latestSentenceByDate.offence')
                    ->label('Offence')
                    ->sortable()
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
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'reviewing' => 'Reviewing',
                        'published' => 'Published',
                    ])
            ], layout: FiltersLayout::AboveContent)
            ->actions([
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
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }
}
