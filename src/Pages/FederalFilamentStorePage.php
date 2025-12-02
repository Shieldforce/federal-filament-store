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

    protected static string  $view               = 'federal-filament-store::pages.store';
    protected static ?string $label              = 'Loja de Produtos';
    protected static ?string $navigationLabel    = 'Loja de Produtos';
    protected static ?string $title              = 'Loja de Produtos';
    public int               $perPage            = 6;
    public int               $page               = 1;
    public array             $result             = [];
    public array             $categories         = [];
    public array             $productsCategories = [];
    public string            $search             = '';
    public ?string           $selectedCategory   = null;
    public ?string           $price_range        = null;
    public ?string           $price_range_min    = null;
    public ?string           $price_range_max    = null;
    protected                $queryString        = [
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
        $this->productsCategories = $this->arrayCategoriesExtract();
    }

    public function arrayCategoriesExtract()
    {
        $cats = array_column($this->result, 'categories');
        $productsCategories = [];
        foreach ($cats as $categories) {
            foreach ($categories as $category) {
                $productsCategories[] = $category;
            }
        }

        $unique = array_values(
            array_reduce(
                $productsCategories, function ($carry, $item) {
                $carry[$item['id']] = $item;
                return $carry;
            },  []
            )
        );

        return $unique;
    }

    public function updated($property)
    {
        if ($property == 'price_range') {
            $this->rangePriceMount();
        }

        $this->resetPage();
    }

    public function rangePriceMount()
    {
        $values = $this->rangePriceValue();
        $this->price_range_min = $values['min'] ?? null;
        $this->price_range_max = $values['max'] ?? null;
    }

    public function rangePriceValue()
    {
        $return = [
            1 => ['min' => 0, 'max' => 9999999999],
            2 => ['min' => 1.00, 'max' => 100.00],
            3 => ['min' => 101.00, 'max' => 500.00],
            4 => ['min' => 501.00, 'max' => 1000.00],
            5 => ['min' => 1001.00, 'max' => 5000.00],
            6 => ['min' => 5000.00, 'max' => 9999999999],
        ];

        return $return[$this->price_range] ?? ['min' => 0, 'max' => 9999999999];
    }

    public function addToCart($id)
    {
        redirect("/admin/ffs-product/$id");
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->price_range = null;
        $this->selectedCategory = null;
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
                fn($q) => $q->filter(
                    function ($item) {
                        return collect($item['categories'] ?? [])
                            ->contains(fn($cat) => $cat['id'] == $this->selectedCategory);
                    }
                )
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
        foreach ($this->productsCategories as $category) {
            $categoryOptions[$category['id']] = $category['name'];
        }

        return [
            Grid::make(1)->schema(
                [
                    TextInput::make('search')
                        ->label('Palavra-chave')
                        ->reactive()
                        ->extraAttributes(
                            [
                                'class' => 'text-sm h-8 px-2'
                            ]
                        ),

                    Select::make('selectedCategory')
                        ->label('Escolha uma categoria')
                        ->options($categoryOptions)
                        ->reactive()
                        ->extraAttributes(
                            [
                                'class' => 'text-sm h-8 px-2'
                            ]
                        ),

                    Select::make('price_range')
                        ->label('Média de Preço')
                        ->options(
                            [
                                1 => "Qualquer Preço",
                                2 => "R$ 1,00 - R$ 100,00",
                                3 => "R$ 101,00 - R$ 500,00",
                                4 => "R$ 501,00 - R$ 1000,00",
                                5 => "R$ 1001,00 - R$ 5.000,00",
                                6 => "Maior que 5.000,00",
                            ]
                        )
                        ->reactive()
                        ->default(1)
                        ->preload()
                        ->extraAttributes(
                            [
                                'class' => 'text-sm h-8 px-2'
                            ]
                        ),
                ]
            ),
        ];
    }
}
