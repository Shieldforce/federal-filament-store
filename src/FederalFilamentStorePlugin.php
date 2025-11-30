<?php

namespace Shieldforce\FederalFilamentStore;

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Illuminate\Support\Facades\Route;
use Shieldforce\FederalFilamentStore\Pages\FederalFilamentCartPage;
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

                Route::get('/ffs-cart', FederalFilamentCartPage::class)
                    ->name('ffs-cart.cart.external')
                    ->defaults('external', 1);
            })
            ->navigationItems([
                NavigationItem::make('loja')
                    ->visible()
                    ->label('Produtos')
                    ->url("/admin/ffs-store")
                    ->icon('heroicon-o-shopping-bag')
                    ->group("Loja"),
                NavigationItem::make('cart')
                    ->visible()
                    ->label('Carrinho')
                    ->url("/admin/ffs-cart")
                    ->icon('heroicon-o-shopping-cart')
                    ->group("Loja"),
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
