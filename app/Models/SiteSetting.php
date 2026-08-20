<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'label',
        'type',
    ];

    /**
     * Get a setting value by key with optional default.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        $value = $setting->value;

        if ($setting->type === 'json' && is_string($value)) {
            return json_decode($value, true) ?? $default;
        }

        if ($setting->type === 'image' && ! empty($value)) {
            return asset('storage/'.$value);
        }

        return $value;
    }

    /**
     * Get all settings grouped by their group.
     */
    public static function getGrouped(): array
    {
        $settings = static::all();
        $grouped = [];

        foreach ($settings as $setting) {
            $value = $setting->value;
            if ($setting->type === 'json' && is_string($value)) {
                $value = json_decode($value, true) ?? $value;
            }
            $grouped[$setting->group][$setting->key] = $value;
        }

        return $grouped;
    }

    /**
     * Get all settings as a flat key-value map.
     */
    public static function getAllAsMap(): array
    {
        $settings = static::all();
        $map = [];

        foreach ($settings as $setting) {
            $value = $setting->value;
            if ($setting->type === 'json' && is_string($value)) {
                $value = json_decode($value, true) ?? $value;
            }
            if ($setting->type === 'image' && ! empty($value)) {
                $value = asset('storage/'.$value);
            }
            $map[$setting->key] = $value;
        }

        return $map;
    }
}
