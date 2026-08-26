@extends('admin.layouts.app')
@section('title', 'Delivery Zones')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Delivery Zones</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.delivery-zones.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> New Zone
                    </a>
                </div>
            </div>
            <div class="card-body">
                @include('admin.layouts._message')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Zone Name</th>
                                <th>Districts</th>
                                <th>Delivery Charge</th>
                                <th>Min. Order (Free)</th>
                                <th>Status</th>
                                <th>Tools</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($zones as $zone)
                            <tr>
                                <td>{{ $zone->id }}</td>
                                <td><strong>{{ $zone->name }}</strong></td>
                                <td>
                                    @if($zone->districts && $zone->districts->count() > 0)
                                        <span class="badge badge-info" style="font-size:11px;">{{ $zone->districts->count() }} districts</span>
                                        <div style="max-width:300px;max-height:100px;overflow-y:auto;" class="mt-1">
                                            @foreach($zone->districts->take(10) as $district)
                                                <span class="badge badge-secondary" style="font-size:10px;margin:1px;">{{ $district->name }}</span>
                                            @endforeach
                                            @if($zone->districts->count() > 10)
                                                <span class="badge badge-light" style="font-size:10px;">+{{ $zone->districts->count() - 10 }} more</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">No districts</span>
                                    @endif
                                </td>
                                <td>{{ format_currency($zone->charge, 2) }}</td>
                                <td>
                                    @if($zone->minimum_order_amount)
                                        {{ format_currency($zone->minimum_order_amount, 2) }}
                                        <small class="text-muted d-block" style="font-size:10px;">(Free above)</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($zone->status == 'active')
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.delivery-zones.edit', $zone->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.delivery-zones.destroy', $zone->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this zone?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                            @if($zones->isEmpty())
                            <tr>
                                <td colspan="7" class="text-center">No delivery zones found.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
