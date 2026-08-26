@extends('admin.layouts.app')

@section('title', 'Create Customer')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/jquery-ui/jquery-ui.css') }}">
@endpush

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Create New Customer</h3>
                    </div>
                    <form action="{{ route('admin.customers.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                                            class="form-control @error('name') is-invalid @enderror"
                                            placeholder="Enter full name" required>
                                        @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                                            class="form-control @error('email') is-invalid @enderror"
                                            placeholder="Enter email address" required>
                                        @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone</label>
                                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            placeholder="Enter phone number">
                                        @error('phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gender">Gender</label>
                                        <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror">
                                            <option value="">Select Gender</option>
                                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('gender')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_of_birth">Date of Birth</label>
                                        <input type="text" name="date_of_birth" id="date_of_birth"
                                            class="form-control datepicker @error('date_of_birth') is-invalid @enderror"
                                            value="{{ old('date_of_birth') }}"
                                            placeholder="dd-mm-yyyy" autocomplete="off">
                                        @error('date_of_birth')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="marketing_opt_in">Marketing Opt-in</label>
                                        <select name="marketing_opt_in" id="marketing_opt_in" class="form-control @error('marketing_opt_in') is-invalid @enderror">
                                            <option value="0" {{ old('marketing_opt_in', false) == false ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ old('marketing_opt_in') == true ? 'selected' : '' }}>Yes</option>
                                        </select>
                                        @error('marketing_opt_in')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            <option value="banned" {{ old('status') == 'banned' ? 'selected' : '' }}>Banned</option>
                                        </select>
                                        @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea name="notes" id="notes" rows="3"
                                          class="form-control @error('notes') is-invalid @enderror"
                                          maxlength="1000" placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                                <small class="text-muted">Max 1000 characters</small>
                                @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Customer
                            </button>
                            <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/admin/plugins/jquery-ui/jquery-ui.js') }}"></script>
<script>
$(function() {
    $('.datepicker').datepicker({
        dateFormat: 'dd-mm-yy',
        yearRange: '-120:+0',
        maxDate: new Date(),
        changeMonth: true,
        changeYear: true
    });
});
</script>
@endpush