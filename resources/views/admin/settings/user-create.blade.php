@extends('admin.layouts.app')

@section('title', 'Create User')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">User Information</h3>
                    </div>
                    <form method="POST" action="{{ route('admin.settings.users.store') }}">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input id="name" type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="Enter name" required>
                                @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="Enter email" required>
                                @error('email')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="password">Password <span class="text-danger">*</span></label>
                                <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter password" required>
                                @error('password')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="Confirm password" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input id="phone" type="text" name="phone" value="{{ old('phone') }}" class="form-control" placeholder="Enter phone">
                                @error('phone')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea id="address" name="address" rows="2" class="form-control" placeholder="Enter address">{{ old('address') }}</textarea>
                                @error('address')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="date_of_birth">Date of Birth</label>
                                <input id="date_of_birth" type="text" name="date_of_birth" value="{{ old('date_of_birth') }}" class="form-control datepicker" placeholder="dd-mm-yyyy" autocomplete="off">
                                @error('date_of_birth')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select id="gender" name="gender" class="form-control">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="roles">Roles</label>
                                <select name="roles[]" id="roles" class="form-control js-select2" multiple="multiple" style="width: 100%;">
                                    @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('roles') && in_array($role->name, old('roles')) ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('roles')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="permissions">Direct Permissions</label>
                                <select name="permissions[]" id="permissions" class="form-control js-select2" multiple="multiple" style="width: 100%;">
                                    @foreach($permissions as $permission)
                                    <option value="{{ $permission->name }}" {{ old('permissions') && in_array($permission->name, old('permissions')) ? 'selected' : '' }}>
                                        {{ $permission->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('permissions')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success">Create User</button>
                            <a href="{{ route('admin.settings.users.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/jquery-ui/jquery-ui.css') }}">
@endpush

@section('js')
<script src="{{ asset('assets/admin/plugins/jquery-ui/jquery-ui.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if ($.fn.select2) {
        $('.js-select2').select2({ placeholder: 'Select...', allowClear: true });
    }
    $('.datepicker').datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '-100:+0',
        maxDate: new Date(),
    });
});
</script>
@endsection