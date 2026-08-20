@extends('admin.layouts.app')

@section('title', 'Customer Details')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Customer Details</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Customer Information</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="40%">Customer Code</th>
                                        <td><span class="badge bg-info">{{ $customer->customer_code }}</span></td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            @if($customer->status === 'active')
                                            <span class="badge bg-success">Active</span>
                                            @elseif($customer->status === 'inactive')
                                            <span class="badge bg-warning">Inactive</span>
                                            @else
                                            <span class="badge bg-danger">Banned</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Gender</th>
                                        <td>{{ ucfirst($customer->gender ?? '-') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Date of Birth</th>
                                        <td>{{ $customer->date_of_birth ? \Carbon\Carbon::parse($customer->date_of_birth)->format('F d, Y') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Marketing Opt-in</th>
                                        <td>{{ $customer->marketing_opt_in ? 'Yes' : 'No' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Notes</th>
                                        <td>{{ $customer->notes ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created At</th>
                                        <td>{{ $customer->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Updated At</th>
                                        <td>{{ $customer->updated_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4>User Information</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="40%">Name</th>
                                        <td>{{ $customer->customer->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $customer->customer->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone</th>
                                        <td>{{ $customer->customer->phone ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Avatar</th>
                                        <td>
                                            @if($customer->customer->avatar)
                                            <img src="{{ asset('storage/' . $customer->customer->avatar) }}" alt="Avatar" class="img-thumbnail" width="80">
                                            @else
                                            <span class="badge bg-secondary">No Avatar</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Registered</th>
                                        <td>{{ $customer->customer->created_at->format('M d, Y') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($customer->customer->wallet)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h4>Wallet Information</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="40%">Balance</th>
                                        <td><strong>{{ number_format($customer->customer->wallet->balance, 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Locked Balance</th>
                                        <td>{{ number_format($customer->customer->wallet->locked_balance, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            @if($customer->customer->wallet->status === 'active')
                                            <span class="badge bg-success">Active</span>
                                            @elseif($customer->customer->wallet->status === 'inactive')
                                            <span class="badge bg-warning">Inactive</span>
                                            @else
                                            <span class="badge bg-danger">Frozen</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        @endif

                        @if($customer->customer->addresses->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h4>Addresses ({{ $customer->user->addresses->count() }})</h4>
                                <div class="row">
                                    @foreach($customer->customer->addresses as $address)
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5>{{ $address->recipient_name }}</h5>
                                                <p class="mb-1">{{ $address->address_line_1 }}</p>
                                                @if($address->address_line_2)
                                                <p class="mb-1">{{ $address->address_line_2 }}</p>
                                                @endif
                                                <p class="mb-1">{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
                                                <p class="mb-1">{{ $address->country }}</p>
                                                <p class="mb-0"><strong>Phone:</strong> {{ $address->phone }}</p>
                                                @if($address->is_default)
                                                <span class="badge bg-primary">Default</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection