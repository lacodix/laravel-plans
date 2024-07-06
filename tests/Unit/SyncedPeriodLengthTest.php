<?php

use Carbon\Carbon;
use Lacodix\LaravelPlans\Classes\Period;
use Lacodix\LaravelPlans\Enums\Interval;

it('calculates period length correct', function ($interval, $period, $input, $expectedLength) {
    $period = new Period($interval, $period, $input, true);

    expect($period)->getLengthInPercent()->toBe($expectedLength);
})->with([
    'month' => [Interval::MONTH, 1, '2020-01-01 12:00:00', 100],
    'month_middle_of' => [Interval::MONTH, 1, '2020-01-12 23:59:00', 65],
    'month_end_short' => [Interval::MONTH, 1, '2020-02-29 23:59:00', 3],
    'month_end_long' => [Interval::MONTH, 1, '2020-01-31 23:59:00', 3],

    '2months' => [Interval::MONTH, 2, '2020-01-01 12:00:00', 100],
    '2months_middle_of' => [Interval::MONTH, 2, '2020-01-12 23:59:00', 82],
    '2months_end_short' => [Interval::MONTH, 2, '2020-02-29 23:59:00', 53],
    '2months_end_long' => [Interval::MONTH, 2, '2020-01-31 23:59:00', 50],

    '3months' => [Interval::MONTH, 3, '2020-01-01 12:00:00', 100],
    '3months_middle_of' => [Interval::MONTH, 3, '2020-01-12 23:59:00', 88],
    '3months_end_short' => [Interval::MONTH, 3, '2020-02-29 23:59:00', 69],
    '3months_end_long' => [Interval::MONTH, 3, '2020-01-31 23:59:00', 67],

    'bi_month' => [Interval::TWO_MONTHS, 1, '2020-01-01 12:00:00', 100],
    'bi_month_middle_of' => [Interval::TWO_MONTHS, 1, '2020-01-15 23:59:00', 77],
    'bi_month_second_one' => [Interval::TWO_MONTHS, 1, '2020-02-29 23:59:00', 2],
    'bi_month_end_of_first' => [Interval::TWO_MONTHS, 1, '2020-01-31 23:59:00', 50],

    '2bi_month' => [Interval::TWO_MONTHS, 2, '2020-01-01 12:00:00', 100],
    '2bi_month_middle_of' => [Interval::TWO_MONTHS, 2, '2020-01-15 23:59:00', 88],
    '2bi_month_second_one' => [Interval::TWO_MONTHS, 2, '2020-02-29 23:59:00', 51],
    '2bi_month_end_of_first' => [Interval::TWO_MONTHS, 2, '2020-01-31 23:59:00', 75],

    '3bi_month' => [Interval::TWO_MONTHS, 3, '2020-01-01 12:00:00', 100],
    '3bi_month_middle_of' => [Interval::TWO_MONTHS, 3, '2020-01-15 23:59:00', 92],
    '3bi_month_second_one' => [Interval::TWO_MONTHS, 3, '2020-02-29 23:59:00', 68],
    '3bi_month_end_of_first' => [Interval::TWO_MONTHS, 3, '2020-01-31 23:59:00', 84],

    'week_middle_of' => [Interval::WEEK, 1, '2020-01-01 12:00:00', 71],
    'week_start_of' => [Interval::WEEK, 1, '2020-01-06 23:59:00', 100],

    '2week_middle_of' => [Interval::WEEK, 2, '2020-01-01 12:00:00', 86],
    '2week_start_of' => [Interval::WEEK, 2, '2020-01-06 23:59:00', 100],

    '3week_middle_of' => [Interval::WEEK, 3, '2020-01-01 12:00:00', 90],
    '3week_start_of' => [Interval::WEEK, 3, '2020-01-06 23:59:00', 100],

    'half_year' => [Interval::HALF_YEAR, 1, '2020-01-01 12:00:00', 100],
    'half_year_in_second' => [Interval::HALF_YEAR, 1, '2020-08-15 23:59:00', 76],
    'half_year_in_first' => [Interval::HALF_YEAR, 1, '2020-02-29 23:59:00', 68],
    'half_year_end_of' => [Interval::HALF_YEAR, 1, '2020-12-31 23:59:00', 1],

    '2half_year' => [Interval::HALF_YEAR, 2, '2020-01-01 12:00:00', 100],
    '2half_year_in_second' => [Interval::HALF_YEAR, 2, '2020-08-15 23:59:00', 88],
    '2half_year_in_first' => [Interval::HALF_YEAR, 2, '2020-02-29 23:59:00', 84],
    '2half_year_end_of' => [Interval::HALF_YEAR, 2, '2020-12-31 23:59:00', 50],

    '3half_year' => [Interval::HALF_YEAR, 3, '2020-01-01 12:00:00', 100],
    '3half_year_in_second' => [Interval::HALF_YEAR, 3, '2020-08-15 23:59:00', 92],
    '3half_year_in_first' => [Interval::HALF_YEAR, 3, '2020-02-29 23:59:00', 89],
    '3half_year_end_of' => [Interval::HALF_YEAR, 3, '2020-12-31 23:59:00', 67],

    'year' => [Interval::YEAR, 1, '2020-01-01 12:00:00', 100],
    'year_middle_of' => [Interval::YEAR, 1, '2020-06-15 23:59:00', 55],
    'year_end_no_of' => [Interval::YEAR, 1, '2020-02-29 23:59:00', 84],
    'year_end_with_of' => [Interval::YEAR, 1, '2020-01-31 23:59:00', 92],
    'year_end' => [Interval::YEAR, 1, '2020-12-30 23:59:00', 1],

    '2year' => [Interval::YEAR, 2, '2020-01-01 12:00:00', 100],
    '2year_middle_of' => [Interval::YEAR, 2, '2020-06-15 23:59:00', 77],
    '2year_end_no_of' => [Interval::YEAR, 2, '2020-02-29 23:59:00', 92],
    '2year_end_with_of' => [Interval::YEAR, 2, '2020-01-31 23:59:00', 96],

    '3year' => [Interval::YEAR, 3, '2020-01-01 12:00:00', 100],
    '3year_middle_of' => [Interval::YEAR, 3, '2020-06-15 23:59:00', 85],
    '3year_end_no_of' => [Interval::YEAR, 3, '2020-02-29 23:59:00', 95],
    '3year_end_with_of' => [Interval::YEAR, 3, '2020-01-31 23:59:00', 97],

    'quarter' => [Interval::QUARTER, 1, '2020-01-01 12:00:00', 100],
    'quarter_third_month' => [Interval::QUARTER, 1, '2020-06-15 23:59:00', 18],
    'quarter_second_month' => [Interval::QUARTER, 1, '2020-02-29 23:59:00', 35],
    'quarter_first_month' => [Interval::QUARTER, 1, '2020-01-31 23:59:00', 67],

    '2quarter' => [Interval::QUARTER, 2, '2020-01-01 12:00:00', 100],
    '2quarter_third_month' => [Interval::QUARTER, 2, '2020-06-15 23:59:00', 59],
    '2quarter_second_month' => [Interval::QUARTER, 2, '2020-02-29 23:59:00', 68],
    '2quarter_first_month' => [Interval::QUARTER, 2, '2020-01-31 23:59:00', 84],

    '3quarter' => [Interval::QUARTER, 3, '2020-01-01 12:00:00', 100],
    '3quarter_third_month' => [Interval::QUARTER, 3, '2020-06-15 23:59:00', 73],
    '3quarter_second_month' => [Interval::QUARTER, 3, '2020-02-29 23:59:00', 78],
    '3quarter_first_month' => [Interval::QUARTER, 3, '2020-01-31 23:59:00', 89],

    // Day behaves the same as unsynced
    'day' => [Interval::DAY, 1, '2020-01-11 12:00:00', 100],
    'day_middle_of' => [Interval::DAY, 1, '2020-01-11 23:59:00', 100],
    'day_end_no_of' => [Interval::DAY, 1, '2020-01-11 23:59:00', 100],
    'day_end_with_of' => [Interval::DAY, 1, '2020-01-11 23:59:00', 100],
    '2day' => [Interval::DAY, 2, '2020-01-11 12:00:00', 100],
    '3day' => [Interval::DAY, 3, '2020-01-11 12:00:00', 100],
]);

it('calculates period length with trial correct', function ($interval, $period, $input, $trialEnd, $expectedLength) {
    $period = new Period($interval, $period, $input, true);

    expect($period)->getLengthInPercent(Carbon::make($trialEnd))->toBe($expectedLength);
})->with([
    'month' => [Interval::MONTH, 1, '2020-01-01 12:00:00', '2020-02-01 12:00:00', 0],
    'month_middle_of' => [Interval::MONTH, 1, '2020-01-12 23:59:00', '2020-02-12 23:59:00', 0],
    'month_end_short' => [Interval::MONTH, 1, '2020-02-29 23:59:00', '2020-03-29 23:59:00', 0],
    'month_end_long' => [Interval::MONTH, 1, '2020-01-31 23:59:00', '2020-03-02 23:59:00', 0],

    '2months' => [Interval::MONTH, 2, '2020-01-01 12:00:00', '2020-02-01 12:00:00', 48],
    '2months_middle_of' => [Interval::MONTH, 2, '2020-01-12 23:59:00', '2020-02-12 23:59:00', 30],
    '2months_end_short' => [Interval::MONTH, 2, '2020-02-29 23:59:00', '2020-03-29 23:59:00', 5],
    '2months_end_long' => [Interval::MONTH, 2, '2020-01-31 23:59:00', '2020-03-02 23:59:00', 0],

    '3months' => [Interval::MONTH, 3, '2020-01-01 12:00:00', '2020-02-01 12:00:00', 66],
    '3months_middle_of' => [Interval::MONTH, 3, '2020-01-12 23:59:00', '2020-02-12 23:59:00', 54],
    '3months_end_short' => [Interval::MONTH, 3, '2020-02-29 23:59:00', '2020-03-29 23:59:00', 37],
    '3months_end_long' => [Interval::MONTH, 3, '2020-01-31 23:59:00', '2020-03-02 23:59:00', 33],

    'bi_month' => [Interval::TWO_MONTHS, 1, '2020-01-01 12:00:00', '2020-02-01 12:00:00', 48],
    'bi_month_middle_of' => [Interval::TWO_MONTHS, 1, '2020-01-15 23:59:00', '2020-02-15 23:59:00', 25],
    'bi_month_second_one' => [Interval::TWO_MONTHS, 1, '2020-02-29 23:59:00', '2020-03-29 23:59:00', 0],
    'bi_month_end_of_first' => [Interval::TWO_MONTHS, 1, '2020-01-31 23:59:00', '2020-03-02 23:59:00', 0],

    '2bi_month' => [Interval::TWO_MONTHS, 2, '2020-01-01 12:00:00', '2020-02-01 12:00:00', 74],
    '2bi_month_middle_of' => [Interval::TWO_MONTHS, 2, '2020-01-15 23:59:00', '2020-02-15 23:59:00', 63],
    '2bi_month_second_one' => [Interval::TWO_MONTHS, 2, '2020-02-29 23:59:00', '2020-03-29 23:59:00', 27],
    '2bi_month_end_of_first' => [Interval::TWO_MONTHS, 2, '2020-01-31 23:59:00', '2020-03-02 23:59:00', 50],

    '3bi_month' => [Interval::TWO_MONTHS, 3, '2020-01-01 12:00:00', '2020-02-01 12:00:00', 83],
    '3bi_month_middle_of' => [Interval::TWO_MONTHS, 3, '2020-01-15 23:59:00', '2020-02-15 23:59:00', 75],
    '3bi_month_second_one' => [Interval::TWO_MONTHS, 3, '2020-02-29 23:59:00', '2020-03-29 23:59:00', 52],
    '3bi_month_end_of_first' => [Interval::TWO_MONTHS, 3, '2020-01-31 23:59:00', '2020-03-02 23:59:00', 66],

    'week_middle_of' => [Interval::WEEK, 1, '2020-01-01 12:00:00', '2020-02-01 12:00:00', 0],
    'week_start_of' => [Interval::WEEK, 1, '2020-01-06 23:59:00', '2020-02-06 23:59:00', 0],

    '2week_middle_of' => [Interval::WEEK, 2, '2020-01-01 12:00:00', '2020-02-01 12:00:00', 0],
    '2week_start_of' => [Interval::WEEK, 2, '2020-01-06 23:59:00', '2020-02-06 23:59:00', 0],

    '3week_middle_of' => [Interval::WEEK, 3, '2020-01-01 12:00:00', '2020-02-01 12:00:00', 0],
    '3week_start_of' => [Interval::WEEK, 3, '2020-01-06 23:59:00', '2020-02-06 23:59:00', 0],

    'half_year' => [Interval::HALF_YEAR, 1, '2020-01-01 12:00:00', '2020-02-01 12:00:00', 83],
    'half_year_in_second' => [Interval::HALF_YEAR, 1, '2020-08-15 23:59:00', '2020-09-15 23:59:00', 59],
    'half_year_in_first' => [Interval::HALF_YEAR, 1, '2020-02-29 23:59:00', '2020-03-29 23:59:00', 52],
    'half_year_end_of' => [Interval::HALF_YEAR, 1, '2020-12-31 23:59:00', '2021-01-31 23:59:00', 0],

    '2half_year' => [Interval::HALF_YEAR, 2, '2020-01-01 12:00:00', '2020-02-01 12:00:00', 92],
    '2half_year_in_second' => [Interval::HALF_YEAR, 2, '2020-08-15 23:59:00', '2020-09-15 23:59:00', 79],
    '2half_year_in_first' => [Interval::HALF_YEAR, 2, '2020-02-29 23:59:00', '2020-03-29 23:59:00', 76],
    '2half_year_end_of' => [Interval::HALF_YEAR, 2, '2020-12-31 23:59:00', '2021-01-31 23:59:00', 41],

    '3half_year' => [Interval::HALF_YEAR, 3, '2020-01-01 12:00:00', '2020-02-01 12:00:00', 94],
    '3half_year_in_second' => [Interval::HALF_YEAR, 3, '2020-08-15 23:59:00', '2020-09-15 23:59:00', 86],
    '3half_year_in_first' => [Interval::HALF_YEAR, 3, '2020-02-29 23:59:00', '2020-03-29 23:59:00', 84],
    '3half_year_end_of' => [Interval::HALF_YEAR, 3, '2020-12-31 23:59:00', '2021-01-31 23:59:00', 61],

    'year' => [Interval::YEAR, 1, '2020-01-01 12:00:00', '2020-02-01 12:00:00', 92],
    'year_middle_of' => [Interval::YEAR, 1, '2020-06-15 23:59:00', '2020-07-15 23:59:00', 46],
    'year_end_no_of' => [Interval::YEAR, 1, '2020-02-29 23:59:00', '2020-03-29 23:59:00', 76],
    'year_end_with_of' => [Interval::YEAR, 1, '2020-01-31 23:59:00', '2020-03-02 23:59:00', 83],
    'year_end' => [Interval::YEAR, 1, '2020-12-30 23:59:00', '2021-01-30 23:59:00', 0],

    '2year' => [Interval::YEAR, 2, '2020-01-01 12:00:00', '2020-02-01 12:00:00', 96],
    '2year_middle_of' => [Interval::YEAR, 2, '2020-06-15 23:59:00', '2020-07-15 23:59:00', 73],
    '2year_end_no_of' => [Interval::YEAR, 2, '2020-02-29 23:59:00', '2020-03-29 23:59:00', 88],
    '2year_end_with_of' => [Interval::YEAR, 2, '2020-01-31 23:59:00', '2020-03-02 23:59:00', 92],

    '3year' => [Interval::YEAR, 3, '2020-01-01 12:00:00', '2020-02-01 12:00:00', 97],
    '3year_middle_of' => [Interval::YEAR, 3, '2020-06-15 23:59:00', '2020-07-15 23:59:00', 82],
    '3year_end_no_of' => [Interval::YEAR, 3, '2020-02-29 23:59:00', '2020-03-29 23:59:00', 92],
    '3year_end_with_of' => [Interval::YEAR, 3, '2020-01-31 23:59:00', '2020-02-02 23:59:00', 97],

    'quarter' => [Interval::QUARTER, 1, '2020-01-01 12:00:00', '2020-02-01 12:00:00', 66],
    'quarter_third_month' => [Interval::QUARTER, 1, '2020-06-15 23:59:00', '2020-07-15 23:59:00', 0],
    'quarter_second_month' => [Interval::QUARTER, 1, '2020-02-29 23:59:00', '2020-03-29 23:59:00', 3],
    'quarter_first_month' => [Interval::QUARTER, 1, '2020-01-31 23:59:00', '2020-03-02 23:59:00', 33],

    '2quarter' => [Interval::QUARTER, 2, '2020-01-01 12:00:00', '2020-02-01 12:00:00', 83],
    '2quarter_third_month' => [Interval::QUARTER, 2, '2020-06-15 23:59:00', '2020-07-15 23:59:00', 43],
    '2quarter_second_month' => [Interval::QUARTER, 2, '2020-02-29 23:59:00', '2020-03-29 23:59:00', 52],
    '2quarter_first_month' => [Interval::QUARTER, 2, '2020-01-31 23:59:00', '2020-03-02 23:59:00', 66],

    '3quarter' => [Interval::QUARTER, 3, '2020-01-01 12:00:00', '2020-02-01 12:00:00', 89],
    '3quarter_third_month' => [Interval::QUARTER, 3, '2020-06-15 23:59:00', '2020-07-15 23:59:00', 62],
    '3quarter_second_month' => [Interval::QUARTER, 3, '2020-02-29 23:59:00', '2020-03-29 23:59:00', 68],
    '3quarter_first_month' => [Interval::QUARTER, 3, '2020-01-31 23:59:00', '2020-03-02 23:59:00', 78],

    // Day behaves the same as unsynced
    'day' => [Interval::DAY, 1, '2020-01-11 12:00:00', '2020-02-11 12:00:00', 0],
    'day_middle_of' => [Interval::DAY, 1, '2020-01-11 23:59:00', '2020-02-11 23:59:00', 0],
    'day_end_no_of' => [Interval::DAY, 1, '2020-01-11 23:59:00', '2020-02-11 23:59:00', 0],
    'day_end_with_of' => [Interval::DAY, 1, '2020-01-11 23:59:00', '2020-02-11 23:59:00', 0],
    '2day' => [Interval::DAY, 2, '2020-01-11 12:00:00', '2020-02-11 12:00:00', 0],
    '3day' => [Interval::DAY, 3, '2020-01-11 12:00:00', '2020-02-11 12:00:00', 0],
]);
