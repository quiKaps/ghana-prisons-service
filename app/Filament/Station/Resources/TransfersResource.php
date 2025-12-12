<?php

namespace App\Filament\Station\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Inmate;
use Filament\Forms\Form;
use App\Models\Transfers;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use App\Actions\SecureEditAction;
use App\Actions\SecureDeleteAction;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use pxlrbt\FilamentExcel\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Station\Resources\TransfersResource\Pages;
use App\Filament\Station\Resources\TransfersResource\RelationManagers;

class TransfersResource extends Resource
{
    protected static ?string $model = Inmate::class;

    protected static ?string $navigationGroup = 'Convicts';

    protected static ?string $navigationLabel = 'Transfers';

    protected static ?string $modelLabel = 'Transfers';

    protected static ?string $pluralModelLabel = 'Transfers';

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

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

            TextColumn::make('transferred_in')
                ->label('Date Transferred')
                ->badge()
                ->color('info')
                ->formatStateUsing(function (Inmate $record) {
                    $activeTab = request()->query('activeTab');

                    if ($record->transferred_out) {
                        return 'Transferred Out on ' . Carbon::parse($record->date_transferred_out)->format('jS M, Y');
                    }

                    if ($record->transferred_in) {
                        return 'Transferred In on ' . Carbon::parse($record->date_transferred_in)->format('jS M, Y');
                    }
                    return 'No';
                }),
            Tables\Columns\TextColumn::make('station.name')
                ->label('Station Transferred To')
                ->formatStateUsing(function ($state, Inmate $record) {
        if (! $record || ! $record->transfers) {
            return 'null-';
        }

        // If transfers is hasMany, get the latest
        $latestTransfer = $record->transfers()->latest()->first();

        return $latestTransfer?->toStation?->name ?? '-';
    })
                ->sortable(),

                 Tables\Columns\TextColumn::make('transfers')
                    ->label('Station Transferred From')
                    ->formatStateUsing(function ($state, Inmate $record) {
        if (! $record || ! $record->transfers) {
            return 'null-';
        }

        // If transfers is hasMany, get the latest
        $latestTransfer = $record->transfers()->latest()->first();

        return $latestTransfer?->fromStation?->name ?? '-';
    })
                    ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('latestSentenceByDate.sentence')
                    ->label('Sentence')
                    ->sortable()
                ->searchable(),
            // Tables\Columns\TextColumn::make('latestSentenceByDate.date_of_sentence')
            //         ->label('Date of Sentence')
            //         ->date()
            //         ->sortable(),
            Tables\Columns\TextColumn::make('admission_date')
                    ->label('Date of Admission')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
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
                ExportBulkAction::make()->exports([
                    ExcelExport::make()
                        //->queue()->withChunkSize(100)
                        ->withFilename(Auth::user()->station->name . ' -Transfers-' . now()->format('Y-m-d') . ' - export')
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
                    ->label('Export Selected Transfers')
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
            'index' => Pages\ListTransfers::route('/'),
        ];
    }

    //show resource navigation to only prison_admin
    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        return $user?->user_type === 'prison_admin';
    }
}
