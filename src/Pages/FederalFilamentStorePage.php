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

        $this->filtrar();
    }

    public function updated()
    {
        $this->resetPage();
        $this->filtrar();
    }

    public function addToCart($id)
    {
        //session()->push('cart.items', $id);

        dd("teste");
    }

    public function filtrar()
    {
        $list = $this->getData();

        $this->result = $list;
    }

    protected function getData(): array
    {
        return config('federal-filament-store.products_callback');
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

    protected function getFormSchema(): array
    {
        return [
            Grid::make(1)->schema([
                TextInput::make('search')
                    ->label('Palavra-chave'),
                Select::make('categories')
                    ->label('Categorias')
                    ->options(array_map(function ($category) {
                        return [
                            $category->id => $category->name,
                        ];
                    }, $this->categories)),
                DatePicker::make('data')->label('Data')->format('Y-m-d'),
            ]),
        ];
    }

}
