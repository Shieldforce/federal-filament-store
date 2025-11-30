<?php

namespace Shieldforce\FederalFilamentStore\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\WithPagination;
use Shieldforce\FederalFilamentStore\Services\Permissions\CanPageTrait;

class FederalFilamentProductPage extends Page implements HasForms
{
    use CanPageTrait;
    use InteractsWithForms;
    use WithPagination;

    protected static string $view = 'vendor.federal-filament-log.pages.product';
    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Loja';
    protected static ?string $label = 'Produto';
    protected static ?string $navigationLabel = 'Produto';
    protected static ?string $slug = 'ffs-product';
    protected static ?string $title = 'Produto';
    protected array $result = [];
    protected static bool $shouldRegisterNavigation = false;

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
        $data = $this->getData();
        $this->result = array_values($data);
    }

    protected function getData(): array
    {
        return [];
    }
}
