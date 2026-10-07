@extends('layouts.admin')

@section('title', 'Add category')

@section('content')
<h1 class="h3 mb-4">Add category</h1>
<div class="card shadow-sm" style="max-width: 560px;">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.categories.store') }}" novalidate>
            @csrf
            @include('admin.categories._form')
        </form>
    </div>
</div>
@endsection
