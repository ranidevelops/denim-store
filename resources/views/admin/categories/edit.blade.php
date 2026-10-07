@extends('layouts.admin')

@section('title', 'Edit category')

@section('content')
<h1 class="h3 mb-4">Edit category</h1>
<div class="card shadow-sm" style="max-width: 560px;">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" novalidate>
            @csrf
            @method('PUT')
            @include('admin.categories._form')
        </form>
    </div>
</div>
@endsection
