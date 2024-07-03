# laravel-plans

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lacodix/laravel-plans.svg?style=flat-square)](https://packagist.org/packages/lacodix/laravel-plans)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/lacodix/laravel-plans/test.yaml?branch=master&label=tests&style=flat-square)](https://github.com/lacodix/laravel-plans/actions?query=workflow%3Atest+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/lacodix/laravel-plans/style.yaml?branch=master&label=code%20style&style=flat-square)](https://github.com/lacodix/laravel-plans/actions?query=workflow%3Astyle+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/lacodix/laravel-plans.svg?style=flat-square)](https://packagist.org/packages/lacodix/laravel-plans)

**!! This package is currently in development and not ready for usage !!** 

## Documentation

You can find the entire documentation for this package on [our documentation site](https://www.lacodix.de/docs/laravel-plans)

## Installation

```bash
composer require lacodix/laravel-plans
```

## Testing

```bash
composer test
```

## Contributing

Please run the following commands and solve potential problems before committing
and think about adding tests for new functionality.

```bash
composer rector:test
composer insights
composer csfixer:test
composer phpstan:test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [lacodix](https://github.com/lacodix)
- [laravel Cameroon](https://github.com/laravelcm)

This package is inspired by [Laravel Subscriptions](https://github.com/laravelcm/laravel-subscriptions) created 
by Laravel Cameroon and was initially started as a fork of it. After several decisions to go different ways for
subscription calculation, it was rewritten from scratch, but still contains several simple methods and other code 
parts of the original. So if this package doesn't fit your needs, try a look into Laravel Cameroons subscription
package.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
