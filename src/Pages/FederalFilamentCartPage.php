<?php

namespace Shieldforce\FederalFilamentStore\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\WithPagination;
use Shieldforce\FederalFilamentStore\Services\Permissions\CanPageTrait;

class FederalFilamentCartPage extends Page implements HasForms
{
    use CanPageTrait;
    use InteractsWithForms;
    use WithPagination;

    protected static string  $view            = 'federal-filament-log::pages.cart';
    protected static ?string $navigationIcon  = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Carrinho';
    protected static ?string $label           = 'Carrinho';
    protected static ?string $navigationLabel = 'Carrinho';
    protected static ?string $slug            = 'ffs-cart';
    protected static ?string $title           = 'Carrinho';
    protected array          $result          = [];

    public function mount(): void
    {
        $this->filtrar();
    }

    public function updated()
    {
        $this->resetPage();
        $this->filtrar();
    }

    public function filtrar()
    {
        $data         = $this->getData();
        $this->result = array_values($data);
    }

    protected function getData(): array
    {
        return [];
    }
}
