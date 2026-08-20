@extends('admin.layouts.app')

@section('title', 'Orders')

@section('content')
<section class="content">
    <div class="container-fluid">
        <!-- Stats Row -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $stats['total'] }}</h3>
                        <p>Total Orders</p>
                    </div>
                    <div class="icon"><i class="fas fa-shopping-cart"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $stats['pending'] }}</h3>
                        <p>Pending</p>
                    </div>
                    <div class="icon"><i class="fas fa-clock"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $stats['completed'] }}</h3>
                        <p>Completed</p>
                    </div>
                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $stats['cancelled'] }}</h3>
                        <p>Cancelled</p>
                    </div>
                    <div class="icon"><i class="fas fa-ban"></i></div>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">All Orders</h3>
                        <div class="card-tools">
                            <div class="btn-group">
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-info {{ !request('status') ? 'active' : '' }}">All</a>
                                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="btn btn-sm btn-warning {{ request('status') === 'pending' ? 'active' : '' }}">Pending</a>
                                <a href="{{ route('admin.orders.index', ['status' => 'processing']) }}" class="btn btn-sm btn-primary {{ request('status') === 'processing' ? 'active' : '' }}">Processing</a>
                                <a href="{{ route('admin.orders.index', ['status' => 'completed']) }}" class="btn btn-sm btn-success {{ request('status') === 'completed' ? 'active' : '' }}">Completed</a>
                                <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}" class="btn btn-sm btn-danger {{ request('status') === 'cancelled' ? 'active' : '' }}">Cancelled</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                        @endif

                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td><span class="badge bg-info">{{ $order->order_number }}</span></td>
                                    <td>
                                        @if($order->customer)
                                        <a href="{{ route('admin.customers.show', $order->customer_id) }}" class="font-weight-bold text-dark">
                                            <strong>{{ $order->customer->name }}</strong>
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ $order->customer->email }}</small>
                                        @elseif($order->guest_email)
                                        <span class="text-muted">
                                            <em>{{ $order->guest_email }}</em>
                                            <span class="badge bg-secondary">Guest</span>
                                        </span>
                                        @else
                                        <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php $firstItems = $order->items->take(3); $remaining = $order->items->count() - 3; @endphp
                                        <div class="d-flex align-items-center gap-1">
                                            @foreach($firstItems as $item)
                                            <div>
                                                @if($item->product && $item->product->thumbnail)
                                                <img src="{{ asset('storage/' . $item->product->thumbnail) }}"
                                                     alt="{{ $item->product_name }}"
                                                     width="32" height="32"
                                                     style="object-fit:cover;border-radius:4px;"
                                                     loading="lazy"
                                                     title="{{ $item->product_name }}">
                                                @elseif($item->product && $item->product->images->isNotEmpty())
                                                <img src="{{ asset('storage/' . $item->product->images->first()->image) }}"
                                                     alt="{{ $item->product_name }}"
                                                     width="32" height="32"
                                                     style="object-fit:cover;border-radius:4px;"
                                                     loading="lazy"
                                                     title="{{ $item->product_name }}">
                                                @else
                                                <span style="display:inline-block;width:32px;height:32px;line-height:32px;text-align:center;background:#f3f4f6;border-radius:4px;font-size:14px;" title="{{ $item->product_name }}">📦</span>
                                                @endif
                                            </div>
                                            @endforeach
                                            @if($remaining > 0)
                                            <span class="badge bg-secondary" title="{{ $remaining }} more item(s)">+{{ $remaining }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td><strong>{{ number_format($order->grand_total, 2) }}</strong></td>
                                    <td>
                                        @php
                                            $statusClass = match($order->status) {
                                                'pending' => 'bg-warning',
                                                'confirmed' => 'bg-info',
                                                'processing' => 'bg-primary',
                                                'packed' => 'bg-secondary',
                                                'shipped' => 'bg-info',
                                                'delivered' => 'bg-success',
                                                'completed' => 'bg-success',
                                                'cancelled' => 'bg-danger',
                                                'returned' => 'bg-danger',
                                                'refunded' => 'bg-danger',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ $order->getStatusLabel() }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $paymentClass = match($order->payment_status) {
                                                'paid' => 'bg-success',
                                                'partially_paid' => 'bg-warning',
                                                'pending' => 'bg-warning',
                                                'failed' => 'bg-danger',
                                                'refunded' => 'bg-info',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $paymentClass }}">{{ $order->getPaymentStatusLabel() }}</span>
                                    </td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete this order? This action cannot be undone.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">No orders found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="card-footer clearfix">
                            @if(method_exists($orders, 'links'))
                                {{ $orders->links() }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
