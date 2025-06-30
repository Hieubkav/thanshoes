<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
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
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\OrderResource;
use App\Filament\Resources\CustomerResource;
use App\Filament\Resources\WebsiteVisitResource;
use App\Filament\Resources\ProductViewResource;
use App\Filament\Resources\CartResource;
use App\Filament\Resources\CartItemResource;
use App\Filament\Widgets\LiveTrackingWidget;
use App\Filament\Widgets\RealtimeNotificationsWidget;
use App\Filament\Widgets\TopCartItemsWidget;
use App\Filament\Widgets\TopCartProductsTableWidget;
use App\Filament\Widgets\CartInsightsWidget;
use App\Filament\Widgets\WebsiteTrafficWidget;
use App\Filament\Widgets\ProductViewInsightsWidget;
use App\Filament\Widgets\TrafficInsightsWidget;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('dashboard')
            ->login()
            ->brandName('ThanShoes Admin')
            ->colors([
                'primary' => Color::Orange,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->resources([
                UserResource::class,
                CustomerResource::class,
                OrderResource::class,
                CartResource::class,
                CartItemResource::class,
                WebsiteVisitResource::class,
                ProductViewResource::class,
            ])
            ->pages([
                Pages\Dashboard::class,
            ])
            ->widgets([
                WebsiteTrafficWidget::class,
                ProductViewInsightsWidget::class,
                TopCartItemsWidget::class,
                TopCartProductsTableWidget::class,
                CartInsightsWidget::class,
                TrafficInsightsWidget::class,
                LiveTrackingWidget::class,
                RealtimeNotificationsWidget::class,
                Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
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
            ->renderHook(
                PanelsRenderHook::TOPBAR_END,
                fn (): string => Blade::render('<x-filament::button
                    tag="a"
                    href="{{ route(\'shop.store_front\') }}"
                    target="_blank"
                    icon="heroicon-o-home"
                    color="gray"
                    size="sm"
                    tooltip="Mở trang chủ"
                >
                    Trang chủ
                </x-filament::button>')
            );
    }
}