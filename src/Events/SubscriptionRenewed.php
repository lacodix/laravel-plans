<?php

namespace Lacodix\LaravelPlans\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Lacodix\LaravelPlans\Models\Subscription;

class SubscriptionRenewed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Subscription $subscription,
    ) {
    }
}
