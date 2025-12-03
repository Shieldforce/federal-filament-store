<?php

namespace Shieldforce\FederalFilamentStore;

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Illuminate\Support\Facades\Route;
use Shieldforce\FederalFilamentStore\Middleware\SetStoreMiddleware;
use Shieldforce\FederalFilamentStore\Pages\FederalFilamentCartPage;
use Shieldforce\FederalFilamentStore\Pages\FederalFilamentProductPage;
use Shieldforce\FederalFilamentStore\Pages\FederalFilamentStorePage;

class FederalFilamentStorePlugin implements Plugin
{
    public string $labelGroupSidebar = "Loja";

    public function getId(): string
    {
        return 'federal-filament-store';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->routes(function () {
                Route::get('/ffs-store', FederalFilamentStorePage::class)
                    ->name('ffs-store.store.external')
                    ->defaults('external', 1);

                Route::get('/ffs-product/{uuid?}', FederalFilamentProductPage::class)
                    ->name('ffs-product.product.external')
                    ->defaults('external', 1);

                Route::get('/ffs-cart', FederalFilamentCartPage::class)
                    ->name('ffs-cart.cart.external')
                    ->defaults('external', 1);

                Route::get('/api/cart-count', function () {
                    $cart = json_decode(request()->cookie('cart_items', '[]'), true);
                    return response()->json(collect($cart)->sum('amount'));
                });
            })
            ->navigationItems([
                NavigationItem::make('loja')
                    ->visible()
                    ->label('Loja')
                    ->url("/admin/ffs-store")
                    ->sort(998)
                    ->icon('heroicon-o-shopping-bag'),
                NavigationItem::make('cart')
                    ->visible()
                    ->label('Carrinho')
                    ->url("/admin/ffs-cart")
                    ->icon('heroicon-o-shopping-cart')
                    ->badgeTooltip('Itens do carrinho')
                    ->sort(999)
                    ->badge(function () {
                        $cart = json_decode(request()->cookie('cart_items', '[]'), true);
                        return collect($cart)->sum('amount');
                    }, 'danger'),
            ])
            ->pages([
                \Shieldforce\FederalFilamentStore\Pages\FederalFilamentStorePage::class,
                \Shieldforce\FederalFilamentStore\Pages\FederalFilamentProductPage::class,
                \Shieldforce\FederalFilamentStore\Pages\FederalFilamentCartPage::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        config()->set('federal-filament-store.sidebar_group', $this->labelGroupSidebar);
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function setLabelGroupSidebar(
        string $labelGroupSidebar
    ): static
    {
        $this->labelGroupSidebar = $labelGroupSidebar;
        return $this;
    }

    public function getLabelGroupSidebar()
    {
        return $this->labelGroupSidebar;
    }
}
