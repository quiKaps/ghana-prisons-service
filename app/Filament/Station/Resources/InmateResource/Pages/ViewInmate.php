<?php

namespace App\Filament\Station\Resources\InmateResource\Pages;

use Filament\Actions;
use App\Models\Inmate;
use App\Models\Station;
use Filament\Forms\Get;
use App\Models\Sentence;
use App\Models\Transfer;
use App\Models\Discharge;
use Filament\Actions\Action;
use App\Actions\SecureEditAction;
use Filament\Actions\ActionGroup;
use Illuminate\Support\Facades\DB;
use App\Actions\SecureDeleteAction;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Session;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use App\Filament\Station\Resources\InmateResource;

class ViewInmate extends ViewRecord
{
    protected static string $resource = InmateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //back to convicts actions
            Action::make('back')
                ->label('Back to Convicts')
                ->color('success')
                ->icon('heroicon-o-arrow-left')
                ->url(InmateResource::getUrl('index')),
            //back to all convicts actions

            //print action starts
            Action::make('print')
                ->label('Print Profile')
                ->color('warning')
                ->icon('heroicon-o-printer'),
            //print action ends
            ActionGroup::make([

                //transfer action
                Action::make('transfer')
                    ->label('Transfer')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('green')
                    ->fillForm(fn(Inmate $record): array => [
                        'serial_number' => $record->serial_number,
                        'full_name' => $record->full_name,
                        'sentence' => $record->latestSentenceByDate->sentence,
                        'offence' => $record->latestSentenceByDate->offence,
                    ])->form([
                        Group::make()
                            ->columns(2)
                            ->schema([
                                TextInput::make('serial_number')
                                    ->readOnly(),
                                TextInput::make('full_name')
                                    ->label("Prisoner's Name")
                                    ->readonly(),
                                TextInput::make('sentence')
                                    ->label('Sentence')
                                    ->readonly(),
                                TextInput::make('offence')
                                    ->label('Offence')
                                    ->readonly(),
                                DatePicker::make('date_of_transfer')
                                    ->label('Date of Transfer')
                                    ->default(now())
                                    ->maxDate(now())
                                    ->required(),
                                Select::make('station_transferred_to_id')
                                    ->label('Transfer To: (Station)')
                                    ->required()
                                    ->options(
                                        fn() => Station::withoutGlobalScopes()
                                            ->where('id', '!=', Auth::user()->station_id)
                                            ->pluck('name', 'id')
                                            ->toArray()
                                    )
                                    ->searchable(),
                                TextInput::make('reason')
                                    ->label('Reason for Transfer')
                                    ->placeholder('Enter reason for transfer'),
                            ])
                    ])
                    ->modalHeading('Prisoner Transfer')
                    ->modalSubmitActionLabel('Transfer Prisoner')
                    ->action(function (array $data, Inmate $record): void {
                        try {
                            \Illuminate\Support\Facades\DB::transaction(function () use ($data, $record) {
                                \App\Models\Transfer::create([
                                    'inmate_id' => $record->id,
                                    'from_station_id' => Auth::user()->station_id,
                                    'to_station_id' => $data['station_transferred_to_id'],
                                    'transfer_date' => $data['date_of_transfer'],
                                    'status' => 'completed',
                                    'reason' => $data['reason'],
                                    'requested_by' => Auth::id(),
                                    'approved_by' => null,
                                    'rejected_by' => null,
                                ]);
                                $record->update([
                                    'transferred_out' => true,
                                    'station_transferred_to_id' => $data['station_transferred_to_id'],
                                    'date_transferred_out' => $data['date_of_transfer'],
                                ]);
                            //if use online, i will have to set inmate transfered in as 1 and station transfered from

                        });

                            Notification::make()
                                ->success()
                                ->title('Transfer Request Submitted')
                                ->body("The transfer request for {$record->full_name} has been submitted.")
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->danger()
                                ->title('Transfer Failed')
                                ->body('An error occurred: ' . $e->getMessage())
                                ->send();
                        }
                    }),
                //transfer action end

                // special discharge action
                Action::make('special_discharge')
                    ->label('Special Discharge')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('info')
                    ->fillForm(fn(Inmate $record): array => [
                        'serial_number' => $record->serial_number,
                        'full_name' => $record->full_name,
                        'sentence' => $record->latestSentenceByDate->sentence,
                        'offence' => $record->latestSentenceByDate->offence,
                    ])->form([
                        Group::make()
                            ->columns(2)
                            ->schema([
                                TextInput::make('serial_number')
                                    ->readOnly(),
                                TextInput::make('full_name')
                                    ->label("Prisoner's Name")
                                    ->readonly(),
                                TextInput::make('sentence')
                                    ->label('Sentence')
                                    ->readonly(),
                                TextInput::make('offence')
                                    ->label('Offence')
                                    ->readonly(),
                                Select::make('mode_of_discharge')
                                    ->label('Mode of Discharge')
                                    ->options([
                                        'amnesty' => 'Amnesty',
                                        'fine_paid' => 'Fine Paid',
                                        'presidential_pardon' => 'Presidential Pardon',
                                        'acquitted_and_discharged' => 'Acquitted and Discharged',
                                        'bail_bond' => 'Bail Bond',
                                        'reduction_of_sentence' => 'Reduction of Sentence',
                                        'escape' => 'Escape',
                                        'death' => 'Death',
                                        'one_third_remission' => '1/3 Remission',
                                        'other' => 'Other',
                                    ])
                                    ->required(),
                                DatePicker::make('date_of_discharge')
                                    ->label('Date of Discharge')
                                    ->default(now())
                                    ->maxDate(now())
                                    ->required(),
                                FileUpload::make('discharge_document')
                                    ->label('Discharge Document')
                                    ->placeholder('Upload Discharge Document')
                                    ->visibility('private')
                                    ->acceptedFileTypes(['application/pdf', 'png', 'jpg', 'jpeg'])
                                    ->openable()
                                    ->previewable()
                                    ->uploadingMessage('Uploading discharge document...'),

                        ])

                ])

                    ->modalHeading('Special Discharge')
                    ->modalSubmitActionLabel('Discharge Prisoner')
                    ->action(function (array $data, Inmate $record): void {
                        try {
                            \Illuminate\Support\Facades\DB::transaction(function () use ($data, $record) {
                                \App\Models\Discharge::create([
                                    'inmate_id' => $record->id,
                                    'discharge_type' => $data['mode_of_discharge'],
                                    'discharge_date' => $data['date_of_discharge'],
                                    //'reason' => $data['reason'],
                                    'discharge_document' => $data['discharge_document'],
                                    'discharged_by' => Auth::id(),
                                ]);

                                $record->update([
                                    'is_discharged' => true,
                                    'date_of_discharge' => $data['date_of_discharge'],
                                ]);

                                //if use online, i will have to set inmate transfered in as 1 and station transfered from
                            });

                            Notification::make()
                                ->success()
                                ->title('Discharge Successful')
                                ->body("{$record->full_name} has been discharged successfully.")
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->danger()
                                ->title('Discharge Failed')
                                ->body('An error occurred: ' . $e->getMessage())
                                ->send();
                        }
                    }),
                // special discharge action end

                //sentence reduction action
                Action::make('sentence_reduction')
                    ->label('Sentence Reduction')
                    ->icon('heroicon-o-arrow-trending-down')
                    ->color('success')
                    ->fillForm(fn(Inmate $record): array => [
                        'serial_number' => $record->serial_number,
                        'full_name' => $record->full_name,
                        'sentence' => $record->latestSentenceByDate->sentence,
                        'offence' => $record->latestSentenceByDate->offence,
                        'date_of_sentence' => $record->sentences->first()->date_of_sentence
                    ])->form([
                        Group::make()
                            ->columns(2)
                            ->schema([
                                TextInput::make('serial_number')
                                    ->readOnly(),
                                TextInput::make('full_name')
                                    ->label("Prisoner's Name")
                                    ->readonly(),
                                TextInput::make('sentence')
                                    ->label('Sentence')
                                    ->readonly(),
                                TextInput::make('offence')
                                    ->label('Offence')
                                    ->readonly(),
                                TextInput::make('reduced_sentence')
                                    ->label('Reduced Sentence')
                                    ->placeholder('Enter Reduced Sentence')
                                    ->required(),
                                TextInput::make('date_of_sentence')
                                    ->label('Date_of_Sentence')
                                    ->placeholder('Enter Date Sentence')
                                    ->readOnly(),
                                TextInput::make('court_of_committal')
                                    ->label('Appellate Court')
                                    ->placeholder('Enter Appellate Court')
                                    ->required(),
                                DatePicker::make('EPD')
                                    ->label('EPD (Earliest Possible Date of Discharge)')
                                    ->required(),
                                DatePicker::make('LPD')
                                    ->label('LPD (Latest Possible Date of Discharge)')
                                    ->required(),
                            FileUpload::make('warrant_document')
                                ->label('Upload Document')
                                ->placeholder('Upload Warrant Document')
                                ->visibility('private')
                                ->acceptedFileTypes(['application/pdf', 'png', 'jpg', 'jpeg'])
                                ->openable()
                                ->previewable()
                                ->uploadingMessage('Uploading warrant document...'),
                        ])

                ])

                    ->modalHeading('Sentence Reduction')
                    ->modalSubmitActionLabel('Reduce Sentence')
                    ->action(function (array $data, Inmate $record): void {
                        try {
                            \Illuminate\Support\Facades\DB::transaction(function () use ($data, $record) {
                                \App\Models\Sentence::create([
                                    'inmate_id' => $record->id,
                                    'sentence' => $data['reduced_sentence'],
                                    'offence' => $data['offence'],
                                    'date_of_sentence' => $data['date_of_sentence'],
                                    'reduced_sentence' => $data['reduced_sentence'], //this is redundant
                                    'court_of_committal' => $data['court_of_committal'],
                                    'EPD' =>  $data['EPD'],
                                    'LPD' => $data['LPD'],
                                    'warrant_document' => $data['warrant_document'],
                                ]);
                            });

                            Notification::make()
                                ->success()
                                ->title('Reduced Sentence Success')
                                ->body("The reduced sentence for {$record->full_name} has been completed.")
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->danger()
                            ->title('Reduced Sentence Failed')
                            ->body('An error occurred: ' . $e->getMessage()) // edit the error message
                            ->send();
                    }
                    }),
                //sentence reduction action end

                // additional sentence action
                Action::make('additional_sentence')
                    ->label('Additional Sentence')
                    ->icon('heroicon-o-plus-circle')
                    ->color('warning')
                    ->fillForm(fn(Inmate $record): array => [
                    'serial_number' => $record->serial_number,
                    'full_name' => $record->full_name,
                    'date_of_sentence' => $record->sentences->first()->date_of_sentence
                    ])->form([
                        Group::make()
                            ->columns(2)
                            ->schema([
                                TextInput::make('serial_number')
                                    ->readOnly(),
                                TextInput::make('full_name')
                                    ->label("Prisoner's Name")
                                    ->readonly(),
                                TextInput::make('sentence')
                                    ->label('Sentence')
                                    ->placeholder('Enter Sentence')
                                    ->required(),
                                TextInput::make('offence')
                                    ->label('Offence')
                                    ->placeholder('Enter Offence')
                                    ->required(),
                                TextInput::make('date_of_sentence')
                                    ->label('Date_of_Sentence')
                                    ->placeholder('Enter Date Sentence')
                                    ->readOnly(),
                                //rectify 
                                TextInput::make('total_sentence')
                                    ->label('Total Sentence')
                                    ->placeholder('Enter Total Sentence') //should be the sum of the current sentence and the additional sentence
                                    ->required(),
                                TextInput::make('court_of_committal')
                                    ->label('Court of Committal')
                                    ->placeholder('Enter Court of Committal')
                                    ->required(),
                                DatePicker::make('EPD')
                                    ->label('EPD (Earliest Possible Date of Discharge)')
                                    ->required(),
                                DatePicker::make('LPD')
                                    ->label('LPD (Latest Possible Date of Discharge)')
                            ->required(),
                            FileUpload::make('warrant_document')
                                ->label('Upload Document')
                                ->placeholder('Upload Warrant Document')
                                ->visibility('private')
                                ->acceptedFileTypes(['application/pdf', 'png', 'jpg', 'jpeg'])
                                ->openable()
                                ->previewable()
                                ->uploadingMessage('Uploading warrant document...'),
                        ])

                ])

                    ->modalHeading('Additional Sentence')
                    ->modalSubmitActionLabel('Add Sentence')
                    ->action(function (array $data, Inmate $record): void {
                        try {
                            \Illuminate\Support\Facades\DB::transaction(function () use ($data, $record) {
                                \App\Models\Sentence::create([
                                    'inmate_id' => $record->id,
                                    'sentence' => $data['total_sentence'],
                                    'offence' => $data['offence'],
                                    'total_sentence' => $data['total_sentence'], //this is redundant
                                    'court_of_committal' => $data['court_of_committal'],
                                    'date_of_sentence' => $record->sentences->first()->date_of_sentence,
                                    'EPD' =>  $data['EPD'],
                                    'LPD' => $data['LPD'],
                                    'warrant_document' => $data['warrant_document'],
                                ]);
                            });

                            Notification::make()
                                ->success()
                                ->title('Additional Sentence Success')
                                ->body("The additional sentence for {$record->full_name} has been completed.")
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->danger()
                            ->title('Additional Sentence Failed')
                            ->body('An error occurred: ' . $e->getMessage()) // edit the error message
                            ->send();
                    }
                    }),
                // additional sentence action end

                // amnesty action
                //this form is for convicts who are comdemned to death or life imprisonment only show for those inmates)

                Action::make('amnesty')
                    ->label('Amnesty')
                    ->icon('heroicon-o-sparkles')
                    ->color('blue')
                    ->fillForm(fn(Inmate $record): array => [
                        'serial_number' => $record->serial_number,
                        'full_name' => $record->full_name,
                        'sentence' => $record->latestSentenceByDate->sentence,
                        'offence' => $record->latestSentenceByDate->offence,

                    ])->form([
                        Group::make()
                            ->columns(2)
                            ->schema([
                                TextInput::make('serial_number')
                                    ->readOnly(),
                                TextInput::make('full_name')
                                    ->label("Prisoner's Name")
                                    ->readonly(),
                                TextInput::make('sentence')
                                    ->label('Sentence')
                                    ->readOnly(),
                                TextInput::make('offence')
                                    ->label('Offence')
                                    ->readOnly(),
                                Select::make('commutted_sentence')
                                    ->label('Commutted Sentence')
                                    ->live()
                                    ->options([
                                        'life' => 'Life',
                                        '20yrs_ihl' => '20 Years',
                                        'others' => 'Others',
                                    ])
                                    ->required(),
                                Select::make('commutted_by')
                                    ->label('Commuted By')
                                    ->options([
                                        'amnesty' => 'Amnesty',
                                        'others' => 'Others',
                                    ]),
                                DatePicker::make('EPD')
                                    ->label('EPD (Earliest Possible Date of Discharge)')
                                    ->visible(fn(Get $get): bool => $get('commutted_sentence') == '20yrs_ihl')
                                    // ->dehydrated(fn(Get $get) => $get('commutted_sentence') == '20yrs_ihl')
                                    ->required(fn(Get $get): bool => $get('commutted_sentence') == '20yrs_ihl'),
                                DatePicker::make('LPD')
                                    ->label('LPD (Latest Possible Date of Discharge)')
                                    ->visible(fn(Get $get): bool => $get('commutted_sentence') == '20yrs_ihl')
                                    //->dehydrated(fn(Get $get) => $get('commutted_sentence') == '20yrs_ihl')
                                    ->required(fn(Get $get): bool => $get('commutted_sentence') == '20yrs_ihl'),
                                DatePicker::make('date_of_amnesty')
                                    ->label('Date of Amnesty')
                                    ->required()
                                    ->default(now()),
                                FileUpload::make('amnesty_document')
                                    ->label('Upload Document')
                                    ->placeholder('Upload Document')
                                    ->visibility('private')
                                    ->acceptedFileTypes(['application/pdf', 'png', 'jpg', 'jpeg'])
                                    ->openable()
                                    ->previewable()
                                    ->uploadingMessage('Uploading document...'),
                            ])

                    ])
                    ->modalHeading('Convict Amnesty')
                    ->modalSubmitActionLabel('Grant Amnesty')
                    ->action(function (array $data, Inmate $record): void {
                        try {
                            \Illuminate\Support\Facades\DB::transaction(function () use ($data, $record) {
                                \App\Models\Sentence::create([
                                    'inmate_id' => $record->id,
                                    'sentence' => $data['commutted_sentence'],
                                    'offence' => $data['offence'],
                                    'commutted_sentence' => $data['commutted_sentence'],
                                    'commutted_by' => $data['commutted_by'],
                                    'EPD' => array_key_exists('EPD', $data) && $data['EPD'] !== '' ? $data['EPD'] : null,
                                    'LPD' => array_key_exists('LPD', $data) && $data['LPD'] !== '' ? $data['LPD'] : null,
                                    'date_of_amnesty' => $data['date_of_amnesty'],
                                    'amnesty_document' => $data['amnesty_document'],
                                ]);
                            });

                        Notification::make()
                            ->success()
                            ->title('Amnesty Success')
                            ->body("The amnesty for {$record->full_name} has been completed.")
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->danger()
                            ->title('Amnesty Failed')
                            ->body('An error occurred: ' . $e->getMessage()) // edit the error message
                            ->send();
                    }
                    }),
                // amnesty action end

                Actions\Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary')
                    ->modalWidth('md')
                    ->modalHeading('Protected Data Access')
                    ->modalDescription('This is a secure area of the application. Please confirm your password within the modal before continuing.')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('password')
                            ->label('Confirm Password')
                            ->placeholder('Enter your password')
                            ->password()
                            ->required(),
                    ])
                    ->action(function (array $data, $record) {
                        if (! \Illuminate\Support\Facades\Hash::check($data['password'], Auth::user()->password)) {
                            \Filament\Notifications\Notification::make()
                                ->title('Incorrect Password')
                                ->body('You must confirm your password to edit this record.')
                                ->danger()
                                ->send();
                            return;
                        }
                        return redirect()->route(
                            'filament.station.resources.inmates.edit',
                            ['record' => $record]
                        );
                    }),
                Actions\DeleteAction::make()
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        \Filament\Forms\Components\TextInput::make('password')
                            ->label('Confirm Password')
                            ->placeholder('Enter your password')
                            ->password()
                            ->required(),
                    ])
                    ->action(function (array $data, $record) {
                        if (! \Illuminate\Support\Facades\Hash::check($data['password'], Auth::user()->password)) {
                            \Filament\Notifications\Notification::make()
                                ->title('Incorrect Password')
                                ->danger()
                                ->body('You must confirm your password to delete this record.')
                                ->send();
                            return;
                        }
                        $record->delete();

                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Inmate Deleted')
                            ->send();
                    }),
            ])
                ->button()
                ->visible(fn() => $this->record->is_discharged === false)
                ->label('More Actions'),

        ];
    }

    public function getHeading(): string
    {
        return "{$this->record->full_name}'s Profile";
    }


    protected function mutateFormDataBeforeFill(array $data): array
    {
        $sentences = Sentence::where('inmate_id', $data['id'])->first();

        $latestSentence = Sentence::where('inmate_id', $data['id'])->latest()->first();


        if ($latestSentence != null) {
            $data['offence'] = $sentences?->offence;
            $data['sentence'] = $latestSentence?->sentence;
            $data['date_sentenced'] = $latestSentence?->date_of_sentence;
            $data['EPD'] = $latestSentence?->EPD;
            $data['LPD'] = $latestSentence?->LPD;
            $data['court_of_committal'] = $sentences?->court_of_committal;
            $data['warrant_document'] = $latestSentence?->warrant_document;

            //Session::put('latestSentenceId', $latestSentence?->id); // temporarily store it again for afterCreate

        }


        return $data;
    }
}
