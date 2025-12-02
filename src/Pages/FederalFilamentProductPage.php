<?php

namespace Shieldforce\FederalFilamentStore\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class FederalFilamentProductPage extends Page implements HasForms
{

    use InteractsWithForms;
    use WithPagination;

    protected static string  $view               = 'federal-filament-store::pages.product';
    protected static ?string $label              = 'Produto';
    protected static ?string $navigationLabel    = 'Produto';
    protected static ?string $title              = 'Produto';

    public function getLayout(): string
    {
        if (request()->query('external') === '1') {
            return 'federal-filament-store::layouts.external';
        }

        return parent::getLayout();
    }

    public static function getSlug(): string
    {
        return 'external-ffs-product';
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
    }

    public function updated($property)
    {

    }

}

