<?php

namespace App\Filament\Station\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Inmate;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ConvictDischarge;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use pxlrbt\FilamentExcel\Columns\Column;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Station\Resources\ConvictDischargeResource\Pages;
use App\Filament\Station\Resources\ConvictDischargeResource\RelationManagers;

class ConvictDischargeResource extends Resource
{

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-start-on-rectangle';

    protected static ?string $navigationLabel = 'Convict Discharges';

    protected static ?string $navigationGroup = 'Convicts';

    //protected static ?string $modelLabel = 'Convicts Discharged';

    protected ?string $subheading = 'List of inmates scheduled for discharge tomorrow';

    protected static ?string $model = Inmate::class;

    public static function getLabel(): string
    {
        return class_basename(static::$model);
    }


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
            ->emptyStateHeading('No Prisoners Available for Discharge')
            ->emptyStateDescription('There are currently no prisoners available for discharge here.')
            ->emptyStateIcon('heroicon-s-user')
            ->columns([
                TextColumn::make('serial_number')
                    ->weight(FontWeight::Bold)
                    ->label('S.N.'),
                TextColumn::make('full_name')
                    ->searchable()
                ->label("Prisoner's Name"),
            TextColumn::make('discharge.discharge_type')
                ->searchable()
                ->badge()
                ->color(fn(string $state): string => match ($state) {
                    'amnesty' => 'success',
                    'fine_paid' => 'green',
                    'presidential_pardon' => 'info',
                    'acquitted_and_discharged' => 'warning',
                    'bail_bond' => 'blue',
                    'reduction_of_sentence' => 'purple',
                    'death' => 'danger',
                'one-third remission' => 'warning',
                    default => 'primary',
                })
                ->formatStateUsing(fn($state) => match ($state) {
                    'amnesty' => 'Amnesty',
                    'fine_paid' => 'Fine Paid',
                    'presidential_pardon' => 'Presidential Pardon',
                    'acquitted_and_discharged' => 'Acquitted and Discharged',
                    'bail_bond' => 'Bail Bond',
                    'reduction_of_sentence' => 'Reduction of Sentence',
                    'escape' => 'Escape',
                    'death' => 'Death',
                'one-third remission' => '1/3 Remission',
                default => $state,
                })
                ->label("Mode of Discharge"),
                TextColumn::make('admission_date')
                    ->label('Admission Date')
                ->date(),
            TextColumn::make('discharge.discharge_date')
                ->label('Date of Discharge')
                ->date(),
        ])
            ->filters([
                //
            ])
            ->actions([
            Tables\Actions\Action::make('view_warrant_document')
                ->label('View Document')
                ->icon('heroicon-o-document-text')
                ->color('purple')
                ->button()
                ->url(function ($record) {
                $document =  $record->discharge?->discharge_document;

                    return $document
                        ? route('warrant.document.view', ['document' => $document])
                        : null;
            }, true)
                ->visible(fn($record) => $record->discharge?->discharge_document !== null)

                ->openUrlInNewTab(),
            Tables\Actions\ViewAction::make()
                    ->button()
                    ->label('Profile')
                    ->icon('heroicon-o-user')
                ->url(fn(Inmate $record) => route('filament.station.resources.inmates.view', [
                    'record' => $record->getKey(),
                ]))
                ->color('primary'),
        ])
            ->headerActions([])
            ->bulkActions([
            ExportBulkAction::make()->exports([
                ExcelExport::make()
                    //->queue()->withChunkSize(100)
                    ->withFilename(Auth::user()->station->name . ' -Convict Discharges-' . now()->format('Y-m-d') . ' - export')
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
                        Column::make('cell.cell_number')
                            ->heading('Cell Number - Block')
                            ->getStateUsing(function ($record) {
                                if ($record->cell) {
                                    return "{$record->cell->block} - {$record->cell->cell_number}";
                                }
                                return '';
                            }),
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
            'index' => Pages\ListConvictDischarges::route('/'),
            'create' => Pages\CreateConvictDischarge::route('/create'),
            'view' => Pages\ViewConvictDischarge::route('/{record}'),
            'edit' => Pages\EditConvictDischarge::route('/{record}/edit'),
        ];
    }

    //show resource navigation to only prison_admin
    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        return $user?->user_type === 'prison_admin';
    }
}
