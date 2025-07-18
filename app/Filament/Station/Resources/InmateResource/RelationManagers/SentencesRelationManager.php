<?php

namespace App\Filament\Station\Resources\InmateResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SentencesRelationManager extends RelationManager
{
    protected static string $relationship = 'sentences';

    public function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('inmate_id')
            ->columns([
                Tables\Columns\TextColumn::make('sentence'),
                Tables\Columns\TextColumn::make('offence')->badge(),
                Tables\Columns\TextColumn::make('EPD')->date()->label('EPD'),
            Tables\Columns\TextColumn::make('LPD')->date()->label('LPD'),
                Tables\Columns\TextColumn::make('court_of_committal')->label('Court of Committal'),
                Tables\Columns\TextColumn::make('commutted_by')->label('Commutted By'),
                Tables\Columns\TextColumn::make('commutted_sentence')->label('Commutted Sentence'),

            ])
            ->filters([
                //
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\Action::make('view_warrant_document')
                    ->label('View Warrant')
                ->color('purple')
                ->icon('heroicon-o-document-text')
                    ->button()
                    ->url(function ($record) {
                        $document = $record->warrant_document ?? $record->amnesty_document;

                        return $document
                            ? route('warrant.document.view', ['document' => $document])
                            : null;
                    }, true)
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([]);
    }
}
