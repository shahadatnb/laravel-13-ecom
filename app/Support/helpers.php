<?php

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Config;

if (! function_exists('module_enabled')) {
    /**
     * Whether a feature module is enabled in this deployment.
     */
    function module_enabled(string $module): bool
    {
        return (bool) Config::get("modules.{$module}.enabled", true);
    }
}

if (! function_exists('enabled_modules')) {
    /**
     * Keys of every module enabled in this deployment.
     */
    function enabled_modules(): array
    {
        $enabled = [];

        foreach (Config::get('modules', []) as $key => $meta) {
            if (Config::get("modules.{$key}.enabled", true)) {
                $enabled[] = $key;
            }
        }

        return $enabled;
    }
}

if (! function_exists('currency_symbol')) {
    /**
     * Get the currency symbol from site settings.
     */
    function currency_symbol(): string
    {
        return SiteSetting::getValue('currency_symbol', '৳');
    }
}

if (! function_exists('currency_code')) {
    /**
     * Get the currency code from site settings.
     */
    function currency_code(): string
    {
        return SiteSetting::getValue('currency_code', 'BDT');
    }
}

if (! function_exists('format_currency')) {
    /**
     * Format a number with the currency symbol from site settings.
     */
    function format_currency($amount, int $decimals = 0): string
    {
        $symbol = currency_symbol();
        $position = SiteSetting::getValue('currency_position', 'before');

        $formatted = number_format((float) $amount, $decimals);

        return $position === 'after'
            ? $formatted . ' ' . $symbol
            : $symbol . $formatted;
    }
}
