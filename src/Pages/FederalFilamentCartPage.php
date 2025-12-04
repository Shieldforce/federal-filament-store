<?php

namespace Shieldforce\FederalFilamentStore\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Auth;
use Shieldforce\FederalFilamentStore\Models\Cart;

class FederalFilamentCartPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string  $view            = 'federal-filament-store::pages.cart';
    protected static ?string $navigationIcon  = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Loja';
    protected static ?string $label           = 'Carrinho';
    protected static ?string $navigationLabel = 'Carrinho';
    protected static ?string $title           = 'Carrinho';
    protected array          $result          = [];
    protected Cart           $cart;
    protected array          $items;
    public float             $totalPrice;

    public function getLayout(): string
    {
        if (request()->query('external') === '1') {
            return 'federal-filament-store::layouts.external';
        }

        return parent::getLayout();
    }

    public static function getSlug(): string
    {
        return 'external-ffs-cart';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getNavigationGroup(): ?string
    {
        return config()->get('federal-filament-store.sidebar_group');
    }

    public function mount(): void
    {
        if (!Auth::check()) {
            filament()
                ->getCurrentPanel()
                ->topNavigation()/*
                ->topbar(false)*/
            ;
        }

        $this->filtrar();

        $this->cart = Cart::where("identifier", request()->cookie("ffs_identifier"))
            ->first();

        $this->items = json_decode($this->cart->items ?? [], true);

        $this->totalPrice = collect($this->items)->sum(
            function ($item) {
                return $item['price'] * $item['amount'];
            }
        );
    }

    public function updated()
    {
        $this->resetPage();
        $this->filtrar();
    }

    public function filtrar()
    {
        $data = $this->getData();
        $this->result = array_values($data);
    }

    protected function getData(): array
    {
        return [];
    }

}
