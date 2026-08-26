@extends('admin.layouts.app')
@section('title', 'Coupon Management')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/jquery-ui/jquery-ui.css') }}">
<style>
    .small-box { border-radius: .25rem; }
    .small-box>.inner h3 { font-size: 2rem; }
    .small-box .icon>i { font-size: 50px; }
    .filter-form .form-group { margin-bottom: 0; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Stats Row --}}
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ number_format($stats['total']) }}</h3>
                        <p>Total Coupons</p>
                    </div>
                    <div class="icon"><i class="fas fa-ticket-alt"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ number_format($stats['active']) }}</h3>
                        <p>Active</p>
                    </div>
                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ number_format($stats['expired']) }}</h3>
                        <p>Expired</p>
                    </div>
                    <div class="icon"><i class="fas fa-clock"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ format_currency($stats['total_discount'], 2) }}</h3>
                        <p>Total Discount</p>
                    </div>
                    <div class="icon"><i class="fas fa-percentage"></i></div>
                </div>
            </div>
        </div>

        {{-- Filter Card --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filters</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body filter-form">
                <form method="GET" action="{{ route('admin.coupons.index') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-sm">Search</label>
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Code or title...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="text-sm">Status</label>
                                <select name="status" class="form-control form-control-sm select2" style="width:100%">
                                    <option value="">All</option>
                                    @foreach($statuses as $s)
                                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="text-sm">Type</label>
                                <select name="type" class="form-control form-control-sm select2" style="width:100%">
                                    <option value="">All</option>
                                    @foreach($types as $t)
                                        <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $t)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="text-sm">From Date</label>
                                <input type="text" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm datepicker" placeholder="dd-mm-yyyy" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="text-sm">To Date</label>
                                <input type="text" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm datepicker" placeholder="dd-mm-yyyy" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <div class="form-group w-100">
                                <button type="submit" class="btn btn-primary btn-sm btn-block">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Coupons Table --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Coupons</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Create Coupon
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
                                <th>Title</th>
                                <th>Type</th>
                                <th>Discount</th>
                                <th>Usage</th>
                                <th>Valid</th>
                                <th>Status</th>
                                <th class="text-center" style="width:120px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($coupons as $coupon)
                            <tr>
                                <td><span class="font-weight-bold text-primary">{{ $coupon->code }}</span></td>
                                <td>
                                    {{ $coupon->title }}
                                    @if($coupon->description)
                                        <br><small class="text-muted">{{ Str::limit($coupon->description, 60) }}</small>
                                    @endif
                                </td>
                                <td><span class="text-muted">{{ $coupon->getTypeLabel() }}</span></td>
                                <td>
                                    @if($coupon->isPercentage())
                                        <span class="font-weight-bold">{{ number_format($coupon->discount_value, 0) }}%</span>
                                    @elseif($coupon->isFreeShipping())
                                        <span class="font-weight-bold text-info">Free Shipping</span>
                                    @else
                                        <span class="font-weight-bold">{{ format_currency($coupon->discount_value, 2) }}</span>
                                    @endif
                                    @if($coupon->max_discount)
                                        <br><small class="text-muted">Max: {{ format_currency($coupon->max_discount, 2) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="font-weight-bold">{{ $coupon->total_used }}</span>
                                    @if($coupon->usage_limit)
                                        <small class="text-muted">/ {{ $coupon->usage_limit }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($coupon->valid_from)
                                        <small>From: {{ $coupon->valid_from->format('d-m-Y') }}</small><br>
                                    @endif
                                    @if($coupon->valid_until)
                                        <small>Until: {{ $coupon->valid_until->format('d-m-Y') }}</small>
                                    @endif
                                    @if(!$coupon->valid_from && !$coupon->valid_until)
                                        <small class="text-muted">No expiry</small>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $badgeMap = [
                                            'active' => 'success',
                                            'draft' => 'secondary',
                                            'inactive' => 'warning',
                                            'expired' => 'danger',
                                            'cancelled' => 'danger',
                                        ];
                                        $badge = $badgeMap[$coupon->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge badge-{{ $badge }}">{{ $coupon->getStatusLabel() }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.coupons.show', $coupon) }}" class="btn btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.coupons.toggle-status', $coupon) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-{{ $coupon->status === 'active' ? 'warning' : 'success' }}"
                                                title="{{ $coupon->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                                <i class="fas {{ $coupon->status === 'active' ? 'fa-pause' : 'fa-play' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Delete this coupon permanently?')">
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
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="fas fa-ticket-alt fa-3x mb-3 d-block text-muted"></i>
                                    No coupons found.
                                    <a href="{{ route('admin.coupons.create') }}" class="text-primary ml-1">Create your first coupon</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($coupons->hasPages())
            <div class="card-footer clearfix">
                {{ $coupons->links() }}
            </div>
            @endif
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/admin/plugins/jquery-ui/jquery-ui.js') }}"></script>
<script src="{{ asset('assets/admin/plugins/moment/moment-with-locales.js') }}"></script>
<script src="{{ asset('assets/admin/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script>
$(function () {
    $('.datepicker').datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '-10:+10',
    });

    $('.select2').select2({
        theme: 'bootstrap4',
        minimumResultsForSearch: -1,
    });
});
</script>
@endpush