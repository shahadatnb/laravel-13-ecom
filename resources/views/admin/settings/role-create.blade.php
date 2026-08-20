@extends('admin.layouts.app')

@section('title', 'Create Role')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Role Information</h3>
                    </div>
                    <form method="POST" action="{{ route('admin.settings.roles.store') }}">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Role Name <span class="text-danger">*</span></label>
                                <input id="name" type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="Enter role name" required>
                                @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="permissions">Permissions</label>
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
                            <button type="submit" class="btn btn-success">Create Role</button>
                            <a href="{{ route('admin.settings.roles.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if ($.fn.select2) {
        $('.js-select2').select2({ placeholder: 'Select permissions', allowClear: true });
    }
});
</script>
@endsection