<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ReturnModel extends Model
{
    // use Concerns\HasUuidColumn;
    use Concerns\ScopedByBranch;
    use Concerns\Syncable;

    protected $table = 'returns';

    protected $fillable = [
        'uuid', // مهم جداً
        'date',
        'type',
        'supplier_uuid',
        'note',
        'type_location',
        'user_id',
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

    /**
     * استخدم UUID القادم من Flutter
     * وإذا لم يوجد أنشئ UUID جديد
     */
    // protected static function booted()
    // {
    //     static::creating(function ($model) {
    //         if (empty($model->uuid)) {
    //             $model->uuid = (string) Str::uuid();
    //         }
    //     });
    // }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(suppliers::class, 'supplier_uuid', 'uuid');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReturnItem::class, 'return_uuid', 'uuid');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}