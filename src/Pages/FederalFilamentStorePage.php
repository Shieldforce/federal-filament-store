<?php

namespace Shieldforce\FederalFilamentStore\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
    protected static ?string $label = 'Loja de Produtos';
    protected static ?string $navigationLabel = 'Loja de Produtos';
    protected static ?string $title = 'Loja de Produtos';
    public array $result = [];
    public array $categories = [];
    protected int $perPage = 6;
    public string $search = '';
    public ?string $selectedCategory = null;
    public ?string $data = null;
    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategory' => ['except' => null],
        'data' => ['except' => null],
        'page' => ['except' => 1], // controla a página atual
    ];

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

        $this->result = config('federal-filament-store.products_callback');
        $this->categories = config('federal-filament-store.categories_callback');
    }

    public function updated($property)
    {
        $this->filtrar();
        $this->resetPage();
    }

    public function addToCart($id)
    {
        //session()->push('cart.items', $id);

        dd("teste");
    }

    public function filtrar()
    {
        $list = $this->form->getState();

        dd($list);

        //$this->result = $list;
    }

    protected function getData(): array
    {
        return config('federal-filament-store.products_callback');
    }

    public function getPaginatedProductsProperty()
    {
        $filtered = collect($this->result)
            ->when(
                $this->search,
                fn($q) => $q->filter(fn($item) => str_contains(strtolower($item['name']), strtolower($this->search))
                    || str_contains(strtolower($item['code']), strtolower($this->search)))
            )
            ->when(
                $this->selectedCategory,
                fn($q) => $q->where('category_id', $this->selectedCategory)
            )
            ->when(
                $this->data,
                fn($q) => $q->filter(fn($item) => isset($item['created_at']) && $item['created_at'] === $this->data)
            )
            ->values()
            ->toArray();

        $page = $this->getPage();
        $offset = ($page - 1) * $this->perPage;
        $items = array_slice($filtered, $offset, $this->perPage);

        return new LengthAwarePaginator(
            $items,
            count($filtered),
            $this->perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }

    protected function getFormSchema(): array
    {
        $categoryOptions = [];
        foreach ($this->categories as $category) {
            $categoryOptions[$category['id']] = $category['name'];
        }

        return [
            Grid::make(1)->schema([
                TextInput::make('search')
                    ->label('Palavra-chave')
                    ->reactive(),

                Select::make('selectedCategory')
                    ->label('Categorias')
                    ->options($categoryOptions)
                    ->reactive(),

                DatePicker::make('data')
                    ->label('Data')
                    ->format('Y-m-d')
                    ->reactive(),
            ]),
        ];
    }

}
