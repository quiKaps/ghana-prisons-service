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
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;

class ForeignersOnRemand extends Page implements \Filament\Tables\Contracts\HasTable
{

    use \Filament\Tables\Concerns\InteractsWithTable;


    protected static string $view = 'filament.station.pages.foreigners-on-remand';

    protected static ?string $navigationGroup = 'Remand and Trials';

    protected static ?string $navigationLabel = 'Foreigners - Remand';

    protected static ?string $title = 'Foreigners On Remand';

    protected ?string $subheading = 'View and manage foreign remand inmates';

    protected static ?string $model = RemandTrial::class;

    public function table(Table $table): Table
    {
        return $table
            ->query(RemandTrial::query()
                ->where('detention_type', 'remand')
            ->where('country_of_origin', '!=', 'Ghana')
            ->orderBy('created_at', 'DESC'))
            ->columns([
                TextColumn::make('serial_number')
                    ->weight(FontWeight::Bold)
                    ->label('S.N.'),
            TextColumn::make('full_name')
                    ->searchable()
                    ->label('Inmate Name'),
                TextColumn::make('country_of_origin')
                    ->label('Country'),
                TextColumn::make('admission_date')
                    ->label('Admission Date')
                    ->date(),
                TextColumn::make('court')
                    ->label('Court'),
                TextColumn::make('next_court_date')
                    ->badge()
                    ->color('success')
                    ->label('Next Court Date')
                    ->date(),

            TextColumn::make('police_contact')
                    ->label('Police Contact'),
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
