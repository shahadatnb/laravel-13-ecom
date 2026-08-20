@extends('admin.layouts.app')
@section('title', 'Create Warehouse')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-plus-circle mr-1"></i> New Warehouse</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.inventory.warehouses') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
            </div>
            <form action="{{ route('admin.inventory.warehouse-store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label>Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" value="{{ old('code') }}" class="form-control @error('code') is-invalid @enderror font-weight-bold" placeholder="WH-01">
                                @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" name="address" value="{{ old('address') }}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Notes</label>
                                <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>City</label>
                                        <input type="text" name="city" value="{{ old('city') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>State</label>
                                        <input type="text" name="state" value="{{ old('state') }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Country</label>
                                        <input type="text" name="country" value="{{ old('country') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Zip Code</label>
                                        <input type="text" name="zip_code" value="{{ old('zip_code') }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Phone</label>
                                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="email" value="{{ old('email') }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Manager Name</label>
                                <input type="text" name="manager_name" value="{{ old('manager_name') }}" class="form-control">
                            </div>
                            <div class="form-group mb-0">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="is_default" value="1" class="custom-control-input" id="is_default" {{ old('is_default') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_default">Set as Default Warehouse</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('admin.inventory.warehouses') }}" class="btn btn-default">Cancel</a>
                    <button type="submit" class="btn btn-primary ml-2">
                        <i class="fas fa-save mr-1"></i> Create Warehouse
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
