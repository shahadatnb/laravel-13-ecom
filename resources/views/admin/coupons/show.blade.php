@extends('admin.layouts.app')
@section('title', 'Coupon: ' . $coupon->code)

@section('content')
<div class="row">
    <div class="col-lg-8">

        {{-- Coupon Information --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle mr-1"></i> Coupon Information</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-default btn-sm ml-1">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped mb-0">
                    <tbody>
                        <tr>
                            <th style="width:200px">Code</th>
                            <td><span class="font-weight-bold text-primary h4">{{ $coupon->code }}</span></td>
                        </tr>
                        <tr>
                            <th>Title</th>
                            <td>{{ $coupon->title }}</td>
                        </tr>
                        <tr>
                            <th>Type</th>
                            <td><span class="badge badge-info">{{ $coupon->getTypeLabel() }}</span></td>
                        </tr>
                        <tr>
                            <th>Discount Type</th>
                            <td><span class="text-capitalize">{{ str_replace('_', ' ', $coupon->discount_type) }}</span></td>
                        </tr>
                        <tr>
                            <th>Discount Value</th>
                            <td>
                                @if($coupon->isPercentage())
                                    <span class="font-weight-bold text-success h5">{{ number_format($coupon->discount_value, 0) }}%</span>
                                @elseif($coupon->isFreeShipping())
                                    <span class="font-weight-bold text-info">Free Shipping</span>
                                @else
                                    <span class="font-weight-bold text-success h5">{{ format_currency($coupon->discount_value, 2) }}</span>
                                @endif
                                @if($coupon->max_discount)
                                    <br><small class="text-muted">Max discount: {{ format_currency($coupon->max_discount, 2) }}</small>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @php
                                    $badgeMap = [
                                        'active' => 'success',
                                        'draft' => 'secondary',
                                        'inactive' => 'warning',
                                        'expired' => 'danger',
                                        'cancelled' => 'danger',
                                    ];
                                    $badge = $badgeMap[$coupon->status] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $badge }} badge-lg">{{ $coupon->getStatusLabel() }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Scope</th>
                            <td><span class="text-capitalize">{{ $coupon->scope }}</span></td>
                        </tr>
                        <tr>
                            <th>Priority</th>
                            <td><span class="badge badge-dark">{{ $coupon->priority }}</span></td>
                        </tr>
                        @if($coupon->description)
                        <tr>
                            <th>Description</th>
                            <td>{{ $coupon->description }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Validity & Usage --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calendar-alt mr-1"></i> Validity & Usage</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center p-3 border rounded bg-light">
                            <div class="text-muted text-sm">Valid From</div>
                            <div class="font-weight-bold">{{ $coupon->valid_from ? $coupon->valid_from->format('d-m-Y H:i') : '—' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 border rounded bg-light">
                            <div class="text-muted text-sm">Valid Until</div>
                            <div class="font-weight-bold">{{ $coupon->valid_until ? $coupon->valid_until->format('d-m-Y H:i') : '—' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 border rounded bg-light">
                            <div class="text-muted text-sm">Last Used</div>
                            <div class="font-weight-bold">{{ $coupon->last_used_at ? $coupon->last_used_at->diffForHumans() : 'Never' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4 mt-3">
                        <div class="text-center p-3 border rounded bg-light">
                            <div class="text-muted text-sm">Min Order Amount</div>
                            <div class="font-weight-bold">{{ $coupon->min_order_amount ? format_currency($coupon->min_order_amount, 2) : '—' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4 mt-3">
                        <div class="text-center p-3 border rounded bg-light">
                            <div class="text-muted text-sm">Usage Limit</div>
                            <div class="font-weight-bold">{{ $coupon->usage_limit ?? 'Unlimited' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4 mt-3">
                        <div class="text-center p-3 border rounded bg-light">
                            <div class="text-muted text-sm">Per User Limit</div>
                            <div class="font-weight-bold">{{ $coupon->per_user_limit }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Usage History --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history mr-1"></i> Usage History ({{ $coupon->usages->count() }})</h3>
            </div>
            <div class="card-body p-0">
                @if($coupon->usages->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Order</th>
                                <th class="text-right">Discount</th>
                                <th class="text-right">Order Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($coupon->usages->sortByDesc('created_at') as $usage)
                            <tr>
                                <td>{{ $usage->user?->name ?? 'Guest' }}</td>
                                <td><span class="font-mono">{{ $usage->order?->order_number ?? '—' }}</span></td>
                                <td class="text-right text-success font-weight-bold">-{{ number_format($usage->discount_amount, 2) }}</td>
                                <td class="text-right">{{ number_format($usage->order_amount, 2) }}</td>
                                <td>{{ $usage->created_at->format('d-m-Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-5">
                    <i class="fas fa-receipt fa-3x mb-3 d-block"></i>
                    No usage history yet.
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        {{-- Products --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-box mr-1"></i> Products</h3>
            </div>
            <div class="card-body p-0">
                @if($coupon->products->count() > 0)
                <ul class="list-group list-group-flush">
                    @foreach($coupon->products as $product)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $product->name }}
                        @if($product->pivot->is_excluded)
                            <span class="badge badge-danger">Excluded</span>
                        @endif
                    </li>
                    @endforeach
                </ul>
                @else
                <div class="text-center text-muted py-4">
                    <i class="fas fa-box-open fa-2x mb-2 d-block"></i>
                    <small>All products</small>
                </div>
                @endif
            </div>
        </div>

        {{-- Categories --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tags mr-1"></i> Categories</h3>
            </div>
            <div class="card-body p-0">
                @if($coupon->categories->count() > 0)
                <ul class="list-group list-group-flush">
                    @foreach($coupon->categories as $category)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $category->name }}
                        @if($category->pivot->is_excluded)
                            <span class="badge badge-danger">Excluded</span>
                        @endif
                    </li>
                    @endforeach
                </ul>
                @else
                <div class="text-center text-muted py-4">
                    <i class="fas fa-tag fa-2x mb-2 d-block"></i>
                    <small>All categories</small>
                </div>
                @endif
            </div>
        </div>

        {{-- Settings --}}
        @if($coupon->settings)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-cog mr-1"></i> Settings</h3>
            </div>
            <div class="card-body p-0">
                <pre class="p-3 mb-0 bg-light" style="font-size:12px; overflow-x:auto;">{{ json_encode($coupon->settings, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
        @endif

        {{-- Metadata --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-clock mr-1"></i> Metadata</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-striped mb-0">
                    <tbody>
                        <tr>
                            <th style="width:120px">Created</th>
                            <td>{{ $coupon->created_at->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <th>By</th>
                            <td>{{ $coupon->createdBy?->name ?? 'System' }}</td>
                        </tr>
                        <tr>
                            <th>Auto Apply</th>
                            <td>
                                <span class="badge badge-{{ $coupon->is_auto_apply ? 'success' : 'secondary' }}">
                                    {{ $coupon->is_auto_apply ? 'Yes' : 'No' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>First Order Only</th>
                            <td>
                                <span class="badge badge-{{ $coupon->is_first_order_only ? 'success' : 'secondary' }}">
                                    {{ $coupon->is_first_order_only ? 'Yes' : 'No' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Guest Allowed</th>
                            <td>
                                <span class="badge badge-{{ $coupon->is_guest_allowed ? 'success' : 'secondary' }}">
                                    {{ $coupon->is_guest_allowed ? 'Yes' : 'No' }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
