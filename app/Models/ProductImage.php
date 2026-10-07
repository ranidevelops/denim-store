<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    protected $fillable = ['product_id', 'image'];

    // asset() follows the current request host, so images work on any local URL.
    protected function url(): Attribute
    {
        return Attribute::get(fn () => asset('storage/'.$this->image));
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
