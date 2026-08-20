@extends('admin.layouts.app')

@section('title', 'Order #' . $order->order_number)

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Order #{{ $order->order_number }}</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Orders
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                        @endif

                        <div class="row">
                            <!-- Order Info -->
                            <div class="col-md-6">
                                <div class="card card-outline card-info">
                                    <div class="card-header"><h5 class="m-0">Order Information</h5></div>
                                    <div class="card-body p-0">
                                        <table class="table table-bordered mb-0">
                                            <tr><th width="35%">Order Number</th><td><span class="badge bg-info">{{ $order->order_number }}</span></td></tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>
                                                    <span class="badge {{ match($order->status) { 'pending' => 'bg-warning', 'processing' => 'bg-primary', 'completed' => 'bg-success', 'cancelled' => 'bg-danger', default => 'bg-secondary' } }}">
                                                        {{ $order->getStatusLabel() }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr><th>Payment Status</th><td><span class="badge {{ $order->payment_status === 'paid' ? 'bg-success' : 'bg-warning' }}">{{ $order->getPaymentStatusLabel() }}</span></td></tr>
                                            <tr><th>Payment Method</th><td>{{ $order->payment_method ?? '-' }}</td></tr>
                                            <tr><th>Shipping Status</th><td>{{ ucfirst(str_replace('_', ' ', $order->shipping_status)) }}</td></tr>
                                            <tr><th>Subtotal</th><td>{{ number_format($order->subtotal, 2) }}</td></tr>
                                            <tr><th>Discount</th><td>{{ number_format($order->discount, 2) }}</td></tr>
                                            <tr><th>Tax</th><td>{{ number_format($order->tax, 2) }}</td></tr>
                                            <tr><th>Shipping</th><td>{{ number_format($order->shipping_charge, 2) }}</td></tr>
                                            <tr><th><strong>Grand Total</strong></th><td><strong>{{ number_format($order->grand_total, 2) }}</strong></td></tr>
                                            <tr><th>Paid</th><td>{{ number_format($order->paid_amount, 2) }}</td></tr>
                                            <tr><th>Due</th><td>{{ number_format($order->due_amount, 2) }}</td></tr>
                                            <tr><th>Coupon</th><td>{{ $order->coupon_code ?? '-' }}</td></tr>
                                            <tr><th>Created</th><td>{{ $order->created_at->format('M d, Y h:i A') }}</td></tr>
                                        </table>
                                    </div>
                                </div>

                                @if($order->notes)
                                <div class="card card-outline card-warning mt-3">
                                    <div class="card-header"><h5 class="m-0">Customer Notes</h5></div>
                                    <div class="card-body"><p class="mb-0">{{ $order->notes }}</p></div>
                                </div>
                                @endif
                            </div>

                            <!-- Customer Info -->
                            <div class="col-md-6">
                                <div class="card card-outline card-primary">
                                    <div class="card-header"><h5 class="m-0">Customer Information</h5></div>
                                    <div class="card-body p-0">
                                        <table class="table table-bordered mb-0">
                                            <tr><th width="35%">Name</th><td>{{ $order->customer->name ?? $order->guest_email ?? 'N/A' }}</td></tr>
                                            <tr><th>Email</th><td>{{ $order->customer->email ?? $order->guest_email ?? 'N/A' }}</td></tr>
                                            <tr><th>Phone</th><td>{{ $order->customer->phone ?? 'N/A' }}</td></tr>
                                        </table>
                                    </div>
                                </div>

                                <div class="card card-outline card-success mt-3">
                                    <div class="card-header"><h5 class="m-0">Shipping Address</h5></div>
                                    <div class="card-body">
                                        @if($order->shipping_address)
                                        <p class="mb-1">{{ $order->shipping_address['recipient_name'] ?? 'N/A' }}</p>
                                        <p class="mb-1">{{ $order->shipping_address['address_line_1'] ?? '' }}</p>
                                        @if(!empty($order->shipping_address['address_line_2']))
                                        <p class="mb-1">{{ $order->shipping_address['address_line_2'] }}</p>
                                        @endif
                                        <p class="mb-1">{{ ($order->shipping_address['city'] ?? '') . ', ' . ($order->shipping_address['state'] ?? '') }}</p>
                                        <p class="mb-1">{{ $order->shipping_address['country'] ?? '' }} - {{ $order->shipping_address['postal_code'] ?? '' }}</p>
                                        <p class="mb-0"><strong>Phone:</strong> {{ $order->shipping_address['phone'] ?? '' }}</p>
                                        @else
                                        <p class="text-muted">No shipping address provided.</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Order Items -->
                                <div class="card card-outline card-secondary mt-3">
                                    <div class="card-header"><h5 class="m-0">Order Items ({{ $order->items->count() }})</h5></div>
                                    <div class="card-body p-0">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                                <tr><th style="width:60px">Image</th><th>Product</th><th>SKU</th><th>Price</th><th>Qty</th><th>Total</th></tr>
                                            </thead>
                                            <tbody>
                                                @foreach($order->items as $item)
                                                <tr>
                                                    <td class="align-middle">
                                                        @if($item->product && $item->product->thumbnail)
                                                        <img src="{{ asset('storage/' . $item->product->thumbnail) }}"
                                                             alt="{{ $item->product_name }}"
                                                             width="48" height="48"
                                                             style="object-fit:cover;border-radius:6px;"
                                                             loading="lazy">
                                                        @elseif($item->product && $item->product->images->isNotEmpty())
                                                        <img src="{{ asset('storage/' . $item->product->images->first()->image) }}"
                                                             alt="{{ $item->product_name }}"
                                                             width="48" height="48"
                                                             style="object-fit:cover;border-radius:6px;"
                                                             loading="lazy">
                                                        @else
                                                        <div class="text-center text-muted" style="width:48px;height:48px;line-height:48px;background:#f3f4f6;border-radius:6px;font-size:20px;">📦</div>
                                                        @endif
                                                    </td>
                                                    <td class="align-middle">{{ $item->product_name }}</td>
                                                    <td class="align-middle"><small>{{ $item->product_sku ?? '-' }}</small></td>
                                                    <td class="align-middle">{{ number_format($item->unit_price, 2) }}</td>
                                                    <td class="align-middle">{{ $item->quantity }}</td>
                                                    <td class="align-middle">{{ number_format($item->total, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Management -->
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="card card-outline card-warning">
                                    <div class="card-header"><h5 class="m-0">Update Order Status</h5></div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <select name="status" class="form-control">
                                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                                    <option value="packed" {{ $order->status === 'packed' ? 'selected' : '' }}>Packed</option>
                                                    <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                                    <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <textarea name="notes" class="form-control" rows="2" placeholder="Status change notes (optional)"></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-warning btn-block">Update Status</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card card-outline card-success">
                                    <div class="card-header"><h5 class="m-0">Update Payment</h5></div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.orders.update-payment', $order) }}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <select name="payment_status" class="form-control">
                                                    <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                                                    <option value="partially_paid" {{ $order->payment_status === 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                                                    <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                                                    <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-success btn-block">Update Payment</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card card-outline card-info">
                                    <div class="card-header"><h5 class="m-0">Update Shipping</h5></div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.orders.update-shipping', $order) }}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <select name="shipping_status" class="form-control">
                                                    <option value="pending" {{ $order->shipping_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="processing" {{ $order->shipping_status === 'processing' ? 'selected' : '' }}>Processing</option>
                                                    <option value="packed" {{ $order->shipping_status === 'packed' ? 'selected' : '' }}>Packed</option>
                                                    <option value="handed_to_courier" {{ $order->shipping_status === 'handed_to_courier' ? 'selected' : '' }}>Handed to Courier</option>
                                                    <option value="in_transit" {{ $order->shipping_status === 'in_transit' ? 'selected' : '' }}>In Transit</option>
                                                    <option value="delivered" {{ $order->shipping_status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                                    <option value="failed" {{ $order->shipping_status === 'failed' ? 'selected' : '' }}>Failed</option>
                                                    <option value="returned" {{ $order->shipping_status === 'returned' ? 'selected' : '' }}>Returned</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-info btn-block">Update Shipping</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status History -->
                        @if($order->statusHistories->count() > 0)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card card-outline card-secondary">
                                    <div class="card-header"><h5 class="m-0">Order Timeline</h5></div>
                                    <div class="card-body">
                                        <ul class="timeline">
                                            @foreach($order->statusHistories as $history)
                                            <li>
                                                <i class="fas fa-circle bg-{{ $history->to_status === 'completed' ? 'success' : ($history->to_status === 'cancelled' ? 'danger' : 'info') }}"></i>
                                                <div class="timeline-item">
                                                    <span class="time"><i class="fas fa-clock"></i> {{ $history->created_at->format('M d, Y h:i A') }}</span>
                                                    <h3 class="timeline-header">
                                                        Status changed to <strong>{{ ucfirst(str_replace('_', ' ', $history->to_status)) }}</strong>
                                                        <small>by {{ $history->changed_by_type }}</small>
                                                    </h3>
                                                    @if($history->notes)
                                                    <div class="timeline-body">{{ $history->notes }}</div>
                                                    @endif
                                                </div>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Admin Notes -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card card-outline card-secondary">
                                    <div class="card-header"><h5 class="m-0">Admin Notes</h5></div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                                            @csrf @method('PUT')
                                            <div class="form-group">
                                                <textarea name="admin_notes" class="form-control" rows="3" placeholder="Add internal notes...">{{ $order->admin_notes }}</textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Notes</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
