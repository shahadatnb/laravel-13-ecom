@extends('admin.layouts.app')
@section('title', 'New Stock Transfer')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.css') }}">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-exchange-alt mr-1"></i> New Transfer</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.inventory.transfers') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
            </div>
            <form action="{{ route('admin.inventory.transfer-store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>From Warehouse <span class="text-danger">*</span></label>
                                <select name="from_warehouse_id" class="form-control select2 @error('from_warehouse_id') is-invalid @enderror" style="width:100%">
                                    <option value="">Select source...</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}" {{ old('from_warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                    @endforeach
                                </select>
                                @error('from_warehouse_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>To Warehouse <span class="text-danger">*</span></label>
                                <select name="to_warehouse_id" class="form-control select2 @error('to_warehouse_id') is-invalid @enderror" style="width:100%">
                                    <option value="">Select destination...</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}" {{ old('to_warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                    @endforeach
                                </select>
                                @error('to_warehouse_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Product <span class="text-danger">*</span></label>
                                <select name="product_id" class="form-control select2 @error('product_id') is-invalid @enderror" style="width:100%">
                                    <option value="">Select product...</option>
                                    @foreach(\App\Models\Product::select('id', 'name', 'sku')->get() as $p)
                                        <option value="{{ $p->id }}" {{ old('product_id', request('product_id')) == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->sku ?? 'No SKU' }})</option>
                                    @endforeach
                                </select>
                                @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Notes</label>
                                <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                    @error('error')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('admin.inventory.transfers') }}" class="btn btn-default">Cancel</a>
                    <button type="submit" class="btn btn-primary ml-2">
                        <i class="fas fa-exchange-alt mr-1"></i> Initiate Transfer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/admin/plugins/select2/js/select2.min.js') }}"></script>
<script>
$(function () { $('.select2').select2({ theme: 'bootstrap4' }); });
</script>
@endpush
