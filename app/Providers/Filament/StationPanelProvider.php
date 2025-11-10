<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use App\Models\RemandTrial;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use App\Http\Middleware\UserStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\PasswordReset;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;
use App\Filament\Station\Pages\ViewInmate;
use Filament\Http\Middleware\Authenticate;
use App\Http\Middleware\PanelAccessControl;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use App\Filament\Station\Resources\InmateResource;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Station\Resources\RemandTrialResource;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use App\Filament\Station\Resources\UserResource\Pages\CreateUser;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use App\Filament\Station\Resources\InmateResource\Pages\CreateInmate;
use App\Filament\Station\Resources\InmateResource\Pages\ConvictedForiegners;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;
use App\Filament\Station\Resources\RemandTrialResource\Pages\CreateRemandTrial;

class StationPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('station')
            ->path('station')
            ->breadcrumbs(false)
            ->colors([
            'primary' => Color::hex('#654321'),
            ])
            ->topNavigation(fn() => Auth::user()?->user_type === 'officer')
            ->sidebarCollapsibleOnDesktop()
            //->viteTheme('resources/css/filament/station/theme.css')
            ->theme(asset('css/filament/station/theme.css'))
            ->favicon(asset('gps-logo.png'))
            ->brandLogo(asset('gps-logo.png'))
            ->databaseNotifications()
            ->brandLogoHeight('4rem')
            ->brandName(Auth::user()?->station?->name ?? 'Ghana Prisons Service Portal') //tab
            ->navigationGroups([
            NavigationGroup::make()
                ->collapsible(false)
                ->label('Convicts'),
            NavigationGroup::make()
                ->collapsible(false)
                ->label('Remand and Trials'),
            NavigationGroup::make()
                ->collapsible(false)
                ->label('Facility Management'),
            ])
            ->navigationItems([
            NavigationItem::make('Admit a Convict')
                ->url(fn(): string => CreateInmate::getUrl())
                ->icon('heroicon-o-user-plus')
                ->group('Convicts')
                ->isActiveWhen(fn(): bool => request()->routeIs(CreateInmate::getRouteName()))
                ->sort(3),
            NavigationItem::make('Add Users')
                ->url(fn(): string => CreateUser::getUrl())
                ->icon('heroicon-o-user-plus')
                ->visible(fn() => Auth::user()?->user_type === 'prison_admin')
                ->group('Facility Management')
                ->isActiveWhen(fn() => request()->routeIs('filament.station.pages.create-user'))
                ->sort(3),
            NavigationItem::make('My Account')
                ->group('Facility Management')
                ->url(fn(): string => EditProfilePage::getUrl())
                ->isActiveWhen(fn() => request()->url() === EditProfilePage::getUrl())
                ->icon('heroicon-m-user-circle')
                ->sort(4),
            NavigationItem::make('Admit on Remand or Trial')
                ->url(fn(): string => CreateRemandTrial::getUrl())
                ->icon('heroicon-o-user-plus')
                ->group('Remand and Trials')
                ->isActiveWhen(fn() => request()->url() === RemandTrialResource::getUrl('create'))
                ->sort(3),

        ])
            ->discoverResources(in: app_path('Filament/Station/Resources'), for: 'App\\Filament\\Station\\Resources')
            ->discoverPages(in: app_path('Filament/Station/Pages'), for: 'App\\Filament\\Station\\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Station/Widgets'), for: 'App\\Filament\\Station\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
        ])
            ->plugins([
            FilamentSpatieLaravelBackupPlugin::make(),
                FilamentEditProfilePlugin::make()
                    ->slug('my-profile')
                    ->setTitle('My Profile')
                    ->setNavigationLabel('My Profile')
                    ->setNavigationGroup('Group Profile')
                    ->setIcon('heroicon-o-user')
                    ->setSort(10)
                    ->shouldRegisterNavigation(false)
                    ->shouldShowEmailForm()
                    ->shouldShowDeleteAccountForm(false)
                    ->shouldShowSanctumTokens(false)
                    ->shouldShowBrowserSessionsForm()
                    ->shouldShowAvatarForm()
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label(fn() => Auth::user()->name)
                    ->url(fn(): string => EditProfilePage::getUrl())
                ->icon('heroicon-m-user-circle')
        ])
            ->authMiddleware([
            Authenticate::class,
            PanelAccessControl::class,
            //PasswordReset::class,
            UserStatus::class
            ]);
    }

    public function boot(): void
    {
        FilamentColor::register([
            'brown' => Color::hex('#654321'),
            'cream' => Color::hex('#F9E4BC'),
            'pink' => Color::hex('#FFC0CB'),
            'blue' => Color::hex('#779ECB'),
            'green' => Color::hex('#79B791'),
            'purple' => Color::hex('#CE93D8'),
        ]);
    }
}
