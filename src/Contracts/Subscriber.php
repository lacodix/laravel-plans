<?php

namespace Lacodix\LaravelPlans\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Lacodix\LaravelPlans\Models\Subscription;

interface Subscriber
{
    /**
     * @return MorphMany<Subscription, Model>
     */
    public function subscriptions(): MorphMany;
}
