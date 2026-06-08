<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'custom', 'code'])]
#[Table(timestamps: false, keyType: 'string', key: 'code', incrementing: false)]

class Color extends Model
{
    use SoftDeletes;

    public function order_items(): HasMany
    {
        return $this->hasMany(Order_item::class, 'color_code', 'code');
    }
}
