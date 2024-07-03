---
title: Installation
weight: 3
---

You can install the package via composer:

```bash
composer require lacodix/laravel-plans
```

## Config file

The package brings a config file that can be published.

```bash
php artisan vendor:publish --tag="plans-config"
```

The config file contains names of the tables and classnames for the models. You
can overwrite all of the models with own classes with the same name in your application.

Additionally you can decide if subscriptions shall be synced with the selected interval
or not. Default behaviour is syncing. See documentation for more details.
