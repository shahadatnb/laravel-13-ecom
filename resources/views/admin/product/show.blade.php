@extends('admin.layouts.app')
@section('title', 'Product: ' . $product->name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ $product->name }}</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.product.edit', $product) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <a href="{{ route('admin.stock.stock-in-form', $product) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus mr-1"></i> Add Stock
                    </a>
                    <a href="{{ route('admin.stock.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-6">
                        <div class="info-box info-box-sm">
                            <span class="info-box-icon bg-info"><i class="fas fa-tag"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">SKU</span>
                                <span class="info-box-number font-mono">{{ $product->sku ?? '—' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="info-box info-box-sm">
                            <span class="info-box-icon bg-warning"><i class="fas fa-box"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Stock</span>
                                <span class="info-box-number">{{ number_format($product->stock) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="info-box info-box-sm">
                            <span class="info-box-icon bg-success"><i class="fas fa-dollar-sign"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Price</span>
                                <span class="info-box-number">{{ number_format($product->regular_price, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="info-box info-box-sm">
                            <span class="info-box-icon bg-secondary"><i class="fas fa-layer-group"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Type</span>
                                <span class="info-box-number">
                                    @if($product->product_type === 'variable')
                                        <span class="badge badge-info">Variable</span>
                                    @else
                                        <span class="badge badge-secondary">Simple</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($product->variants->isNotEmpty())
                <div class="mt-4">
                    <h4><i class="fas fa-layer-group mr-1"></i> Variants</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Variant</th>
                                    <th>SKU</th>
                                    <th>Price</th>
                                    <th class="text-right">Stock</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->variants as $variant)
                                @php
                                    $stock = $variant->stock ?? 0;
                                @endphp
                                <tr>
                                    <td>
                                        <span class="font-weight-bold">{{ $variant->name }}</span>
                                        @if($variant->attributes)
                                            @php
                                                $attrs = is_string($variant->attributes) ? json_decode($variant->attributes, true) : $variant->attributes;
                                            @endphp
                                            @if(is_array($attrs))
                                                <div>
                                                    @foreach($attrs as $key => $value)
                                                        <span class="badge badge-pill badge-light mr-1">{{ $key }}: {{ $value }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                    <td><span class="font-mono">{{ $variant->sku ?? '—' }}</span></td>
                                    <td>{{ number_format($variant->price, 2) }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('admin.stock.variant-show', [$product, $variant]) }}" class="font-weight-bold {{ $stock > 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($stock) }}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        @if($stock <= 0)
                                            <span class="badge badge-danger">Out of Stock</span>
                                        @elseif($stock <= ($product->minimum_stock ?? 0))
                                            <span class="badge badge-warning">Low Stock</span>
                                        @else
                                            <span class="badge badge-success">In Stock</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.stock.variant-show', [$product, $variant]) }}" class="btn btn-sm btn-info" title="Manage Stock">
                                            <i class="fas fa-box"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle mr-1"></i>
                    This product has no variants. <a href="{{ route('admin.product.edit', $product) }}">Add variants</a>.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection