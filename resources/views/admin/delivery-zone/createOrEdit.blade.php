@extends('admin.layouts.app')
@section('title', empty($deliveryZone) ? 'Create Delivery Zone' : 'Edit Delivery Zone')

@php
$allDistricts = [
    'Dhaka', 'Faridpur', 'Gazipur', 'Gopalganj', 'Jamalpur', 'Kishoreganj',
    'Madaripur', 'Manikganj', 'Munshiganj', 'Mymensingh', 'Narayanganj',
    'Narsingdi', 'Netrokona', 'Rajbari', 'Shariatpur', 'Sherpur', 'Tangail',
    'Bandarban', 'Brahmanbaria', 'Chandpur', 'Chittagong', 'Comilla',
    'Cox\'s Bazar', 'Feni', 'Khagrachhari', 'Lakshmipur', 'Noakhali', 'Rangamati',
    'Bagerhat', 'Chuadanga', 'Jessore', 'Jhenaidah', 'Khulna', 'Kushtia',
    'Magura', 'Meherpur', 'Narail', 'Satkhira',
    'Bogra', 'Joypurhat', 'Naogaon', 'Natore', 'Nawabganj', 'Pabna',
    'Rajshahi', 'Sirajganj',
    'Dinajpur', 'Gaibandha', 'Kurigram', 'Lalmonirhat', 'Nilphamari',
    'Panchagarh', 'Rangpur', 'Thakurgaon',
    'Habiganj', 'Maulvibazar', 'Sunamganj', 'Sylhet',
    'Barguna', 'Barisal', 'Bhola', 'Jhalokati', 'Patuakhali', 'Pirojpur',
];

$selectedDistricts = old('districts', !empty($deliveryZone) ? $deliveryZone->districts->pluck('name')->toArray() : []);
@endphp

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ empty($deliveryZone) ? 'Create Delivery Zone' : 'Edit Delivery Zone' }}</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.delivery-zones.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                @include('admin.layouts._message')

                <form method="POST" action="{{ empty($deliveryZone) ? route('admin.delivery-zones.store') : route('admin.delivery-zones.update', $deliveryZone->id) }}">
                    @csrf
                    @if(!empty($deliveryZone))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Zone Name <span class="text-danger">*</span></label>
                                <input id="name" name="name" type="text" value="{{ old('name', $deliveryZone->name ?? '') }}" required class="form-control" placeholder="e.g. Inside Dhaka" />
                                @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="charge">Delivery Charge ($) <span class="text-danger">*</span></label>
                                <input id="charge" name="charge" type="number" step="0.01" min="0" value="{{ old('charge', $deliveryZone->charge ?? '') }}" required class="form-control" placeholder="e.g. 10.00" />
                                <small class="text-muted">The delivery fee charged to customers in this zone.</small>
                                @error('charge')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="minimum_order_amount">Minimum Order for Free Delivery ($)</label>
                                <input id="minimum_order_amount" name="minimum_order_amount" type="number" step="0.01" min="0" value="{{ old('minimum_order_amount', $deliveryZone->minimum_order_amount ?? '') }}" class="form-control" placeholder="e.g. 100.00" />
                                <small class="text-muted">Orders above this amount get free delivery. Leave empty to always charge.</small>
                                @error('minimum_order_amount')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Select Districts <span class="text-danger">*</span></label>
                        <small class="text-muted d-block mb-2">Choose all districts that belong to this delivery zone.</small>
                        <div class="row">
                            @php
                                $divisions = [
                                    'Dhaka Division' => ['Dhaka', 'Faridpur', 'Gazipur', 'Gopalganj', 'Jamalpur', 'Kishoreganj', 'Madaripur', 'Manikganj', 'Munshiganj', 'Mymensingh', 'Narayanganj', 'Narsingdi', 'Netrokona', 'Rajbari', 'Shariatpur', 'Sherpur', 'Tangail'],
                                    'Chittagong Division' => ['Bandarban', 'Brahmanbaria', 'Chandpur', 'Chittagong', 'Comilla', 'Cox\'s Bazar', 'Feni', 'Khagrachhari', 'Lakshmipur', 'Noakhali', 'Rangamati'],
                                    'Khulna Division' => ['Bagerhat', 'Chuadanga', 'Jessore', 'Jhenaidah', 'Khulna', 'Kushtia', 'Magura', 'Meherpur', 'Narail', 'Satkhira'],
                                    'Rajshahi Division' => ['Bogra', 'Joypurhat', 'Naogaon', 'Natore', 'Nawabganj', 'Pabna', 'Rajshahi', 'Sirajganj'],
                                    'Rangpur Division' => ['Dinajpur', 'Gaibandha', 'Kurigram', 'Lalmonirhat', 'Nilphamari', 'Panchagarh', 'Rangpur', 'Thakurgaon'],
                                    'Sylhet Division' => ['Habiganj', 'Maulvibazar', 'Sunamganj', 'Sylhet'],
                                    'Barisal Division' => ['Barguna', 'Barisal', 'Bhola', 'Jhalokati', 'Patuakhali', 'Pirojpur'],
                                ];
                            @endphp
                            @foreach($divisions as $division => $districts)
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header py-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input division-toggle" id="div_{{ Str::slug($division) }}" data-division="{{ $division }}">
                                            <label class="custom-control-label font-weight-bold" for="div_{{ Str::slug($division) }}" style="font-size:13px;">{{ $division }}</label>
                                        </div>
                                    </div>
                                    <div class="card-body py-2" style="max-height:200px;overflow-y:auto;">
                                        <div class="row">
                                            @foreach($districts as $district)
                                            <div class="col-6">
                                                <div class="custom-control custom-checkbox mb-1">
                                                    <input type="checkbox"
                                                           class="custom-control-input district-checkbox"
                                                           id="district_{{ Str::slug($district) }}"
                                                           name="districts[]"
                                                           value="{{ $district }}"
                                                           data-division="{{ $division }}"
                                                           {{ in_array($district, $selectedDistricts) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="district_{{ Str::slug($district) }}" style="font-size:12px;">{{ $district }}</label>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @error('districts')<span class="text-danger">{{ $message }}</span>@enderror
                        @error('districts.*')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select id="status" name="status" class="form-control" required>
                                    <option value="">Select Status</option>
                                    <option value="active" {{ old('status', $deliveryZone->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $deliveryZone->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-success">{{ empty($deliveryZone) ? 'Create' : 'Update' }}</button>
                        <a href="{{ route('admin.delivery-zones.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Division toggle — check/uncheck all districts in a division
    document.querySelectorAll('.division-toggle').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var division = this.getAttribute('data-division');
            var checked = this.checked;
            document.querySelectorAll('.district-checkbox[data-division="' + division + '"]').forEach(function(dc) {
                dc.checked = checked;
            });
        });
    });

    // When any district changes, update its division toggle state
    document.querySelectorAll('.district-checkbox').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var division = this.getAttribute('data-division');
            var allInDiv = document.querySelectorAll('.district-checkbox[data-division="' + division + '"]');
            var allChecked = true;
            allInDiv.forEach(function(dc) {
                if (!dc.checked) allChecked = false;
            });
            var divToggle = document.querySelector('.division-toggle[data-division="' + division + '"]');
            if (divToggle) {
                divToggle.checked = allChecked;
            }
        });
    });
});
</script>
@endpush
