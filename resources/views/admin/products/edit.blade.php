@extends('layouts.admin')

@section('title', 'Edit product')

@section('content')
<h1 class="h3 mb-4">Edit product</h1>
<form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" novalidate>
    @csrf
    @method('PUT')
    @include('admin.products._form')
    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-denim">Save changes</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>

{{-- Separate forms: HTML does not allow nesting a form inside the edit form above. --}}
@foreach ($product->images as $image)
    <form id="delete-image-{{ $image->id }}" method="POST" action="{{ route('admin.product-images.destroy', $image) }}">
        @csrf
        @method('DELETE')
    </form>
@endforeach
@endsection
