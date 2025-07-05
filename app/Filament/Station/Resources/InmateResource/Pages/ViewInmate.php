<?php

namespace App\Filament\Station\Resources\InmateResource\Pages;

use Filament\Actions;
use Filament\Actions\ViewAction;
use App\Actions\SecureEditAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use App\Actions\SecureDeleteAction;
use Filament\Resources\Pages\ViewRecord;
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
                //transfer action starts
                Action::make('Transfer')
                    ->icon('heroicon-o-arrow-right-on-rectangle'),
                //transfer action ends

                //special discharge action starts
                Action::make('Special Discharge')
                    ->icon('heroicon-o-arrow-right-on-rectangle'),
                //special discharge action ends

                // additional sentence action starts
                Action::make('Additional Sentence')
                    ->icon('heroicon-o-plus-circle'),
                //additional sentence action ends

                //amnesty action ends
                Action::make('Amnesty')
                    ->icon('heroicon-o-sparkles'),
                //amnesty action ends

                //sentence reduction action starts
                Action::make('Sentence Reduction')
                    ->icon('heroicon-o-arrow-trending-down'),
                //sentence reduction action ends

                //edit action starts
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
                        if (! \Illuminate\Support\Facades\Hash::check($data['password'], auth()->user()->password)) {
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
                //edit action ends

                //delete action starts
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
                        if (! \Illuminate\Support\Facades\Hash::check($data['password'], auth()->user()->password)) {
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
                            ->title('Record Deleted')
                            ->send();
                    }),
                //delete action ends
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
}
