<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

#[Fillable(['customer_id', 'category_id', 'name', 'description', 'image_url', 'custom'])]

class Tshirt_image extends Model
{
    use SoftDeletes;

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function order_items(): HasMany
    {
        return $this->hasMany(Order_item::class);
    }

    public function getImageFullUrlAttribute()
    {
        if ($this->image_url && Storage::disk('public')->exists("tshirt_images/{$this->image_url}")) {
            return asset("storage/tshirt_images/{$this->image_url}");
        } else {
            return response()->file(Storage::disk('local')->exists("tshirt_images_private/{$this->image_url}"));
        }
    }
}
