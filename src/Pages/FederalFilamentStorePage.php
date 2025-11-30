<?php

namespace Shieldforce\FederalFilamentStore\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Shieldforce\FederalFilamentStore\FederalFilamentStorePlugin;

class FederalFilamentStorePage extends Page implements HasForms
{
    use InteractsWithForms;
    use WithPagination;

    protected FederalFilamentStorePlugin $plugin;
    protected static string $view = 'federal-filament-store::pages.store';
    protected static ?string $label = 'Loja';
    protected static ?string $navigationLabel = 'Loja';
    protected static ?string $title = 'Loja';
    public array $result = [];
    protected int $perPage = 8;

    public function getLayout(): string
    {
        if (request()->query('external') === '1') {
            return 'federal-filament-store::layouts.external';
        }

        return parent::getLayout();
    }

    public static function getSlug(): string
    {
        return 'external-ffs-store';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getNavigationGroup(): ?string
    {
        return config()->get('federal-filament-store.sidebar_group');
    }

    public function mount(FederalFilamentStorePlugin $plugin): void
    {
        $this->plugin = $plugin;

        if (!Auth::check()) {
            filament()
                ->getCurrentPanel()
                ->topNavigation()/*
                ->topbar(false)*/
            ;
        }

        $this->filtrar();
    }

    public function updated()
    {
        $this->resetPage();
        $this->filtrar();
    }

    public function addToCart($id)
    {
        session()->push('cart.items', $id);

        dd($id);
    }

    public function filtrar()
    {
        $list = $this->getData();

        $this->result = $list;
    }

    /*protected function getData(): array
    {
        return array_reverse($this->plugin->getProducts());

        return array_reverse([
            ['id' => 1, 'name' => 'Camiseta ShieldForce', 'price' => 79.90, 'categories' => ['Roupas'], 'image' => null],
            ['id' => 2, 'name' => 'Mouse Gamer RGB', 'price' => 149.90, 'categories' => ['Eletrônicos'], 'image' => null],
            ['id' => 3, 'name' => 'Teclado Mecânico ShieldForce', 'price' => 399.00, 'categories' => ['Eletrônicos'], 'image' => null],
            ['id' => 4, 'name' => 'Boné ShieldForce', 'price' => 59.90, 'categories' => ['Roupas'], 'image' => null],
            ['id' => 5, 'name' => 'Pulseira Smart FitBand', 'price' => 199.90, 'categories' => ['Acessórios'], 'image' => null],
            ['id' => 6, 'name' => 'Caneca Gamer Neon', 'price' => 49.90, 'categories' => ['Acessórios'], 'image' => null],
            ['id' => 7, 'name' => 'Capa de Celular Anti-Impacto', 'price' => 89.90, 'categories' => ['Acessórios'], 'image' => null],
            ['id' => 8, 'name' => 'Cadeira Gamer Thunder Pro', 'price' => 1299.00, 'categories' => ['Móveis'], 'image' => null],
            ['id' => 9, 'name' => 'Mesa de Escritório Compacta', 'price' => 599.99, 'categories' => ['Móveis'], 'image' => null],
            ['id' => 10, 'name' => 'Fone Bluetooth Bass+', 'price' => 249.90, 'categories' => ['Eletrônicos'], 'image' => null],
            ['id' => 11, 'name' => 'Camiseta Dev Dark Mode', 'price' => 89.90, 'categories' => ['Roupas'], 'image' => null],
            ['id' => 12, 'name' => 'Luminária Smart RGB', 'price' => 159.90, 'categories' => ['Decoração'], 'image' => null],
            ['id' => 13, 'name' => 'Quadro Decorativo Cyberpunk', 'price' => 129.90, 'categories' => ['Decoração'], 'image' => null],
            ['id' => 14, 'name' => 'Mini Drone 4K', 'price' => 399.90, 'categories' => ['Eletrônicos'], 'image' => null],
            ['id' => 15, 'name' => 'Smartwatch ShieldForce Active', 'price' => 599.90, 'categories' => ['Eletrônicos'], 'image' => null],
        ]);
    }*/

    protected function getData(): array
    {
        // Recupera a instância do plugin registrada no painel
        $plugin = \Filament\Facades\Filament::getPlugin(
            \Shieldforce\FederalFilamentStore\FederalFilamentStorePlugin::class
        );

        if (!$plugin) {
            return []; // fallback caso o plugin não esteja registrado
        }

        $products = $plugin->getProducts();

        return array_reverse($products);
    }

    public function getPaginatedProductsProperty()
    {
        $page = $this->getPage();
        $offset = ($page - 1) * $this->perPage;
        $items = array_slice($this->result, $offset, $this->perPage);

        $lengthAwarePaginator = new LengthAwarePaginator(
            $items,
            count($this->result),
            $this->perPage,
            $page,
            ['path' => request()->url()],
        );

        return $lengthAwarePaginator;
    }
}
