<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use App\Models\RemandTrial;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Route;
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
use App\Filament\Station\Resources\UserResource\Pages\CreateUser;
use App\Filament\Station\Resources\InmateResource\Pages\CreateInmate;
use App\Filament\Station\Resources\InmateResource\Pages\ConvictedForiegners;
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
            ->navigationGroups([
                NavigationGroup::make()
                ->collapsible(false)
                    ->label('General Management'),
                NavigationGroup::make()
                ->collapsible(false)
                    ->label('Inmate Management'),
                NavigationGroup::make()
                ->collapsible(false)
                ->label('Cell Management'),
            NavigationGroup::make()
                ->collapsible(false)
                ->label('Remand and Trials'),
            NavigationGroup::make()
                ->collapsible(false)
                ->label('Escapees'),
            NavigationGroup::make()
                ->collapsible(false)
                ->label('User Management'),
            ])
            ->navigationItems([
            NavigationItem::make('Convict Admission Form')
                    ->url(fn(): string => CreateInmate::getUrl())
                    ->icon('heroicon-o-user-plus')
                ->group('Convicts')
                    ->isActiveWhen(fn() => request()->url() === InmateResource::getUrl('create'))
                ->sort(3),
                NavigationItem::make('Add Users')
                    ->url(fn(): string => CreateUser::getUrl())
                    ->icon('heroicon-o-user-plus')
                    ->group('User Management')
                ->isActiveWhen(fn() => request()->routeIs('filament.station.pages.create-user'))
                    ->sort(3),
            NavigationItem::make('Remand & Trial Admission Form')
                ->url(fn(): string => CreateRemandTrial::getUrl())
                ->icon('heroicon-o-user-plus')
                ->group('Inmate Management')
                ->isActiveWhen(fn() => request()->url() === RemandTrialResource::getUrl('create'))
                ->sort(3),
            NavigationItem::make('Forigners - Convicts')
                ->url(fn(): string => ConvictedForiegners::getUrl())
                ->icon('heroicon-o-user-plus')
                ->group('Inmate Management')
                ->isActiveWhen(fn() => request()->url() === fn(): string => ConvictedForiegners::getUrl())
            ])
            ->discoverResources(in: app_path('Filament/Station/Resources'), for: 'App\\Filament\\Station\\Resources')
            ->discoverPages(in: app_path('Filament/Station/Pages'), for: 'App\\Filament\\Station\\Pages')
            ->pages([
                \App\Filament\Station\Pages\ViewInmate::class,
            ])
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
            ->authMiddleware([
                Authenticate::class,
            PanelAccessControl::class
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
