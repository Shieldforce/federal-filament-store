<?php

namespace Shieldforce\FederalFilamentStore\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class StoreList extends Component
{
    use WithPagination;

    public int $page = 1;   // 👈 CORREÇÃO AQUI
    public string $search = '';
    public string $category = '';

    protected $paginationTheme = 'tailwind';

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    // produtos simulados
    public array $products = [
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
    ];

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
            ->when($this->search, fn($q) => $q->filter(fn($p) => str_contains(strtolower($p['name']), strtolower($this->search))
            )
            )
            ->when($this->category, fn($q) => $q->where('category', $this->category)
            )
            ->values();
    }

    public function mount()
    {
        $this->usePageResolver(fn() => $this->page);
    }

    public function resultPaginated()
    {
        $perPage = 12;
        $items = $this->result;

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items->forPage($this->page, $perPage),
            count($items),
            $perPage,
            $this->page,
            ['path' => url()->current()]
        );
    }

    public function addToCart($id)
    {
        session()->push('cart.items', $id);

        dd($id);
    }

    public function render()
    {
        return view('federal-filament-store::livewire.store-list');
    }
}
