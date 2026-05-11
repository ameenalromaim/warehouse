<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class suppliers extends Model
{
    use Concerns\HasUuidColumn;
    use Concerns\ScopedByBranch;
    use Concerns\Syncable;

    protected $table = 'suppliers';

    protected $fillable = [
        'name',
        'phone',
        'address',
        'note',
        'type_location',
        'user_id',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
