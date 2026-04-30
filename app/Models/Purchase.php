<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    protected $table = 'purchase';

    protected $fillable = [
        'supplier_id',
        'date',
        'invoice_number',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(suppliers::class, 'supplier_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(purchaseitem::class, 'purchase_id');
    }
}
