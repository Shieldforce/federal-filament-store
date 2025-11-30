<?php

namespace Shieldforce\FederalFilamentStore\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class FederalFilamentStorePage extends Page implements HasForms
{
    use InteractsWithForms;
    use WithPagination;

    protected static string $view = 'federal-filament-store::pages.store';
    protected static ?string $label = 'Loja';
    protected static ?string $navigationLabel = 'Loja';
    protected static ?string $title = 'Loja';
    public array $result = [];
    protected int $perPage = 20;

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

    protected function getData(): array
    {
        return array_reverse([
            ['id' => 1, 'name' => 'Camiseta ShieldForce', 'price' => 79.90, 'category' => 'Roupas', 'image' => null],
            ['id' => 2, 'name' => 'Mouse Gamer RGB', 'price' => 149.90, 'category' => 'Eletrônicos', 'image' => null],
            ['id' => 3, 'name' => 'Teclado Mecânico ShieldForce', 'price' => 399.00, 'category' => 'Eletrônicos', 'image' => null],
            ['id' => 4, 'name' => 'Boné ShieldForce', 'price' => 59.90, 'category' => 'Roupas', 'image' => null],
            ['id' => 5, 'name' => 'Pulseira Smart FitBand', 'price' => 199.90, 'category' => 'Acessórios', 'image' => null],
            ['id' => 6, 'name' => 'Caneca Gamer Neon', 'price' => 49.90, 'category' => 'Acessórios', 'image' => null],
            ['id' => 7, 'name' => 'Capa de Celular Anti-Impacto', 'price' => 89.90, 'category' => 'Acessórios', 'image' => null],
            ['id' => 8, 'name' => 'Cadeira Gamer Thunder Pro', 'price' => 1299.00, 'category' => 'Móveis', 'image' => null],
            ['id' => 9, 'name' => 'Mesa de Escritório Compacta', 'price' => 599.99, 'category' => 'Móveis', 'image' => null],
            ['id' => 10, 'name' => 'Fone Bluetooth Bass+', 'price' => 249.90, 'category' => 'Eletrônicos', 'image' => null],
            ['id' => 11, 'name' => 'Camiseta Dev Dark Mode', 'price' => 89.90, 'category' => 'Roupas', 'image' => null],
            ['id' => 12, 'name' => 'Luminária Smart RGB', 'price' => 159.90, 'category' => 'Decoração', 'image' => null],
            ['id' => 13, 'name' => 'Quadro Decorativo Cyberpunk', 'price' => 129.90, 'category' => 'Decoração', 'image' => null],
            ['id' => 14, 'name' => 'Mini Drone 4K', 'price' => 399.90, 'category' => 'Eletrônicos', 'image' => null],
            ['id' => 15, 'name' => 'Smartwatch ShieldForce Active', 'price' => 599.90, 'category' => 'Eletrônicos', 'image' => null],
        ]);
    }

    public function getPaginatedProductsProperty()
    {
        $page = $this->getPage();
        $offset = ($page - 1) * $this->perPage;
        $items = array_slice($this->result, $offset, $this->perPage);

        return new LengthAwarePaginator(
            $items,
            count($this->result),
            $this->perPage,
            $page,
            ['path' => request()->url()]
        );
    }
}
