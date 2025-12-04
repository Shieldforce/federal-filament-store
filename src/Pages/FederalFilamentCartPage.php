<?php

namespace Shieldforce\FederalFilamentStore\Pages;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
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
    protected static ?string $title           = 'Aqui estão seus produtos do carrinho!';
    protected array          $result          = [];
    public string            $email           = "";
    public string            $password        = "";
    public bool              $is_user         = false;
    protected array          $items           = [];
    protected Cart           $cart;
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

        $this->loadData();
    }

    public function loadData()
    {
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
        $this->loadData();
    }

    public function submit()
    {
        $data = $this->form->getState();

        dd($data);
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(1)->schema(
                [
                    Toggle::make("is_user")
                        ->label("Já tenho conta")
                        ->default(false)
                        ->reactive()
                        ->live(),

                    TextInput::make('email')
                        ->label('E-mail')
                        ->reactive()
                        ->live()
                        ->visible(fn(Get $get) => $get("is_user"))
                        ->email()
                        ->required(),

                    TextInput::make('password')
                        ->label('Senha')
                        ->reactive()
                        ->live()
                        ->visible(fn(Get $get) => $get("is_user"))
                        ->password()
                        ->required(),
                ]
            ),
        ];
    }

}
