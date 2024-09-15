---
title: Renew Subscriptions
weight: 5
---

This package doesn't care about renewing subscriptions. You have to do this on your own and you have to decide how 
you want to do that. But for convenience this package contains an artisan command that will care about all renewals for you

```bash 
php artisan plans:renew-subscriptions
```

This command can run every day or every week or every month depending on your needs. It will find all ended but not
canceled subscriptions and call the renew function on it. Depending on your configuration it will group it by 
subscriber or not. 

Keep in mind that it renews all subscriptions. If you want to make use of onetime-buys that doesn't renew,
you have to cancel this subscriptions right after subscribing to avoid renewal. 
