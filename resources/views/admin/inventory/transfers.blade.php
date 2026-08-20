@extends('admin.layouts.app')
@section('title', 'Stock Transfers')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/jquery-ui/jquery-ui.css') }}">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filters</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.inventory.transfers') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control form-control-sm select2" style="width:100%">
                                    <option value="">All Statuses</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_transit" {{ request('status') === 'in_transit' ? 'selected' : '' }}>In Transit</option>
                                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>From Date</label>
                                <input type="text" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm datepicker" placeholder="dd-mm-yyyy" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>To Date</label>
                                <input type="text" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm datepicker" placeholder="dd-mm-yyyy" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-group w-100">
                                <button type="submit" class="btn btn-primary btn-sm btn-block"><i class="fas fa-search"></i> Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-exchange-alt mr-1"></i> All Transfers</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.inventory.transfer-create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> New Transfer
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Transfer #</th>
                                <th>Product</th>
                                <th>From</th>
                                <th>To</th>
                                <th class="text-right">Qty</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width:120px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transfers as $t)
                            <tr>
                                <td><span class="font-weight-bold text-primary">{{ $t->transfer_number }}</span></td>
                                <td>{{ $t->product?->name }}</td>
                                <td>{{ $t->fromWarehouse?->name }}</td>
                                <td>{{ $t->toWarehouse?->name }}</td>
                                <td class="text-right font-weight-bold">{{ number_format($t->quantity) }}</td>
                                <td class="text-center">
                                    @php
                                        $badgeMap = ['pending' => 'warning', 'in_transit' => 'info', 'completed' => 'success', 'cancelled' => 'danger'];
                                    @endphp
                                    <span class="badge badge-{{ $badgeMap[$t->status] ?? 'secondary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $t->status)) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($t->status === 'pending')
                                        <div class="btn-group btn-group-sm">
                                            <form action="{{ route('admin.inventory.transfer-complete', $t) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success" title="Complete"><i class="fas fa-check"></i></button>
                                            </form>
                                            <form action="{{ route('admin.inventory.transfer-cancel', $t) }}" method="POST" class="d-inline" onsubmit="return confirm('Cancel this transfer?')">
                                                @csrf
                                                <button type="submit" class="btn btn-danger" title="Cancel"><i class="fas fa-times"></i></button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="fas fa-exchange-alt fa-3x mb-3 d-block"></i>
                                    No transfers yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($transfers->hasPages())
            <div class="card-footer clearfix">{{ $transfers->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/admin/plugins/jquery-ui/jquery-ui.js') }}"></script>
<script>
$(function () {
    $('.datepicker').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true, yearRange: '-5:+5' });
    $('.select2').select2({ theme: 'bootstrap4', minimumResultsForSearch: -1 });
});
</script>
@endpush
