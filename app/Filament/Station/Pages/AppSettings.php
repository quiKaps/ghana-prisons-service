<?php

namespace App\Filament\Station\Pages;

use Filament\Forms\Form;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;

class AppSettings extends Page implements HasForms
{
        use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.station.pages.app-settings';

      public function mount(): void 
    {
        $this->form->fill();
    }
 
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
            ])
            ->statePath('data');
    } 
}
