<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductGallery;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $brands = Brand::all()->keyBy('name');
        $categories = Category::all()->keyBy('name');

        $products = $this->getProducts();

        foreach ($products as $data) {
            $brandId = $brands[$data['brand']]->id ?? null;
            $categoryId = $categories[$data['category']]->id ?? null;
            $slug = Str::slug($data['name']);

            // Handle duplicate slugs
            if (Product::where('slug', $slug)->exists()) {
                $slug .= '-' . Str::random(4);
            }

            $product = Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'brand_id'          => $brandId,
                    'category_id'       => $categoryId,
                    'name'              => $data['name'],
                    'name_bn'           => $data['name_bn'] ?? null,
                    'short_description' => $data['short'] ?? null,
                    'description'       => $data['desc'] ?? null,
                    'sku'               => $data['sku'] ?? null,
                    'regular_price'     => $data['price'],
                    'sale_price'        => isset($data['sale']) ? $data['sale'] : round($data['price'] * 0.85, 2),
                    'wholesale_price'   => round($data['price'] * 0.70, 2),
                    'cost_price'        => round($data['price'] * 0.50, 2),
                    'stock'             => $data['stock'],
                    'minimum_stock'     => 10,
                    'maximum_order'     => 10,
                    'weight'            => $data['weight'] ?? 0.5,
                    'tax_class'         => 'standard',
                    'shipping_class'    => 'standard',
                    'status'            => 'published',
                    'product_type'      => $data['type'],
                    'featured'          => $data['featured'] ?? false,
                    'visibility'        => 'public',
                ]
            );

            // ── Thumbnail ──
            if (!$product->thumbnail) {
                $product->update(['thumbnail' => $this->img($data['name'], 400, 400)]);
            }

            // ── Gallery Images ──
            if ($product->images()->count() === 0) {
                $galleryColors = ['f8f9fa', 'e9ecef', 'dee2e6'];
                for ($i = 0; $i < 3; $i++) {
                    ProductGallery::create([
                        'product_id' => $product->id,
                        'image'      => $this->img($data['name'], 600, 600, $galleryColors[$i]),
                        'alt_text'   => $data['name'] . ' image ' . ($i + 1),
                        'sort_order' => $i,
                    ]);
                }
            }

            // ── Variants ──
            if ($data['type'] === 'variable' && $product->variants()->count() === 0 && isset($data['variants'])) {
                foreach ($data['variants'] as $i => $v) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'name'       => $v['name'],
                        'sku'        => ($data['sku'] ?? Str::slug($data['name'])) . '-' . Str::slug($v['name']),
                        'price'      => $v['price'] ?? $data['price'],
                        'stock'      => $v['stock'] ?? 30,
                        'attributes' => $v['attributes'] ?? [],
                        'sort_order' => $i,
                    ]);
                }
            }
        }

        $count = Product::count();
        $this->command->info("✅ Products seeded: {$count} total (with images & variants).");
    }

    private function img(string $text, int $w, int $h, string $bg = '0d6efd'): string
    {
        $clean = urlencode(Str::limit($text, 20));
        return "https://placehold.co/{$w}x{$h}/{$bg}/ffffff?text={$clean}";
    }

    private function getProducts(): array
    {
        return [
            // ═══════════════ SMARTPHONES ═══════════════
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'name_bn' => 'স্যামসাং গ্যালাক্সি এস২৪ আলট্রা',
                'category' => 'Smartphones & Tablets',
                'brand' => 'Samsung',
                'sku' => 'SAM-S24U-256',
                'type' => 'variable',
                'price' => 139999,
                'stock' => 50,
                'weight' => 0.23,
                'featured' => true,
                'short' => 'Galaxy AI flagship with S Pen, 200MP camera, and titanium frame.',
                'desc' => '<h2>Samsung Galaxy S24 Ultra</h2><p>The ultimate Galaxy experience. Featuring the Snapdragon 8 Gen 3 processor, a stunning 6.8" QHD+ Dynamic AMOLED display, and an embedded S Pen for productivity on the go.</p><ul><li>200MP main camera with AI-enhanced photography</li><li>Titanium frame — stronger and lighter</li><li>5000mAh battery with 45W fast charging</li><li>Galaxy AI: Live Translate, Circle to Search, Chat Assist</li></ul>',
                'variants' => [
                    ['name' => '256GB Titanium Black', 'price' => 139999, 'stock' => 20, 'attributes' => ['Storage' => '256GB', 'Color' => 'Titanium Black']],
                    ['name' => '256GB Titanium Gray', 'price' => 139999, 'stock' => 15, 'attributes' => ['Storage' => '256GB', 'Color' => 'Titanium Gray']],
                    ['name' => '512GB Titanium Black', 'price' => 159999, 'stock' => 10, 'attributes' => ['Storage' => '512GB', 'Color' => 'Titanium Black']],
                    ['name' => '1TB Titanium Violet', 'price' => 189999, 'stock' => 5, 'attributes' => ['Storage' => '1TB', 'Color' => 'Titanium Violet']],
                ],
            ],
            [
                'name' => 'Samsung Galaxy S24+',
                'category' => 'Smartphones & Tablets',
                'brand' => 'Samsung',
                'sku' => 'SAM-S24P',
                'type' => 'variable',
                'price' => 99999,
                'stock' => 80,
                'weight' => 0.19,
                'featured' => true,
                'short' => 'Big screen Galaxy AI experience with 4900mAh battery.',
                'variants' => [
                    ['name' => '256GB Onyx Black', 'price' => 99999, 'stock' => 30, 'attributes' => ['Storage' => '256GB', 'Color' => 'Onyx Black']],
                    ['name' => '256GB Cobalt Violet', 'price' => 99999, 'stock' => 25, 'attributes' => ['Storage' => '256GB', 'Color' => 'Cobalt Violet']],
                    ['name' => '512GB Amber Yellow', 'price' => 114999, 'stock' => 15, 'attributes' => ['Storage' => '512GB', 'Color' => 'Amber Yellow']],
                ],
            ],
            [
                'name' => 'Samsung Galaxy A54 5G',
                'category' => 'Smartphones & Tablets',
                'brand' => 'Samsung',
                'type' => 'simple',
                'price' => 38999,
                'stock' => 120,
                'short' => 'Premium mid-range with IP67 water resistance and 50MP OIS camera.',
            ],
            [
                'name' => 'Apple iPhone 15 Pro Max',
                'name_bn' => 'অ্যাপল আইফোন ১৫ প্রো ম্যাক্স',
                'category' => 'Smartphones & Tablets',
                'brand' => 'Apple',
                'sku' => 'APL-15PM',
                'type' => 'variable',
                'price' => 159999,
                'stock' => 40,
                'weight' => 0.22,
                'featured' => true,
                'short' => 'Titanium design, A17 Pro chip, and a 5x Telephoto camera.',
                'variants' => [
                    ['name' => '256GB Natural Titanium', 'price' => 159999, 'stock' => 12, 'attributes' => ['Storage' => '256GB', 'Color' => 'Natural Titanium']],
                    ['name' => '256GB Blue Titanium', 'price' => 159999, 'stock' => 10, 'attributes' => ['Storage' => '256GB', 'Color' => 'Blue Titanium']],
                    ['name' => '512GB Black Titanium', 'price' => 189999, 'stock' => 8, 'attributes' => ['Storage' => '512GB', 'Color' => 'Black Titanium']],
                    ['name' => '1TB White Titanium', 'price' => 219999, 'stock' => 5, 'attributes' => ['Storage' => '1TB', 'Color' => 'White Titanium']],
                ],
            ],
            [
                'name' => 'Apple iPhone 15',
                'category' => 'Smartphones & Tablets',
                'brand' => 'Apple',
                'type' => 'variable',
                'price' => 109999,
                'stock' => 60,
                'featured' => true,
                'short' => 'Dynamic Island, 48MP camera, USB-C, and color-infused glass back.',
                'variants' => [
                    ['name' => '128GB Black', 'price' => 109999, 'stock' => 20, 'attributes' => ['Storage' => '128GB', 'Color' => 'Black']],
                    ['name' => '128GB Blue', 'price' => 109999, 'stock' => 15, 'attributes' => ['Storage' => '128GB', 'Color' => 'Blue']],
                    ['name' => '256GB Green', 'price' => 124999, 'stock' => 10, 'attributes' => ['Storage' => '256GB', 'Color' => 'Green']],
                ],
            ],
            [
                'name' => 'Apple iPhone 14',
                'category' => 'Smartphones & Tablets',
                'brand' => 'Apple',
                'type' => 'simple',
                'price' => 79999,
                'stock' => 100,
                'short' => 'A15 Bionic chip, 12MP dual camera system, Ceramic Shield front.',
            ],
            [
                'name' => 'Xiaomi 14 Ultra',
                'category' => 'Smartphones & Tablets',
                'brand' => 'Samsung',
                'type' => 'simple',
                'price' => 89999,
                'stock' => 30,
                'short' => 'Leica Summilux optics, Snapdragon 8 Gen 3, 5000mAh battery.',
            ],

            // ═══════════════ LAPTOPS ═══════════════
            [
                'name' => 'Apple MacBook Pro 16" M3 Max',
                'name_bn' => 'অ্যাপল ম্যাকবুক প্রো ১৬" এম৩ ম্যাক্স',
                'category' => 'Computers & Laptops',
                'brand' => 'Apple',
                'sku' => 'APL-MBP16-M3',
                'type' => 'variable',
                'price' => 319999,
                'stock' => 20,
                'weight' => 2.14,
                'featured' => true,
                'short' => 'The most powerful Mac laptop ever. Up to 128GB unified memory.',
                'variants' => [
                    ['name' => '36GB / 1TB Space Black', 'price' => 319999, 'stock' => 8, 'attributes' => ['RAM' => '36GB', 'Storage' => '1TB', 'Color' => 'Space Black']],
                    ['name' => '48GB / 1TB Silver', 'price' => 379999, 'stock' => 5, 'attributes' => ['RAM' => '48GB', 'Storage' => '1TB', 'Color' => 'Silver']],
                    ['name' => '128GB / 2TB Space Black', 'price' => 519999, 'stock' => 3, 'attributes' => ['RAM' => '128GB', 'Storage' => '2TB', 'Color' => 'Space Black']],
                ],
            ],
            [
                'name' => 'Apple MacBook Air 15" M3',
                'category' => 'Computers & Laptops',
                'brand' => 'Apple',
                'type' => 'simple',
                'price' => 159999,
                'stock' => 45,
                'weight' => 1.51,
                'short' => 'Impossibly thin. Incredibly powerful. 18-hour battery life.',
            ],
            [
                'name' => 'Dell XPS 15 (2024)',
                'category' => 'Computers & Laptops',
                'brand' => 'Dell',
                'type' => 'variable',
                'price' => 189999,
                'stock' => 20,
                'weight' => 1.86,
                'short' => 'InfinityEdge OLED display, Intel Core Ultra 7, NVIDIA RTX 4060.',
                'variants' => [
                    ['name' => 'i7 / 16GB / 512GB', 'price' => 189999, 'stock' => 8, 'attributes' => ['Processor' => 'Core Ultra 7', 'RAM' => '16GB', 'Storage' => '512GB']],
                    ['name' => 'i9 / 32GB / 1TB', 'price' => 249999, 'stock' => 5, 'attributes' => ['Processor' => 'Core Ultra 9', 'RAM' => '32GB', 'Storage' => '1TB']],
                ],
            ],
            [
                'name' => 'Lenovo ThinkPad X1 Carbon Gen 12',
                'category' => 'Computers & Laptops',
                'brand' => 'Lenovo',
                'type' => 'simple',
                'price' => 179999,
                'stock' => 35,
                'weight' => 1.08,
                'short' => 'Ultra-light business laptop with 14" OLED display and Intel vPro.',
            ],
            [
                'name' => 'Asus ROG Zephyrus G16',
                'category' => 'Computers & Laptops',
                'brand' => 'Asus',
                'type' => 'variable',
                'price' => 209999,
                'stock' => 25,
                'weight' => 1.85,
                'featured' => true,
                'short' => 'NVIDIA RTX 4070, Intel Core Ultra 9, 16" ROG Nebula OLED.',
                'variants' => [
                    ['name' => 'RTX 4060 / 16GB', 'price' => 179999, 'stock' => 10, 'attributes' => ['GPU' => 'RTX 4060', 'RAM' => '16GB']],
                    ['name' => 'RTX 4070 / 32GB', 'price' => 209999, 'stock' => 8, 'attributes' => ['GPU' => 'RTX 4070', 'RAM' => '32GB']],
                    ['name' => 'RTX 4080 / 32GB', 'price' => 279999, 'stock' => 4, 'attributes' => ['GPU' => 'RTX 4080', 'RAM' => '32GB']],
                ],
            ],
            [
                'name' => 'HP Pavilion 15',
                'category' => 'Computers & Laptops',
                'brand' => 'HP',
                'type' => 'simple',
                'price' => 64999,
                'stock' => 40,
                'weight' => 1.74,
                'short' => 'Everyday laptop with AMD Ryzen 5, 8GB RAM, 512GB SSD.',
            ],

            // ═══════════════ AUDIO ═══════════════
            [
                'name' => 'Apple AirPods Pro 2 (USB-C)',
                'category' => 'Audio & Headphones',
                'brand' => 'Apple',
                'type' => 'simple',
                'price' => 27999,
                'stock' => 200,
                'weight' => 0.05,
                'featured' => true,
                'short' => 'Active Noise Cancellation, Adaptive Audio, USB-C, 6hr battery.',
            ],
            [
                'name' => 'Sony WH-1000XM5',
                'category' => 'Audio & Headphones',
                'brand' => 'Sony',
                'type' => 'variable',
                'price' => 37999,
                'stock' => 100,
                'weight' => 0.25,
                'featured' => true,
                'short' => 'Industry-leading noise cancellation with Auto NC Optimizer.',
                'variants' => [
                    ['name' => 'Black', 'price' => 37999, 'stock' => 40, 'attributes' => ['Color' => 'Black']],
                    ['name' => 'Silver', 'price' => 37999, 'stock' => 30, 'attributes' => ['Color' => 'Silver']],
                    ['name' => 'Midnight Blue', 'price' => 39999, 'stock' => 15, 'attributes' => ['Color' => 'Midnight Blue']],
                ],
            ],
            [
                'name' => 'Bose QuietComfort Ultra Headphones',
                'category' => 'Audio & Headphones',
                'brand' => 'Bose',
                'type' => 'simple',
                'price' => 39999,
                'stock' => 80,
                'weight' => 0.25,
                'short' => 'Immersive spatial audio, world-class noise cancellation.',
            ],
            [
                'name' => 'JBL Charge 5',
                'category' => 'Audio & Headphones',
                'brand' => 'JBL',
                'type' => 'variable',
                'price' => 16999,
                'stock' => 150,
                'weight' => 0.96,
                'short' => 'Portable Bluetooth speaker with IP67 waterproof and powerbank.',
                'variants' => [
                    ['name' => 'Black', 'price' => 16999, 'stock' => 50, 'attributes' => ['Color' => 'Black']],
                    ['name' => 'Blue', 'price' => 16999, 'stock' => 40, 'attributes' => ['Color' => 'Blue']],
                    ['name' => 'Red', 'price' => 16999, 'stock' => 30, 'attributes' => ['Color' => 'Red']],
                    ['name' => 'Teal', 'price' => 16999, 'stock' => 20, 'attributes' => ['Color' => 'Teal']],
                ],
            ],
            [
                'name' => 'Sony PlayStation 5 Slim',
                'category' => 'Electronics',
                'brand' => 'Sony',
                'type' => 'variable',
                'price' => 54999,
                'stock' => 60,
                'weight' => 3.2,
                'featured' => true,
                'short' => 'Slimmer PS5 with 1TB SSD, 4K gaming, and DualSense controller.',
                'variants' => [
                    ['name' => 'Digital Edition', 'price' => 47999, 'stock' => 25, 'attributes' => ['Edition' => 'Digital']],
                    ['name' => 'Disc Edition', 'price' => 54999, 'stock' => 25, 'attributes' => ['Edition' => 'Disc']],
                ],
            ],

            // ═══════════════ CLOTHING ═══════════════
            [
                'name' => 'Nike Air Max 270',
                'name_bn' => 'নাইকে এয়ার ম্যাক্স ২৭০',
                'category' => "Men's Clothing",
                'brand' => 'Nike',
                'sku' => 'NIKE-AM270',
                'type' => 'variable',
                'price' => 14999,
                'stock' => 200,
                'weight' => 0.34,
                'featured' => true,
                'short' => 'Max Air unit delivers unrivaled, all-day comfort.',
                'variants' => [
                    ['name' => 'Black/White - 40', 'price' => 14999, 'stock' => 20, 'attributes' => ['Color' => 'Black/White', 'Size' => '40']],
                    ['name' => 'Black/White - 42', 'price' => 14999, 'stock' => 25, 'attributes' => ['Color' => 'Black/White', 'Size' => '42']],
                    ['name' => 'Black/White - 44', 'price' => 14999, 'stock' => 15, 'attributes' => ['Color' => 'Black/White', 'Size' => '44']],
                    ['name' => 'White/Blue - 42', 'price' => 14999, 'stock' => 20, 'attributes' => ['Color' => 'White/Blue', 'Size' => '42']],
                    ['name' => 'Red/Black - 42', 'price' => 14999, 'stock' => 15, 'attributes' => ['Color' => 'Red/Black', 'Size' => '42']],
                ],
            ],
            [
                'name' => 'Nike Dri-FIT T-Shirt',
                'category' => "Men's Clothing",
                'brand' => 'Nike',
                'sku' => 'NIKE-DRIFIT',
                'type' => 'variable',
                'price' => 2999,
                'stock' => 500,
                'weight' => 0.15,
                'short' => 'Moisture-wicking fabric keeps you dry and comfortable.',
                'variants' => [
                    ['name' => 'Black / S', 'price' => 2999, 'stock' => 40, 'attributes' => ['Color' => 'Black', 'Size' => 'S']],
                    ['name' => 'Black / M', 'price' => 2999, 'stock' => 50, 'attributes' => ['Color' => 'Black', 'Size' => 'M']],
                    ['name' => 'Black / L', 'price' => 2999, 'stock' => 45, 'attributes' => ['Color' => 'Black', 'Size' => 'L']],
                    ['name' => 'Black / XL', 'price' => 2999, 'stock' => 30, 'attributes' => ['Color' => 'Black', 'Size' => 'XL']],
                    ['name' => 'White / M', 'price' => 2999, 'stock' => 40, 'attributes' => ['Color' => 'White', 'Size' => 'M']],
                    ['name' => 'Navy / L', 'price' => 2999, 'stock' => 35, 'attributes' => ['Color' => 'Navy', 'Size' => 'L']],
                ],
            ],
            [
                'name' => 'Nike Running Shorts',
                'category' => "Men's Clothing",
                'brand' => 'Nike',
                'type' => 'simple',
                'price' => 3999,
                'stock' => 350,
                'short' => 'Lightweight woven shorts with built-in liner.',
            ],
            [
                'name' => 'Adidas Ultraboost Light',
                'category' => "Men's Clothing",
                'brand' => 'Adidas',
                'sku' => 'ADI-UBL',
                'type' => 'variable',
                'price' => 17999,
                'stock' => 150,
                'weight' => 0.28,
                'featured' => true,
                'short' => 'The lightest Ultraboost ever with Light BOOST midsole.',
                'variants' => [
                    ['name' => 'Core Black / 42', 'price' => 17999, 'stock' => 25, 'attributes' => ['Color' => 'Core Black', 'Size' => '42']],
                    ['name' => 'Core Black / 44', 'price' => 17999, 'stock' => 20, 'attributes' => ['Color' => 'Core Black', 'Size' => '44']],
                    ['name' => 'White / 42', 'price' => 17999, 'stock' => 15, 'attributes' => ['Color' => 'White', 'Size' => '42']],
                ],
            ],
            [
                'name' => 'Adidas Track Pants',
                'category' => "Men's Clothing",
                'brand' => 'Adidas',
                'type' => 'variable',
                'price' => 5999,
                'stock' => 300,
                'weight' => 0.3,
                'short' => 'Classic 3-Stripes track pants with zip pockets.',
                'variants' => [
                    ['name' => 'Black / S', 'price' => 5999, 'stock' => 30, 'attributes' => ['Color' => 'Black', 'Size' => 'S']],
                    ['name' => 'Black / M', 'price' => 5999, 'stock' => 40, 'attributes' => ['Color' => 'Black', 'Size' => 'M']],
                    ['name' => 'Black / L', 'price' => 5999, 'stock' => 35, 'attributes' => ['Color' => 'Black', 'Size' => 'L']],
                    ['name' => 'Navy / M', 'price' => 5999, 'stock' => 25, 'attributes' => ['Color' => 'Navy', 'Size' => 'M']],
                ],
            ],

            // ═══════════════ HOME & KITCHEN ═══════════════
            [
                'name' => 'Philips Air Fryer XXL',
                'category' => 'Home & Kitchen',
                'brand' => 'Philips',
                'type' => 'simple',
                'price' => 22999,
                'stock' => 40,
                'weight' => 7.5,
                'short' => 'Rapid Air technology for fat-free frying with Starfish design.',
            ],
            [
                'name' => 'Walton Refrigerator 265L',
                'category' => 'Home & Kitchen',
                'brand' => 'Walton',
                'type' => 'simple',
                'price' => 32999,
                'stock' => 25,
                'weight' => 55,
                'short' => 'Frost-free double door refrigerator with inverter compressor.',
            ],
            [
                'name' => 'Walton Washing Machine 10kg',
                'category' => 'Home & Kitchen',
                'brand' => 'Walton',
                'type' => 'simple',
                'price' => 28999,
                'stock' => 30,
                'weight' => 40,
                'short' => 'Fully automatic top-load with magic filter and diamond drum.',
            ],

            // ═══════════════ BEAUTY & HEALTH ═══════════════
            [
                'name' => 'L\'Oreal Paris Revitalift Cream',
                'category' => 'Beauty & Health',
                'brand' => 'Sony',
                'type' => 'simple',
                'price' => 1999,
                'stock' => 200,
                'weight' => 0.05,
                'short' => 'Anti-aging face cream with Pro-Retinol and Hyaluronic Acid.',
            ],
            [
                'name' => 'Nivea Sun Protect SPF 50',
                'category' => 'Beauty & Health',
                'brand' => 'Sony',
                'type' => 'simple',
                'price' => 1499,
                'stock' => 180,
                'weight' => 0.08,
                'short' => 'UV protection for face and body. Water-resistant formula.',
            ],

            // ═══════════════ SPORTS & OUTDOOR ═══════════════
            [
                'name' => 'Nike Brasilia Backpack',
                'category' => 'Sports & Outdoor',
                'brand' => 'Nike',
                'type' => 'variable',
                'price' => 4999,
                'stock' => 120,
                'weight' => 0.5,
                'short' => 'Durable backpack with padded laptop sleeve and water bottle pocket.',
                'variants' => [
                    ['name' => 'Black - Small (20L)', 'price' => 4999, 'stock' => 30, 'attributes' => ['Color' => 'Black', 'Size' => '20L']],
                    ['name' => 'Black - Medium (26L)', 'price' => 5999, 'stock' => 25, 'attributes' => ['Color' => 'Black', 'Size' => '26L']],
                    ['name' => 'Navy - Medium (26L)', 'price' => 5999, 'stock' => 20, 'attributes' => ['Color' => 'Navy', 'Size' => '26L']],
                ],
            ],
            [
                'name' => 'Yoga Mat Premium 6mm',
                'category' => 'Sports & Outdoor',
                'brand' => 'Nike',
                'type' => 'variable',
                'price' => 2499,
                'stock' => 200,
                'weight' => 1.2,
                'short' => 'Non-slip TPE material, eco-friendly, with carrying strap.',
                'variants' => [
                    ['name' => 'Purple', 'price' => 2499, 'stock' => 40, 'attributes' => ['Color' => 'Purple']],
                    ['name' => 'Teal', 'price' => 2499, 'stock' => 35, 'attributes' => ['Color' => 'Teal']],
                    ['name' => 'Pink', 'price' => 2499, 'stock' => 30, 'attributes' => ['Color' => 'Pink']],
                ],
            ],

            // ═══════════════ ACCESSORIES ═══════════════
            [
                'name' => 'Apple Watch Series 9',
                'category' => 'Accessories',
                'brand' => 'Apple',
                'sku' => 'APL-AW9',
                'type' => 'variable',
                'price' => 44999,
                'stock' => 80,
                'weight' => 0.05,
                'featured' => true,
                'short' => 'S9 chip, Double Tap gesture, brighter display, carbon neutral.',
                'variants' => [
                    ['name' => '41mm GPS Midnight', 'price' => 44999, 'stock' => 20, 'attributes' => ['Size' => '41mm', 'Type' => 'GPS', 'Color' => 'Midnight']],
                    ['name' => '45mm GPS Midnight', 'price' => 49999, 'stock' => 15, 'attributes' => ['Size' => '45mm', 'Type' => 'GPS', 'Color' => 'Midnight']],
                    ['name' => '45mm GPS+Cellular Starlight', 'price' => 59999, 'stock' => 10, 'attributes' => ['Size' => '45mm', 'Type' => 'GPS+Cellular', 'Color' => 'Starlight']],
                ],
            ],
            [
                'name' => 'Samsung Galaxy Watch 6 Classic',
                'category' => 'Accessories',
                'brand' => 'Samsung',
                'type' => 'variable',
                'price' => 34999,
                'stock' => 60,
                'weight' => 0.06,
                'short' => 'Rotating bezel, sapphire crystal, advanced health monitoring.',
                'variants' => [
                    ['name' => '47mm Bluetooth Black', 'price' => 34999, 'stock' => 20, 'attributes' => ['Size' => '47mm', 'Connectivity' => 'Bluetooth', 'Color' => 'Black']],
                    ['name' => '47mm LTE Black', 'price' => 39999, 'stock' => 15, 'attributes' => ['Size' => '47mm', 'Connectivity' => 'LTE', 'Color' => 'Black']],
                ],
            ],

            // ═══════════════ MORE ELECTRONICS ═══════════════
            [
                'name' => 'LG OLED C3 55" 4K TV',
                'category' => 'Electronics',
                'brand' => 'LG',
                'type' => 'simple',
                'price' => 129999,
                'stock' => 20,
                'weight' => 18,
                'featured' => true,
                'short' => 'OLED evo panel, α9 Gen6 AI Processor, Dolby Vision & Atmos.',
            ],
            [
                'name' => 'Samsung 55" QLED 4K TV',
                'category' => 'Electronics',
                'brand' => 'Samsung',
                'type' => 'simple',
                'price' => 69999,
                'stock' => 25,
                'weight' => 15,
                'short' => 'Quantum Dot color, 100% Color Volume, Ambient Mode+.',
            ],
            [
                'name' => 'Sony Bravia XR 65" OLED',
                'category' => 'Electronics',
                'brand' => 'Sony',
                'type' => 'simple',
                'price' => 199999,
                'stock' => 15,
                'weight' => 22,
                'short' => 'Cognitive Processor XR, Acoustic Surface Audio+, BRAVIA XR.',
            ],

            // ═══════════════ WATCHES ═══════════════
            [
                'name' => 'Casio G-Shock GA-2100',
                'category' => 'Accessories',
                'brand' => 'Sony',
                'type' => 'variable',
                'price' => 12999,
                'stock' => 100,
                'weight' => 0.05,
                'short' => 'Carbon Core Guard structure, 200m water resistance.',
                'variants' => [
                    ['name' => 'All Black', 'price' => 12999, 'stock' => 30, 'attributes' => ['Color' => 'All Black']],
                    ['name' => 'Navy Blue', 'price' => 12999, 'stock' => 25, 'attributes' => ['Color' => 'Navy Blue']],
                    ['name' => 'Green', 'price' => 12999, 'stock' => 20, 'attributes' => ['Color' => 'Green']],
                ],
            ],
        ];
    }
}
