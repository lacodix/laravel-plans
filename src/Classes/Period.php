<?php

namespace Lacodix\LaravelPlans\Classes;

use Carbon\Carbon;
use Lacodix\LaravelPlans\Enums\Interval;

class Period
{
    private readonly ?Carbon $end;
    private ?Carbon $virtualStart = null;

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

    public function getVirtualStartDate(): Carbon
    {
        return $this->virtualStart ??= $this->calculateVirtualPeriodStart();
    }

    public function getEndDate(): Carbon
    {
        return $this->end;
    }

    public function getLengthInDays(?Carbon $start = null): int
    {
        $start ??= $this->start;

        // Given Time will only be taken, if it is later than start-time
        $start = $start->max($this->start);

        // start=00:00, end=23:59 -> +1 for next day
        // less than 0 can happen, if start in future is given, when trial is enabled.
        return max(0, $start->diffInDays($this->end) + 1);
    }

    public function getVirtualLengthInDays(): int
    {
        return (int) $this->getVirtualStartDate()->diffInDays($this->end) + 1;
    }

    public function getLengthInPercent(?Carbon $start = null): int
    {
        if (! $this->synced) {
            return 100;
        }

        return (int) round(100 / $this->getVirtualLengthInDays() * $this->getLengthInDays($start));
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

    protected function calculateVirtualPeriodStart(): Carbon
    {
        if (! $this->synced) {
            return $this->start->clone()->startOfDay();
        }

        return $this->interval->getSyncedPeriodStart($this->start);
    }
}
