<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class suppliers extends Model
{
    use Concerns\HasUuidColumn;
    use Concerns\Syncable;

    protected $table = 'suppliers';

    protected $fillable = [
        'name',
        'phone',
        'address',
        'note',
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
}
