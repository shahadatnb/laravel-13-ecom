<?php

use App\Models\ProductVariant;

test('derives weight from a weight-named attribute value with unit', function () {
    expect(ProductVariant::deriveUnitWeight(['Weight' => '5kg']))->toEqual(5.0);
});

test('derives weight from spaced and decimal values', function () {
    expect(ProductVariant::deriveUnitWeight(['Weight' => '0.5 kg']))->toEqual(0.5)
        ->and(ProductVariant::deriveUnitWeight(['Weight' => '2.5kg']))->toEqual(2.5);
});

test('accepts a bare number on a weight-named key', function () {
    expect(ProductVariant::deriveUnitWeight(['Weight' => '10']))->toEqual(10.0);
});

test('derives weight from the variant name when attributes carry none', function () {
    expect(ProductVariant::deriveUnitWeight(['name' => '25kg Sack']))->toEqual(25.0);
});

test('converts bengali digits and keywords', function () {
    expect(ProductVariant::deriveUnitWeight(['ওজন' => '১০ কেজি']))->toEqual(10.0);
});

test('parses a leading-dot decimal weight', function () {
    expect(ProductVariant::deriveUnitWeight(['ওজন' => '.5 কেজি']))->toEqual(0.5)
        ->and(ProductVariant::deriveUnitWeight(['name' => 'চিপস .5kg']))->toEqual(0.5)
        ->and(ProductVariant::deriveUnitWeight(['Weight' => '.25 kg']))->toEqual(0.25);
});

test('converts gram weights to kilograms', function () {
    expect(ProductVariant::deriveUnitWeight(['Weight' => '500g']))->toEqual(0.5)
        ->and(ProductVariant::deriveUnitWeight(['ওজন' => '৫০০ গ্রাম']))->toEqual(0.5)
        ->and(ProductVariant::deriveUnitWeight(['name' => 'চা 250 গ্রাম']))->toEqual(0.25);
});

test('treats comma as a decimal separator', function () {
    expect(ProductVariant::deriveUnitWeight(['Weight' => '1,5kg']))->toEqual(1.5)
        ->and(ProductVariant::deriveUnitWeight(['Weight' => '0,5 কেজি']))->toEqual(0.5);
});

test('does not treat non-weight words as units', function () {
    expect(ProductVariant::deriveUnitWeight(['Size' => '5 green']))->toBeNull()
        ->and(ProductVariant::deriveUnitWeight(['Size' => '38', 'Color' => 'gold']))->toBeNull();
});

test('prefers the weight-named key over other values', function () {
    expect(ProductVariant::deriveUnitWeight(['Color' => 'Red', 'Weight' => '5kg']))->toEqual(5.0);
});

test('returns null when no weight can be determined', function () {
    expect(ProductVariant::deriveUnitWeight([]))->toBeNull()
        ->and(ProductVariant::deriveUnitWeight(['Size' => 'Large']))->toBeNull()
        ->and(ProductVariant::deriveUnitWeight(['Size' => '38', 'Color' => 'Red']))->toBeNull();
});

test('resolveUnitWeight prefers the stored column and falls back to attributes', function () {
    $variant = new ProductVariant(['unit_weight' => 5.0, 'attributes' => ['Weight' => '10kg']]);
    expect($variant->resolveUnitWeight())->toEqual(5.0);

    $variantWithoutColumn = new ProductVariant(['unit_weight' => null, 'attributes' => ['Weight' => '10kg']]);
    expect($variantWithoutColumn->resolveUnitWeight())->toEqual(10.0);
});
