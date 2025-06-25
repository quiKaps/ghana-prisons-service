<?php

namespace App\Filament\Station\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Models\RemandTrial;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;

class ForeignersOnTrial extends Page implements HasTable
{

    use \Filament\Tables\Concerns\InteractsWithTable;

    protected static string $view = 'filament.station.pages.foreigners-on-trial';

    protected static ?string $navigationGroup = 'Remand and Trials';

    protected static ?string $navigationLabel = 'Foreigners - Trial';

    protected static ?string $title = 'Foreigners On Trial';

    protected ?string $subheading = "View and manage foreign trial Prisoners";

    protected static ?string $model = RemandTrial::class;

    public function table(Table $table): Table
    {
        return $table
            ->query(RemandTrial::query()->where('detention_type', 'trial')
            ->where('country_of_origin', '!=', 'Ghana')
            ->orderBy('created_at', 'DESC'))
            ->columns([
                TextColumn::make('serial_number')
                    ->weight(FontWeight::Bold)
                    ->label('S.N.'),
            TextColumn::make('full_name')
                    ->searchable()
                ->label("Prisoner's Name"),
            TextColumn::make('country_of_origin')
                    ->label('Country'),
                TextColumn::make('next_court_date')
                    ->label('Next Court Date')
                    ->badge()
                    ->color('success')
                    ->date(),
                TextColumn::make('court')
                    ->searchable()
                    ->label('Court'),
                TextColumn::make('admission_date')
                    ->label('Admission Date')
                    ->date(),
                TextColumn::make('court')
                ->label('Court'),
            ])
            ->filters([
                // Define any filters here if needed
            ])
            ->actions([
            Action::make('Profile')
                ->color('gray')
                ->icon('heroicon-o-user')
                ->label('Profile')
                ->button()
                ->color('blue')
                ->url(fn(RemandTrial $record) => route('filament.station.resources.remand-trials.view', [
                    'record' => $record->getKey(),
                ])),
            ]);
    }
}
