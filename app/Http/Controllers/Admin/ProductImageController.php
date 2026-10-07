<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    public function destroy(ProductImage $image): RedirectResponse
    {
        if ($image->product->images()->count() <= 1) {
            return back()->with('error', 'A product must keep at least one image. Upload another before removing this one.');
        }

        Storage::disk('public')->delete($image->image);
        $image->delete();

        return back()->with('success', 'Image removed.');
    }
}
