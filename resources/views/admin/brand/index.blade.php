@extends('admin.layouts.app')
@section('title', 'Brand Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Brand List</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.brand.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> New Brand
                    </a>
                </div>
            </div>
            <div class="card-body">
                @include('admin.layouts._message')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="brandTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Logo</th>
                                <th>Banner</th>
                                <th>Country</th>
                                <th>Status</th>
                                <th>Tools</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($brands as $brand)
                            <tr>
                                <td>{{ $brand->id }}</td>
                                <td>{{ $brand->name }}</td>
                                <td>{{ $brand->slug }}</td>
                                <td>
                                    @if($brand->logo && Storage::disk('public')->exists($brand->logo))
                                        <img src="{{ asset('storage/' . $brand->logo) }}" width="60" height="40" style="object-fit:cover;" />
                                    @else
                                        <span class="text-muted">No image</span>
                                    @endif
                                </td>
                                <td>
                                    @if($brand->banner && Storage::disk('public')->exists($brand->banner))
                                        <img src="{{ asset('storage/' . $brand->banner) }}" width="80" height="40" style="object-fit:cover;" />
                                    @else
                                        <span class="text-muted">No image</span>
                                    @endif
                                </td>
                                <td>{{ $brand->country ?? 'N/A' }}</td>
                                <td>
                                    @if($brand->status == 'active')
                                        <span class="badge badge-success">Active</span>
                                    @elseif($brand->status == 'inactive')
                                        <span class="badge badge-warning">Inactive</span>
                                    @else
                                        <span class="badge badge-danger">Archived</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.brand.edit', $brand->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.brand.destroy', $brand->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this brand?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                            @if($brands->isEmpty())
                            <tr>
                                <td colspan="8" class="text-center">No brands found.</td>
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
