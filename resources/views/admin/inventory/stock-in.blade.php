@extends('admin.layouts.app')
@section('title', 'Add Stock')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.css') }}">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-plus-circle text-success mr-1"></i> Add Stock</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.inventory.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
            </div>
            <form action="{{ route('admin.inventory.stock-in') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Product <span class="text-danger">*</span></label>
                                <select name="product_id" id="product_id" class="form-control select2 @error('product_id') is-invalid @enderror" style="width:100%">
                                    <option value="">Select product...</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}" {{ old('product_id', request('product_id')) == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->sku ?? 'No SKU' }})</option>
                                    @endforeach
                                </select>
                                @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Variant</label>
                                <select name="product_variant_id" id="product_variant_id" class="form-control select2 @error('product_variant_id') is-invalid @enderror" style="width:100%" disabled>
                                    <option value="">Select product first...</option>
                                </select>
                                @error('product_variant_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" class="form-control @error('quantity') is-invalid @enderror">
                                @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Unit Cost</label>
                                <input type="number" name="unit_cost" value="{{ old('unit_cost') }}" step="0.01" min="0" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Reference Number</label>
                                <input type="text" name="reference_number" value="{{ old('reference_number') }}" class="form-control" placeholder="PO-001, INV-001, etc.">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Reason</label>
                                <input type="text" name="reason" value="{{ old('reason') }}" class="form-control" placeholder="Purchase received, return, etc.">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('admin.inventory.index') }}" class="btn btn-default">Cancel</a>
                    <button type="submit" class="btn btn-success ml-2">
                        <i class="fas fa-plus mr-1"></i> Add Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const productsWithVariants = {!! $productsWithVariants !!};

$(function () {
    $('.select2').select2({ theme: 'bootstrap4' });

    const $product = $('#product_id');
    const $variant = $('#product_variant_id');

    function loadVariants(productId) {
        $variant.empty().prop('disabled', true);
        if (!productId) {
            $variant.append('<option value="">Select product first...</option>');
            $variant.select2({ theme: 'bootstrap4' });
            return;
        }

        const product = productsWithVariants.find(p => String(p.id) === String(productId));
        if (product && product.variants.length > 0) {
            $variant.append('<option value="">Select variant...</option>');
            product.variants.forEach(v => {
                $variant.append(`<option value="${v.id}">${v.name} (${v.sku || 'No SKU'})</option>`);
            });
            $variant.prop('disabled', false);
        } else {
            $variant.append('<option value="">No variants available</option>');
            $variant.prop('disabled', true);
        }
        $variant.select2({ theme: 'bootstrap4' });
    }

    $product.on('change', function () {
        loadVariants($(this).val());
    });

    @if(old('product_id', request('product_id')))
        loadVariants('{{ old('product_id', request('product_id')) }}');
    @endif
});
</script>
@endpush
