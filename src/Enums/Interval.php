<?php

namespace Lacodix\LaravelPlans\Enums;

use Carbon\Carbon;

enum Interval: string
{
    case DAY = 'day';
    case WEEK = 'week';
    case MONTH = 'month';
    case TWO_MONTHS = 'two_months';
    case QUARTER = 'quarter';
    case HALF_YEAR = 'half_year';
    case YEAR = 'year';

    public function getIntervalValue(): int
    {
        return match ($this) {
            Interval::TWO_MONTHS => 2,
            Interval::HALF_YEAR => 6,
            default => 1
        };
    }

    public function getIntervalName(): string
    {
        return match ($this) {
            Interval::TWO_MONTHS, Interval::HALF_YEAR => 'month',
            default => $this->value
        };
    }

    public function getSyncedPeriodEnd(Carbon $start, int $count = 1): Carbon
    {
        // If we sync the subscriptions, we have to care about the current interval and calculate different endings
        return match ($this) {
            Interval::DAY => $start->clone()
                ->addDays($count - 1)
                ->endOfDay(),
            Interval::WEEK => $start->clone()
                ->addWeeks($count - 1)
                ->endOfWeek(),
            Interval::MONTH => $start->clone()
                ->add('month', $count - 1, false)
                ->endOfMonth(),
            Interval::TWO_MONTHS => $start->clone()
                ->add('month', $this->calculateMonthsToAddForTwoMonths($start, $count), false)
                ->endOfMonth(),
            Interval::QUARTER => $start->clone()
                ->add('quarter', $count - 1, false)
                ->endOfQuarter(),
            Interval::HALF_YEAR => $start->clone()
                ->add('month', $this->calculateMonthsToAddForHalfYear($start, $count), false)
                ->endOfMonth(),
            Interval::YEAR => $start->clone()
                ->add('year', $count - 1, false)
                ->endOfYear(),
        };
    }

    public function getSyncedPeriodStart(Carbon $start): Carbon
    {
        // If we sync the subscriptions, we have to care about the current interval and calculate different endings
        // @phpstan-ignore-next-line - PHPStan sees CarbonInterface here, but it isn't
        return match ($this) {
            Interval::DAY => $start->clone()
                ->startOfDay(),
            Interval::WEEK => $start->clone()
                ->startOfWeek(),
            Interval::MONTH => $start->clone()
                ->startOfMonth(),
            Interval::TWO_MONTHS => $start->clone()
                ->startOfMonth()
                ->sub('month', $start->month % 2 === 0 ? 1 : 0),
            Interval::QUARTER => $start->clone()
                ->startOfQuarter(),
            Interval::HALF_YEAR => $start->clone()
                ->startOfMonth()
                ->sub('month', $start->month - ($start->month > 6 ? 6 : 0) - 1),
            Interval::YEAR => $start->clone()
                ->startOfYear(),
        };
    }

    private function calculateMonthsToAddForTwoMonths(Carbon $start, int $count): int
    {
        // it is always count-1 * 2, and then finally "endOfTwoMonths"
        // - if 1 is given, add nothing, got to endOfTwoMonths.
        // - if 2 is given, add 2 months, got to endOfTwoMonths and so on...
        // endOfTwoMonths is realized by adding 1 month it currently month is 1, 3, 5 aso.
        // finally endOfMonth is called outside.
        return ($count - 1) * 2 + ($start->month % 2 !== 0 ? 1 : 0);
    }

    private function calculateMonthsToAddForHalfYear(Carbon $start, int $count): int
    {
        // It is following the same schema as for twoMonths
        // but with 6 instead of *2
        // and finally add the missing amount of months to reach 6 or 12.
        $monthsToAdd = ($start->month <= 6 ? 6 : 12) - $start->month;

        return ($count - 1) * 6 + $monthsToAdd;
    }
}
