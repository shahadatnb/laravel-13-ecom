<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $pageSettings = [
            [
                'key' => 'about_us',
                'value' => '<h2>Welcome to E-Commerce</h2>
<p>We are a leading online retailer committed to providing you with the best shopping experience. Our mission is to offer quality products at competitive prices with exceptional customer service.</p>
<p>We offer a wide range of products including electronics, fashion, home & living, and much more.</p>
<h3>Why Choose Us?</h3>
<ul>
<li>Wide range of products</li>
<li>Competitive prices</li>
<li>Fast and reliable delivery</li>
<li>Secure payment options</li>
<li>24/7 customer support</li>
<li>Easy returns and exchanges</li>
</ul>',
                'group' => 'pages',
                'label' => 'About Us',
                'type' => 'richtext',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'faq',
                'value' => '<div class="faq-item"><h3>How do I place an order?</h3><p>Simply browse our products, add items to your cart, and proceed to checkout. You can create an account or checkout as a guest.</p></div>
<div class="faq-item"><h3>What payment methods do you accept?</h3><p>We accept credit/debit cards, mobile banking, and cash on delivery.</p></div>
<div class="faq-item"><h3>How long does delivery take?</h3><p>Standard delivery takes 3-5 business days. Express delivery is available within 24-48 hours.</p></div>
<div class="faq-item"><h3>Can I cancel my order?</h3><p>Yes, you can cancel your order within 24 hours of placing it. After that, please contact our support team.</p></div>
<div class="faq-item"><h3>Do you ship internationally?</h3><p>Currently we only ship within Bangladesh. International shipping will be available soon.</p></div>',
                'group' => 'pages',
                'label' => 'FAQ',
                'type' => 'richtext',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'shipping_info',
                'value' => '<h2>Shipping Information</h2>
<h3>Delivery Options</h3>
<p>We offer the following delivery options:</p>
<ul>
<li><strong>Standard Delivery:</strong> 3-5 business days — $5.00</li>
<li><strong>Express Delivery:</strong> 24-48 hours — $12.00</li>
<li><strong>Free Shipping:</strong> On all orders over $50.00</li>
</ul>
<h3>Delivery Areas</h3>
<p>We deliver to all districts in Bangladesh. Our delivery partners cover both urban and rural areas.</p>
<h3>Tracking</h3>
<p>Once your order is shipped, you will receive a tracking number via email and SMS. You can track your order from your account dashboard.</p>
<h3>Delivery Hours</h3>
<p>Deliveries are made from 9:00 AM to 8:00 PM, Sunday to Thursday. Friday and Saturday deliveries are available in select areas.</p>',
                'group' => 'pages',
                'label' => 'Shipping Info',
                'type' => 'richtext',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'returns',
                'value' => '<h2>Returns & Exchanges Policy</h2>
<h3>30-Day Return Policy</h3>
<p>We offer a 30-day return policy from the date of delivery. If you are not satisfied with your purchase, you can return it for a full refund or exchange.</p>
<h3>Conditions</h3>
<ul>
<li>Product must be unused and in original packaging</li>
<li>All accessories and tags must be included</li>
<li>Proof of purchase required</li>
</ul>
<h3>How to Return</h3>
<ol>
<li>Log in to your account and go to Orders</li>
<li>Select the order and click "Return"</li>
<li>Choose the reason for return</li>
<li>Print the return label</li>
<li>Pack the item securely and drop it off at your nearest delivery point</li>
</ol>
<h3>Refund Processing</h3>
<p>Refunds are processed within 5-7 business days after we receive the returned item. The amount will be credited to your original payment method.</p>',
                'group' => 'pages',
                'label' => 'Returns Policy',
                'type' => 'richtext',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('site_settings')->insert($pageSettings);
    }

    public function down(): void
    {
        DB::table('site_settings')->whereIn('key', ['about_us', 'faq', 'shipping_info', 'returns'])->delete();
    }
};
