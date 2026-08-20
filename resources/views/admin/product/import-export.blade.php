@extends('admin.layouts.app')
@section('title', 'Product Import / Export')

@section('content')
<div class="row">
    {{-- Import Section --}}
    <div class="col-lg-6">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file-import mr-1"></i> Import Products</h3>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('import_success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle mr-1"></i> {{ session('import_success') }}
                    </div>
                @endif

                <form action="{{ route('admin.product.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf
                    <div class="form-group">
                        <label for="csv_file">Select CSV File <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('csv_file') is-invalid @enderror"
                                       id="csv_file" name="csv_file" accept=".csv,.txt" required>
                                <label class="custom-file-label" for="csv_file">Choose CSV file...</label>
                            </div>
                        </div>
                        <small class="form-text text-muted">Max file size: 10MB. Supported formats: .csv, .txt</small>
                    </div>

                    <div class="callout callout-info">
                        <h5><i class="fas fa-info-circle"></i> Import Notes</h5>
                        <ul class="mb-0" style="font-size: 13px;">
                            <li>Products are matched by <strong>SKU</strong> or <strong>Name</strong> — matching products will be updated.</li>
                            <li>New brands and categories are created automatically if they don't exist.</li>
                            <li>The import is wrapped in a transaction — any error rolls back all changes.</li>
                            <li>Download the <a href="{{ route('admin.product.template') }}">template file</a> to see the expected format.</li>
                        </ul>
                    </div>

                    <button type="submit" class="btn btn-primary" id="importBtn">
                        <i class="fas fa-upload mr-1"></i> Import Products
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Export Section --}}
    <div class="col-lg-6">
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file-export mr-1"></i> Export Products</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.product.export') }}" method="GET">
                    <div class="form-group">
                        <label>Brand</label>
                        <select name="brand_id" class="form-control form-control-sm">
                            <option value="">All Brands</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id" class="form-control form-control-sm">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control form-control-sm">
                            <option value="">All Statuses</option>
                            <option value="draft">Draft</option>
                            <option value="pending">Pending</option>
                            <option value="published">Published</option>
                            <option value="hidden">Hidden</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>

                    <div class="d-flex align-items-center justify-content-between">
                        <span class="text-muted">
                            <i class="fas fa-box mr-1"></i> {{ number_format($totalProducts) }} products total
                        </span>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-download mr-1"></i> Export CSV
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Template Download --}}
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file-csv mr-1"></i> Import Template</h3>
            </div>
            <div class="card-body">
                <p class="text-muted" style="font-size: 13px;">
                    Download a sample CSV template with the correct column headers and one example row.
                    Fill in your product data and upload it using the import form above.
                </p>
                <a href="{{ route('admin.product.template') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-download mr-1"></i> Download Template
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Import Progress --}}
<div id="importProgress" class="d-none" style="position:fixed;bottom:20px;right:20px;z-index:9999;">
    <div class="card card-outline card-primary shadow">
        <div class="card-body py-2 px-3 d-flex align-items-center">
            <div class="spinner-border spinner-border-sm text-primary mr-2" role="status"></div>
            <span>Importing products...</span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('importForm').addEventListener('submit', function() {
    document.getElementById('importProgress').classList.remove('d-none');
    document.getElementById('importBtn').disabled = true;
});

document.querySelector('.custom-file-input').addEventListener('change', function(e) {
    var fileName = e.target.files[0].name;
    e.target.nextElementSibling.innerText = fileName;
});
</script>
@endpush
