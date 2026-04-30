<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class purchaseitem extends Model
{
    protected $table = 'purchaseitem';

    protected $fillable = [
        'purchase_id',
        'product_id',
        'unit_id',
        'quantity',
        'price',
    ];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(product::class, 'product_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(units::class, 'unit_id');
    }
}
