<?php

namespace App\Support;

class PriceFormatter
{
    public static function format(string|int|float $price): string
    {
        $formatted = number_format((float) $price, 2, ',', ' ');

        return str_ends_with($formatted, ',00')
            ? substr($formatted, 0, -3)
            : $formatted;
    }
}
