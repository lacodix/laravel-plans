<?php

namespace Lacodix\LaravelPlans\Events;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Lacodix\LaravelPlans\Models\Subscription;

class SubscriptionsRenewed
{
    use Dispatchable, SerializesModels;

    /**
     * @param Collection<int, Subscription> $subscriptions
     */
    public function __construct(
        public Collection $subscriptions,
    ) {
    }
}
