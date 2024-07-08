<?php

namespace Lacodix\LaravelPlans\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Lacodix\LaravelPlans\Models\Plan;

class PlanChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Plan $plan
    ) {
    }
}
