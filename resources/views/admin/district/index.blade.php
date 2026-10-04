@extends('admin.layouts.app')
@section('title', 'Districts')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Districts</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.delivery-zones.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-truck"></i> Delivery Zones
                    </a>
                </div>
            </div>
            <div class="card-body">
                @include('admin.layouts._message')

                <form method="GET" action="{{ route('admin.districts.index') }}" class="mb-3">
                    <div class="input-group" style="max-width:320px;">
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search district..." />
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>District Name</th>
                                <th>Delivery Zone</th>
                                <th>Status</th>
                                <th>Tools</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($districts as $district)
                            <tr>
                                <td>{{ $district->id }}</td>
                                <td><strong>{{ $district->name }}</strong></td>
                                <td>{{ $district->zone->name ?? '—' }}</td>
                                <td>
                                    @if($district->status == 'active')
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.districts.edit', $district->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.districts.toggle-status', $district->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-{{ $district->status === 'active' ? 'warning' : 'success' }}"
                                            title="{{ $district->status === 'active' ? 'Deactivate (hide from frontend)' : 'Activate (show on frontend)' }}">
                                            <i class="fas {{ $district->status === 'active' ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                            @if($districts->isEmpty())
                            <tr>
                                <td colspan="5" class="text-center">No districts found.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{ $districts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
