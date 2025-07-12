<?php

namespace App\Filament\Station\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Actions\SecureEditAction;
use App\Actions\SecureDeleteAction;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Station\Resources\UserResource\Pages;
use App\Filament\Station\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Facility Management';

    protected static ?string $navigationLabel = 'All Users';

    protected ?string $subheading = 'Manage and track users in this facility';



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
                        ->label('Officer Number')
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

                        ->unique(User::class, 'email', ignoreRecord: true)
                                    ->maxLength(50),
                                Forms\Components\Select::make('user_type')
                                    ->label('User Type')
                                    ->required()
                                    ->placeholder('Select User Type')
                                    ->options([
                                        'officer' => 'Prison Officer',
                        'prison_admin' => 'Prison Administrator',
                        ]),

                    Toggle::make('is_active')
                        ->label("User Active Status")
                        ->helperText('Click to toggle user on/off')
                        ->default(true)
                        ->inline(false)
                        ->onColor('success')
                        ->onIcon('heroicon-m-bolt')
                        ->offIcon('heroicon-o-x-circle')
                ])->columns(2),
                    ]),
                Group::make()
                    ->schema([
                Forms\Components\Section::make('')
                            ->schema([
                    FileUpload::make('avatar_url')
                                    ->label('Officer Photo')
                                    ->image()
                                    ->maxSize(1024)
                        //->disk('public')
                        //->directory('officer_photos')
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
                ->label("Officer")
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
                default => 'danger',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'officer' => 'Prison Officer',
                'prison_admin' => 'Prison Administrator',
                default => 'Unknown',
                    }),
            IconColumn::make('is_active')
                ->label('Status')
                ->tooltip('Only active users can login and access the system')
                ->boolean()
                ->trueIcon('heroicon-s-check-circle')
                ->falseIcon('heroicon-o-x-circle'),
                IconColumn::make('password_changed_at')
                    ->label('Password Changed')
                    ->boolean()
                    ->trueIcon('heroicon-s-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                ->state(fn($record): bool => !is_null($record->password_changed_at)),
            ])
            ->filters([
                //
            ])
            ->actions([
            ActionGroup::make([
                SecureEditAction::make('edit', 'filament.station.resources.users.edit')
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(fn($record) => $record->serial_number . '-' . strtoupper($record->rank) . '-' . $record->name . ' Details')
                    ->schema([
                ImageEntry::make('avatar_url')
                            ->label('Officer Photo')
                            ->disk('public')
                            ->defaultImageUrl('https://ui-avatars.com/api/?name=Officer+Photo&background=random')
                            ->height(100)
                            ->circular()
                            ->columnSpanFull(),
                        TextEntry::make('name'),
                        TextEntry::make('serial_number'),
                        TextEntry::make('rank')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'dg' => 'primary',
                                'ddg' => 'secondary',
                                'doc' => 'warning',
                                'dcp' => 'danger',
                                'acp' => 'success',
                                'csp' => 'info',
                                'sp' => 'primary',
                                'dsp' => 'secondary',
                                'asp' => 'success',
                                'coi' => 'info',
                                'soi' => 'warning',
                                'oi' => 'danger',
                                'lo' => 'primary',
                                'sco' => 'secondary',
                                default => 'default',
                            })
                            ->formatStateUsing(fn($state) => match ($state) {
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
                                'sco' => 'Second Class Officer',
                                default => $state,
                            }),
                        TextEntry::make('phone'),
                        TextEntry::make('email'),
                        TextEntry::make('station.name'),
                        TextEntry::make('user_type')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'officer' => 'info',
                    'prison_admin' => 'warning',
                    default => 'danger',
                            })
                            ->formatStateUsing(fn($state) => match ($state) {
                                'officer' => 'Prison Officer',
                    'prison_admin' => 'Prison Administrator',
                    default => 'Unknown',
                            }),
                        TextEntry::make('is_active')
                            ->label('User Status')
                            ->formatStateUsing(fn(bool $state) => match ($state) {
                                true => 'Active',
                                false => 'Inactive'
                            })
                            ->badge()
                            ->color(fn(bool $state): string => match ($state) {
                                true => 'success',
                                false => 'danger',
                            }),
                    ])->columns(2)
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('station_id', Auth::user()->station_id);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
