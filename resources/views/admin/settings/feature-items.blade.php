@extends('admin.layouts.app')

@section('title', 'Feature Items')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-star mr-1"></i> Feature Items</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal">
                                <i class="fas fa-plus mr-1"></i> Add Item
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @include('admin.layouts._message')
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th style="width:60px">Icon</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th style="width:120px" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $index => $item)
                                <tr>
                                    <td class="text-center text-2xl">{{ $item['icon'] ?? '⭐' }}</td>
                                    <td><strong>{{ $item['title'] ?? '' }}</strong></td>
                                    <td>{{ $item['description'] ?? '' }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-info btn-sm" data-toggle="modal"
                                            data-target="#editModal{{ $index }}" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.settings.feature-items.destroy', $index) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('Delete this feature item?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal{{ $index }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form action="{{ route('admin.settings.feature-items.update', $index) }}" method="POST">
                                            @csrf @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Edit Feature Item</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Icon (emoji)</label>
                                                        <input type="text" name="icon" class="form-control"
                                                            value="{{ $item['icon'] ?? '' }}" required maxlength="10">
                                                        <small class="text-muted">e.g. 🚚 🔒 ↩️ 💬 🎁 ⭐</small>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Title</label>
                                                        <input type="text" name="title" class="form-control"
                                                            value="{{ $item['title'] ?? '' }}" required maxlength="100">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Description</label>
                                                        <input type="text" name="description" class="form-control"
                                                            value="{{ $item['description'] ?? '' }}" required maxlength="255">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success">Update</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        No feature items yet. Click "Add Item" to create one.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle mr-1"></i> Help</h3>
                    </div>
                    <div class="card-body">
                        <p>Feature items are displayed on the homepage as trust badges (e.g., Free Shipping, Secure Payment).</p>
                        <hr>
                        <p><strong>Common Icons:</strong></p>
                        <ul class="list-unstyled">
                            <li>🚚 Free Shipping</li>
                            <li>🔒 Secure Payment</li>
                            <li>↩️ Easy Returns</li>
                            <li>💬 24/7 Support</li>
                            <li>🎁 Gift Wrapping</li>
                            <li>⭐ Quality Guarantee</li>
                            <li>🚚 Fast Delivery</li>
                            <li>💳 Cash on Delivery</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.settings.feature-items.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Feature Item</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Icon (emoji)</label>
                        <input type="text" name="icon" class="form-control" required maxlength="10"
                            placeholder="e.g. 🚚">
                        <small class="text-muted">Copy-paste an emoji from the list on the right.</small>
                    </div>
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" required maxlength="100"
                            placeholder="e.g. Free Shipping">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <input type="text" name="description" class="form-control" required maxlength="255"
                            placeholder="e.g. Free shipping on all orders over ৳5000">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Item</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
