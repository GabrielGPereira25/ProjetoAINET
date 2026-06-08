<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'custom'])]
#[Table(timestamps: false)]


class Category extends Model
{
    use SoftDeletes;

     public function getImageFullUrlAttribute()
    {
        if ($this->photo_url && Storage::disk('public')->exists("categories/{$this->image_url}")) {
            return asset("storage/categories/{$this->image_url}");
        } else {
            return asset("storage/categories/no_category.png");
        }
    }

    public function tshirts_images(): HasMany
    {
        return $this->hasMany(Tshirt_image::class);
    }
}
