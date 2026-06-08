<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['status', 'customer_id', 'date', 'total_price', 'nodes', 'reason_for_cancellation', 'nif', 'address', 'payment_type', 'payment_ref', 'receipt_url','custom'])]

class Order extends Model
{
    public function order_items(): HasMany
    {
        return $this->hasMany(Order_item::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }
}
