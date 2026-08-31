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
                                <th>Image</th>
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
                                <td>
                                    @if($category->thumbnail)
                                        <img src="{{ asset('storage/' . $category->thumbnail) }}" alt="{{ $category->name }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>
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
                                    <form action="{{ route('admin.category.update', $category->id) }}" method="POST" class="d-inline featured-toggle-form">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="name" value="{{ $category->name }}" />
                                        <input type="hidden" name="slug" value="{{ $category->slug }}" />
                                        <input type="hidden" name="status" value="{{ $category->status }}" />
                                        <input type="hidden" name="visibility" value="{{ $category->visibility }}" />
                                        <input type="hidden" name="featured" value="{{ $category->featured ? 0 : 1 }}" />
                                        <button type="submit" class="btn btn-sm {{ $category->featured ? 'btn-info' : 'btn-secondary' }} toggle-featured">
                                            <i class="fas {{ $category->featured ? 'fa-star' : 'fa-star-o' }}"></i>
                                            {{ $category->featured ? 'Yes' : 'No' }}
                                        </button>
                                    </form>
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
                                <td colspan="9" class="text-center">No categories found.</td>
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

@section('js')
<script>
$(document).on('submit', '.featured-toggle-form', function(e) {
    e.preventDefault();
    var form = $(this);
    var btn = form.find('.toggle-featured');
    var url = form.attr('action');
    var token = form.find('input[name=_token]').val();
    var featured = form.find('input[name=featured]').val();

    $.ajax({
        url: url,
        type: 'POST',
        data: form.serialize(),
        success: function(response) {
            if (featured === '1') {
                btn.removeClass('btn-secondary').addClass('btn-info');
                btn.find('i').removeClass('fa-star-o').addClass('fa-star');
                btn.find('i').next().text('Yes');
                form.find('input[name=featured]').val('0');
            } else {
                btn.removeClass('btn-info').addClass('btn-secondary');
                btn.find('i').removeClass('fa-star').addClass('fa-star-o');
                btn.find('i').next().text('No');
                form.find('input[name=featured]').val('1');
            }
        },
        error: function() {
            alert('Failed to update featured status.');
        }
    });
});
</script>
@endsection
