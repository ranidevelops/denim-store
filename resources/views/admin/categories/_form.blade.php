<div class="mb-3">
    <label for="name" class="form-label">Name</label>
    <input type="text" id="name" name="name" value="{{ old('name', $category->name ?? '') }}"
           class="form-control @error('name') is-invalid @enderror" maxlength="100" required autofocus>
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    <div class="form-text">The URL slug is generated automatically from the name.</div>
</div>
<div class="d-flex gap-2">
    <button type="submit" class="btn btn-denim">Save</button>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
