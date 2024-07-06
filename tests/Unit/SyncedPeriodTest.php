<?php

use Lacodix\LaravelPlans\Classes\Period;
use Lacodix\LaravelPlans\Enums\Interval;

it('calculates period end correct', function ($interval, $period, $input, $expectedStart, $expectedEnd) {
    $period = new Period($interval, $period, $input, true);

    expect($period)->getStartDate()->toBeCarbon($expectedStart)
        ->getEndDate()->toBeCarbon($expectedEnd);
})->with([
    'month' => [Interval::MONTH, 1, '2020-01-01 12:00:00', '2020-01-01 00:00:00', '2020-01-31 23:59:59'],
    'month_middle_of' => [Interval::MONTH, 1, '2020-01-12 23:59:00', '2020-01-12 00:00:00', '2020-01-31 23:59:59'],
    'month_end_short' => [Interval::MONTH, 1, '2020-02-29 23:59:00', '2020-02-29 00:00:00', '2020-02-29 23:59:59'],
    'month_end_long' => [Interval::MONTH, 1, '2020-01-31 23:59:00', '2020-01-31 00:00:00', '2020-01-31 23:59:59'],

    '2months' => [Interval::MONTH, 2, '2020-01-01 12:00:00', '2020-01-01 00:00:00', '2020-02-29 23:59:59'],
    '2months_middle_of' => [Interval::MONTH, 2, '2020-01-12 23:59:00', '2020-01-12 00:00:00', '2020-02-29 23:59:59'],
    '2months_end_short' => [Interval::MONTH, 2, '2020-02-29 23:59:00', '2020-02-29 00:00:00', '2020-03-31 23:59:59'],
    '2months_end_long' => [Interval::MONTH, 2, '2020-01-31 23:59:00', '2020-01-31 00:00:00', '2020-02-29 23:59:59'],

    '3months' => [Interval::MONTH, 3, '2020-01-01 12:00:00', '2020-01-01 00:00:00', '2020-03-31 23:59:59'],
    '3months_middle_of' => [Interval::MONTH, 3, '2020-01-12 23:59:00', '2020-01-12 00:00:00', '2020-03-31 23:59:59'],
    '3months_end_short' => [Interval::MONTH, 3, '2020-02-29 23:59:00', '2020-02-29 00:00:00', '2020-04-30 23:59:59'],
    '3months_end_long' => [Interval::MONTH, 3, '2020-01-31 23:59:00', '2020-01-31 00:00:00', '2020-03-31 23:59:59'],

    'bi_month' => [Interval::TWO_MONTHS, 1, '2020-01-01 12:00:00', '2020-01-01 00:00:00', '2020-02-29 23:59:59'],
    'bi_month_middle_of' => [Interval::TWO_MONTHS, 1, '2020-01-15 23:59:00', '2020-01-15 00:00:00', '2020-02-29 23:59:59'],
    'bi_month_second_one' => [Interval::TWO_MONTHS, 1, '2020-02-29 23:59:00', '2020-02-29 00:00:00', '2020-02-29 23:59:59'],
    'bi_month_end_of_first' => [Interval::TWO_MONTHS, 1, '2020-01-31 23:59:00', '2020-01-31 00:00:00', '2020-02-29 23:59:59'],

    '2bi_month' => [Interval::TWO_MONTHS, 2, '2020-01-01 12:00:00', '2020-01-01 00:00:00', '2020-04-30 23:59:59'],
    '2bi_month_middle_of' => [Interval::TWO_MONTHS, 2, '2020-01-15 23:59:00', '2020-01-15 00:00:00', '2020-04-30 23:59:59'],
    '2bi_month_second_one' => [Interval::TWO_MONTHS, 2, '2020-02-29 23:59:00', '2020-02-29 00:00:00', '2020-04-30 23:59:59'],
    '2bi_month_end_of_first' => [Interval::TWO_MONTHS, 2, '2020-01-31 23:59:00', '2020-01-31 00:00:00', '2020-04-30 23:59:59'],

    '3bi_month' => [Interval::TWO_MONTHS, 3, '2020-01-01 12:00:00', '2020-01-01 00:00:00', '2020-06-30 23:59:59'],
    '3bi_month_middle_of' => [Interval::TWO_MONTHS, 3, '2020-01-15 23:59:00', '2020-01-15 00:00:00', '2020-06-30 23:59:59'],
    '3bi_month_second_one' => [Interval::TWO_MONTHS, 3, '2020-02-29 23:59:00', '2020-02-29 00:00:00', '2020-06-30 23:59:59'],
    '3bi_month_end_of_first' => [Interval::TWO_MONTHS, 3, '2020-01-31 23:59:00', '2020-01-31 00:00:00', '2020-06-30 23:59:59'],

    'week_middle_of' => [Interval::WEEK, 1, '2020-01-01 12:00:00', '2020-01-01 00:00:00', '2020-01-05 23:59:59'],
    'week_start_of' => [Interval::WEEK, 1, '2020-01-06 23:59:00', '2020-01-06 00:00:00', '2020-01-12 23:59:59'],

    '2week_middle_of' => [Interval::WEEK, 2, '2020-01-01 12:00:00', '2020-01-01 00:00:00', '2020-01-12 23:59:59'],
    '2week_start_of' => [Interval::WEEK, 2, '2020-01-06 23:59:00', '2020-01-06 00:00:00', '2020-01-19 23:59:59'],

    '3week_middle_of' => [Interval::WEEK, 3, '2020-01-01 12:00:00', '2020-01-01 00:00:00', '2020-01-19 23:59:59'],
    '3week_start_of' => [Interval::WEEK, 3, '2020-01-06 23:59:00', '2020-01-06 00:00:00', '2020-01-26 23:59:59'],

    'half_year' => [Interval::HALF_YEAR, 1, '2020-01-01 12:00:00', '2020-01-01 00:00:00', '2020-06-30 23:59:59'],
    'half_year_in_second' => [Interval::HALF_YEAR, 1, '2020-08-15 23:59:00', '2020-08-15 00:00:00', '2020-12-31 23:59:59'],
    'half_year_in_first' => [Interval::HALF_YEAR, 1, '2020-02-29 23:59:00', '2020-02-29 00:00:00', '2020-06-30 23:59:59'],
    'half_year_end_of' => [Interval::HALF_YEAR, 1, '2020-12-31 23:59:00', '2020-12-31 00:00:00', '2020-12-31 23:59:59'],

    '2half_year' => [Interval::HALF_YEAR, 2, '2020-01-01 12:00:00', '2020-01-01 00:00:00', '2020-12-31 23:59:59'],
    '2half_year_in_second' => [Interval::HALF_YEAR, 2, '2020-08-15 23:59:00', '2020-08-15 00:00:00', '2021-06-30 23:59:59'],
    '2half_year_in_first' => [Interval::HALF_YEAR, 2, '2020-02-29 23:59:00', '2020-02-29 00:00:00', '2020-12-31 23:59:59'],
    '2half_year_end_of' => [Interval::HALF_YEAR, 2, '2020-12-31 23:59:00', '2020-12-31 00:00:00', '2021-06-30 23:59:59'],

    '3half_year' => [Interval::HALF_YEAR, 3, '2020-01-01 12:00:00', '2020-01-01 00:00:00', '2021-06-30 23:59:59'],
    '3half_year_in_second' => [Interval::HALF_YEAR, 3, '2020-08-15 23:59:00', '2020-08-15 00:00:00', '2021-12-31 23:59:59'],
    '3half_year_in_first' => [Interval::HALF_YEAR, 3, '2020-02-29 23:59:00', '2020-02-29 00:00:00', '2021-06-30 23:59:59'],
    '3half_year_end_of' => [Interval::HALF_YEAR, 3, '2020-12-31 23:59:00', '2020-12-31 00:00:00', '2021-12-31 23:59:59'],

    'year' => [Interval::YEAR, 1, '2020-01-01 12:00:00', '2020-01-01 00:00:00', '2020-12-31 23:59:59'],
    'year_middle_of' => [Interval::YEAR, 1, '2020-06-15 23:59:00', '2020-06-15 00:00:00', '2020-12-31 23:59:59'],
    'year_end_no_of' => [Interval::YEAR, 1, '2020-02-29 23:59:00', '2020-02-29 00:00:00', '2020-12-31 23:59:59'],
    'year_end_with_of' => [Interval::YEAR, 1, '2020-01-31 23:59:00', '2020-01-31 00:00:00', '2020-12-31 23:59:59'],

    '2year' => [Interval::YEAR, 2, '2020-01-01 12:00:00', '2020-01-01 00:00:00', '2021-12-31 23:59:59'],
    '2year_middle_of' => [Interval::YEAR, 2, '2020-06-15 23:59:00', '2020-06-15 00:00:00', '2021-12-31 23:59:59'],
    '2year_end_no_of' => [Interval::YEAR, 2, '2020-02-29 23:59:00', '2020-02-29 00:00:00', '2021-12-31 23:59:59'],
    '2year_end_with_of' => [Interval::YEAR, 2, '2020-01-31 23:59:00', '2020-01-31 00:00:00', '2021-12-31 23:59:59'],

    '3year' => [Interval::YEAR, 3, '2020-01-01 12:00:00', '2020-01-01 00:00:00', '2022-12-31 23:59:59'],
    '3year_middle_of' => [Interval::YEAR, 3, '2020-06-15 23:59:00', '2020-06-15 00:00:00', '2022-12-31 23:59:59'],
    '3year_end_no_of' => [Interval::YEAR, 3, '2020-02-29 23:59:00', '2020-02-29 00:00:00', '2022-12-31 23:59:59'],
    '3year_end_with_of' => [Interval::YEAR, 3, '2020-01-31 23:59:00', '2020-01-31 00:00:00', '2022-12-31 23:59:59'],

    'quarter' => [Interval::QUARTER, 1, '2020-01-01 12:00:00', '2020-01-01 00:00:00', '2020-03-31 23:59:59'],
    'quarter_third_month' => [Interval::QUARTER, 1, '2020-06-15 23:59:00', '2020-06-15 00:00:00', '2020-06-30 23:59:59'],
    'quarter_second_month' => [Interval::QUARTER, 1, '2020-02-29 23:59:00', '2020-02-29 00:00:00', '2020-03-31 23:59:59'],
    'quarter_first_month' => [Interval::QUARTER, 1, '2020-01-31 23:59:00', '2020-01-31 00:00:00', '2020-03-31 23:59:59'],

    '2quarter' => [Interval::QUARTER, 2, '2020-01-01 12:00:00', '2020-01-01 00:00:00', '2020-06-30 23:59:59'],
    '2quarter_third_month' => [Interval::QUARTER, 2, '2020-06-15 23:59:00', '2020-06-15 00:00:00', '2020-09-30 23:59:59'],
    '2quarter_second_month' => [Interval::QUARTER, 2, '2020-02-29 23:59:00', '2020-02-29 00:00:00', '2020-06-30 23:59:59'],
    '2quarter_first_month' => [Interval::QUARTER, 2, '2020-01-31 23:59:00', '2020-01-31 00:00:00', '2020-06-30 23:59:59'],

    '3quarter' => [Interval::QUARTER, 3, '2020-01-01 12:00:00', '2020-01-01 00:00:00', '2020-09-30 23:59:59'],
    '3quarter_third_month' => [Interval::QUARTER, 3, '2020-06-15 23:59:00', '2020-06-15 00:00:00', '2020-12-31 23:59:59'],
    '3quarter_second_month' => [Interval::QUARTER, 3, '2020-02-29 23:59:00', '2020-02-29 00:00:00', '2020-09-30 23:59:59'],
    '3quarter_first_month' => [Interval::QUARTER, 3, '2020-01-31 23:59:00', '2020-01-31 00:00:00', '2020-09-30 23:59:59'],

    // Day behaves the same as unsynced
    'day' => [Interval::DAY, 1, '2020-01-11 12:00:00', '2020-01-11 00:00:00', '2020-01-11 23:59:59'],
    'day_middle_of' => [Interval::DAY, 1, '2020-01-11 23:59:00', '2020-01-11 00:00:00', '2020-01-11 23:59:59'],
    'day_end_no_of' => [Interval::DAY, 1, '2020-01-11 23:59:00', '2020-01-11 00:00:00', '2020-01-11 23:59:59'],
    'day_end_with_of' => [Interval::DAY, 1, '2020-01-11 23:59:00', '2020-01-11 00:00:00', '2020-01-11 23:59:59'],
    '2day' => [Interval::DAY, 2, '2020-01-11 12:00:00', '2020-01-11 00:00:00', '2020-01-12 23:59:59'],
    '3day' => [Interval::DAY, 3, '2020-01-11 12:00:00', '2020-01-11 00:00:00', '2020-01-13 23:59:59'],
]);

it('calculates period start correct', function ($interval, $period, $input, $expectedVirtualStart) {
    $period = new Period($interval, $period, $input, true);

    expect($period)->getVirtualStartDate()->toBeCarbon($expectedVirtualStart);
})->with([
    'month' => [Interval::MONTH, 1, '2020-01-01 12:00:00', '2020-01-01 00:00:00'],
    'month_middle_of' => [Interval::MONTH, 1, '2020-01-12 23:59:00', '2020-01-01 00:00:00'],
    'month_end_short' => [Interval::MONTH, 1, '2020-02-29 23:59:00', '2020-02-01 00:00:00'],
    'month_end_long' => [Interval::MONTH, 1, '2020-01-31 23:59:00', '2020-01-01 00:00:00'],

    'bi_month' => [Interval::TWO_MONTHS, 1, '2020-01-01 12:00:00', '2020-01-01 00:00:00'],
    'bi_month_middle_of' => [Interval::TWO_MONTHS, 1, '2020-01-15 23:59:00', '2020-01-01 00:00:00'],
    'bi_month_second_one' => [Interval::TWO_MONTHS, 1, '2020-02-29 23:59:00', '2020-01-01 00:00:00'],
    'bi_month_end_of_first' => [Interval::TWO_MONTHS, 1, '2020-01-31 23:59:00', '2020-01-01 00:00:00'],
    'bi_month_end_of_third' => [Interval::TWO_MONTHS, 1, '2020-03-31 23:59:00', '2020-03-01 00:00:00'],

    'week_middle_of' => [Interval::WEEK, 1, '2020-01-01 12:00:00', '2019-12-30 00:00:00'],
    'week_start_of' => [Interval::WEEK, 1, '2020-01-06 23:59:00', '2020-01-06 00:00:00'],
    'week_end_of' => [Interval::WEEK, 1, '2020-01-12 23:59:00', '2020-01-06 00:00:00'],

    'half_year' => [Interval::HALF_YEAR, 1, '2020-01-01 12:00:00', '2020-01-01 00:00:00'],
    'half_year_in_second' => [Interval::HALF_YEAR, 1, '2020-08-15 23:59:00', '2020-07-01 00:00:00'],
    'half_year_in_first' => [Interval::HALF_YEAR, 1, '2020-02-29 23:59:00', '2020-01-01 00:00:00'],
    'half_year_end_of' => [Interval::HALF_YEAR, 1, '2020-12-31 23:59:00', '2020-07-01 00:00:00'],

    'year' => [Interval::YEAR, 1, '2020-01-01 12:00:00', '2020-01-01 00:00:00'],
    'year_middle_of' => [Interval::YEAR, 1, '2020-06-15 23:59:00', '2020-01-01 00:00:00'],
    'year_end_no_of' => [Interval::YEAR, 1, '2020-02-29 23:59:00', '2020-01-01 00:00:00'],
    'year_end_with_of' => [Interval::YEAR, 1, '2020-01-31 23:59:00', '2020-01-01 00:00:00'],

    'quarter' => [Interval::QUARTER, 1, '2020-01-01 12:00:00', '2020-01-01 00:00:00'],
    'quarter_second_month' => [Interval::QUARTER, 1, '2020-02-29 23:59:00', '2020-01-01 00:00:00'],
    'quarter_first_month' => [Interval::QUARTER, 1, '2020-01-31 23:59:00', '2020-01-01 00:00:00'],
    'quarter_third_month' => [Interval::QUARTER, 1, '2020-03-31 23:59:00', '2020-01-01 00:00:00'],
    '3rt_quarter_second_month' => [Interval::QUARTER, 1, '2020-08-15 23:59:00', '2020-07-01 00:00:00'],

    'day' => [Interval::DAY, 1, '2020-01-11 12:00:00', '2020-01-11 00:00:00'],
    'day_middle_of' => [Interval::DAY, 1, '2020-01-11 23:59:00', '2020-01-11 00:00:00'],
    'day_end_no_of' => [Interval::DAY, 1, '2020-01-11 23:59:00', '2020-01-11 00:00:00'],
    'day_end_with_of' => [Interval::DAY, 1, '2020-01-11 23:59:00', '2020-01-11 00:00:00'],
]);

it('period keeps synced over months', function () {
    $period = new Period(Interval::MONTH, 1, '2020-01-31 12:00:00', true);

    expect($period)->getStartDate()->toBeCarbon('2020-01-31 00:00:00')
        ->getEndDate()->toBeCarbon('2020-01-31 23:59:59');

    $period = new Period(Interval::MONTH, 1, $period->getEndDate()->addSecond(), true);

    expect($period)->getStartDate()->toBeCarbon('2020-02-01 00:00:00')
        ->getEndDate()->toBeCarbon('2020-02-29 23:59:59');

    $period = new Period(Interval::MONTH, 1, $period->getEndDate()->addSecond(), true);

    expect($period)->getStartDate()->toBeCarbon('2020-03-01 00:00:00')
        ->getEndDate()->toBeCarbon('2020-03-31 23:59:59');

    $period = new Period(Interval::MONTH, 1, $period->getEndDate()->addSecond(), true);
    $period = new Period(Interval::MONTH, 1, $period->getEndDate()->addSecond(), true);
    $period = new Period(Interval::MONTH, 1, $period->getEndDate()->addSecond(), true);

    expect($period)->getStartDate()->toBeCarbon('2020-06-01 00:00:00')
        ->getEndDate()->toBeCarbon('2020-06-30 23:59:59');

    $period = new Period(Interval::MONTH, 1, $period->getEndDate()->addSecond(), true);

    expect($period)->getStartDate()->toBeCarbon('2020-07-01 00:00:00')
        ->getEndDate()->toBeCarbon('2020-07-31 23:59:59');
});
