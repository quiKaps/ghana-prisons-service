<?php

namespace App\Filament\Station\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Inmate;
use App\Models\Report;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\InmateRemandUnion;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Station\Resources\InmateResource;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Station\Resources\RemandTrialResource;
use App\Filament\Station\Resources\ReportResource\Pages;
use App\Filament\Station\Resources\ReportResource\RelationManagers;

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
                    'convict' => 'Convict',
                    'remand' => 'Remand',
                    'trial' => 'Trial',
                ]),
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
                        return InmateResource::getUrl('view', ['record' => $record->unique_id]);
                    }

                // It's a remand/trial → TrialResource
                return RemandTrialResource::getUrl('view', ['record' => $record->unique_id]);
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

    //show resource navigation to only prison_admin
    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        return $user?->user_type === 'prison_admin';
    }
}
