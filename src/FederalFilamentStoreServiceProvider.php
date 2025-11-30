<?php

namespace Shieldforce\FederalFilamentStore;

use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Shieldforce\FederalFilamentStore\Commands\FederalFilamentStoreCommand;
use Shieldforce\FederalFilamentStore\Testing\TestsFederalFilamentStore;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FederalFilamentStoreServiceProvider extends PackageServiceProvider
{
    public static string $name          = 'federal-filament-store';
    public static string $viewNamespace = 'federal-filament-store';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('shieldforce/federal-filament-store');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/federal-filament-store/{$file->getFilename()}"),
                ], 'federal-filament-store-stubs');
            }
        }

        // Testing
        Testable::mixin(new TestsFederalFilamentStore());
    }

    protected function getAssetPackageName(): ?string
    {
        return 'shieldforce/federal-filament-store';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('federal-filament-store', __DIR__ . '/../resources/dist/components/federal-filament-store.js'),
            Css::make('federal-filament-store-styles', __DIR__ . '/../resources/dist/federal-filament-store.css'),
            Js::make('federal-filament-store-scripts', __DIR__ . '/../resources/dist/federal-filament-store.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            FederalFilamentStoreCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [

        ];
    }

    public function boot()
    {
        $viewsPath = __DIR__ . '/../../resources/views';

        if (is_dir($viewsPath)) {
            $this->loadViewsFrom($viewsPath, 'federal-filament-store');
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/federal-filament-store'),
            ], 'federal-filament-store-views');
        }

        return parent::boot();
    }
}
