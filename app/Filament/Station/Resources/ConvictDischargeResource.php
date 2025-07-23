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
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\SoftDeletingScope;
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
            ->bulkActions([

            //

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
