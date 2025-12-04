<?php

namespace Shieldforce\FederalFilamentStore;

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
use Ramsey\Uuid\Uuid;
use Shieldforce\FederalFilamentStore\Enums\StatusCartEnum;
use Shieldforce\FederalFilamentStore\Models\Cart;
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
            ->routes(
                function () {
                    Route::get('/ffs-store', FederalFilamentStorePage::class)
                        ->name('ffs-store.store.external')
                        ->defaults('external', 1);

                    Route::get('/ffs-product/{uuid?}', FederalFilamentProductPage::class)
                        ->name('ffs-product.product.external')
                        ->defaults('external', 1);

                    Route::get('/ffs-cart', FederalFilamentCartPage::class)
                        ->name('ffs-cart.cart.external')
                        ->defaults('external', 1);

                    Route::get(
                        '/cart-count', function () {
                        $identifier = request()->cookie('ffs_identifier');
                        $cartModel = Cart::where("identifier", $identifier)->first();
                        return response()->json(collect(json_decode($cartModel->items, true))->sum('amount'));
                    }
                    );
                }
            )
            ->navigationItems(
                [
                    NavigationItem::make('loja')
                        ->visible()
                        ->label('Loja')
                        ->url("/admin/ffs-store")
                        ->sort(998)
                        ->icon('heroicon-o-shopping-bag'),
                    NavigationItem::make('cart')
                        ->visible(fn() => Cart::where("identifier", request()->cookie("ffs_identifier"))
                            ->exists())
                        ->label('Carrinho')
                        ->url("/admin/ffs-cart")
                        ->icon('heroicon-o-shopping-cart')
                        ->badgeTooltip('Itens do carrinho')
                        ->sort(999)
                        ->badge(
                            function () {

                                $identifierVerify = request()->cookie('ffs_identifier');

                                $cartModel = Cart::where("identifier", $identifierVerify)
                                    ->whereNotNull("identifier")
                                    ->where("status", "!=", StatusCartEnum::finalizado->value)
                                    ->first();

                                if (isset($cartModel->id)) {
                                    return collect(json_decode($cartModel->items, true))
                                        ->sum('amount');
                                }

                                $tokenSession = request()->session()->get('_token');

                                $minutes = 60 * 24 * 30; // 30 dias

                                $tt = Cookie::make(
                                    name: 'ffs_identifier',
                                    value: $tokenSession,
                                    minutes: $minutes
                                );

                                Cookie::queue($tt);

                                $identifier = $tt->getValue();

                                $cartModel = Cart::updateOrCreate(
                                    ["identifier" => $identifier],
                                    ['status' => StatusCartEnum::comprando->value]
                                );

                                return collect(json_decode($cartModel->items, true))
                                    ->sum('amount');

                            }, 'danger'
                        ),
                ]
            )
            ->pages(
                [
                    \Shieldforce\FederalFilamentStore\Pages\FederalFilamentStorePage::class,
                    \Shieldforce\FederalFilamentStore\Pages\FederalFilamentProductPage::class,
                    \Shieldforce\FederalFilamentStore\Pages\FederalFilamentCartPage::class,
                ]
            );
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
    ): static {
        $this->labelGroupSidebar = $labelGroupSidebar;
        return $this;
    }

    public function getLabelGroupSidebar()
    {
        return $this->labelGroupSidebar;
    }
}
