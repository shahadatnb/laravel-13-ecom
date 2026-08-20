<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            ['title' => 'About Us', 'slug' => 'about-us', 'sort_order' => 1, 'meta_title' => 'About Us - MyVoucher', 'meta_description' => 'Learn more about MyVoucher.', 'content' => $this->aboutUs()],
            ['title' => 'FAQ', 'slug' => 'faq', 'sort_order' => 2, 'meta_title' => 'FAQ - MyVoucher', 'meta_description' => 'Frequently asked questions.', 'content' => $this->faq()],
            ['title' => 'Shipping Policy', 'slug' => 'shipping-policy', 'sort_order' => 3, 'meta_title' => 'Shipping Policy - MyVoucher', 'meta_description' => 'Shipping methods and delivery times.', 'content' => $this->shipping()],
            ['title' => 'Return Policy', 'slug' => 'return-policy', 'sort_order' => 4, 'meta_title' => 'Return Policy - MyVoucher', 'meta_description' => 'Hassle-free return policy.', 'content' => $this->returns()],
            ['title' => 'Privacy Policy', 'slug' => 'privacy-policy', 'sort_order' => 5, 'meta_title' => 'Privacy Policy - MyVoucher', 'meta_description' => 'How we protect your data.', 'content' => $this->privacy()],
            ['title' => 'Terms & Conditions', 'slug' => 'terms', 'sort_order' => 6, 'meta_title' => 'Terms & Conditions - MyVoucher', 'meta_description' => 'Terms of using MyVoucher.', 'content' => $this->terms()],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(['slug' => $page['slug']], array_merge($page, ['status' => 'published']));
        }

        $this->command->info('✅ Static pages seeded successfully (' . count($pages) . ' pages).');
    }

    private function aboutUs(): string
    {
        return '<h2>Welcome to MyVoucher</h2>
<p>MyVoucher is Bangladesh\'s premier online shopping destination, offering a curated selection of quality products at competitive prices. Founded with the mission to make online shopping accessible, reliable, and enjoyable for everyone.</p>

<h2>Our Mission</h2>
<p>We believe that everyone deserves access to quality products without breaking the bank. Our team works tirelessly to source the best products from trusted brands and deliver them right to your doorstep.</p>

<h2>Why Choose Us?</h2>
<ul>
<li><strong>Quality Guaranteed</strong> — Every product is carefully vetted before listing.</li>
<li><strong>Best Prices</strong> — We offer competitive pricing with regular promotions.</li>
<li><strong>Fast Delivery</strong> — Reliable delivery across all 8 divisions of Bangladesh.</li>
<li><strong>Secure Payments</strong> — Multiple payment options with SSL encryption.</li>
<li><strong>Customer Support</strong> — Our dedicated team is available 7 days a week.</li>
</ul>

<h2>Our Values</h2>
<p><strong>Integrity:</strong> We are honest and transparent in all our dealings.</p>
<p><strong>Quality:</strong> We never compromise on product quality.</p>
<p><strong>Customer First:</strong> Your satisfaction is our top priority.</p>
<p><strong>Innovation:</strong> We constantly improve to serve you better.</p>

<h2>Contact Us</h2>
<p>Have questions? We\'d love to hear from you.</p>
<p><strong>Phone:</strong> +880 1700-123456</p>
<p><strong>Email:</strong> support@myvoucher.com</p>
<p><strong>Address:</strong> Gulshan-2, Dhaka 1212, Bangladesh</p>';
    }

    private function faq(): string
    {
        return '<h2>General Questions</h2>

<h3>What is MyVoucher?</h3>
<p>MyVoucher is an online shopping platform based in Bangladesh. We offer a wide range of products including electronics, fashion, home goods, and more — all at competitive prices with reliable delivery.</p>

<h3>Do I need an account to shop?</h3>
<p>No, you can browse products and place orders as a guest. However, creating an account gives you access to order tracking, wishlist, wallet, and exclusive member offers.</p>

<h3>How do I create an account?</h3>
<p>Click the "Join Now" button at the top of the page and fill in your details. You can also register during checkout.</p>

<h2>Orders &amp; Payment</h2>

<h3>What payment methods do you accept?</h3>
<p>We accept Cash on Delivery (COD), bKash, Nagad, Bank Transfer, and Credit/Debit Cards.</p>

<h3>Can I modify or cancel my order?</h3>
<p>You can cancel your order before it is shipped. Once shipped, cancellation is not possible, but you can refuse delivery. To modify or cancel, contact our support team immediately.</p>

<h3>How do I apply a coupon code?</h3>
<p>During checkout, you will see a "Coupon Code" field. Enter your code and click "Apply" to receive the discount.</p>

<h2>Delivery</h2>

<h3>How long does delivery take?</h3>
<ul>
<li><strong>Dhaka:</strong> 1-2 business days</li>
<li><strong>Outside Dhaka:</strong> 2-5 business days</li>
</ul>

<h3>Is there free shipping?</h3>
<p>Yes! Orders above ৳5,000 qualify for free standard delivery within Bangladesh.</p>

<h3>How can I track my order?</h3>
<p>Go to <strong>Track Order</strong> and enter your email address and order number to see real-time status updates.</p>

<h2>Returns &amp; Refunds</h2>

<h3>What is your return policy?</h3>
<p>We offer a 7-day return policy for most items. The product must be unused and in its original packaging.</p>

<h3>How long does a refund take?</h3>
<p>Refunds are processed within 5-7 business days after we receive and inspect the returned item.</p>';
    }

    private function shipping(): string
    {
        return '<h2>Shipping Overview</h2>
<p>We deliver to all 8 divisions and 64 districts across Bangladesh. Shipping costs and delivery times vary based on your location and order size.</p>

<h2>Delivery Areas &amp; Timeframes</h2>
<ul>
<li><strong>Dhaka City:</strong> 1-2 business days</li>
<li><strong>Gazipur, Narayanganj, Savar (Greater Dhaka):</strong> 1-3 business days</li>
<li><strong>Chattogram Division:</strong> 2-4 business days</li>
<li><strong>Rajshahi, Khulna, Sylhet Divisions:</strong> 3-5 business days</li>
<li><strong>Barishal, Rangpur, Mymensingh Divisions:</strong> 3-5 business days</li>
</ul>

<h2>Shipping Charges</h2>
<ul>
<li><strong>Inside Dhaka:</strong> ৳60 per kg</li>
<li><strong>Outside Dhaka:</strong> ৳100 per kg</li>
<li><strong>Free Shipping:</strong> Orders above ৳5,000</li>
</ul>

<h2>Order Tracking</h2>
<p>Once your order is shipped, you will receive an SMS with your tracking information. You can also track your order from the <strong>Track Order</strong> page.</p>

<h2>Cash on Delivery (COD)</h2>
<p>Cash on Delivery is available for all locations. Please keep the exact amount ready at the time of delivery.</p>

<h2>Delivery Partners</h2>
<p>We work with trusted courier partners including Pathao, Paperfly, and SA Paribahan to ensure your orders arrive safely and on time.</p>

<h2>Failed Delivery</h2>
<p>If delivery fails due to incorrect address or unavailability, our team will contact you to reschedule. After 3 failed attempts, the order may be cancelled and a refund initiated.</p>';
    }

    private function returns(): string
    {
        return '<h2>Return Policy</h2>
<p>We want you to be completely satisfied with your purchase. If you\'re not happy, we make returns easy.</p>

<h2>Eligibility</h2>
<p>You can return items within <strong>7 days</strong> of delivery if:</p>
<ul>
<li>The item is unused and in original condition</li>
<li>The original packaging is intact</li>
<li>You have the receipt or order confirmation</li>
</ul>

<h2>Non-Returnable Items</h2>
<ul>
<li>Perishable goods (food, flowers)</li>
<li>Personal care and hygiene products</li>
<li>Undergarments and swimwear</li>
<li>Customized or personalized items</li>
<li>Digital products and gift cards</li>
</ul>

<h2>How to Return</h2>
<ol>
<li>Contact our support team at <strong>support@myvoucher.com</strong> or call <strong>+880 1700-123456</strong></li>
<li>Provide your order number and reason for return</li>
<li>Our rider will pick up the item (Dhaka) or you can send it via courier (outside Dhaka)</li>
<li>Once we receive and inspect the item, your refund will be processed</li>
</ol>

<h2>Refunds</h2>
<ul>
<li><strong>Wallet Credit:</strong> Within 24 hours of return approval</li>
<li><strong>bKash / Nagad:</strong> Within 3-5 business days</li>
<li><strong>Bank Transfer:</strong> Within 5-7 business days</li>
<li><strong>Cash (COD orders):</strong> Refunded via bKash or bank transfer</li>
</ul>

<h2>Exchanges</h2>
<p>We offer exchanges for different sizes or colors of the same product, subject to availability. Contact us to arrange an exchange.</p>

<h2>Damaged or Wrong Items</h2>
<p>If you receive a damaged or wrong item, please contact us within <strong>48 hours</strong> of delivery with photos. We will arrange an immediate replacement or full refund at no extra cost.</p>';
    }

    private function privacy(): string
    {
        return '<h2>Privacy Policy</h2>
<p><em>Last updated: August 2026</em></p>
<p>At MyVoucher, we take your privacy seriously. This policy explains how we collect, use, and protect your personal information.</p>

<h2>Information We Collect</h2>
<h3>Personal Information</h3>
<ul>
<li>Name, email address, phone number</li>
<li>Shipping and billing addresses</li>
<li>Payment information (processed securely, never stored in full)</li>
</ul>

<h3>Automated Information</h3>
<ul>
<li>Device type, browser, and IP address</li>
<li>Pages visited and time spent on our site</li>
<li>Referring website or search engine</li>
</ul>

<h2>How We Use Your Information</h2>
<ul>
<li>Process and fulfill your orders</li>
<li>Send order updates and delivery notifications</li>
<li>Improve our products and services</li>
<li>Send promotional offers (with your consent)</li>
<li>Prevent fraud and ensure platform security</li>
</ul>

<h2>Information Sharing</h2>
<p>We do not sell your personal information. We share data only with:</p>
<ul>
<li><strong>Courier partners</strong> — to deliver your orders</li>
<li><strong>Payment processors</strong> — to handle transactions securely</li>
<li><strong>Legal authorities</strong> — when required by law</li>
</ul>

<h2>Data Security</h2>
<p>We use industry-standard SSL encryption, firewalls, and access controls to protect your data. All payment transactions are processed through PCI-DSS compliant systems.</p>

<h2>Your Rights</h2>
<ul>
<li>Access your personal data</li>
<li>Correct inaccurate information</li>
<li>Request deletion of your account</li>
<li>Opt out of marketing communications</li>
</ul>

<h2>Contact</h2>
<p>For privacy-related questions, contact us at <strong>privacy@myvoucher.com</strong>.</p>';
    }

    private function terms(): string
    {
        return '<h2>Terms &amp; Conditions</h2>
<p><em>Last updated: August 2026</em></p>
<p>By using MyVoucher, you agree to these terms and conditions. Please read them carefully.</p>

<h2>General Terms</h2>
<ul>
<li>You must be at least 18 years old to create an account</li>
<li>You are responsible for maintaining the confidentiality of your account</li>
<li>One account per person; duplicate accounts may be suspended</li>
<li>Providing false information may result in account termination</li>
</ul>

<h2>Products &amp; Pricing</h2>
<ul>
<li>All prices are in Bangladeshi Taka (BDT) and include applicable taxes unless stated otherwise</li>
<li>Product images are for illustration; actual products may vary slightly</li>
<li>We reserve the right to change prices without prior notice</li>
<li>In case of pricing errors, we will contact you before processing the order</li>
</ul>

<h2>Orders</h2>
<ul>
<li>An order is confirmed only after successful payment or COD acceptance</li>
<li>We reserve the right to cancel orders due to stock unavailability, payment issues, or suspected fraud</li>
<li>Order quantities may be limited per customer for promotional items</li>
</ul>

<h2>Payments</h2>
<ul>
<li>Cash on Delivery is available for all locations in Bangladesh</li>
<li>Online payments are processed through secure, encrypted channels</li>
<li>Failed payments may result in order cancellation</li>
</ul>

<h2>Intellectual Property</h2>
<p>All content on MyVoucher including logos, images, text, and design is our intellectual property and may not be reproduced without permission.</p>

<h2>Limitation of Liability</h2>
<p>MyVoucher is not liable for indirect damages, delays caused by third-party couriers, or issues arising from incorrect user-provided information.</p>

<h2>Changes to Terms</h2>
<p>We may update these terms periodically. Continued use of the platform after changes constitutes acceptance of the updated terms.</p>

<h2>Governing Law</h2>
<p>These terms are governed by the laws of Bangladesh. Any disputes shall be resolved in the courts of Dhaka.</p>';
    }
}
