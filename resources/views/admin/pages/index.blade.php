@extends('admin.layouts.app')

@section('title', 'Pages')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">All Pages</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.pages.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add New Page
                            </a>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th width="50">Order</th>
                                    <th>Title</th>
                                    <th>Slug</th>
                                    <th>Status</th>
                                    <th width="180">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pages as $page)
                                <tr>
                                    <td>{{ $page->sort_order }}</td>
                                    <td>
                                        <strong>{{ $page->title }}</strong>
                                        @if($page->meta_title)
                                        <br><small class="text-muted">{{ $page->meta_title }}</small>
                                        @endif
                                    </td>
                                    <td><code>/page/{{ $page->slug }}</code></td>
                                    <td>
                                        @if($page->status === 'published')
                                            <span class="badge badge-success">Published</span>
                                        @else
                                            <span class="badge badge-secondary">Draft</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.pages.destroy', $page) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Delete this page?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        No pages found.
                                        <a href="{{ route('admin.pages.create') }}">Create one</a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
