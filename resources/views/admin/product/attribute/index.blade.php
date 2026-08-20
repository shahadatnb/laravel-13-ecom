@extends('admin.layouts.app')
@section('title', 'Product Attributes')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Product Attributes</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.attribute.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> New Attribute
                    </a>
                </div>
            </div>
            <div class="card-body">
                @include('admin.layouts._message')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Values</th>
                                <th>Filterable</th>
                                <th>Sort Order</th>
                                <th>Tools</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attributes as $attribute)
                            <tr>
                                <td>{{ $attribute->id }}</td>
                                <td>{{ $attribute->name }}</td>
                                <td>
                                    @if($attribute->type == 'select')
                                        <span class="badge badge-info">Select</span>
                                    @elseif($attribute->type == 'color')
                                        <span class="badge badge-success">Color</span>
                                    @else
                                        <span class="badge badge-warning">Text</span>
                                    @endif
                                </td>
                                <td>
                                    @foreach($attribute->values as $value)
                                        <span class="badge badge-secondary mr-1">{{ $value->value }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @if($attribute->is_filterable)
                                        <span class="badge badge-success">Yes</span>
                                    @else
                                        <span class="badge badge-danger">No</span>
                                    @endif
                                </td>
                                <td>{{ $attribute->sort_order }}</td>
                                <td>
                                    <a href="{{ route('admin.attribute.edit', $attribute->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.attribute.destroy', $attribute->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                            @if($attributes->isEmpty())
                            <tr>
                                <td colspan="7" class="text-center">No attributes found.</td>
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
