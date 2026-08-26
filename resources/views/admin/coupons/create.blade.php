@extends('admin.layouts.app')
@section('title', 'Create Coupon')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/jquery-ui/jquery-ui.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.css') }}">
<style>
    .toggle-label { cursor: pointer; user-select: none; }
    .toggle-label:hover { background: #f4f6f9; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-plus-circle mr-1"></i> New Coupon</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
            </div>
            <form action="{{ route('admin.coupons.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        {{-- Left Column --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Coupon Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" value="{{ old('code') }}" maxlength="50"
                                    class="form-control @error('code') is-invalid @enderror"
                                    placeholder="SUMMER20">
                                @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="form-text text-muted">Leave empty for auto-generated.</small>
                            </div>

                            <div class="form-group">
                                <label>Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" value="{{ old('title') }}" maxlength="255"
                                    class="form-control @error('title') is-invalid @enderror"
                                    placeholder="Summer Sale 20% Off">
                                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Coupon Type <span class="text-danger">*</span></label>
                                        <select name="type" class="form-control @error('type') is-invalid @enderror">
                                            @foreach($types as $type)
                                                <option value="{{ $type }}" {{ old('type') === $type ? 'selected' : '' }}>
                                                    {{ ucfirst(str_replace('_', ' ', $type)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Discount Type <span class="text-danger">*</span></label>
                                        <select name="discount_type" class="form-control @error('discount_type') is-invalid @enderror">
                                            <option value="cart" {{ old('discount_type') === 'cart' ? 'selected' : '' }}>Cart Discount</option>
                                            <option value="product" {{ old('discount_type') === 'product' ? 'selected' : '' }}>Product Discount</option>
                                            <option value="category" {{ old('discount_type') === 'category' ? 'selected' : '' }}>Category Discount</option>
                                            <option value="shipping" {{ old('discount_type') === 'shipping' ? 'selected' : '' }}>Shipping Discount</option>
                                            <option value="order" {{ old('discount_type') === 'order' ? 'selected' : '' }}>Order Discount</option>
                                        </select>
                                        @error('discount_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Discount Value <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" name="discount_value" value="{{ old('discount_value', 0) }}"
                                                step="0.01" min="0" class="form-control @error('discount_value') is-invalid @enderror"
                                                placeholder="20">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="discount-symbol">%</span>
                                            </div>
                                        </div>
                                        @error('discount_value') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        <small class="form-text text-muted">Percentage value or fixed amount.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Max Discount</label>
                                        <input type="number" name="max_discount" value="{{ old('max_discount') }}"
                                            step="0.01" min="0" class="form-control" placeholder="50.00">
                                        <small class="form-text text-muted">Only for percentage type.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" rows="3" class="form-control" placeholder="Coupon description...">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        {{-- Right Column --}}
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-control @error('status') is-invalid @enderror">
                                            @foreach($statuses as $status)
                                                <option value="{{ $status }}" {{ old('status', 'draft') === $status ? 'selected' : '' }}>
                                                    {{ ucfirst($status) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Scope</label>
                                        <select name="scope" class="form-control">
                                            <option value="all" {{ old('scope') === 'all' ? 'selected' : '' }}>All Products</option>
                                            <option value="products" {{ old('scope') === 'products' ? 'selected' : '' }}>Selected Products</option>
                                            <option value="categories" {{ old('scope') === 'categories' ? 'selected' : '' }}>Selected Categories</option>
                                            <option value="customers" {{ old('scope') === 'customers' ? 'selected' : '' }}>Selected Customers</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Valid From</label>
                                        <input type="text" name="valid_from" value="{{ old('valid_from') }}"
                                            class="form-control datepicker" placeholder="dd-mm-yyyy" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Valid Until</label>
                                        <input type="text" name="valid_until" value="{{ old('valid_until') }}"
                                            class="form-control datepicker" placeholder="dd-mm-yyyy" autocomplete="off">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Min Order Amount</label>
                                        <input type="number" name="min_order_amount" value="{{ old('min_order_amount') }}"
                                            step="0.01" min="0" class="form-control" placeholder="100.00">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Max Order Amount</label>
                                        <input type="number" name="max_order_amount" value="{{ old('max_order_amount') }}"
                                            step="0.01" min="0" class="form-control" placeholder="500.00">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Usage Limit</label>
                                        <input type="number" name="usage_limit" value="{{ old('usage_limit') }}"
                                            min="1" class="form-control" placeholder="100">
                                        <small class="form-text text-muted">Leave empty for unlimited.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Per User Limit</label>
                                        <input type="number" name="per_user_limit" value="{{ old('per_user_limit', 1) }}"
                                            min="1" class="form-control" placeholder="1">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Priority</label>
                                        <input type="number" name="priority" value="{{ old('priority', 0) }}"
                                            min="-999" max="999" class="form-control" placeholder="0">
                                        <small class="form-text text-muted">Higher = applied first.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Customer Restriction</label>
                                        <select name="customer_restriction" class="form-control">
                                            <option value="">None</option>
                                            <option value="vip" {{ old('customer_restriction') === 'vip' ? 'selected' : '' }}>VIP Customers</option>
                                            <option value="wholesale" {{ old('customer_restriction') === 'wholesale' ? 'selected' : '' }}>Wholesale</option>
                                            <option value="new" {{ old('customer_restriction') === 'new' ? 'selected' : '' }}>New Customers</option>
                                            <option value="returning" {{ old('customer_restriction') === 'returning' ? 'selected' : '' }}>Returning Customers</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Payment Method</label>
                                <input type="text" name="payment_method" value="{{ old('payment_method') }}"
                                    class="form-control" placeholder="stripe, paypal, etc.">
                            </div>
                        </div>
                    </div>

                    {{-- Toggle Switches --}}
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <div class="custom-control custom-checkbox toggle-label p-3 border rounded">
                                    <input type="checkbox" name="is_auto_apply" value="1" class="custom-control-input"
                                        id="is_auto_apply" {{ old('is_auto_apply') ? 'checked' : '' }}>
                                    <label class="custom-control-label font-weight-normal" for="is_auto_apply">
                                        Auto Apply
                                        <br><small class="text-muted">Automatically apply if order qualifies</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <div class="custom-control custom-checkbox toggle-label p-3 border rounded">
                                    <input type="checkbox" name="is_first_order_only" value="1" class="custom-control-input"
                                        id="is_first_order_only" {{ old('is_first_order_only') ? 'checked' : '' }}>
                                    <label class="custom-control-label font-weight-normal" for="is_first_order_only">
                                        First Order Only
                                        <br><small class="text-muted">Only for customer's first order</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <div class="custom-control custom-checkbox toggle-label p-3 border rounded">
                                    <input type="checkbox" name="is_guest_allowed" value="1" class="custom-control-input"
                                        id="is_guest_allowed" {{ old('is_guest_allowed') ? 'checked' : '' }}>
                                    <label class="custom-control-label font-weight-normal" for="is_guest_allowed">
                                        Guest Allowed
                                        <br><small class="text-muted">Allow guest checkout usage</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-right">
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-default">Cancel</a>
                    <button type="submit" class="btn btn-primary ml-2">
                        <i class="fas fa-save mr-1"></i> Create Coupon
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/admin/plugins/jquery-ui/jquery-ui.js') }}"></script>
<script>
$(function () {
    $('.datepicker').datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '-5:+5',
    });

    function updateDiscountSymbol() {
        const type = $('select[name="type"]').val();
        const isPercentage = type === 'percentage' || type === 'percent';
        $('#discount-symbol').text(isPercentage ? '%' : '{{ currency_symbol() }}');
    }
    $('select[name="type"]').on('change', updateDiscountSymbol);
    updateDiscountSymbol();
});
</script>
@endpush