<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnItem extends Model
{
    protected $table = 'return_items';

    protected $fillable = [
        'return_id',
        'product_id',
        'unit_id',
        'quantity',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(product::class, 'product_id', 'id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(units::class, 'unit_id', 'id');
    }
}
