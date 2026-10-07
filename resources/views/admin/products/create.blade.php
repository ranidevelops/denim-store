@extends('layouts.admin')

@section('title', 'Add product')

@section('content')
<h1 class="h3 mb-4">Add product</h1>
<form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" novalidate>
    @csrf
    @include('admin.products._form')
    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-denim">Create product</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection
