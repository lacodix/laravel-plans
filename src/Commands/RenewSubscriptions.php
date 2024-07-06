<?php

namespace Lacodix\LaravelPlans\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Lacodix\LaravelPlans\Models\Subscription;

class RenewSubscriptions extends Command
{
    protected $signature = 'plans:renew-subscriptions {--force : Force renewal even if subscription not yet ended }';

    protected $description = 'Renews all subscriptions that are ended.';

    public function handle(): void
    {
        $subscriptions = Subscription::query()
            ->uncanceled()
            ->when(! $this->option('force'), static fn (Builder $query) => $query->ended())
            ->get();

        foreach ($subscriptions as $subscription) {
            $subscription->renew($this->option('force'));
        }
    }
}
