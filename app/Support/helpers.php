<?php

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
