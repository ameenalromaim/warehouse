<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class units extends Model
{
    // use Concerns\HasUuidColumn;
    use Concerns\Syncable;

    protected $table = 'units';

   protected $fillable = [
    'uuid',
    'name',
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
