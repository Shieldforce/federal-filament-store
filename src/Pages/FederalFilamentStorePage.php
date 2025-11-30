<?php

namespace Shieldforce\FederalFilamentStore\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class FederalFilamentStorePage extends Page implements HasForms
{
    use InteractsWithForms;
    use WithPagination;

    protected static string $view = 'federal-filament-store::pages.store';

    public string $search = '';
    public string $category = '';

    protected array $products = [
        ['id' => 1, 'name' => 'Camiseta ShieldForce', 'price' => 79.90, 'category' => 'Roupas'],
        ['id' => 2, 'name' => 'Mouse Gamer RGB', 'price' => 149.90, 'category' => 'Eletrônicos'],
        ['id' => 3, 'name' => 'Teclado Mecânico ShieldForce', 'price' => 399.00, 'category' => 'Eletrônicos'],
        ['id' => 4, 'name' => 'Boné ShieldForce', 'price' => 59.90, 'category' => 'Roupas'],
        ['id' => 5, 'name' => 'Pulseira Smart FitBand', 'price' => 199.90, 'category' => 'Acessórios'],
        ['id' => 6, 'name' => 'Caneca Gamer Neon', 'price' => 49.90, 'category' => 'Acessórios'],
        ['id' => 7, 'name' => 'Capa de Celular Anti-Impacto', 'price' => 89.90, 'category' => 'Acessórios'],
        ['id' => 8, 'name' => 'Cadeira Gamer Thunder Pro', 'price' => 1299.00, 'category' => 'Móveis'],
        ['id' => 9, 'name' => 'Mesa de Escritório Compacta', 'price' => 599.99, 'category' => 'Móveis'],
        ['id' => 10, 'name' => 'Fone Bluetooth Bass+', 'price' => 249.90, 'category' => 'Eletrônicos'],
        ['id' => 11, 'name' => 'Camiseta Dev Dark Mode', 'price' => 89.90, 'category' => 'Roupas'],
        ['id' => 12, 'name' => 'Luminária Smart RGB', 'price' => 159.90, 'category' => 'Decoração'],
        ['id' => 13, 'name' => 'Quadro Decorativo Cyberpunk', 'price' => 129.90, 'category' => 'Decoração'],
        ['id' => 14, 'name' => 'Mini Drone 4K', 'price' => 399.90, 'category' => 'Eletrônicos'],
        ['id' => 15, 'name' => 'Smartwatch ShieldForce Active', 'price' => 599.90, 'category' => 'Eletrônicos'],
    ];

    public function getLayout(): string
    {
        return request()->query('external') === '1'
            ? 'federal-filament-store::layouts.external'
            : parent::getLayout();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount()
    {
        if (!Auth::check()) {
            filament()->getCurrentPanel()->topNavigation();
        }
    }

    public function updated($field)
    {
        if (in_array($field, ['search', 'category'])) {
            $this->resetPage();
        }
    }

    public function getCategoriesProperty()
    {
        return collect($this->products)->pluck('category')->unique()->values();
    }

    public function getResultProperty()
    {
        return collect($this->products)
            ->when(
                $this->search,
                fn($q) => $q->filter(fn($p) => str_contains(strtolower($p['name']), strtolower($this->search)))
            )->when($this->category, fn($q) => $q->where('category', $this->category)
            )->values();
    }

    public function getResultPaginatedProperty()
    {
        return $this->paginateCollection($this->result, 12);
    }

    private function paginateCollection($items, int $perPage)
    {
        $page = $this->page ?? 1;
        $offset = ($page - 1) * $perPage;

        return new \Illuminate\Pagination\LengthAwarePaginator(
            array_slice($items->toArray(), $offset, $perPage),
            count($items),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function addToCart($id)
    {
        session()->push('cart.items', $id);

        $this->dispatch('notify', title: 'Adicionado ao carrinho');
    }
}
