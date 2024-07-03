<?php

namespace Lacodix\LaravelPlans\Classes;

use Carbon\Carbon;
use Lacodix\LaravelPlans\Enums\Interval;

class Period
{
    private readonly ?Carbon $end;

    public function __construct(
        private readonly Interval $interval = Interval::MONTH,
        private readonly int $count = 1,
        private null|string|Carbon $start = null,
        private readonly bool $synced = false
    ) {
        $this->start ??= Carbon::now();
        if (! $start instanceof Carbon) {
            $this->start = new Carbon($start);
        }

        // Start is always the beginning of the day of the input
        // when prolonging subscriptions it expects to get the next day after the last period as input.
        $this->start = $this->start->startOfDay();

        // depending on the settings, and on the interval, we get different period-endings
        $this->end = $this->calculatePeriodEnd();
    }

    public function getStartDate(): Carbon
    {
        return $this->start;
    }

    public function getEndDate(): Carbon
    {
        return $this->end;
    }

    public function getInterval(): Interval
    {
        return $this->interval;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    protected function calculatePeriodEnd(): Carbon
    {
        if (! $this->synced) {
            return $this->start->clone()
                ->add(
                    $this->interval->getIntervalName(),
                    $this->interval->getIntervalValue() * $this->count,
                    false
                )->subDay()
                ->endOfDay();
        }

        return $this->interval->getSyncedPeriodEnd($this->start, $this->count);
    }
}
