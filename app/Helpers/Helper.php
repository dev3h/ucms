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

if (!function_exists('get_current_user_login')) {
    /**
     * Retrieves the currently logged in user.
     *
     * @return mixed
     */
    function get_current_user_login()
    {
        if (auth()->check()) {
            return auth()->user();
        }

        return null;
    }
}
