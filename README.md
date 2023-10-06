<p align="center"><img src="/art/logo.jpg" style="width:70%;" alt="Logo Laravel Pail"></p>

<p align="center">
<a href="https://github.com/laravel/pail/actions"><img src="https://github.com/laravel/pail/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/pail"><img src="https://img.shields.io/packagist/dt/laravel/pail" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/pail"><img src="https://img.shields.io/packagist/v/laravel/pail" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/pail"><img src="https://img.shields.io/packagist/l/laravel/pail" alt="License"></a>
</p>

## Introduction

Laravel Pail is a package that allows you to easily dive into your Laravel application's log files directly from the command line. Unlike other log tailing packages, Pail is designed to work with any log driver, including [Sentry](https://sentry.io) or [Flare](https://flareapp.io).

In addition, Pail focuses on the developer experience: it provides a sleek CLI interface with a user-friendly design and a set of useful filters to help you find what you're looking for.

## Installation

> **Requires [PHP 8.2+](https://php.net/releases/) with [PCNTL](https://www.php.net/manual/en/book.pcntl.php) extension.**

To get started, install Pail into your project using the Composer package manager:

> **Note:** Pail is currently in beta and is not yet ready for production use.

```bash
composer require laravel/pail:^1.0@beta
```

## Usage

To start tailing logs, run the `pail` command:

```bash
php artisan pail
```

To increase the verbosity of the output, avoiding truncation (…), use the `-v` option:

```bash
php artisan pail -v
```

For maximum verbosity and to display exception stack traces, use the `-vv` option:

```bash
php artisan pail -vv
```

To stop tailing logs, press `Ctrl+C` at any time.

### Filtering Logs

#### `--filter`

You may use the `--filter` option to filter logs by their type, file, message, and stack trace content:

```bash
php artisan pail --filter="QueryException"
```

#### `--message`

To filter logs by only their message, you may use the `--message` option:

```bash
php artisan pail --message="User created"
```

#### `--level`

The `--level` option may be used to filter logs by their log level:

```bash
php artisan pail --level=error
```

#### `--user`

To only display logs that were written while a given user was authenticated, you may use the `--user` option:

```bash
php artisan pail --user=1
```

## Contributing

Thank you for considering contributing to Laravel Pail! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

Please review [our security policy](https://github.com/laravel/folio/security/policy) on how to report security vulnerabilities.

## License

Laravel Pail is open-sourced software licensed under the [MIT license](LICENSE.md).
