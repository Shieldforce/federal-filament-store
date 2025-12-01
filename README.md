# Plugin para filament store

[![Latest Version on Packagist](https://img.shields.io/packagist/v/shieldforce/checkout-payment.svg?style=flat-square)](https://packagist.org/packages/shieldforce/checkout-payment)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/shieldforce/checkout-payment/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/shieldforce/checkout-payment/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/shieldforce/checkout-payment/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/shieldforce/checkout-payment/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/shieldforce/checkout-payment.svg?style=flat-square)](https://packagist.org/packages/shieldforce/checkout-payment)

Este plugin implementa checkout de pagamento interno e externo para o filament!

## Instalação

Instalar Via Composer:

```bash
composer require shieldforce/federal-filament-store
```

Você precisa publicar as migrações, views e assets:

```bash

php artisan federal-filament-store:install

# Publicar as tabelas
php artisan vendor:publish --tag="federal-filament-store-migrations"
php artisan migrate

# Publicar a config
php artisan vendor:publish --tag="federal-filament-store-config"

# Publicar as views
php artisan vendor:publish --tag="federal-filament-store-views"

# Publicar as assets
php artisan vendor:publish --tag=federal-filament-store-assets --force

# Publicar os css
php artisan vendor:publish --tag=federal-filament-store-css --force
```

# Algumas configurações obrigatórias
```php
// Adicionar o plugin no AdminPanel plugins
->plugins([
    //... outros plugins
    \Shieldforce\FederalFilamentStore\FederalFilamentStorePlugin::make()
      ->setLabelGroupSidebar("Loja"),
])

// Criar p Middleware
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class SetStoreMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if($request->route() && str_contains($request->route()->getName(), "ffs-")) {
            $this->setPluginStore();
        }

        return $next($request);
    }

    public function setPluginStore()
    {
        if (
            Schema::hasTable('products') &&
            Schema::hasTable('categories') &&
            Schema::hasTable('products_categories')
        ) {
            $products = \App\Models\Product::with('categories')->get()->map(fn($p) => [
                'id'         => $p->id,
                'name'       => $p->name,
                'price'      => $p->price_main,
                'categories' => $p->categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->toArray(),
                'image'      => $p->picture,
            ])->toArray();

            $categories = \App\Models\Category::all()->map(fn($c) => [
                'id'   => $c->id,
                'name' => $c->name,
            ])->toArray();

            config([
                'federal-filament-store.products_callback'   => $products,
                'federal-filament-store.categories_callback' => $categories,
            ]);
        }
    }
}
    
// Adicionar o middleware no AdminPanel em ->middlewares([]) para alimentar as paginas com informações locais
->middlewares([
    //...outros middlewares
    \App\Http\Middleware\SetStoreMiddleware::class
])

```

# CSS
```css

/*Precisa para paginação ter espaço entre texto e botões, 
pode colocar no vendor/filament/assets.blade.php, dentro da tag style*/
nav[aria-label="Pagination Navigation"]
> .sm\:flex-1.sm\:flex.sm\:items-center.sm\:justify-between
> div:last-child {
    margin-left: 2rem;
}

```

## Changelog

Consulte [CHANGELOG](CHANGELOG.md) para obter mais informações sobre o que mudou recentemente.

## Contributing

Consulte [CONTRIBUTING](.github/CONTRIBUTING.md) para obter detalhes.

## Security Vulnerabilities

Revise [nossa política de segurança](../../security/policy) sobre como relatar vulnerabilidades de segurança.

## Credits

- [Alexandre Ferreira](https://github.com/Shieldforce)
- [All Contributors](../../contributors)

## License

A Licença do MIT (MIT). Consulte [Arquivo de Licença](LICENSE.md) para obter mais informações.
