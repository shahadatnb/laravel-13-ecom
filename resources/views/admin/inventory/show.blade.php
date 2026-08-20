@extends('admin.layouts.app')
@section('title', 'Inventory: ' . ($inventory->product?->name ?? 'N/A'))

@push('styles')
<style>
    .info-box-icon { border-radius: .25rem 0 0 .25rem; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-8">

        {{-- Stock Information --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle mr-1"></i> Stock Information</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.inventory.adjust-form', $inventory) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-sliders-h mr-1"></i> Adjust Stock
                    </a>
                    <a href="{{ route('admin.inventory.index') }}" class="btn btn-default btn-sm ml-1">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped mb-0">
                    <tbody>
                        <tr><th style="width:180px">Product</th><td class="font-weight-bold">{{ $inventory->product?->name }}</td></tr>
                        <tr><th>Warehouse</th><td>{{ $inventory->warehouse?->name }}</td></tr>
                        @if($inventory->sku)<tr><th>SKU</th><td><span class="font-mono">{{ $inventory->sku }}</span></td></tr>@endif
                        @if($inventory->barcode)<tr><th>Barcode</th><td><span class="font-mono">{{ $inventory->barcode }}</span></td></tr>@endif
                        <tr><th>Location</th><td>{{ $inventory->location ?? '—' }}</td></tr>
                        <tr><th>Variant</th><td>{{ $inventory->variant?->name ?? '—' }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Stock Levels --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-bar mr-1"></i> Stock Levels</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-6">
                        <div class="text-center p-3 border rounded bg-light">
                            <div class="h2 font-weight-bold">{{ number_format($inventory->current_stock) }}</div>
                            <div class="text-muted small">Current Stock</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-center p-3 border rounded bg-light">
                            <div class="h2 font-weight-bold text-warning">{{ number_format($inventory->reserved_stock) }}</div>
                            <div class="text-muted small">Reserved</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-center p-3 border rounded bg-light">
                            <div class="h2 font-weight-bold {{ $inventory->available_stock <= 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($inventory->available_stock) }}
                            </div>
                            <div class="text-muted small">Available</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-center p-3 border rounded bg-light">
                            <div class="h2 font-weight-bold text-info">{{ number_format($inventory->minimum_stock) }}</div>
                            <div class="text-muted small">Min Stock</div>
                        </div>
                    </div>
                </div>
                @if($inventory->maximum_stock)
                <div class="text-center text-muted small mt-2">
                    Max Stock: {{ number_format($inventory->maximum_stock) }} · Reorder at: {{ number_format($inventory->reorder_level) }}
                </div>
                @endif
            </div>
        </div>

        {{-- Recent Transactions --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history mr-1"></i> Recent Transactions</h3>
            </div>
            <div class="card-body p-0">
                @if($inventory->transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th class="text-right">Before</th>
                                <th class="text-right">Change</th>
                                <th class="text-right">After</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventory->transactions as $txn)
                            <tr>
                                <td class="text-nowrap">{{ $txn->created_at->format('d-m-Y H:i') }}</td>
                                <td>
                                    <span class="badge badge-{{ $txn->isInbound() ? 'success' : 'danger' }}">
                                        {{ ucfirst(str_replace('_', ' ', $txn->type)) }}
                                    </span>
                                </td>
                                <td class="text-right">{{ number_format($txn->quantity_before) }}</td>
                                <td class="text-right font-weight-bold {{ $txn->quantity_change >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $txn->quantity_change >= 0 ? '+' : '' }}{{ number_format($txn->quantity_change) }}
                                </td>
                                <td class="text-right">{{ number_format($txn->quantity_after) }}</td>
                                <td>{{ $txn->reason ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-5">
                    <i class="fas fa-receipt fa-3x mb-3 d-block"></i>
                    No transactions yet.
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-check-circle mr-1"></i> Stock Status</h3>
            </div>
            <div class="card-body">
                @if($inventory->isOutOfStock())
                    <div class="alert alert-danger mb-0 text-center font-weight-bold">Out of Stock</div>
                @elseif($inventory->isLowStock())
                    <div class="alert alert-warning mb-0 text-center font-weight-bold">Low Stock</div>
                @else
                    <div class="alert alert-success mb-0 text-center font-weight-bold">In Stock</div>
                @endif
                @if($inventory->isOverstocked())
                    <div class="alert alert-info text-center font-weight-bold mt-2 mb-0">Overstocked</div>
                @endif
                @if($inventory->needsReorder())
                    <div class="alert alert-warning text-center font-weight-bold mt-2 mb-0">Needs Reorder</div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bolt mr-1"></i> Quick Actions</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.inventory.adjust-form', $inventory) }}" class="btn btn-warning btn-block mb-2">
                    <i class="fas fa-sliders-h mr-1"></i> Adjust Stock
                </a>
                <a href="{{ route('admin.inventory.stock-in-form') }}?product_id={{ $inventory->product_id }}" class="btn btn-success btn-block">
                    <i class="fas fa-plus mr-1"></i> Stock In
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
