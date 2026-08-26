@extends('admin.layouts.app')
@section('title', 'Stock Management')

@section('content')
{{-- ═══════════════ Summary Cards ═══════════════ --}}
<div class="row mb-3">
  <div class="col-lg-3 col-md-6">
    <div class="info-box">
      <span class="info-box-icon" style="background:#cce5ff;color:#007bff;"><i class="fas fa-cube"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Simple Products</span>
        <span class="info-box-number">{{ number_format($simpleCount) }}</span>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6">
    <div class="info-box">
      <span class="info-box-icon" style="background:#d4edda;color:#28a745;"><i class="fas fa-boxes"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Total Simple Stock</span>
        <span class="info-box-number">{{ number_format($simpleTotalStock) }}</span>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6">
    <div class="info-box">
      <span class="info-box-icon" style="background:#fff3cd;color:#e0a800;"><i class="fas fa-exclamation-triangle"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Low Stock</span>
        <span class="info-box-number">{{ $lowStockCount }}</span>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6">
    <div class="info-box">
      <span class="info-box-icon" style="background:#f8d7da;color:#721c24;"><i class="fas fa-times-circle"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Out of Stock</span>
        <span class="info-box-number">{{ $outOfStockCount }}</span>
      </div>
    </div>
  </div>
</div>

{{-- ═══════════════ Filters ═══════════════ --}}
<div class="card mb-3">
  <div class="card-body">
    <div class="row align-items-center">
      <div class="col-md-4">
        <form method="GET" action="{{ route('admin.stock.index') }}">
          <div class="input-group input-group-sm">
            <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search product name or SKU...">
            @if(request('stock_status'))
              <input type="hidden" name="stock_status" value="{{ request('stock_status') }}">
            @endif
            <div class="input-group-append">
              <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
            </div>
          </div>
        </form>
      </div>
      <div class="col-md-8 text-right">
        @php $qs = request()->query(); @endphp
        <a href="{{ route('admin.stock.index') }}" class="btn btn-sm {{ empty($stockStatus) ? 'btn-dark' : 'btn-outline-dark' }}">
          <i class="fas fa-list mr-1"></i> All
        </a>
        <a href="{{ route('admin.stock.bulk-adjust-form') }}" class="btn btn-sm btn-warning mr-1" title="Bulk Adjust Stock">
            <i class="fas fa-layer-group mr-1"></i> Bulk Adjust
        </a>
        <a href="{{ route('admin.stock.index', array_merge($qs, ['stock_status' => 'simple'])) }}" class="btn btn-sm {{ $stockStatus === 'simple' ? 'btn-primary' : 'btn-outline-primary' }}">
          <i class="fas fa-cube mr-1"></i> Simple Only
        </a>
        <a href="{{ route('admin.stock.index', array_merge($qs, ['stock_status' => 'has_variants'])) }}" class="btn btn-sm {{ $stockStatus === 'has_variants' ? 'btn-info' : 'btn-outline-info' }}">
          <i class="fas fa-layer-group mr-1"></i> With Variants
        </a>
        <a href="{{ route('admin.stock.index', array_merge($qs, ['stock_status' => 'no_variants'])) }}" class="btn btn-sm {{ $stockStatus === 'no_variants' ? 'btn-secondary' : 'btn-outline-secondary' }}">
          <i class="fas fa-cube mr-1"></i> No Variants
        </a>
        <a href="{{ route('admin.stock.index', array_merge($qs, ['stock_status' => 'low_stock'])) }}" class="btn btn-sm {{ $stockStatus === 'low_stock' ? 'btn-warning' : 'btn-outline-warning' }}">
          <i class="fas fa-exclamation mr-1"></i> Low Stock
        </a>
        <a href="{{ route('admin.stock.index', array_merge($qs, ['stock_status' => 'out_of_stock'])) }}" class="btn btn-sm {{ $stockStatus === 'out_of_stock' ? 'btn-danger' : 'btn-outline-danger' }}">
          <i class="fas fa-times mr-1"></i> Out of Stock
        </a>
      </div>
    </div>
  </div>
</div>

{{-- ═══════════════ Product Table ═══════════════ --}}
<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-bordered table-striped table-hover mb-0">
        <thead>
          <tr>
            <th style="width:50px">#</th>
            <th>Product</th>
            <th>SKU</th>
            <th>Type</th>
            <th class="text-right">Product Stock</th>
            <th class="text-right">Variant Stock</th>
            <th class="text-right">Total Stock</th>
            <th class="text-center">Status</th>
            <th class="text-center" style="width:130px">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($products as $i => $product)
          @php
            $isSimple = $product->product_type === 'simple';
            $variantStock = $product->variants->sum('stock');
            $productStock = $isSimple ? $product->stock : 0;
            $totalStock = $product->total_stock;
            $minStock = $product->minimum_stock ?? 0;
          @endphp
          <tr>
            <td class="text-muted">{{ ($products->currentPage() - 1) * $products->perPage() + $i + 1 }}</td>
            <td>
              <div class="d-flex align-items-center">
                @if($product->thumbnail)
                  <img src="{{ $product->thumbnail }}" style="width:36px;height:36px;object-fit:cover;border-radius:6px;" class="mr-2" alt="">
                @endif
                <div>
                  <strong>{{ $product->name }}</strong>
                  @if($product->brand)
                    <br><small class="text-muted">{{ $product->brand->name }}</small>
                  @endif
                </div>
              </div>
            </td>
            <td><code class="text-muted">{{ $product->sku ?? '—' }}</code></td>
            <td>
              @if($isSimple)
                <span class="badge badge-secondary">Simple</span>
              @else
                <span class="badge badge-info">Variable ({{ $product->variants_count }})</span>
              @endif
            </td>
            {{-- Product Stock (simple only) --}}
            <td class="text-right">
              @if($isSimple)
                <strong class="{{ $productStock <= 0 ? 'text-danger' : ($productStock <= $minStock ? 'text-warning' : 'text-success') }}">
                  {{ number_format($productStock) }}
                </strong>
                @if($minStock > 0)
                  <br><small class="text-muted">min: {{ number_format($minStock) }}</small>
                @endif
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
            {{-- Variant Stock --}}
            <td class="text-right">
              @if(!$isSimple)
                <strong class="{{ $variantStock <= 0 ? 'text-danger' : ($variantStock <= $minStock ? 'text-warning' : 'text-success') }}">
                  {{ number_format($variantStock) }}
                </strong>
                <br><small class="text-muted">{{ $product->variants_count }} variants</small>
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
            {{-- Total Stock --}}
            <td class="text-right">
              <strong class="font-weight-bold {{ $totalStock <= 0 ? 'text-danger' : ($totalStock <= $minStock ? 'text-warning' : 'text-dark') }}">
                {{ number_format($totalStock) }}
              </strong>
            </td>
            {{-- Status Badge --}}
            <td class="text-center">
              @if($totalStock <= 0)
                <span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i>Out of Stock</span>
              @elseif($totalStock <= $minStock)
                <span class="badge badge-warning"><i class="fas fa-exclamation-triangle mr-1"></i>Low Stock</span>
              @else
                <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>In Stock</span>
              @endif
            </td>
            {{-- Actions --}}
            <td class="text-center">
              <div class="btn-group btn-group-sm">
                <a href="{{ route('admin.stock.stock-in-form', $product) }}" class="btn btn-success" title="Stock In">
                  <i class="fas fa-plus"></i>
                </a>
                @if(!$isSimple)
                  <a href="{{ route('admin.product.show', $product) }}" class="btn btn-info" title="View Variants">
                    <i class="fas fa-layer-group"></i>
                  </a>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="9" class="text-center text-muted py-5">
              <i class="fas fa-boxes fa-3x mb-3 d-block"></i>
              No products found.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  @if($products->hasPages())
  <div class="card-footer clearfix">
    {{ $products->withQueryString()->links() }}
  </div>
  @endif
</div>
@endsection
