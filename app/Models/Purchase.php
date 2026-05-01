<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    use Concerns\HasUuidColumn;
    use Concerns\Syncable;

    protected $table = 'purchase';

    protected $fillable = [
        'supplier_uuid',
        'date',
        'invoice_number',
        'updated_by_device',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'deleted_at' => 'datetime',
            'synced_at' => 'datetime',
            'version' => 'integer',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(suppliers::class, 'supplier_uuid', 'uuid');
    }

    public function items(): HasMany
    {
        return $this->hasMany(purchaseitem::class, 'purchase_uuid', 'uuid');
    }
}
