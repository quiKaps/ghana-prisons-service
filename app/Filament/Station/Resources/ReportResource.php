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
use pxlrbt\FilamentExcel\Columns\Column;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use App\Filament\Station\Resources\InmateResource;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Station\Resources\RemandTrialResource;
use App\Filament\Station\Resources\ReportResource\Pages;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
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
                ->label('Detention Type')
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
            ])->bulkActions([
                ExportBulkAction::make()->exports([
                    ExcelExport::make()
                        //->queue()->withChunkSize(100)
                        ->withFilename(Auth::user()->station->name . ' -' . now()->format('Y-m-d') . ' - Report Export')
                        ->withColumns([
                            Column::make('station.name')->heading('Station'),
                            Column::make('serial_number')->heading('Serial Number'),
                            Column::make('full_name')->heading("Name of Prisoner"),
                            Column::make('age_on_admission')->heading('Age'),
                            Column::make('latestSentenceByDate.offence')
                                ->heading('offence')
                                ->getStateUsing(function ($record) {
                                    if ($record->latestSentenceByDate) {
                                        return $record->latestSentenceByDate->offence;
                                    }
                                    return '';
                                }),
                            Column::make('latestSentenceByDate.total_sentence')
                                ->heading('Sentence')
                                ->getStateUsing(function ($record) {
                                    if ($record->latestSentenceByDate) {
                                        return $record->latestSentenceByDate->total_sentence;
                                    }
                                    return '';
                                }),
                            Column::make('admission_date')->formatStateUsing(fn($state) => date_format($state, 'Y-m-d'))->heading('Date of Admission'),
                            Column::make('latestSentenceByDate.date_of_sentence')
                                ->heading('Date of Sentence')
                                ->getStateUsing(function ($record) {
                                    if ($record->latestSentenceByDate) {
                                        return date_format($record->latestSentenceByDate->date_of_sentence, 'Y-m-d');
                                    }
                                    return '';
                                }),
                            Column::make('latestSentenceByDate.EPD')
                                ->heading('EPD')
                                ->getStateUsing(function ($record) {
                                    if ($record->latestSentenceByDate) {
                                        return date_format($record->latestSentenceByDate->EPD, 'Y-m-d');
                                    }
                                    return '';
                                }),
                            Column::make('latestSentenceByDate.LPD')
                                ->heading('LPD')
                                ->getStateUsing(function ($record) {
                                    if ($record->latestSentenceByDate) {
                                        return date_format($record->latestSentenceByDate->LPD, 'Y-m-d');
                                    }
                                    return '';
                                }),
                            Column::make('latestSentenceByDate.court_of_committal')
                                ->heading('Court of Committal')
                                ->getStateUsing(function ($record) {
                                    if ($record->latestSentenceByDate) {
                                        return $record->latestSentenceByDate->court_of_committal;
                                    }
                                    return '';
                                }),
                            Column::make('cell_id')
                                ->heading('Cell Number - Block'),
                            Column::make('latestSentenceByDate.warrant')
                                ->heading('Warrant')
                                ->getStateUsing(function ($record) {
                                    if ($record->latestSentenceByDate) {
                                        return $record->latestSentenceByDate->warrant;
                                    }
                                    return '';
                                }),
                            Column::make('transferred_in')->heading('Transferred In')->getStateUsing(function ($record) {
                                if ($record->transferred_in == 1) {
                                    return 'Yes';
                                }
                                return 'No';
                            }),
                            Column::make('disability')->heading('Disability')->getStateUsing(function ($record) {
                                if ($record->disability == 1) {
                                    return 'Yes';
                                }
                                return 'No';
                            }),
                            Column::make('tribe')->heading('Tribe'),


                            Column::make('languages_spoken')
                                ->heading('Languages Spoken')
                                ->getStateUsing(function ($record) {
                                    if (!empty($record->languages_spoken)) {
                                        $languages = is_array($record->languages_spoken)
                                            ? $record->languages_spoken
                                            : json_decode($record->languages_spoken, true);

                                        if (is_array($languages)) {
                                            return implode(', ', $languages);
                                        }
                                    }
                                    return '';
                                }),
                            Column::make('hometown')->heading('Hometown'),


                            Column::make('married_status')->heading('Marital Status'),

                            Column::make('nationality')->heading('Country of Origin'),


                            Column::make('education_level')->heading('Education Background'),

                            Column::make('religion')->heading('Religious Background'),

                            Column::make('occupation')->heading('Occupation'),

                            Column::make('next_of_kin_name')->heading('Next of Kin Name'),
                            Column::make('next_of_kin_relationship')->heading('Next of Kin Relationship'),
                            Column::make('next_of_kin_contact')->heading('Contact of Next of Kin'),
                            Column::make('distinctive_marks')
                                ->heading('Distinctive Marks')
                                ->getStateUsing(function ($record) {
                                    if (is_array($record->distinctive_marks)) {
                                        return implode(', ', $record->distinctive_marks);
                                    }
                                    return '';
                                }),
                            Column::make('goaler')->heading('Goaler')->getStateUsing(function ($record) {
                                if ($record->goaler == 1) {
                                    return 'Yes';
                                }
                                return 'No';
                            }),
                            Column::make('previously_convicted')->heading('Previous Conviction')->getStateUsing(function ($record) {
                                if ($record->previous_conviction == 1) {
                                    return 'Yes';
                                }
                                return 'No';
                            }),
                            Column::make('police_name')->heading('Police Officer'),
                            Column::make('police_station')->heading('Police Station'),
                            Column::make('police_contact')->heading('Police Contact'),
                        ])
                ])
                    ->label('Export Selected Convicts')
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray')
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
        ];
    }

    //show resource navigation to only prison_admin
    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        return $user?->user_type === 'prison_admin';
    }
}
