<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class suppliers extends Model
{
    protected $table = 'suppliers';

    protected $fillable = [
        'name',
        'phone',
        'address',
        'note',
    ];
}
