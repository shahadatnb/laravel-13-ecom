@extends('admin.layouts.app')
@section('title', 'Stock: ' . $product->name . ' - ' . $variant->name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-box text-success mr-1"></i> {{ $product->name }}
                    <small class="text-muted ml-2">- {{ $variant->name }}</small>
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.stock.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                    <a href="{{ route('admin.stock.stock-in-form', $product) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus mr-1"></i> Add Stock
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3 col-6">
                        <div class="info-box info-box-sm">
                            <span class="info-box-icon bg-info"><i class="fas fa-tag"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">SKU</span>
                                <span class="info-box-number font-mono">{{ $variant->sku ?? '—' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="info-box info-box-sm">
                            <span class="info-box-icon bg-warning"><i class="fas fa-box"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Current Stock</span>
                                <span class="info-box-number">{{ number_format($variant->stock) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="info-box info-box-sm">
                            <span class="info-box-icon bg-success"><i class="fas fa-dollar-sign"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Price</span>
                                <span class="info-box-number">{{ number_format($variant->price, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="info-box info-box-sm">
                            <span class="info-box-icon bg-secondary"><i class="fas fa-layer-group"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Status</span>
                                <span class="info-box-number">
                                    @if($variant->stock <= 0)
                                        <span class="badge badge-danger">Out of Stock</span>
                                    @elseif($variant->stock <= ($product->minimum_stock ?? 0))
                                        <span class="badge badge-warning">Low Stock</span>
                                    @else
                                        <span class="badge badge-success">In Stock</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($variant->attributes)
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card card-secondary card-outline">
                            <div class="card-header"><h3 class="card-title">Attributes</h3></div>
                            <div class="card-body">
                                @php
                                    $attrs = is_string($variant->attributes) ? json_decode($variant->attributes, true) : $variant->attributes;
                                @endphp
                                @if(is_array($attrs))
                                    @foreach($attrs as $key => $value)
                                        <span class="badge badge-pill badge-light mr-1 mb-1">
                                            <strong>{{ $key }}:</strong> {{ $value }}
                                        </span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-history mr-1"></i> Inventory Records</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>Warehouse</th>
                                                <th class="text-right">Current Stock</th>
                                                <th class="text-right">Reserved</th>
                                                <th class="text-right">Available</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($inventory as $inv)
                                            <tr>
                                                <td>{{ $inv->warehouse_name ?? 'N/A' }}</td>
                                                <td class="text-right">{{ number_format($inv->current_stock) }}</td>
                                                <td class="text-right text-muted">{{ number_format($inv->reserved_stock) }}</td>
                                                <td class="text-right font-weight-bold {{ ($inv->current_stock - $inv->reserved_stock) <= 0 ? 'text-danger' : 'text-success' }}">
                                                    {{ number_format($inv->current_stock - $inv->reserved_stock) }}
                                                </td>
                                                <td class="text-center">
                                                    @if($inv->current_stock <= 0)
                                                        <span class="badge badge-danger">Out</span>
                                                    @elseif($inv->current_stock <= ($product->minimum_stock ?? 0))
                                                        <span class="badge badge-warning">Low</span>
                                                    @else
                                                        <span class="badge badge-success">OK</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">No inventory records for this variant.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection