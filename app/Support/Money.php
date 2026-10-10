<?php

namespace App\Support;

/** Money is calculated in integer minor units (paise) to avoid floating-point drift. */
final class Money
{
    public static function toMinor(string|int|float $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }

    /** Human-readable amount, e.g. 2,500.00 (no currency symbol). */
    public static function format(int $minor): string
    {
        return number_format($minor / 100, 2);
    }

    /** Plain decimal string suitable for a DECIMAL(x,2) column. */
    public static function decimal(int $minor): string
    {
        return number_format($minor / 100, 2, '.', '');
    }
}
