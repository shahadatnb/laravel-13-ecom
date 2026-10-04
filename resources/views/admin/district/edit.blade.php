@extends('admin.layouts.app')
@section('title', 'Edit District')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit District</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.districts.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                @include('admin.layouts._message')

                <form method="POST" action="{{ route('admin.districts.update', $district->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name">District Name <span class="text-danger">*</span></label>
                        <input id="name" name="name" type="text" value="{{ old('name', $district->name) }}" required class="form-control" placeholder="e.g. Gazipur" />
                        @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="delivery_zone_id">Delivery Zone <span class="text-danger">*</span></label>
                        <select id="delivery_zone_id" name="delivery_zone_id" class="form-control" required>
                            <option value="">Select Zone</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}" {{ old('delivery_zone_id', $district->delivery_zone_id) == $zone->id ? 'selected' : '' }}>
                                    {{ $zone->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">The zone that determines the delivery charge for this district.</small>
                        @error('delivery_zone_id')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="status">Status <span class="text-danger">*</span></label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="active" {{ old('status', $district->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $district->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        <small class="text-muted">Inactive districts are hidden from the frontend checkout.</small>
                        @error('status')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Update</button>
                        <a href="{{ route('admin.districts.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
