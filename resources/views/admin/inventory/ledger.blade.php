@extends('admin.layouts.app')
@section('title', 'Inventory Ledger')

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
                <form method="GET" action="{{ route('admin.inventory.ledger') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Transaction Type</label>
                                <select name="type" class="form-control form-control-sm select2" style="width:100%">
                                    <option value="">All Types</option>
                                    <option value="purchase" {{ request('type') === 'purchase' ? 'selected' : '' }}>Purchase</option>
                                    <option value="sale" {{ request('type') === 'sale' ? 'selected' : '' }}>Sale</option>
                                    <option value="return" {{ request('type') === 'return' ? 'selected' : '' }}>Return</option>
                                    <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                                    <option value="transfer_in" {{ request('type') === 'transfer_in' ? 'selected' : '' }}>Transfer In</option>
                                    <option value="transfer_out" {{ request('type') === 'transfer_out' ? 'selected' : '' }}>Transfer Out</option>
                                    <option value="damage" {{ request('type') === 'damage' ? 'selected' : '' }}>Damage</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Warehouse</label>
                                <select name="warehouse_id" class="form-control form-control-sm select2" style="width:100%">
                                    <option value="">All Warehouses</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>From Date</label>
                                <input type="text" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm datepicker" placeholder="dd-mm-yyyy" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>To Date</label>
                                <input type="text" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm datepicker" placeholder="dd-mm-yyyy" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="form-group w-100">
                                <button type="submit" class="btn btn-primary btn-sm btn-block"><i class="fas fa-search"></i> Search</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-book mr-1"></i> Ledger Entries</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Warehouse</th>
                                <th>Type</th>
                                <th class="text-right">Before</th>
                                <th class="text-right">Change</th>
                                <th class="text-right">After</th>
                                <th>Reference</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $txn)
                            <tr>
                                <td class="text-nowrap">{{ $txn->created_at->format('d-m-Y H:i') }}</td>
                                <td>{{ $txn->product?->name ?? 'N/A' }}</td>
                                <td>{{ $txn->warehouse?->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-{{ $txn->isInbound() ? 'success' : 'danger' }}">
                                        {{ ucfirst(str_replace('_', ' ', $txn->type)) }}
                                    </span>
                                </td>
                                <td class="text-right">{{ number_format($txn->quantity_before) }}</td>
                                <td class="text-right font-weight-bold {{ $txn->quantity_change >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $txn->quantity_change >= 0 ? '+' : '' }}{{ number_format($txn->quantity_change) }}
                                </td>
                                <td class="text-right">{{ number_format($txn->quantity_after) }}</td>
                                <td>{{ $txn->reference_number ?? ($txn->reason ?? '—') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="fas fa-book fa-3x mb-3 d-block"></i>
                                    No transactions found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($transactions->hasPages())
            <div class="card-footer clearfix">{{ $transactions->links() }}</div>
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
