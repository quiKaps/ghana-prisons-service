<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use App\Http\Middleware\UserStatus;
use Illuminate\Support\Facades\Auth;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\Authenticate;
use App\Http\Middleware\PanelAccessControl;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;

class HQPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('hQ')
            ->path('hq')
            //->breadcrumbs(false)
            ->favicon(asset('gps-logo.png'))
            ->brandLogo(asset('gps-logo.png'))
            ->brandLogoHeight('4rem')
            ->brandName('HQ - Ghana Prisons Service Portal') //tab

            // ->theme(asset('css/filament/station/theme.css'))
            ->colors([
                'primary' => Color::hex('#654321'),
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->collapsible(false)
                    ->label('Convicts'),
                NavigationGroup::make()
                    ->collapsible(false)
                    ->label('Remand and Trials'),
                NavigationGroup::make()
                    ->collapsible(false)
                    ->label('User Management'),
            ])
            ->navigationItems([
                NavigationItem::make('My Account')
                    ->group('User Management')
                    ->url(fn(): string => EditProfilePage::getUrl())
                    ->isActiveWhen(fn() => request()->url() === EditProfilePage::getUrl())
                    ->icon('heroicon-m-user-circle')
                    ->sort(4),
            ])
            ->discoverResources(in: app_path('Filament/HQ/Resources'), for: 'App\\Filament\\HQ\\Resources')
            ->discoverPages(in: app_path('Filament/HQ/Pages'), for: 'App\\Filament\\HQ\\Pages')
            ->pages([
            // Pages\Dashboard::class,
        ])
            ->discoverWidgets(in: app_path('Filament/HQ/Widgets'), for: 'App\\Filament\\HQ\\Widgets')
            ->widgets([
            // Widgets\AccountWidget::class,

        ])
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label(fn() => Auth::user()->name)
                    ->url(fn(): string => EditProfilePage::getUrl())
                    ->icon('heroicon-m-user-circle')
            ])
            ->plugins([
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
            ->authMiddleware([
                Authenticate::class,
            PanelAccessControl::class,
            UserStatus::class
            ]);
    }
}
