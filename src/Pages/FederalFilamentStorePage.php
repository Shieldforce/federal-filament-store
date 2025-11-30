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
        [
            'id' => 1,
            'name' => 'Camiseta ShieldForce',
            'price' => 79.90,
            'category' => 'Roupas',
            'image' => '/images/camisa1.jpg',
        ],
        [
            'id' => 2,
            'name' => 'Mouse Gamer',
            'price' => 149.90,
            'category' => 'Eletrônicos',
            'image' => '/images/mouse.jpg',
        ],
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
            filament()
                ->getCurrentPanel()
                ->topNavigation();
        }
    }

    public function updated()
    {
        $this->resetPage();
    }

    public function getCategoriesProperty()
    {
        return collect($this->products)
            ->pluck('category')
            ->unique()
            ->values();
    }

    public function getResultProperty()
    {
        return collect($this->products)
            ->when($this->search, fn ($q) =>
            $q->filter(fn ($p) =>
            str_contains(strtolower($p['name']), strtolower($this->search))
            )
            )
            ->when($this->category, fn ($q) =>
            $q->where('category', $this->category)
            )
            ->values();
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
