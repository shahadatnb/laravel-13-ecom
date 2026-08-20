@extends('admin.layouts.app')
@section('title', 'Create Product')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create Product</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.product.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                @include('admin.layouts._message')

                @include('admin.product.partials._form-header')

                @include('admin.product.partials._basic-info')
                @include('admin.product.partials._pricing')
                @include('admin.product.partials._variants-create')
                @include('admin.product.partials._images-create')
                @include('admin.product.partials._seo')

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    @include('admin.product.partials._styles')
@endpush

@section('js')
    @include('admin.product.partials._scripts-create')
@endsection
