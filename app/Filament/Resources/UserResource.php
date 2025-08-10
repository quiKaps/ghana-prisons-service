<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Actions\SecureEditAction;
use App\Actions\SecureDeleteAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\Pages\ViewUser;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Forms\Components\Section::make('User Details')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label("Officer's Name")
                                    ->placeholder('e.g. Ohene Adjei Samuel')
                                    ->required()
                                    ->columnSpanFull()
                                    ->maxLength(50),
                                Forms\Components\Select::make('rank')
                                    ->required()
                                    ->placeholder('Select Officer Rank')
                                    ->label('Officer Rank')
                                    ->options([
                                        'dg' => 'Director-General',
                                        'ddg' => 'Deputy Director-General',
                                        'doc' => 'Director of Corrections',
                                        'dcp' => 'Deputy Director of Corrections',
                                        'acp' => 'Assistant Director of Corrections',
                                        'csp' => 'Chief Superintendent',
                                        'sp' => 'Superintendent',
                                        'dsp' => 'Deputy Superintendent',
                                        'asp' => 'Assistant Superintendent',
                                        'coi' => 'Chief Officer',
                                        'soi' => 'Senior Officer',
                                        'oi' => 'Officer',
                                        'lo' => 'Lance Officer',
                                        'sco' => 'Second Class Officer'
                                    ]),
                                Forms\Components\TextInput::make('serial_number')
                                    ->label('Officer Serial Number')
                                    ->placeholder('e.g. 112')
                                    ->required()
                                    ->unique(User::class, 'serial_number', ignoreRecord: true)
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Officer Phone Number')
                                    ->placeholder('e.g. 024-123-4567')
                                    ->required()
                                    ->unique(User::class, 'phone', ignoreRecord: true)
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('email')
                                    ->label('Officer Email Address')
                                    ->placeholder('e.g. example@example.com')
                                    ->required()
                                    ->unique(User::class, 'email', ignoreRecord: true)
                                    ->maxLength(50),
                                Forms\Components\Select::make('user_type')
                                    ->label('User Type')
                                    ->required()
                                    ->placeholder('Select User Type')
                                    ->options([
                                        'officer' => 'Prison Officer',
                                        'prison_admin' => 'Prison Administrator',
                                        'hq_admin' => 'Headquarters Administrator',
                                    ]),
                                Forms\Components\Select::make('station_id')
                                    ->label('Prison Facility')
                                    ->relationship('station', 'name')
                                    ->required()
                                    ->placeholder('Select Prison Facility')
                                    ->searchable()
                                    ->preload()
                                    ->options(fn() => \App\Models\Station::all()->pluck('name', 'id'))
                                    ->columnSpanFull(),
                            ])->columns(2),
                    ]),
                Group::make()
                    ->schema([
                        Forms\Components\Section::make('')
                            ->schema([
                                FileUpload::make('photo')
                                    ->label('Officer Photo')
                                    ->image()
                                    ->maxSize(1024)
                                    ->disk('public')
                                    ->directory('officer_photos')
                                    ->columnSpanFull(),
                            ])->columns(1),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label("Officer's Name")
                    ->formatStateUsing(fn($record) => $record->serial_number . '-' . strtoupper($record->rank) . '-' . $record->name)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label("Phone Number")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_type')
                    ->label('User Type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'officer' => 'info',
                        'prison_admin' => 'warning',
                        'hq_admin' => 'success',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'officer' => 'Prison Officer',
                        'prison_admin' => 'Prison Administrator',
                        'hq_admin' => 'Headquarters Administrator',
                        default => 'Unknown',
                    }),
                // ToggleColumn::make('is_active')
                //     ->label('Active Status'),
                IconColumn::make('password_changed_at')
                    ->label('Password Changed')
                    ->boolean()
                    ->trueIcon('heroicon-s-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->state(fn($record): bool => !is_null($record->password_changed_at)),
                Tables\Columns\TextColumn::make('station.name')
                    ->label('Prison Facility')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('prison_officers')
                    ->label('Prison Officers')
                    ->query(fn(Builder $query): Builder => $query->where('user_type', 'officer')),
                Filter::make('prison_administrators')
                    ->label('Prison Administrators')
                    ->query(fn(Builder $query): Builder => $query->where('user_type', 'prison_admin')),
                Filter::make('prison_hq_admins')
                    ->label('Prison HQ Administrators')
                    ->query(fn(Builder $query): Builder => $query->where('user_type', 'hq_admin')),
            ])
            ->actions([
                ActionGroup::make([
                    SecureEditAction::make('edit', 'filament.admin.resources.users.edit')
                        ->modalWidth('md')
                        ->modalHeading('Protected Data Access')
                        ->modalDescription('This is a secure area of the application. Please confirm your password before continuing.')
                        ->label('Edit User')
                        ->modalSubmitActionLabel('Authenticate'),
                    SecureDeleteAction::make('delete')
                        ->label('Delete User'),
                    Action::make('resetpassword')
                        ->label('Reset Password')
                        ->icon('heroicon-s-key')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading("Reset Password")
                        ->modalDescription('Are you sure you\'d like to proceed? This will reset user password')
                        ->modalSubmitActionLabel('Reset Password')
                        ->modalIcon('heroicon-s-key')
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
                                    ->body('You must confirm your password to complete this action.')
                                    ->send();

                                return;
                            }

                            $record->update([
                                'password' => Hash::make('password'),
                                'password_changed_at' => null
                            ]);

                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title('Password reset successful!')
                                ->send();
                        })
                ])->button()

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
