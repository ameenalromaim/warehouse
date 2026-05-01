<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnItem extends Model
{
    use Concerns\HasUuidColumn;
    use Concerns\Syncable;

    protected $table = 'return_items';

    protected $fillable = [
        'return_uuid',
        'product_uuid',
        'unit_uuid',
        'quantity',
        'updated_by_device',
    ];

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
            'synced_at' => 'datetime',
            'version' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(product::class, 'product_uuid', 'uuid');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(units::class, 'unit_uuid', 'uuid');
    }
}
