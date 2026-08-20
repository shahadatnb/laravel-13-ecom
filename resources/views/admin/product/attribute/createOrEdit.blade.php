@extends('admin.layouts.app')
@section('title', empty($attribute) ? 'Create Attribute' : 'Edit Attribute')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ empty($attribute) ? 'Create Attribute' : 'Edit Attribute' }}</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.attribute.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                @include('admin.layouts._message')
                    {{-- @dd($attribute) --}}
                <form method="POST" action="{{ empty($attribute) ? route('admin.attribute.store') : route('admin.attribute.update', $attribute) }}">
                    @csrf
                    @if(!empty($attribute))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">Attribute Name <span class="text-danger">*</span></label>
                                <input id="name" name="name" type="text" value="{{ old('name', $attribute->name ?? '') }}" required class="form-control" placeholder="e.g. Color, Size" />
                                @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="type">Type <span class="text-danger">*</span></label>
                                <select id="type" name="type" class="form-control" required>
                                    <option value="">Select Type</option>
                                    <option value="select" {{ old('type', $attribute->type ?? '') == 'select' ? 'selected' : '' }}>Select</option>
                                    <option value="text" {{ old('type', $attribute->type ?? '') == 'text' ? 'selected' : '' }}>Text</option>
                                    <option value="color" {{ old('type', $attribute->type ?? '') == 'color' ? 'selected' : '' }}>Color</option>
                                </select>
                                @error('type')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sort_order">Sort Order</label>
                                <input id="sort_order" name="sort_order" type="number" value="{{ old('sort_order', $attribute->sort_order ?? 0) }}" class="form-control" />
                                @error('sort_order')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" rows="2" class="form-control" placeholder="Short description">{{ old('description', $attribute->description ?? '') }}</textarea>
                                @error('description')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="is_required">Required</label>
                                <select id="is_required" name="is_required" class="form-control">
                                    <option value="0" {{ old('is_required', $attribute->is_required ?? false) == false ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('is_required', $attribute->is_required ?? false) == true ? 'selected' : '' }}>Yes</option>
                                </select>
                                @error('is_required')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="is_filterable">Filterable</label>
                                <select id="is_filterable" name="is_filterable" class="form-control">
                                    <option value="0" {{ old('is_filterable', $attribute->is_filterable ?? true) == false ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('is_filterable', $attribute->is_filterable ?? true) == true ? 'selected' : '' }}>Yes</option>
                                </select>
                                @error('is_filterable')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Values</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm" id="add-value">
                                    <i class="fas fa-plus"></i> Add Value
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered" id="values-table">
                                <thead>
                                    <tr>
                                        <th>Value</th>
                                        <th>Color Code</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($attribute) && $attribute->values->count() > 0)
                                        @foreach($attribute->values as $value)
                                            <tr>
                                                <td><input type="text" name="values[{{ $loop->index }}][value]" value="{{ $value->value }}" class="form-control" placeholder="Value" /></td>
                                                <td><input type="text" name="values[{{ $loop->index }}][color_code]" value="{{ $value->color_code }}" class="form-control" placeholder="#000000" /></td>
                                                <td><button type="button" class="btn btn-danger btn-sm remove-value">Remove</button></td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            <p class="text-muted">Use Color Code field for color attributes (e.g. #FF0000).</p>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-success">{{ empty($attribute) ? 'Create' : 'Update' }}</button>
                        <a href="{{ route('admin.attribute.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const colorCodeHeaders = document.querySelectorAll('.color-code-header');
    
    function toggleColorColumn() {
        const isColor = typeSelect && typeSelect.value === 'color';
        colorCodeHeaders.forEach(function(header) {
            header.style.display = isColor ? '' : 'none';
        });
        document.querySelectorAll('input[name$="[color_code]"]').forEach(function(input) {
            input.closest('td').style.display = isColor ? '' : 'none';
        });
    }

    if (typeSelect) {
        typeSelect.addEventListener('change', toggleColorColumn);
        toggleColorColumn();
    }

    const addValueBtn = document.getElementById('add-value');
    const valuesTableBody = document.querySelector('#values-table tbody');

    if (addValueBtn && valuesTableBody) {
        addValueBtn.addEventListener('click', function() {
            const index = valuesTableBody.querySelectorAll('tr').length;
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="text" name="values[${index}][value]" class="form-control" placeholder="Value" /></td>
                <td><input type="text" name="values[${index}][color_code]" class="form-control" placeholder="#000000" /></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-value">Remove</button></td>
            `;
            valuesTableBody.appendChild(tr);
        });
    }

    if (valuesTableBody) {
        valuesTableBody.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-value')) {
                e.target.closest('tr').remove();
            }
        });
    }
});
</script>
@endsection
