<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $couponId = $this->route('coupon')?->id;

        return [
            'code' => 'required|string|max:50|unique:coupons,code,'.($couponId ?: 'NULL'),
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|string|in:percentage,fixed,free_shipping,buy_x_get_y,cashback,gift,referral',
            'discount_type' => 'required|string|in:product,category,cart,shipping,order',
            'discount_value' => 'required|numeric|min:0|max:999999.99',
            'max_discount' => 'nullable|numeric|min:0|max:999999.99',
            'min_order_amount' => 'nullable|numeric|min:0|max:999999.99',
            'max_order_amount' => 'nullable|numeric|min:0|max:999999.99',
            'usage_limit' => 'nullable|integer|min:1',
            'per_user_limit' => 'nullable|integer|min:1|max:999',
            'status' => 'required|string|in:draft,active,inactive,expired,cancelled',
            'priority' => 'nullable|integer|min:-999|max:999',
            'scope' => 'required|string|in:all,products,categories,brands,customers',
            'is_auto_apply' => 'boolean',
            'is_first_order_only' => 'boolean',
            'is_guest_allowed' => 'boolean',
            'customer_restriction' => 'nullable|string|in:guest,registered,vip,wholesale,new,returning',
            'payment_method' => 'nullable|string|max:50',
            'shipping_method' => 'nullable|string|max:50',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'settings' => 'nullable|array',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'excluded_product_ids' => 'nullable|array',
            'excluded_product_ids.*' => 'exists:products,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'excluded_category_ids' => 'nullable|array',
            'excluded_category_ids.*' => 'exists:categories,id',
            'customer_ids' => 'nullable|array',
            'customer_ids.*' => 'exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'code.unique' => 'This coupon code is already in use.',
            'valid_until.after_or_equal' => 'The expiry date must be after or equal to the start date.',
        ];
    }
}
