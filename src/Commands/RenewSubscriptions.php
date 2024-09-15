<?php

namespace Lacodix\LaravelPlans\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Lacodix\LaravelPlans\Events\SubscriptionsRenewed;
use Lacodix\LaravelPlans\Models\Subscription;

class RenewSubscriptions extends Command
{
    protected $signature = 'plans:renew-subscriptions {--force : Force renewal even if subscription not yet ended }';

    protected $description = 'Renews all subscriptions that are ended.';

    public function handle(): void
    {
        $force = $this->option('force') ?? false;

        $subscriptions = Subscription::query()
            ->uncanceled()
            ->when(! $force, static fn (Builder $query) => $query->ended())
            ->get();

        if (! config('plans.aggregate_renewals')) {
            foreach ($subscriptions as $subscription) {
                $subscription->renew($force);
            }

            return;
        }

        // Group subscriptions by subscriber and renew it in blocks
        $subscriptions
            ->groupBy(static fn ($item) => $item->subscriber_type . ':' . $item->subscriber_id)
            ->each(static function (Collection $subscriptions) use ($force): void {
                foreach ($subscriptions as $subscription) {
                    $subscription->renew($force);
                }

                SubscriptionsRenewed::dispatch($subscriptions);
            });
    }
}
