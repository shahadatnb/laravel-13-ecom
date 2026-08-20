@extends('admin.layouts.app')
@section('title', 'Add Stock: ' . $product->name)

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.css') }}">
<style>
    .variant-stock-row {
        transition: background-color 0.2s;
    }
    .variant-stock-row:hover {
        background-color: #f8f9fa;
    }
    .variant-stock-row.has-stock {
        background-color: #f0fff4;
    }
    .variant-stock-row.low-stock {
        background-color: #fff8e1;
    }
    .variant-stock-row.out-of-stock {
        background-color: #fff0f0;
    }
    .stock-badge {
        font-size: 0.85em;
        padding: 0.25em 0.5em;
    }
    .bulk-quantity-input {
        max-width: 100px;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-plus-circle text-success mr-1"></i> Add Stock: {{ $product->name }}
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.stock.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="info-box info-box-sm">
                            <span class="info-box-icon bg-info"><i class="fas fa-cube"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Product</span>
                                <span class="info-box-number">{{ $product->name }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box info-box-sm">
                            <span class="info-box-icon bg-warning"><i class="fas fa-layer-group"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Type</span>
                                <span class="info-box-number">
                                    @if($product->variants->isNotEmpty())
                                        <span class="badge badge-info">Variable ({{ $product->variants->count() }} variants)</span>
                                    @else
                                        <span class="badge badge-secondary">Simple</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($product->variants->isEmpty())
                {{-- Simple product: single quantity stock-in --}}
                <form action="{{ route('admin.stock.stock-in', $product) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Warehouse <span class="text-danger">*</span></label>
                                <select name="warehouse_id" class="form-control select2 @error('warehouse_id') is-invalid @enderror" style="width:100%">
                                    <option value="">Select warehouse...</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                    @endforeach
                                </select>
                                @error('warehouse_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" class="form-control @error('quantity') is-invalid @enderror">
                                @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Reason</label>
                                <input type="text" name="reason" class="form-control" placeholder="Purchase, return, etc." value="{{ old('reason') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Unit Cost</label>
                                <input type="number" name="unit_cost" step="0.01" min="0" class="form-control" placeholder="0.00" value="{{ old('unit_cost') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Reference Number</label>
                                <input type="text" name="reference_number" class="form-control" placeholder="PO-001" value="{{ old('reference_number') }}">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <a href="{{ route('admin.stock.index') }}" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-success ml-2">
                            <i class="fas fa-plus mr-1"></i> Add Stock
                        </button>
                    </div>
                </form>
                @else
                {{-- Variable product: bulk variant stock-in --}}
                <form action="{{ route('admin.stock.stock-in', $product) }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Warehouse <span class="text-danger">*</span></label>
                                <select name="warehouse_id" id="warehouse_select" class="form-control select2 @error('warehouse_id') is-invalid @enderror" style="width:100%">
                                    <option value="">Select warehouse...</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                    @endforeach
                                </select>
                                @error('warehouse_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Reason</label>
                                <input type="text" name="global_reason" class="form-control" placeholder="Purchase, return, etc." value="{{ old('global_reason') }}">
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-info btn-sm mr-2" id="select-all-variants">
                                <i class="fas fa-check-double"></i> Select All
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm mr-2" id="deselect-all-variants">
                                <i class="fas fa-times"></i> Deselect All
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="variant-stock-table">
                            <thead>
                                <tr>
                                    <th style="width:40px">
                                        <input type="checkbox" id="select-all-checkbox" title="Select all variants">
                                    </th>
                                    <th>Variant</th>
                                    <th>SKU</th>
                                    <th>Attributes</th>
                                    <th class="text-right">Current Stock</th>
                                    <th class="text-right">Add Quantity</th>
                                    <th>Reason</th>
                                    <th>Unit Cost</th>
                                    <th>Ref Number</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->variants as $variant)
                                @php
                                    $stockClass = 'variant-stock-row';
                                    if ($variant->stock <= 0) {
                                        $stockClass .= ' out-of-stock';
                                    } elseif ($variant->stock <= ($product->minimum_stock ?? 0)) {
                                        $stockClass .= ' low-stock';
                                    } else {
                                        $stockClass .= ' has-stock';
                                    }
                                @endphp
                                <tr class="{{ $stockClass }}" data-variant-id="{{ $variant->id }}">
                                    <td>
                                        <input type="checkbox" name="variant_stocks[{{ $variant->id }}][variant_id]" value="{{ $variant->id }}" class="variant-checkbox" data-variant-id="{{ $variant->id }}">
                                    </td>
                                    <td>
                                        <span class="font-weight-bold">{{ $variant->name }}</span>
                                        @if($variant->images->isNotEmpty())
                                            <img src="{{ asset('storage/' . $variant->images->first()->image) }}" style="width:30px;height:30px;object-fit:cover;border-radius:4px;" class="ml-1" alt="">
                                        @endif
                                    </td>
                                    <td><span class="font-mono">{{ $variant->sku ?? '—' }}</span></td>
                                    <td>
                                        @php
                                            $attrs = is_string($variant->attributes) ? json_decode($variant->attributes, true) : $variant->attributes;
                                        @endphp
                                        @if(is_array($attrs))
                                            @foreach($attrs as $key => $value)
                                                <span class="badge badge-pill badge-light mr-1">{{ $key }}: {{ $value }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <span class="badge {{ $variant->stock > 0 ? 'badge-success' : 'badge-danger' }} stock-badge">
                                            {{ number_format($variant->stock) }}
                                        </span>
                                    </td>
                                    <td>
                                        <input type="number" name="variant_stocks[{{ $variant->id }}][quantity]" value="{{ old('variant_stocks.' . $variant->id . '.quantity', 1) }}" min="1" class="form-control form-control-sm bulk-quantity-input @error('variant_stocks.' . $variant->id . '.quantity') is-invalid @enderror">
                                        @error('variant_stocks.' . $variant->id . '.quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </td>
                                    <td>
                                        <input type="text" name="variant_stocks[{{ $variant->id }}][reason]" class="form-control form-control-sm" placeholder="Reason" value="{{ old('variant_stocks.' . $variant->id . '.reason') }}">
                                    </td>
                                    <td>
                                        <input type="number" name="variant_stocks[{{ $variant->id }}][unit_cost]" step="0.01" min="0" class="form-control form-control-sm" placeholder="0.00" value="{{ old('variant_stocks.' . $variant->id . '.unit_cost') }}">
                                    </td>
                                    <td>
                                        <input type="text" name="variant_stocks[{{ $variant->id }}][reference_number]" class="form-control form-control-sm" placeholder="PO-001" value="{{ old('variant_stocks.' . $variant->id . '.reference_number') }}">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer text-right">
                        <a href="{{ route('admin.stock.index') }}" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-success ml-2" id="submit-stock-in">
                            <i class="fas fa-plus mr-1"></i> Add Stock to Selected Variants
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    $('.select2').select2({ theme: 'bootstrap4' });

    @if($product->variants->isNotEmpty())
    $('#select-all-checkbox').on('change', function() {
        $('.variant-checkbox').prop('checked', this.checked);
        updateRowHighlights();
    });

    $('#select-all-variants').on('click', function() {
        $('.variant-checkbox').prop('checked', true);
        updateRowHighlights();
    });

    $('#deselect-all-variants').on('click', function() {
        $('.variant-checkbox').prop('checked', false);
        updateRowHighlights();
    });

    $('.variant-checkbox').on('change', function() {
        updateRowHighlights();
    });

    function updateRowHighlights() {
        $('.variant-stock-row').removeClass('selected-row');
        $('.variant-checkbox:checked').each(function() {
            $(this).closest('tr').addClass('selected-row');
        });
    }

    // Validate that warehouse is selected before submit
    $('form').on('submit', function(e) {
        var warehouseId = $('#warehouse_select').val();
        if (!warehouseId) {
            e.preventDefault();
            alert('Please select a warehouse.');
            return false;
        }

        var checkedCount = $('.variant-checkbox:checked').length;
        if (checkedCount === 0) {
            e.preventDefault();
            alert('Please select at least one variant.');
            return false;
        }

        // Validate quantities
        var hasError = false;
        $('.variant-checkbox:checked').each(function() {
            var row = $(this).closest('tr');
            var qtyInput = row.find('input[name$="[quantity]"]');
            var qty = parseInt(qtyInput.val());
            if (!qty || qty < 1) {
                qtyInput.addClass('is-invalid');
                hasError = true;
            } else {
                qtyInput.removeClass('is-invalid');
            }
        });

        if (hasError) {
            e.preventDefault();
            alert('Please enter valid quantities (minimum 1) for all selected variants.');
            return false;
        }

        if (!confirm('Add stock to ' + checkedCount + ' variant(s)?')) {
            e.preventDefault();
        }
    });
    @endif
});
</script>
@endpush