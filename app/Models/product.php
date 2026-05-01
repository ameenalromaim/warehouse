<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class product extends Model
{
    use Concerns\HasUuidColumn;
    use Concerns\Syncable;

    protected $table = 'product';
    protected $fillable = [
        'name',
        'code',
        'description',
        'unit_uuid',
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

// public function unit()
// {
//     return $this->belongsTo(Unit::class);
// }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(units::class, 'unit_uuid', 'uuid');
    }
}
