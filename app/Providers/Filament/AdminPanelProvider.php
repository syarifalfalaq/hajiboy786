<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
//===========================menu navigasi tree
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
//=========

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
            ])
            //===========================menu navigasi tree
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Data Master')
                    // ->icon('heroicon-o-cog')
                    ->collapsed(),
            ])
            ->navigationItems([
                NavigationItem::make('Data Wilayah')
                    ->icon('heroicon-o-globe-alt')
                    ->group('Data Master')
                    ->sort(2)
                    ->url('/admin/provinces') // URL langsung ke halaman provinsi
                    ->isActiveWhen(fn() => request()->is('admin/provinces*') ||
                        request()->is('admin/regencies*') ||
                        request()->is('admin/districts*') ||
                        request()->is('admin/villages*')),

                NavigationItem::make('Management Vendor')
                    ->icon('heroicon-o-puzzle-piece')
                    ->group('Data Master')
                    ->sort(2)
                    ->url('/admin/vendors') // URL langsung ke halaman provinsi
                    ->isActiveWhen(
                        fn() => request()->is('admin/Vendors*') ||
                            request()->is('admin/VendorShippingRate*') ||
                            request()->is('admin/VehicleUnit*'),

                    ),

            ])




            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ]);
    }
}
