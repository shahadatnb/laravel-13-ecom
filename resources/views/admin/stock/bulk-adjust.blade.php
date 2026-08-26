@extends('admin.layouts.app')
@section('title', 'Bulk Stock Adjustment')

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Settings Card --}}
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-cogs mr-1"></i> Adjustment Settings</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.stock.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Stock
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form id="bulkForm" method="POST" action="{{ route('admin.stock.bulk-adjust') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Warehouse <span class="text-danger">*</span></label>
                                <select name="warehouse_id" class="form-control form-control-sm" required>
                                    <option value="">Select Warehouse</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Mode <span class="text-danger">*</span></label>
                                <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                    <label class="btn btn-outline-primary btn-sm active">
                                        <input type="radio" name="mode" value="set" checked> Set
                                    </label>
                                    <label class="btn btn-outline-success btn-sm">
                                        <input type="radio" name="mode" value="add"> Add (+)
                                    </label>
                                    <label class="btn btn-outline-danger btn-sm">
                                        <input type="radio" name="mode" value="subtract"> Subtract (−)
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Reason</label>
                                <input type="text" name="reason" class="form-control form-control-sm" placeholder="e.g. Physical count correction" maxlength="500">
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="form-group w-100">
                                <button type="button" class="btn btn-warning btn-sm btn-block" onclick="applyToAll()">
                                    <i class="fas fa-magic mr-1"></i> Apply to All
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Product Table --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0" id="productTable">
                            <thead>
                                <tr>
                                    <th style="width:40px">
                                        <input type="checkbox" id="selectAll">
                                    </th>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th class="text-right" style="width:120px">Current Stock</th>
                                    <th class="text-right" style="width:140px">New Quantity</th>
                                    <th class="text-right" style="width:120px">After</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                <tr data-product-id="{{ $product->id }}" data-current="{{ $product->stock }}">
                                    <td>
                                        <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" class="product-checkbox">
                                    </td>
                                    <td>
                                        <strong>{{ $product->name }}</strong>
                                    </td>
                                    <td><code class="text-muted">{{ $product->sku ?? '—' }}</code></td>
                                    <td class="text-right">
                                        <span class="current-stock">{{ number_format($product->stock) }}</span>
                                    </td>
                                    <td class="text-right">
                                        <input type="number" name="quantities[{{ $product->id }}]"
                                               class="form-control form-control-sm qty-input text-right"
                                               min="0" placeholder="0" style="width:120px;display:inline-block;">
                                    </td>
                                    <td class="text-right">
                                        <span class="after-stock font-weight-bold">—</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-cube fa-2x mb-2 d-block"></i>
                                        No simple products found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <div>
                            <span id="selectedCount" class="text-muted">0 products selected</span>
                        </div>
                        <button type="submit" class="btn btn-warning" id="submitBtn">
                            <i class="fas fa-save mr-1"></i> Save Adjustments
                        </button>
                    </div>
                </form>

                @if($products->hasPages())
                <div class="mt-3">
                    {{ $products->withQueryString()->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    // Select All checkbox
    $('#selectAll').on('change', function() {
        $('.product-checkbox').prop('checked', this.checked);
        updatePreview();
        updateCount();
    });

    // Individual checkbox
    $('.product-checkbox').on('change', function() {
        updatePreview();
        updateCount();
    });

    // Quantity input changes — live preview
    $(document).on('input', '.qty-input', function() {
        updatePreview();
    });

    // Radio mode changes — refresh preview
    $('input[name="mode"]').on('change', function() {
        updatePreview();
    });

    function updatePreview() {
        var mode = $('input[name="mode"]:checked').val();

        $('#productTable tbody tr').each(function() {
            var $row = $(this);
            var current = parseInt($row.data('current')) || 0;
            var qty = parseInt($row.find('.qty-input').val());
            var $after = $row.find('.after-stock');

            if (isNaN(qty) || $row.find('.qty-input').val() === '') {
                $after.text('—').removeClass('text-success text-danger');
                return;
            }

            var after;
            switch(mode) {
                case 'set': after = qty; break;
                case 'add': after = current + qty; break;
                case 'subtract': after = Math.max(0, current - qty); break;
                default: after = qty;
            }

            var diff = after - current;
            var diffText = diff > 0 ? ' (+' + diff + ')' : (diff < 0 ? ' (' + diff + ')' : '');
            $after.text(after + diffText);

            if (diff > 0) {
                $after.addClass('text-success').removeClass('text-danger');
            } else if (diff < 0) {
                $after.addClass('text-danger').removeClass('text-success');
            } else {
                $after.removeClass('text-success text-danger');
            }
        });
    }

    function updateCount() {
        var count = $('.product-checkbox:checked').length;
        $('#selectedCount').text(count + ' product' + (count !== 1 ? 's' : '') + ' selected');
    }

    // Submit confirmation
    $('#bulkForm').on('submit', function(e) {
        var checked = $('.product-checkbox:checked').length;
        if (checked === 0) {
            e.preventDefault();
            alert('Please select at least one product.');
            return false;
        }

        // Check if any quantity is set
        var hasQty = false;
        $('.product-checkbox:checked').each(function() {
            var $row = $(this).closest('tr');
            var qty = $row.find('.qty-input').val();
            if (qty !== '' && qty !== null && qty !== undefined) {
                hasQty = true;
                return false;
            }
        });

        if (!hasQty) {
            e.preventDefault();
            alert('Please enter a quantity for at least one selected product.');
            return false;
        }

        return confirm('Are you sure you want to adjust stock for ' + checked + ' product(s)?');
    });
});

function applyToAll() {
    var val = prompt('Enter a quantity to apply to all selected/visible products:');
    if (val === null || val === '') return;

    var qty = parseInt(val);
    if (isNaN(qty) || qty < 0) {
        alert('Please enter a valid non-negative number.');
        return;
    }

    var checked = $('.product-checkbox:checked').length;
    if (checked === 0) {
        // Apply to all visible rows
        $('#productTable tbody tr').each(function() {
            $(this).find('.qty-input').val(qty);
            $(this).find('.product-checkbox').prop('checked', true);
        });
    } else {
        // Apply only to checked rows
        $('.product-checkbox:checked').each(function() {
            $(this).closest('tr').find('.qty-input').val(qty);
        });
    }

    // Trigger preview update
    $(document).trigger('input', '.qty-input');
    $('#productTable tbody tr').each(function() {
        $(this).find('.qty-input').trigger('input');
    });
}
</script>
@endpush
