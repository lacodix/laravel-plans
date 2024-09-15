---
title: Configuration
weight: 1
---

The package brings a config file that can be published.

```bash
php artisan vendor:publish --tag="plans-config"
```

## sync_subscriptions

default: true

You should first decide the rhythm of your subscriptions. You can decide between a synchronization to intervals or not.
The default behaviour is synchronized. This means that the renewal of subscriptions is synchronized to the interval of
your plan, independent from the starting date. This means, if your interval is a month, and the subscriptions started 
on the 15th of the month, it will be renewed on the 1st of the next month to get in sync with the interval. The same
is possible for years, quarters and even weeks. If you deactivate the synchronization, the renewal will be always 
exactly after the interval. A plan that started on 15th of the month, will be renewed on the 15th of the next month.
Keep in mind that a plan that started on the 31st of a month will be renewed on the last day of the next month, 
independent of it's length. This new day will then be kept for subsequent renewals. So after a normal February, it 
will be always on the 28th.

## price_precision

default: 2

you can increase the precision of calculations to 4 digits. This is only relevant, if you want to use the price
calculations. Calculation is needed if you need synchronization of your subscriptions and you need to bill for example
10 days of a monthly subscription.

## aggregate_renewals

default: false

Sometimes you have multiple subscriptions per subscriber, and you want to create one single invoice for all of it.
With the default setting you receive only one event for the renewal of each subscription, independent of the subscriber.
If you enable the aggregation, the renewed subscriptions will be grouped by subscribers and renewed together. You will
still receive on event per subscription, but additionally you will receive one event for each subscriber, containing
all renewed subscriptions of the subscriber.

## plans and tables

you can finally use your own models and own table names, but you should always inherit from the base models of this
package.
