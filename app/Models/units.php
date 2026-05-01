<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class units extends Model
{
    use Concerns\HasUuidColumn;
    use Concerns\Syncable;

    protected $table = 'units';

    protected $fillable = [
        'name',
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
