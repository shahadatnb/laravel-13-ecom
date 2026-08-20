@extends('admin.layouts.app')
@section('title', 'Inventory Management')

@push('styles')
<style>
    .small-box { border-radius: .25rem; }
    .small-box>.inner h3 { font-size: 2rem; }
    .small-box .icon>i { font-size: 50px; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">

        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ number_format($stats['warehouses']) }}</h3>
                        <p>Warehouses</p>
                    </div>
                    <div class="icon"><i class="fas fa-warehouse"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ number_format($stats['products_in_stock']) }}</h3>
                        <p>Products in Stock</p>
                    </div>
                    <div class="icon"><i class="fas fa-boxes"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ number_format($stats['low_stock']) }}</h3>
                        <p>Low Stock</p>
                    </div>
                    <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ number_format($stats['out_of_stock']) }}</h3>
                        <p>Out of Stock</p>
                    </div>
                    <div class="icon"><i class="fas fa-times-circle"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ number_format($stats['total_stock_value'] ?? 0, 2) }}</h3>
                        <p>Stock Value</p>
                    </div>
                    <div class="icon"><i class="fas fa-dollar-sign"></i></div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filters</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.inventory.index') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Search</label>
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Product, SKU, or barcode...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Warehouse</label>
                                <select name="warehouse_id" class="form-control form-control-sm select2" style="width:100%">
                                    <option value="">All Warehouses</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Stock Status</label>
                                <select name="stock_status" class="form-control form-control-sm select2" style="width:100%">
                                    <option value="">All Stock</option>
                                    <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Low Stock</option>
                                    <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>Out of Stock</option>
                                    <option value="available" {{ request('stock_status') === 'available' ? 'selected' : '' }}>In Stock</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="form-group w-100">
                                <button type="submit" class="btn btn-primary btn-sm btn-block"><i class="fas fa-search"></i> Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-boxes mr-1"></i> Inventory</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.inventory.stock-in-form') }}" class="btn btn-success btn-sm mr-1">
                        <i class="fas fa-plus mr-1"></i> Stock In
                    </a>
                    <a href="{{ route('admin.inventory.transfer-create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-exchange-alt mr-1"></i> Transfer
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Product / Variant</th>
                                <th>Warehouse</th>
                                <th class="text-right">Current</th>
                                <th class="text-right">Reserved</th>
                                <th class="text-right">Available</th>
                                <th class="text-right">Min Stock</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width:90px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventory as $item)
                            <tr>
                                <td>
                                    <span class="font-weight-bold">{{ $item->product?->name ?? "N/A" }}</span>
                                    @if($item->variant)
                                        <br><span class="badge badge-info" style="font-size:0.7rem;"><i class="fas fa-layer-group mr-1"></i>{{ $item->variant->name }}</span>
                                        @if($item->variant->attributes && count($item->variant->attributes))
                                            <br><small class="text-muted">
                                            @foreach($item->variant->attributes as $attrKey => $attrVal)
                                                <span class="badge badge-light mr-1" style="font-size:0.65rem;">{{ $attrKey }}: {{ $attrVal }}</span>
                                            @endforeach
                                            </small>
                                        @endif
                                    @else
                                        <br><span class="badge badge-secondary" style="font-size:0.65rem;"><i class="fas fa-cube mr-1"></i>Simple</span>
                                        @if($item->sku)<br><small class="text-muted font-mono" style="font-size:0.72rem;">SKU: {{ $item->sku }}</small>@endif
                                        @if($item->barcode)<br><small class="text-muted font-mono" style="font-size:0.72rem;">{{ $item->barcode }}</small>@endif
                                    @endif
                                </td>
                                <td>{{ $item->warehouse?->name ?? 'N/A' }}</td>
                                <td class="text-right">{{ number_format($item->current_stock) }}</td>
                                <td class="text-right text-muted">{{ number_format($item->reserved_stock) }}</td>
                                <td class="text-right font-weight-bold {{ $item->available_stock <= 0 ? 'text-danger' : ($item->isLowStock() ? 'text-warning' : 'text-success') }}">
                                    {{ number_format($item->available_stock) }}
                                </td>
                                <td class="text-right text-muted">{{ number_format($item->minimum_stock) }}</td>
                                <td class="text-center">
                                    @if($item->isOutOfStock())
                                        <span class="badge badge-danger">Out of Stock</span>
                                    @elseif($item->isLowStock())
                                        <span class="badge badge-warning">Low Stock</span>
                                    @else
                                        <span class="badge badge-success">In Stock</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.inventory.show', $item) }}" class="btn btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.inventory.adjust-form', $item) }}" class="btn btn-warning" title="Adjust">
                                            <i class="fas fa-sliders-h"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="fas fa-warehouse fa-3x mb-3 d-block"></i>
                                    No inventory records found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($inventory->hasPages())
            <div class="card-footer clearfix">{{ $inventory->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    $('.select2').select2({ theme: 'bootstrap4', minimumResultsForSearch: -1 });
});
</script>
@endpush
