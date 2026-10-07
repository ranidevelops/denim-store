@php
    $variantRows = old('variants', $product->exists
        ? $product->variants->map(fn ($v) => ['size' => $v->size, 'color' => $v->color, 'stock' => $v->stock])->values()->all()
        : [['size' => '', 'color' => '', 'stock' => 0]]);
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}"
                           class="form-control @error('name') is-invalid @enderror" maxlength="255" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" rows="5" maxlength="5000"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="category_id" class="form-label">Category</label>
                        <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">Select category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((int) old('category_id', $product->category_id) === $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="price" class="form-label">Price ({{ config('denim.currency') }})</label>
                        <input type="number" id="price" name="price" min="0" step="0.01"
                               value="{{ old('price', $product->exists ? $product->price : '') }}"
                               class="form-control @error('price') is-invalid @enderror" required>
                        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" @selected(old('status', $product->status?->value ?? 'active') === $status->value)>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6">Sizes, colors &amp; stock</h2>
                <p class="text-muted small">Each row is one purchasable size/color combination with its own stock. Total product stock is calculated from these rows.</p>
                @error('variants') <div class="alert alert-danger py-2">{{ $message }}</div> @enderror

                <div id="variant-rows">
                    @foreach ($variantRows as $index => $row)
                        @include('admin.products._variant-row', ['index' => $index, 'row' => $row])
                    @endforeach
                </div>
                <button type="button" id="add-variant" class="btn btn-outline-secondary btn-sm">+ Add variant</button>

                <template id="variant-template">
                    @include('admin.products._variant-row', ['index' => '__INDEX__', 'row' => ['size' => '', 'color' => '', 'stock' => 0]])
                </template>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6">Images</h2>
                <p class="text-muted small">JPG, PNG or WebP, up to 2 MB each. {{ $product->exists ? 'New uploads are added to the existing images.' : 'At least one image is required.' }}</p>
                <input type="file" id="images" name="images[]" multiple accept="image/jpeg,image/png,image/webp"
                       class="form-control @error('images') is-invalid @enderror @if ($errors->has('images.*')) is-invalid @endif">
                @error('images') <div class="invalid-feedback">{{ $message }}</div> @enderror
                @foreach ($errors->get('images.*') as $messages)
                    @foreach ($messages as $message) <div class="invalid-feedback d-block">{{ $message }}</div> @endforeach
                @endforeach
                <div id="image-preview" class="d-flex flex-wrap gap-2 mt-3"></div>

                @if ($product->exists && $product->images->isNotEmpty())
                    <hr>
                    <h3 class="h6">Current images</h3>
                    <div class="row g-2">
                        @foreach ($product->images as $image)
                            <div class="col-6">
                                <img src="{{ $image->url }}" alt="{{ $product->name }}" class="img-fluid rounded border" style="aspect-ratio:1;object-fit:cover;width:100%">
                                <button type="submit" form="delete-image-{{ $image->id }}" class="btn btn-sm btn-outline-danger w-100 mt-1"
                                        onclick="return confirm('Remove this image?');">Remove</button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (() => {
        const rows = document.getElementById('variant-rows');
        const template = document.getElementById('variant-template');
        let next = Date.now();

        document.getElementById('add-variant').addEventListener('click', () => {
            rows.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', next++));
        });

        rows.addEventListener('click', (event) => {
            const button = event.target.closest('.remove-variant');
            if (button && rows.querySelectorAll('.variant-row').length > 1) {
                button.closest('.variant-row').remove();
            }
        });

        const input = document.getElementById('images');
        const preview = document.getElementById('image-preview');

        input.addEventListener('change', () => {
            preview.innerHTML = '';
            Array.from(input.files).forEach((file) => {
                if (!file.type.startsWith('image/')) return;
                const img = document.createElement('img');
                img.className = 'img-thumbnail';
                img.style.cssText = 'width:80px;height:80px;object-fit:cover';
                img.src = URL.createObjectURL(file);
                preview.appendChild(img);
            });
        });
    })();
</script>
@endpush
