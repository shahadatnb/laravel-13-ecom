@extends('admin.layouts.app')

@section('title', 'Customers')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Customer Management</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.customers.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add Customer
                            </a>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif

                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer Code</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Gender</th>
                                    <th>Date of Birth</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                <tr>
                                    <td>{{ $customer->id }}</td>
                                    <td><span class="badge bg-info">{{ $customer->customer_code }}</span></td>
                                <td>{{ $customer->customer->name }}</td>
                                <td>{{ $customer->customer->email }}</td>
                                <td>{{ $customer->customer->phone ?? '-' }}</td>
                                    <td>{{ ucfirst($customer->gender ?? '-') }}</td>
                                    <td>{{ $customer->date_of_birth ? \Carbon\Carbon::parse($customer->date_of_birth)->format('M d, Y') : '-' }}</td>
                                    <td>
                                        @if($customer->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                        @elseif($customer->status === 'inactive')
                                        <span class="badge bg-warning">Inactive</span>
                                        @else
                                        <span class="badge bg-danger">Banned</span>
                                        @endif
                                    </td>
                                    <td>{{ $customer->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this customer?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted">No customers found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="card-footer">
                            {{ $customers->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection