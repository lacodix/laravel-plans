# laravel-plans

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lacodix/laravel-plans.svg?style=flat-square)](https://packagist.org/packages/lacodix/laravel-plans)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/lacodix/laravel-plans/test.yaml?branch=master&label=tests&style=flat-square)](https://github.com/lacodix/laravel-plans/actions?query=workflow%3Atest+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/lacodix/laravel-plans/style.yaml?branch=master&label=code%20style&style=flat-square)](https://github.com/lacodix/laravel-plans/actions?query=workflow%3Astyle+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/lacodix/laravel-plans.svg?style=flat-square)](https://packagist.org/packages/lacodix/laravel-plans)

**!! This package is currently in development and not ready for usage !!** 

## Documentation

You can find the entire documentation for this package on [our documentation site](https://www.lacodix.de/docs/laravel-plans). Including several usecases
with detailed explanation.

## What it does

- Manage all **Plans** and **Addons** of your SaaS where users can subscribe to.
- **Subscribe** to one or more plans with different billing intervals.
- Manage optional **features**, if you need it. You can also stick with plans and just check for a subscribed plan.
- Offer **countable** and **uncountable** features. Use uncountable features to just enable/disable a functionality. Use 
countable features, for things like tokens, credits, and others. Attach different values of the feature to different
plans, including "unlimited". Auto reset the values after given intervals.
- **Consume** Features as long as some amount is available. Split up usage on different plans, depending on the order of
the plans.
- **Translate** your plans and features. Identify both by slug in your app, but offer it to your users localized, powered
by [spatie/laravel-translatable](https://github.com/spatie/laravel-translatable)
- **Order** your plans, features (for visualisation to your users) and subscriptions (to keep control over usage order)
- Allow **meta data** in plans and subscriptions. The usage of meta data is up to you. In some usecase we take meta data
to save currency or additional price information. This information can be used by a subsequent billing system. 

## What it doesn't

- **Billing**. We don't create invoices or keep track on bills. Use the invoice service of your choice, doesn't matter which.
Stripe, PayPal, your own software and any other. You just get events from this package, when subscriptions are created 
or renewed, and this can be used to trigger invoices. You are also free to wait for payment before you create the 
subscription or the other way round.
- **Pricing**. Yes there is a price column in the plan model, that can be used for visualisation. You also can use meta data
for additional price information like we do it in some examples. But you don't need to use it. You can keep your prices
in your billing system or save it separate from the plans. But indeed, if you use the price-column you can also get
calculated prices for partial subscription intervals.

## Installation

```bash
composer require lacodix/laravel-plans
```

## Quickstart

To get familiar with all settings and possibilities, please see the more detailed examples. This is only
a very quick overview how the package can work.

### Subscribers

Add our HasSubscription Trait to any model.

```php 
use Lacodix\LaravelPlans\Models\Traits\HasSubscriptions;

class User extends Authenticatable {
    use HasSubscription;
    
    ...
}
```

### Plans and Features

Create a Plan with Features (the latter is optional, if you don't need feature functionality).

```php 
use Lacodix\LaravelPlans\Enums\Interval;
use Lacodix\LaravelPlans\Models\Feature;
use Lacodix\LaravelPlans\Models\Plan;

$myPlan = Plan::create([
    'slug' => 'my-plan',
    'name' => 'My Plan', // can also be locale-array - see Feature below
    'price' => 50.0,
    'active' => true,
    'billing_interval' => Interval::MONTH,
    'billing_period' => 1,
    'meta' => [
        'price_per_token' => 0.05,
    ],
]);

$myFeature = Feature::create([
    'slug' => 'tokens',
    'name' => [
        'de' => 'Zusätzliche Tokens',
        'en' => 'Additional Tokens',
    ],
]);

$myPlan->features()->attach($myFeature, [
    'value' => 1000,
    'resettable_period' => 1,
    'resettable_interval' => Interval::MONTH,
]);
```

### Subscribe and renew

```php 
// Subscribe to multiple plans
$user->subscribe($myPlan1, 'main');
$user->subscribe($myPlan2, 'addon');

// Change Subscription
$user->subscribe($myPlan3, 'main'); // will replace myPlan1 subscription

// Renew
$user->subscriptions()->first()->renew();

// Cancel
$user->subscriptions()->first()->cancel();
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
