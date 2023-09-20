<p align="center">
    <img src="https://raw.githubusercontent.com/nunomaduro/pail/master/docs/example.png" style="width:70%;" alt="Pail">
    <p align="center">
        <a href="https://github.com/nunomaduro/pail/actions"><img alt="GitHub Workflow Status (master)" src="https://github.com/nunomaduro/pail/actions/workflows/tests.yml/badge.svg"></a>
        <a href="https://packagist.org/packages/nunomaduro/pail"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/nunomaduro/pail"></a>
        <a href="https://packagist.org/packages/nunomaduro/pail"><img alt="Latest Version" src="https://img.shields.io/packagist/v/nunomaduro/pail"></a>
        <a href="https://packagist.org/packages/nunomaduro/pail"><img alt="License" src="https://img.shields.io/packagist/l/nunomaduro/pail"></a>
    </p>
</p>

------

**Pail** is an experimental package (or, who knows, perhaps a future Laravel feature?) designed with a single goal in mind: to provide an effortless way to tail logs in our Laravel applications.

Difference from other log tailing packages:

- 🌌 A user-friendly, sleek CLI interface.
- ⚗️ **Compatibility with any log driver**. Whether you're integrated with [Sentry](https://sentry.io) or [Bugsnag](https://bugsnag.com), **Pail** is crafted to work alongside.
- 🔑 **Filter logs by the authenticated user**. Yes, you read it right. **Pail** can filter logs by the authenticated user, the one that triggered the request.

🚧 **Note:** As of now, **Pail** is still in its proof-of-concept phase. It's an idea in the making, not yet optimized for production scenarios. Any feedback is welcome!

## Installation

> **Requires [PHP 8.2+](https://php.net/releases/)**

Get started with **Pail** by installing the package via Composer:

```bash
composer require nunomaduro/pail:dev-main
```

## Usage

To start tailing logs, run the `pail` command:

```bash
php artisan pail
```

To increase the verbosity of the output, avoiding truncation (...), use the `-v` option:

```bash
php artisan pail -v
```

To filter logs by its content, use the `--filter` option:

```bash
php artisan pail --filter="Illuminate\Database"
```

To filter logs by the authenticated user, the one that triggered the request, use the `--user` option:

```bash
php artisan pail --user=1
```

## License

**Pail** was created by **[Nuno Maduro](https://twitter.com/enunomaduro)** under the **[MIT license](https://opensource.org/licenses/MIT)**.
