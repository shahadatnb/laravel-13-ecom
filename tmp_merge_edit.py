import os

base = 'D:/laragon/www/laravel-13-sawf/resources/views/admin/product'

# Read all partials
with open(f'{base}/partials/_form-header.blade.php', 'r', encoding='utf-8') as f:
    form_header = f.read()

with open(f'{base}/partials/_basic-info.blade.php', 'r', encoding='utf-8') as f:
    basic_info = f.read()

with open(f'{base}/partials/_pricing.blade.php', 'r', encoding='utf-8') as f:
    pricing = f.read()

with open(f'{base}/partials/_variants-edit.blade.php', 'r', encoding='utf-8') as f:
    variants_edit = f.read()

with open(f'{base}/partials/_images-edit.blade.php', 'r', encoding='utf-8') as f:
    images_edit = f.read()

with open(f'{base}/partials/_seo.blade.php', 'r', encoding='utf-8') as f:
    seo = f.read()

with open(f'{base}/partials/_styles.blade.php', 'r', encoding='utf-8') as f:
    styles = f.read()

# Read the current edit.blade.php to get the JS section
with open(f'{base}/edit.blade.php', 'r', encoding='utf-8') as f:
    current_edit = f.read()

# Extract the JS section from the current edit.blade.php
js_start = current_edit.find('@section(\'js\')')
js_end = current_edit.find('@endsection', js_start) + len('@endsection')
js_section = current_edit[js_start:js_end]

# Build the merged file
merged = f'''@extends('admin.layouts.app')
@section('title', 'Edit Product')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Product</h3>
                <div class="card-tools">
                    <a href="{{{{ route('admin.product.index') }}}}" class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                @include('admin.layouts._message')

{form_header}

{basic_info}
{pricing}
{variants_edit}
{images_edit}
{seo}

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
{styles}
@endpush

{js_section}
'''

with open(f'{base}/edit.blade.php', 'w', encoding='utf-8') as f:
    f.write(merged)

print('edit.blade.php merged successfully')
