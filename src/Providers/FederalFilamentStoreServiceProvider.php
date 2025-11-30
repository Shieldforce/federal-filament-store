<?php

namespace Shieldforce\FederalFilamentStore\Providers;

use Shieldforce\FederalFilamentStore\FederalFilamentStoreServiceProvider as BaseProvider;

class FederalFilamentStoreServiceProvider extends BaseProvider
{
    public function boot(): void
    {
        parent::boot();

        $viewsPath = __DIR__ . '/../../resources/views';

        if (is_dir($viewsPath)) {
            // Carrega views do plugin
            $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'federal-filament-store');

            $this->publishes([
                $viewsPath => resource_path('views/vendor/federal-filament-store'),
            ], 'views');
        }
    }
}

