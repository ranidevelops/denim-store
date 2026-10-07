<?php

namespace App\Support;

/** Money is calculated in integer minor units (paise) to avoid floating-point drift. */
final class Money
{
    public static function toMinor(string|int|float $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }

    /** Plain decimal string suitable for a DECIMAL(x,2) column. */
    public static function decimal(int $minor): string
    {
        return number_format($minor / 100, 2, '.', '');
    }
}
