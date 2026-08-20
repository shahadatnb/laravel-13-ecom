@extends('admin.layouts.app')
@section('title', 'Product Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Product List</h3>
                <div class="card-tools d-flex align-items-center gap-2">
                    <div class="btn-group btn-group-sm mr-2" role="group" aria-label="Stock filters">
                        <a href="{{ route('admin.product.index') }}"
                           class="btn {{ empty($stockStatus) ? 'btn-info' : 'btn-outline-info' }}">
                            <i class="fas fa-list"></i> All
                        </a>
                        <a href="{{ route('admin.product.index', ['stock_status' => 'in']) }}"
                           class="btn {{ ($stockStatus ?? '') === 'in' ? 'btn-success' : 'btn-outline-success' }}">
                            <i class="fas fa-check-circle"></i> In Stock
                        </a>
                        <a href="{{ route('admin.product.index', ['stock_status' => 'low']) }}"
                           class="btn {{ ($stockStatus ?? '') === 'low' ? 'btn-warning' : 'btn-outline-warning' }}">
                            <i class="fas fa-exclamation-triangle"></i> Low Stock
                        </a>
                        <a href="{{ route('admin.product.index', ['stock_status' => 'out']) }}"
                           class="btn {{ ($stockStatus ?? '') === 'out' ? 'btn-danger' : 'btn-outline-danger' }}">
                            <i class="fas fa-times-circle"></i> Out of Stock
                        </a>
                    </div>
                    <div class="btn-group btn-group-sm mr-2" role="group">
                        <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Show/Hide columns">
                            <i class="fas fa-columns"></i> Columns
                        </button>
                        <div class="dropdown-menu dropdown-menu-right p-0" id="columnToggleMenu" style="min-width:220px;">
                            <h6 class="dropdown-header bg-light" style="font-size:12px;">Show / Hide Columns</h6>
                            <div class="dropdown-item py-1 px-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input col-toggle" id="col-tools" data-col="tools" checked>
                                    <label class="custom-control-label" for="col-tools" style="font-size:13px;">Tools</label>
                                </div>
                            </div>
                            <div class="dropdown-item py-1 px-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input col-toggle" id="col-thumbnail" data-col="thumbnail" checked>
                                    <label class="custom-control-label" for="col-thumbnail" style="font-size:13px;">Thumbnail</label>
                                </div>
                            </div>
                            <div class="dropdown-item py-1 px-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input col-toggle" id="col-name" data-col="name" checked>
                                    <label class="custom-control-label" for="col-name" style="font-size:13px;">Name</label>
                                </div>
                            </div>
                            <div class="dropdown-item py-1 px-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input col-toggle" id="col-name_bn" data-col="name_bn" checked>
                                    <label class="custom-control-label" for="col-name_bn" style="font-size:13px;">Name (BN)</label>
                                </div>
                            </div>
                            <div class="dropdown-item py-1 px-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input col-toggle" id="col-sku" data-col="sku" checked>
                                    <label class="custom-control-label" for="col-sku" style="font-size:13px;">SKU</label>
                                </div>
                            </div>
                            <div class="dropdown-item py-1 px-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input col-toggle" id="col-brand" data-col="brand" checked>
                                    <label class="custom-control-label" for="col-brand" style="font-size:13px;">Brand</label>
                                </div>
                            </div>
                            <div class="dropdown-item py-1 px-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input col-toggle" id="col-category" data-col="category" checked>
                                    <label class="custom-control-label" for="col-category" style="font-size:13px;">Category</label>
                                </div>
                            </div>
                            <div class="dropdown-item py-1 px-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input col-toggle" id="col-price" data-col="price" checked>
                                    <label class="custom-control-label" for="col-price" style="font-size:13px;">Price</label>
                                </div>
                            </div>
                            <div class="dropdown-item py-1 px-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input col-toggle" id="col-stock_status" data-col="stock_status" checked>
                                    <label class="custom-control-label" for="col-stock_status" style="font-size:13px;">Stock / Status</label>
                                </div>
                            </div>
                            <div class="dropdown-divider my-1"></div>
                            <div class="dropdown-item text-center py-1">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="resetColumnsBtn" style="font-size:11px;">
                                    <i class="fas fa-undo"></i> Reset to Default
                                </button>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('admin.product.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> New Product
                    </a>
                </div>
            </div>
            <div class="card-body">
                @include('admin.layouts._message')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="productTable">
                        <thead>
                            <tr>
                                <th data-col="id">#</th>
                                <th data-col="tools">Tools</th>
                                <th data-col="thumbnail">Thumbnail</th>
                                <th data-col="name">Name</th>
                                <th data-col="name_bn">Name BN</th>
                                <th data-col="sku">SKU</th>
                                <th data-col="brand">Brand</th>
                                <th data-col="category">Category</th>
                                <th data-col="price">Regular Price</th>
                                <th data-col="stock_status">
                                    Stock / Status / Featured
                                    <div class="mt-1">
                                        <select
                                            class="form-control form-control-sm"
                                            style="font-size:11px;"
                                            onchange="if(this.value) window.location.href='{{ route('admin.product.index') }}?stock_status='+this.value; else window.location.href='{{ route('admin.product.index') }}';"
                                        >
                                            <option value="">All stock</option>
                                            <option value="in" {{ ($stockStatus ?? '') === 'in' ? 'selected' : '' }}>In Stock</option>
                                            <option value="low" {{ ($stockStatus ?? '') === 'low' ? 'selected' : '' }}>Low Stock</option>
                                            <option value="out" {{ ($stockStatus ?? '') === 'out' ? 'selected' : '' }}>Out of Stock</option>
                                        </select>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                            <tr>
                                <td data-col="id">{{ $product->id }}</td>
                                <td data-col="tools" nowrap>
                                    <a href="{{ route('admin.product.edit', $product->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.product.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                                <td data-col="thumbnail">
                                    @if($product->thumbnail && Storage::disk('public')->exists($product->thumbnail))
                                        <img src="{{ asset('storage/' . $product->thumbnail) }}" width="40" height="40" class="mt-1" style="object-fit:contain;" />
                                    @else
                                        <span class="text-muted">No image</span>
                                    @endif
                                </td>
                                <td data-col="name">{{ $product->name }}</td>
                                <td data-col="name_bn">{{ $product->name_bn ?? 'N/A' }}</td>
                                <td data-col="sku">{{ $product->sku ?? 'N/A' }}</td>
                                <td data-col="brand">{{ $product->brand->name ?? 'N/A' }}</td>
                                <td data-col="category">{{ $product->category->name ?? 'N/A' }}</td>
                                <td data-col="price">{{ $product->regular_price ? '$' . number_format($product->regular_price, 2) : 'N/A' }}</td>
                                <td data-col="stock_status">
                                    <div class="d-flex flex-wrap gap-1 align-items-center">
                                        @php
                                            // For variable products, sum up variant stock instead of product-level stock
                                            $isVariable = $product->product_type === 'variable' || ($product->variants && $product->variants->count() > 0);
                                            if ($isVariable && $product->variants && $product->variants->count() > 0) {
                                                $stock = (int) $product->variants->sum('stock');
                                                $variantCount = $product->variants->count();
                                            } else {
                                                $stock = (int) ($product->stock ?? 0);
                                                $variantCount = 0;
                                            }
                                            $minStock = (int) ($product->minimum_stock ?? 5);
                                            if ($stock <= 0) {
                                                $stockBadgeClass = 'bg-danger';
                                                $stockLabel = 'Out of Stock';
                                            } elseif ($stock <= $minStock) {
                                                $stockBadgeClass = 'bg-warning text-dark';
                                                $stockLabel = 'Low Stock';
                                            } else {
                                                $stockBadgeClass = 'bg-success';
                                                $stockLabel = 'In Stock';
                                            }
                                        @endphp
                                        <span class="badge {{ $stockBadgeClass }}" style="font-size:10px;font-weight:600;padding:3px 8px;" title="Stock: {{ $stock }}{{ $variantCount > 0 ? ' (across '.$variantCount.' variants)' : '' }}">
                                            <i class="fas fa-box"></i> {{ $stockLabel }} ({{ $stock }})
                                            @if($variantCount > 0)
                                                <span class="ml-1" style="opacity:0.7;font-size:9px;">{{ $variantCount }} var.</span>
                                            @endif
                                        </span>
                                        @if($product->status == 'published')
                                            <span class="badge badge-success" style="font-size:10px;font-weight:600;padding:3px 8px;"><i class="fas fa-check-circle"></i> Published</span>
                                        @elseif($product->status == 'draft')
                                            <span class="badge badge-secondary" style="font-size:10px;font-weight:600;padding:3px 8px;"><i class="fas fa-pen"></i> Draft</span>
                                        @elseif($product->status == 'pending')
                                            <span class="badge badge-warning" style="font-size:10px;font-weight:600;padding:3px 8px;"><i class="fas fa-clock"></i> Pending</span>
                                        @elseif($product->status == 'hidden')
                                            <span class="badge badge-dark" style="font-size:10px;font-weight:600;padding:3px 8px;"><i class="fas fa-eye-slash"></i> Hidden</span>
                                        @else
                                            <span class="badge badge-danger" style="font-size:10px;font-weight:600;padding:3px 8px;"><i class="fas fa-archive"></i> Archived</span>
                                        @endif
                                        @if($product->featured)
                                            <span class="badge badge-info" style="font-size:10px;font-weight:600;padding:3px 8px;"><i class="fas fa-star"></i> Featured</span>
                                        @else
                                            <span class="badge badge-secondary" style="font-size:10px;font-weight:600;padding:3px 8px;">No</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @if($products->isEmpty())
                            <tr>
                                <td colspan="99" class="text-center empty-colspan">No products found.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    @if ($products instanceof \Illuminate\Pagination\AbstractPaginator && $products->hasPages())
                        <div class="mt-4 d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Showing {{ $products->firstItem() }}-{{ $products->lastItem() }} of {{ $products->total() }} products
                            </div>
                            <div>
                                {{ $products->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    'use strict';

    var STORAGE_KEY = 'product_table_visible_columns';
    var ALL_COLUMNS = ['id', 'tools', 'thumbnail', 'name', 'name_bn', 'sku', 'brand', 'category', 'price', 'stock_status'];

    /**
     * Restore column visibility from localStorage.
     */
    function restoreColumns() {
        var saved;
        try {
            saved = JSON.parse(localStorage.getItem(STORAGE_KEY));
        } catch(e) {
            saved = null;
        }

        if (!Array.isArray(saved)) return;

        ALL_COLUMNS.forEach(function(col) {
            var visible = saved.indexOf(col) !== -1;
            setColumnVisibility(col, visible, false);
        });

        syncCheckboxes();
    }

    /**
     * Show or hide a column by its data-col value.
     */
    function setColumnVisibility(col, visible, save) {
        if (save === undefined) save = true;

        var els = document.querySelectorAll('#productTable [data-col="' + col + '"]');
        els.forEach(function(el) {
            el.style.display = visible ? '' : 'none';
        });

        updateEmptyColspan();

        if (save) saveState();
    }

    /**
     * Update the empty-state colspan to match visible column count.
     */
    function updateEmptyColspan() {
        var emptyCell = document.querySelector('#productTable td.empty-colspan');
        if (!emptyCell) return;
        var visibleHeaders = document.querySelectorAll('#productTable thead th:not([style*="display: none"])').length;
        emptyCell.colSpan = Math.max(visibleHeaders, 1);
    }

    /**
     * Save current column visibility to localStorage.
     */
    function saveState() {
        var visible = [];
        ALL_COLUMNS.forEach(function(col) {
            var first = document.querySelector('#productTable [data-col="' + col + '"]');
            if (first && first.style.display !== 'none') {
                visible.push(col);
            }
        });
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(visible));
        } catch(e) {}
    }

    /**
     * Sync checkbox states to match actual column visibility.
     */
    function syncCheckboxes() {
        document.querySelectorAll('.col-toggle').forEach(function(cb) {
            var col = cb.getAttribute('data-col');
            var first = document.querySelector('#productTable [data-col="' + col + '"]');
            cb.checked = !first || first.style.display !== 'none';
        });
    }

    /**
     * Reset all columns to visible.
     */
    function resetColumns() {
        ALL_COLUMNS.forEach(function(col) {
            setColumnVisibility(col, true, false);
        });
        syncCheckboxes();
        saveState();
        updateEmptyColspan();
    }

    // --- Event listeners ---

    document.addEventListener('DOMContentLoaded', function() {
        // Restore saved state
        restoreColumns();

        // Checkbox toggles
        document.querySelectorAll('.col-toggle').forEach(function(cb) {
            cb.addEventListener('change', function() {
                var col = this.getAttribute('data-col');
                setColumnVisibility(col, this.checked, true);
            });
        });

        // Reset button
        var resetBtn = document.getElementById('resetColumnsBtn');
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                resetColumns();
            });
        }

        // Re-sync when dropdown is shown (in case of external changes)
        $('#columnToggleMenu').on('show.bs.dropdown', function() {
            syncCheckboxes();
        });
    });
})();
</script>
@endpush
@endsection
