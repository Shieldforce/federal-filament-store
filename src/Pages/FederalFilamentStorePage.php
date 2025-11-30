<?php

namespace Shieldforce\FederalFilamentStore\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\WithPagination;
use Shieldforce\FederalFilamentStore\Services\Permissions\CanPageTrait;

class FederalFilamentStorePage extends Page implements HasForms
{
    //use CanPageTrait;
    use InteractsWithForms;
    use WithPagination;

    protected static string $view = 'federal-filament-store::pages.store';
    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Loja';
    protected static ?string $label = 'Loja';
    protected static ?string $navigationLabel = 'Loja';
    protected static ?string $slug = 'ffs-store';
    protected static ?string $title = 'Loja';
    protected array $result = [];
    public static bool $shouldRegisterNavigation = false;

    public static function canAccess(): bool
    {
        return false;
    }

    public function getLayout(): string
    {
        if (request()->query('external') === '1') {
            return 'filament::components.layouts.base';
        }

        return parent::getLayout();
    }

    public static function getSlug(): string
    {
        return 'ffs-store';
    }

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
