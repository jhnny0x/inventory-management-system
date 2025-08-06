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

function get_date_range(string $period): array
{
    Carbon::setWeekStartsAt(Carbon::SUNDAY);
    Carbon::setWeekEndsAt(Carbon::SATURDAY);

    $date_range = [];
    $current_date = Carbon::now();
    switch ($period) {
        case 'daily':
        case 'today':
            $date_range = [
                $current_date->clone()->startOfDay(),
                $current_date->clone()->endOfDay()
            ];
            break;
        case 'yesterday':
            $date_range = [
                $current_date->clone()->subDay(1)->startOfDay(),
                $current_date->clone()->subDay(1)->endOfDay()
            ];
            break;
        case 'weekly':
        case 'week':
            $date_range = [
                $current_date->clone()->startOfWeek(),
                $current_date->clone()->endOfWeek()
            ];
            break;
        case 'monthly':
        case 'month':
            $date_range = [
                $current_date->clone()->startOfMonth(),
                $current_date->clone()->endOfMonth()
            ];
            break;
        case 'quarterly':
        case 'quarter':
        case 'trimester':
            $date_range = [
                $current_date->clone()->startOfQuarter(),
                $current_date->clone()->endOfQuarter()
            ];
            break;
        case 'annually':
        case 'annual':
        case 'year':
            $date_range = [
                $current_date->clone()->startOfYear(),
                $current_date->clone()->endOfYear()
            ];
            break;
    }

    return $date_range;
}

function get_transaction_names(): array
{
    return [
        'income' => 'Income',
        'payment' => 'Payment',
        'expense' => 'Expense',
        'transfer' => 'Transfer'
    ];
}
