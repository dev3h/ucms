<?php

use Carbon\Carbon;

if (!function_exists('format_date')) {
    /**
     * Formats a date string into the 'Y/m/d' format.
     *
     * @param string $date
     * @return string
     */
    function format_date($date): string
    {
        return Carbon::parse($date)->format('Y/m/d');
    }
}

if (!function_exists('format_datetime')) {
    /**
     * Formats a datetime string into the specified format.
     *
     * @param mixed $datetime
     * @return string
     */
    function format_datetime($datetime): string
    {
        return Carbon::parse($datetime)->format('Y/m/d H:i');
    }
}
