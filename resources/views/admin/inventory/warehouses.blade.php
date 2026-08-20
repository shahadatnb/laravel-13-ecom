@extends('admin.layouts.app')
@section('title', 'Warehouses')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="row">
            <div class="col-lg-4 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $warehouses->total() }}</h3>
                        <p>Total Warehouses</p>
                    </div>
                    <div class="icon"><i class="fas fa-warehouse"></i></div>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ \App\Models\Warehouse::where('status', 'active')->count() }}</h3>
                        <p>Active</p>
                    </div>
                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ \App\Models\Warehouse::where('status', 'inactive')->count() }}</h3>
                        <p>Inactive</p>
                    </div>
                    <div class="icon"><i class="fas fa-ban"></i></div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-warehouse mr-1"></i> All Warehouses</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.inventory.warehouse-create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Add Warehouse
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @include('admin.layouts._message')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Location</th>
                                <th>Manager</th>
                                <th class="text-center">Default</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width:100px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($warehouses as $warehouse)
                            <tr>
                                <td><span class="font-weight-bold text-primary">{{ $warehouse->code }}</span></td>
                                <td>{{ $warehouse->name }}</td>
                                <td>{{ $warehouse->city ? $warehouse->city . ($warehouse->state ? ', ' . $warehouse->state : '') : '—' }}</td>
                                <td>{{ $warehouse->manager_name ?? '—' }}</td>
                                <td class="text-center">
                                    @if($warehouse->is_default)
                                        <span class="text-success"><i class="fas fa-check-circle"></i></span>
                                    @else
                                        <span class="text-muted"><i class="far fa-circle"></i></span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $warehouse->isActive() ? 'success' : 'secondary' }}">
                                        {{ ucfirst($warehouse->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.inventory.warehouse-edit', $warehouse) }}" class="btn btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.inventory.warehouse-destroy', $warehouse) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Delete this warehouse?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="fas fa-warehouse fa-3x mb-3 d-block"></i>
                                    No warehouses yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($warehouses->hasPages())
            <div class="card-footer clearfix">{{ $warehouses->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
