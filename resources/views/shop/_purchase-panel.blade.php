@php
    $sizes = $product->offeredOptions('size', config('denim.sizes'));
    $colors = $product->offeredOptions('color', config('denim.colors'));
    $inStock = $product->isInStock();
@endphp

@if (! $inStock)
    <div class="alert alert-secondary mb-0">This product is currently out of stock.</div>
@else
    @if ($errors->any())
        <div class="alert alert-danger py-2 small">
            <ul class="mb-0">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <fieldset class="mb-3">
        <legend class="form-label fw-semibold fs-6">Size</legend>
        <div class="d-flex flex-wrap gap-2">
            @foreach ($sizes as $size)
                <input type="radio" class="btn-check" name="size" id="size-{{ $loop->index }}" value="{{ $size }}" autocomplete="off" @checked(old('size') === $size)>
                <label class="btn btn-outline-dark btn-sm" for="size-{{ $loop->index }}">{{ $size }}</label>
            @endforeach
        </div>
    </fieldset>

    <fieldset class="mb-3">
        <legend class="form-label fw-semibold fs-6">Color</legend>
        <div class="d-flex flex-wrap gap-2">
            @foreach ($colors as $color)
                <input type="radio" class="btn-check" name="color" id="color-{{ $loop->index }}" value="{{ $color }}" autocomplete="off" @checked(old('color') === $color)>
                <label class="btn btn-outline-dark btn-sm" for="color-{{ $loop->index }}">{{ $color }}</label>
            @endforeach
        </div>
    </fieldset>

    <div class="mb-3" style="max-width: 140px;">
        <label for="quantity" class="form-label fw-semibold">Quantity</label>
        <input type="number" id="quantity" name="quantity" value="{{ old('quantity', 1) }}" min="1" max="100" step="1" class="form-control" required>
    </div>

    <p id="availability" class="small text-muted mb-3" aria-live="polite">Select a size and color</p>

    <button type="submit" id="add-to-cart" class="btn btn-denim btn-lg w-100">Add to Cart</button>

    @push('scripts')
    <script>
        (() => {
            const variants = {{ Js::from($product->variants->map(fn ($v) => ['size' => $v->size, 'color' => $v->color, 'stock' => $v->stock])->values()) }};
            const sizeInputs = [...document.querySelectorAll('input[name="size"]')];
            const colorInputs = [...document.querySelectorAll('input[name="color"]')];
            const quantity = document.getElementById('quantity');
            const availability = document.getElementById('availability');
            const addButton = document.getElementById('add-to-cart');

            const stockFor = (size, color) => {
                const match = variants.find((v) => v.size === size && v.color === color);
                return match ? match.stock : 0;
            };
            const selected = (inputs) => (inputs.find((i) => i.checked) || {}).value || null;

            function refresh() {
                const size = selected(sizeInputs);
                const color = selected(colorInputs);

                sizeInputs.forEach((input) => {
                    input.disabled = color
                        ? stockFor(input.value, color) <= 0
                        : !variants.some((v) => v.size === input.value && v.stock > 0);
                });
                colorInputs.forEach((input) => {
                    input.disabled = size
                        ? stockFor(size, input.value) <= 0
                        : !variants.some((v) => v.color === input.value && v.stock > 0);
                });

                if (!size || !color) {
                    quantity.max = 100;
                    addButton.disabled = true;
                    availability.textContent = 'Select a size and color';
                    return;
                }

                const stock = stockFor(size, color);
                quantity.max = Math.max(stock, 1);
                if (Number(quantity.value) > stock) quantity.value = Math.max(stock, 1);
                addButton.disabled = stock <= 0;
                availability.textContent = stock <= 0
                    ? 'This combination is out of stock'
                    : (stock <= 5 ? `Only ${stock} left` : 'In stock');
            }

            [...sizeInputs, ...colorInputs].forEach((input) => input.addEventListener('change', refresh));
            refresh();
        })();
    </script>
    @endpush
@endif
