<?php

use Carbon\Carbon;

const MONTHS_IN_HALF_YEAR = [1, 2, 3, 4, 5, 6];
const MONTHS_IN_A_YEAR = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

function format_money($money)
{
    if (!$money) {
        return "\$0.00";
    }

    $money = number_format($money, 2);
    if (strpos($money, '-')) {
        $formatted = explode('-', $money);
        return "-\$$formatted[1]";
    }

    return "\$$money";
}

function get_date_range(string $period)
{
    Carbon::setWeekStartsAt(Carbon::SUNDAY);
    Carbon::setWeekEndsAt(Carbon::SATURDAY);

    $current_date = Carbon::now();
    $date_range = [];
    switch ($period) {
        case 'daily':
            $date_range = [$current_date->clone()->startOfDay(), $current_date->clone()->endOfDay()];
        case 'weekly':
            $date_range = [$current_date->clone()->startOfWeek(), $current_date->clone()->endOfWeek()];
        case 'monthly':
            $date_range = [$current_date->clone()->startOfMonth(), $current_date->clone()->endOfMonth()];
        case 'quarterly':
            $date_range = [$current_date->clone()->startOfQuarter(), $current_date->clone()->endOfQuarter()];
        case 'annually':
            $date_range = [$current_date->clone()->startOfYear(), $current_date->clone()->endOfYear()];
    }

    return $date_range;
}

function get_transaction_names()
{
    return [
        'income' => 'Income',
        'payment' => 'Payment',
        'expense' => 'Expense',
        'transfer' => 'Transfer'
    ];
}
