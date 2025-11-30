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

# Publicar as views
php artisan vendor:publish --tag="federal-filament-store-views"

# Publicar as assets
php artisan vendor:publish --tag=federal-filament-store-assets --force
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
