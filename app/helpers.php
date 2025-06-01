<?php

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
