@extends('admin.layouts.app')
@section('title', 'Category Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Category List</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.category.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> New Category
                    </a>
                </div>
            </div>
            <div class="card-body">
                @include('admin.layouts._message')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="categoryTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Name BN</th>
                                <th>Parent</th>
                                <th>Slug</th>
                                <th>Status</th>
                                <th>Featured</th>
                                <th>Tools</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->name_bn ?? 'N/A' }}</td>
                                <td>{{ $category->parent->name ?? 'Root' }}</td>
                                <td>{{ $category->slug }}</td>
                                <td>
                                    @if($category->status == 'active')
                                        <span class="badge badge-success">Active</span>
                                    @elseif($category->status == 'inactive')
                                        <span class="badge badge-warning">Inactive</span>
                                    @else
                                        <span class="badge badge-danger">Archived</span>
                                    @endif
                                </td>
                                <td>
                                    @if($category->featured)
                                        <span class="badge badge-info">Featured</span>
                                    @else
                                        <span class="badge badge-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.category.edit', $category->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.category.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                            @if($categories->isEmpty())
                            <tr>
                                <td colspan="8" class="text-center">No categories found.</td>
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
