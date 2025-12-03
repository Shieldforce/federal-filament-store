<?php

namespace Shieldforce\FederalFilamentStore\Pages;

use Filament\Facades\Filament;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class FederalFilamentProductPage extends Page implements HasForms
{

    use InteractsWithForms;
    use WithPagination;

    protected static string  $view            = 'federal-filament-store::pages.product';
    protected static ?string $label           = 'Produto';
    protected static ?string $navigationLabel = 'Produto';
    protected static ?string $title           = 'Produto';
    public array             $result          = [];
    public array             $categories      = [];
    public array             $images          = [];
    public array             $product;
    public                   $uuid;

    public function getTitle(): string|Htmlable
    {
        return $this->product['name'] ?? parent::getTitle();
    }

    public function getLayout(): string
    {
        if (request()->query('external') === '1') {
            return 'federal-filament-store::layouts.external';
        }

        return parent::getLayout();
    }

    public static function getSlug(): string
    {
        return 'external-ffs-product';
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

        $this->result = config('federal-filament-store.products_callback');
        $this->categories = config('federal-filament-store.categories_callback');
        $this->uuid = explode("/", $_SERVER["REQUEST_URI"])[3] ?? null;

        $productFilter = array_filter(
            $this->result, function ($product) {
            return $product['uuid'] == $this->uuid;
        }
        );

        $this->product = reset($productFilter) ?: [];
        $this->images[] = isset($this->product['image']) ? env("APP_URL") . "/storage/" . $this->product['image']
            : 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=2070&q=80';

        foreach ($this->product['images'] ?? [] as $image) {
            $this->images[] = env("APP_URL") . "/storage/" . $image['path'];
        }
    }

    public function updated($property)
    {

    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(1)->schema(
                [
                    TextInput::make('amount')
                        ->label('Quantidade')
                        ->numeric()
                        ->required(),
                ]
            ),
        ];
    }

    public function addCart($id)
    {
        dd("teste");
    }

}

