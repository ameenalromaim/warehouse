<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class product extends Model
{
    protected $table = 'product';
    protected $fillable = [
    'name',
    'code',
    'description',
    'unit_id'
];

// public function unit()
// {
//     return $this->belongsTo(Unit::class);
// }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(units::class, 'unit_id');
    }
}
