<?php

declare(strict_types=1);

namespace Lacodix\LaravelPlans\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Lacodix\LaravelPlans\Models\Subscription;

class PlanSubscribed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Subscription $subscription,
        public ?Subscription $oldSubscription,
    ) {
    }
}
