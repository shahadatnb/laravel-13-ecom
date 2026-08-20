@extends('admin.layouts.app')
@section('title', 'Adjust Stock: ' . ($inventory->product?->name ?? 'N/A'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-sliders-h text-warning mr-1"></i> Adjust Stock: <span class="text-primary font-weight-bold">{{ $inventory->product?->name ?? 'N/A' }}</span></h3>
                <div class="card-tools">
                    <a href="{{ route('admin.inventory.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3 col-6">
                        <div class="text-center p-3 border rounded bg-light">
                            <div class="h3 font-weight-bold">{{ number_format($inventory->current_stock) }}</div>
                            <div class="text-muted small">Current Stock</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-center p-3 border rounded bg-light">
                            <div class="h3 font-weight-bold text-warning">{{ number_format($inventory->reserved_stock) }}</div>
                            <div class="text-muted small">Reserved</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-center p-3 border rounded bg-light">
                            <div class="h3 font-weight-bold {{ $inventory->available_stock <= 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($inventory->available_stock) }}
                            </div>
                            <div class="text-muted small">Available</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-center p-3 border rounded bg-light">
                            <div class="h3 font-weight-bold text-info">{{ number_format($inventory->minimum_stock) }}</div>
                            <div class="text-muted small">Min Stock</div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.inventory.adjust') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $inventory->product_id }}">
                    <input type="hidden" name="warehouse_id" value="{{ $inventory->warehouse_id }}">
                    <input type="hidden" name="product_variant_id" value="{{ $inventory->product_variant_id }}">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>New Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="new_quantity" value="{{ old('new_quantity', $inventory->current_stock) }}" min="0"
                                    class="form-control @error('new_quantity') is-invalid @enderror">
                                @error('new_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="form-text text-muted">Set to 0 to mark as out of stock.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Reason <span class="text-danger">*</span></label>
                                <select name="reason" class="form-control @error('reason') is-invalid @enderror">
                                    <option value="">Select reason...</option>
                                    <option value="correction" {{ old('reason') === 'correction' ? 'selected' : '' }}>Correction</option>
                                    <option value="damage" {{ old('reason') === 'damage' ? 'selected' : '' }}>Damaged Stock</option>
                                    <option value="loss" {{ old('reason') === 'loss' ? 'selected' : '' }}>Loss</option>
                                    <option value="audit_finding" {{ old('reason') === 'audit_finding' ? 'selected' : '' }}>Audit Finding</option>
                                    <option value="return" {{ old('reason') === 'return' ? 'selected' : '' }}>Return</option>
                                    <option value="opening_balance" {{ old('reason') === 'opening_balance' ? 'selected' : '' }}>Opening Balance</option>
                                </select>
                                @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" rows="2" class="form-control">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <a href="{{ route('admin.inventory.index') }}" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-warning ml-2">
                            <i class="fas fa-check mr-1"></i> Apply Adjustment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
