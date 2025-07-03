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
            Action::make('back')
                ->label('Back to Convicts')
                ->color('success')
                ->icon('heroicon-o-arrow-left')
                ->url(InmateResource::getUrl('index')),
            Action::make('print')
                ->label('Print Profile')
                ->color('info')
                ->icon('heroicon-o-printer'),
            ActionGroup::make([
                Action::make('Transfer')->icon('heroicon-o-arrow-right-on-rectangle'),
                Action::make('Special Discharge')->icon('heroicon-o-arrow-right-on-rectangle'),
                Action::make('Additional Sentence')->icon('heroicon-o-plus-circle'),
                Action::make('Amnesty')->icon('heroicon-o-sparkles'),
                Action::make('Sentence Reduction')
                    ->icon('heroicon-o-arrow-trending-down'),


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
            ])
                ->button()
                ->label('More Actions'),

        ];
    }

    public function getHeading(): string
    {
        return "{$this->record->full_name}'s Profile";
    }
}
