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
            })
            ->navigationItems([
                NavigationItem::make('loja')
                    ->visible()
                    ->label('Produtos')
                    ->url("/admin/ffs-store")
                    ->sort(998)
                    ->icon('heroicon-o-shopping-bag'),
                NavigationItem::make('cart')
                    ->label('')
                    ->visible()
                    ->label('Carrinho')
                    ->url("/admin/ffs-cart")
                    ->icon('
                        <svg xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            class="w-7 h-7">  <!-- tamanho do ícone -->
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.6 8M7 13l-3-6m16 6l1.6 8M5 21h14" />
                        </svg>
                    ')
                    ->sort(999)
                    ->badge(function () {
                        return session()->get('cart_count', 0);
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
