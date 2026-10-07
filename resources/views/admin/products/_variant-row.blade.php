<div class="row g-2 align-items-start mb-2 variant-row">
    <div class="col-sm-4">
        <select name="variants[{{ $index }}][size]" class="form-select @error('variants.'.$index.'.size') is-invalid @enderror" required>
            <option value="">Size</option>
            @foreach (config('denim.sizes') as $size)
                <option value="{{ $size }}" @selected(($row['size'] ?? '') === $size)>{{ $size }}</option>
            @endforeach
        </select>
        @error('variants.'.$index.'.size') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-sm-4">
        <select name="variants[{{ $index }}][color]" class="form-select @error('variants.'.$index.'.color') is-invalid @enderror" required>
            <option value="">Color</option>
            @foreach (config('denim.colors') as $color)
                <option value="{{ $color }}" @selected(($row['color'] ?? '') === $color)>{{ $color }}</option>
            @endforeach
        </select>
        @error('variants.'.$index.'.color') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-sm-3">
        <input type="number" min="0" step="1" name="variants[{{ $index }}][stock]" value="{{ $row['stock'] ?? 0 }}"
               class="form-control @error('variants.'.$index.'.stock') is-invalid @enderror" placeholder="Stock" required>
        @error('variants.'.$index.'.stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-sm-1 text-end">
        <button type="button" class="btn btn-outline-danger remove-variant" aria-label="Remove variant">&times;</button>
    </div>
</div>
