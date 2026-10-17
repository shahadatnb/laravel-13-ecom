<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'barcode',
        'price',
        'stock',
        'unit_weight',
        'attributes',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'unit_weight' => 'float',
        ];
    }

    private const WEIGHT_KEY_PATTERN = '/weight|kg|ওজন|কেজি/i';

    // Value carries an explicit unit: "5kg", "0.5 kg", ".5কেজি", "500g", "৫০০ গ্রাম".
    private const WEIGHT_VALUE_WITH_UNIT = '/(\d+(?:[.,]\d+)?|[.,]\d+)\s*(kg|কেজি|গ্রাম|grams?|gms?|gs?)(?![a-zA-Z])/iu';

    private const GRAM_UNIT = '/^(?:গ্রাম|grams?|gms?|gs?)$/iu';

    private const BARE_NUMBER = '/(\d+(?:[.,]\d+)?|[.,]\d+)/u';

    private const BENGALI_DIGITS = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];

    private const LATIN_DIGITS = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    /**
     * Parse the first weight in a free-text value ("5kg", ".5 কেজি", "500 গ্রাম").
     * Gram values are converted to kilograms. Returns null when no
     * unit-qualified weight is present.
     */
    private static function parseWeightWithUnit(string $text): ?float
    {
        $text = str_replace(self::BENGALI_DIGITS, self::LATIN_DIGITS, $text);

        if (! preg_match(self::WEIGHT_VALUE_WITH_UNIT, $text, $matches)) {
            return null;
        }

        $value = (float) str_replace(',', '.', $matches[1]);

        return preg_match(self::GRAM_UNIT, $matches[2]) ? $value / 1000 : $value;
    }

    /**
     * Parse a bare number ("5", "0.5", "৫") used on weight-named keys
     * where the unit is implied to be kilograms.
     */
    private static function parseBareWeight(string $text): ?float
    {
        $text = str_replace(self::BENGALI_DIGITS, self::LATIN_DIGITS, $text);

        return preg_match(self::BARE_NUMBER, $text, $matches)
            ? (float) str_replace(',', '.', $matches[1])
            : null;
    }

    /**
     * Derive a kilogram weight from the variant's attribute map.
     *
     * Prefers an attribute whose name mentions weight/kg (e.g. "Weight" => "5kg")
     * and falls back to the first value carrying an explicit kg/g unit.
     * Returns null when no weight can be determined.
     *
     * @param  array<string, mixed>  $attributes
     */
    public static function deriveUnitWeight(array $attributes): ?float
    {
        foreach ($attributes as $key => $value) {
            if (! is_scalar($value) || ! preg_match(self::WEIGHT_KEY_PATTERN, (string) $key)) {
                continue;
            }

            $weight = self::parseWeightWithUnit((string) $value)
                ?? self::parseBareWeight((string) $value);

            if ($weight !== null) {
                return $weight;
            }
        }

        foreach ($attributes as $value) {
            if (! is_scalar($value)) {
                continue;
            }

            $weight = self::parseWeightWithUnit((string) $value);

            if ($weight !== null) {
                return $weight;
            }
        }

        return null;
    }

    /**
     * Resolve this variant's kilogram weight: stored column first,
     * then a fallback derivation from the attribute map.
     */
    public function resolveUnitWeight(): ?float
    {
        if ($this->unit_weight !== null && (float) $this->unit_weight > 0) {
            return (float) $this->unit_weight;
        }

        return self::deriveUnitWeight($this->attributes ?? []);
    }

    /**
     * Get the variant attributes as an array.
     *
     * Handles double-encoded JSON from existing records where attributes
     * were stored as a JSON-string inside the JSON column.
     */
    public function getAttributesAttribute($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            // Try normal decode first ({"Size":"S","Color":"Blue"})
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $decoded;
            }

            // Handle double-encoded: "{\"Size\":\"S\"}" → the value is a JSON string
            // json_decode gives us the inner string, then decode again
            $inner = json_decode($value);
            if (is_string($inner)) {
                $decoded = json_decode($inner, true);

                return is_array($decoded) ? $decoded : [];
            }
        }

        return [];
    }

    /**
     * Set the variant attributes, ensuring it's stored as a proper JSON object.
     */
    public function setAttributesAttribute($value): void
    {
        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $this->attributes['attributes'] = json_encode($decoded);

                return;
            }
        }

        $this->attributes['attributes'] = is_array($value)
            ? json_encode($value)
            : ($value ?? '[]');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductGallery::class);
    }
}
