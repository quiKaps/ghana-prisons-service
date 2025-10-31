<?php

namespace App\Filament\Station\Resources\InmateResource\Pages;

use App\Filament\Station\Resources\InmateResource;
use App\Models\Inmate;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListInmates extends ListRecords
{
    protected static string $resource = InmateResource::class;

    protected function authorizeAccess(): void
    {
        abort_unless(
            Auth::user()->user_type === 'prison_admin',
            403,
            'Unauthorized Action!'
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Admit A Convict'),
                 Actions\Action::make('edit')
                    ->label('Import Convicts')
                    ->icon('heroicon-o-arrow-up')
                    ->color('info')
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
                            'filament.station.resources.inmates.index',
                           
                        );
                    }),
              
        ];
    }

    public function getTabs(): array
    {
        return [

            'today_admissions' => Tab::make('All Admissions Today')
                ->modifyQueryUsing(
                fn(Builder $query) => $query->whereDate('created_at', now()->toDateString())
                )
                ->badge(Inmate::whereDate('created_at', now()->toDateString())->count()),

            'Active' => Tab::make('Active')
                ->modifyQueryUsing(fn(Builder $query) => $query->active())
                ->badge(fn() => Inmate::active()->count()),

            'foreigners' => Tab::make('Foreigners')
                ->modifyQueryUsing(fn(Builder $query) => $query->active()
                    ->where('nationality', '!=', 'ghana'))
                ->badge(fn() => Inmate::active()
                    ->where('nationality', '!=', 'ghana')
                    ->count()),

            'escape' => Tab::make('Escapees')
                ->modifyQueryUsing(fn(Builder $query) => $query->escapees())
                ->badge(\App\Models\Inmate::escapees()->count()),

            'Recidivists' => Tab::make('Recidivists')
                ->modifyQueryUsing(fn(Builder $query) => $query->recidivists())
                ->badge(fn() => Inmate::recidivists()->count()),

            'onTrial' => Tab::make('CT')
                ->modifyQueryUsing(fn(Builder $query) => $query->convictOnTrial())
                ->badge(fn() => Inmate::convictOnTrial()->count()),

            'condemn' => Tab::make('Condemn')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->withSentenceType('sentence', 'death')
                )
                ->badge(
                    fn() =>
                    Inmate::withSentenceType('sentence', 'death')->count()
                ),

            'manslaughter' => Tab::make('Manslaughter')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->withSentenceType('offence', 'manslaughter')
                )
                ->badge(
                    fn() =>
                    Inmate::withSentenceType('offence', 'manslaughter')->count()
                ),

            'murder' => Tab::make('Murder')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->withSentenceType('offence', 'murder')
                )
                ->badge(
                    fn() =>
                    Inmate::withSentenceType('offence', 'murder')->count()
                ),

            'robbery' => Tab::make('Robbery')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->withSentenceType('offence', 'robbery')
                )
                ->badge(
                    fn() =>
                    Inmate::withSentenceType('offence', 'robbery')->count()
                ),

            'lifer' => Tab::make('Lifer')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->withSentenceType('sentence', 'life')
                )
                ->badge(
                    fn() =>
                    Inmate::withSentenceType('sentence', 'life')->count()
                ),

            'others' => Tab::make('Others')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                $query->withoutOffences()
                )
                ->badge(
                    fn() =>
                Inmate::withoutOffences()->count()
                ),
        ];
    }
}
