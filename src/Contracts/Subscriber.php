<?php

namespace Lacodix\LaravelPlans\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Lacodix\LaravelPlans\Models\Subscription;

interface Subscriber
{
    /**
     * @return MorphMany<Subscription>
     */
    public function subscriptions(): MorphMany;
}
