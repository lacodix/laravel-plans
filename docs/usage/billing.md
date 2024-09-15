---
title: Billing
weight: 10
---

As mentioned multiple times, this package doesn't care about billing. It doesn't create invoices nor takes care about
payment. But you can use all information of this package to feed the invoice and billing system of your choice.

First of all you can use our events to get information about new and renewed subscriptions. When receiving such an 
event you can react on it.

```php 
// Create a Listener for the SubscriptionRenewed event

class CreateInvoice
{
    /**
     * @param Subscription $subscription
     */
    public function handle(Subscription $subscription): void
    {
        // Get Period start
        $subscription->perid_starts_at;
        
        // Get Period end
        $subscription->period_ends_at;
        
        // Get Prices
        $subscription->plan->price;
        $subscription->plan->signup_fee;
        
        // Optional usages with meta-data of plan
        $subscription->plan->meta['price_per_token'];
        
        // Optional usage with meta-data of subscription
        $subscription->meta['price_per_token'];
        $subscription->meta['discount'];
        
        ...
    }
}
```

You are free to use all this data to do your calculation and create an invoice for your users.

## Sugar

Usually there is a simple use case. All your plans have a price, and you want to bill this price
everytime the subscriptions are renewed with one single exception: the beginning of the subscription
when you might need to bill only a partial month.

This package provides you with the necessary functionality to receive all this information.

```php 
    $subscription->calculatePeriodPrice();
``` 

This function returns the price for the current period. In a normal renewal this will just
be the price given by your plan. But on the first month it will calculate the percentage
depending on the real usage of the month. By default prices are calculated with 2 decimals
but you can change that by configuration.

This function also takes in account possible trial periods - so if a trial ends in the middle
of the period or even after it, you will receive 0 or a partial price.

### Calculate on your own

In some cases you might have your own type of calculation based on metadata or other information.
In such cases you can just receive the percentage of the period (it will also take in account
potentially given trial times):

```php 
    $part = $subscription->calculatePeriodLengthInPercent();
    
    // Now you can calculate your own price
    $plan = $subscription->plan;
    $fullPrice = $plan->price + ($userCount * $plan->meta['price_per_user']);
    $amountToPay = round($fullPrice * $part / 100, config('plans.price_precision', 2));
``` 
