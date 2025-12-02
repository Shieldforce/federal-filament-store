<?php

namespace Shieldforce\FederalFilamentStore\Pages;

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

    protected static string  $view             = 'federal-filament-store::pages.store';
    protected static ?string $label            = 'Loja de Produtos';
    protected static ?string $navigationLabel  = 'Loja de Produtos';
    protected static ?string $title            = 'Loja de Produtos';
    protected int            $perPage          = 6;
    public array             $result           = [];
    public array             $categories       = [];
    public string            $search           = '';
    public ?string           $selectedCategory = null;
    public ?string           $price_range      = null;
    public ?string           $price_range_min  = null;
    public ?string           $price_range_max  = null;
    protected                $queryString      = [
        'search'           => ['except' => ''],
        'selectedCategory' => ['except' => null],
        'price_range'      => ['except' => null],
        'page'             => ['except' => 1],
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
        dd($this->price_range);

        $this->resetPage();
    }

    public function addToCart($id)
    {
        dd("Produto adicionado ao carrinho: {$id}");
    }

    public function getPaginatedProductsProperty()
    {
        $filtered = collect($this->result)
            ->when(
                $this->search,
                fn($q) => $q->filter(
                    fn($item) => str_contains(strtolower($item['name']), strtolower($this->search))
                        || str_contains(strtolower($item['code']), strtolower($this->search))
                )
            )
            ->when(
                $this->selectedCategory,
                fn($q) => $q->where('category_id', $this->selectedCategory)
            )
            ->when(
                $this->price_range,
                fn($q) => $q->filter(
                    fn($item) => isset($item['price'])
                        && $item['price'] >= $this->price_range_min
                        && $item['price'] <= $this->price_range_max
                )
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
                'path'  => request()->url(),
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
            Grid::make(1)->schema(
                [
                    TextInput::make('search')
                        ->label('Palavra-chave')
                        ->reactive(),

                    Select::make('selectedCategory')
                        ->label('Escolha uma categoria')
                        ->options($categoryOptions)
                        ->reactive(),

                    Select::make('price_range')
                        ->label('Média de Preço')
                        ->options(
                            [
                                "R$ 1,00 - R$ 100,00",
                                "R$ 101,00 - R$ 500,00",
                                "R$ 501,00 - R$ 1000,00",
                                "R$ 1001,00 - R$ 5.000,00",
                                "Maior que 5.000,00",
                            ]
                        )
                        ->reactive()
                        ->default("R$ 1,00 - R$ 100,00")
                        ->preload(),
                ]
            ),
        ];
    }
}
