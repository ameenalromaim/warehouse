<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ReturnItem extends Model
{
    // use Concerns\HasUuidColumn;
    use Concerns\ScopedByBranch;
    use Concerns\Syncable;

    protected $table = 'return_items';

    protected $fillable = [
        'uuid', // مهم جداً
        'return_uuid',
        'product_uuid',
        'unit_uuid',
        'quantity',
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

    /**
     * إذا Flutter أرسل UUID استخدمه
     * إذا لا، أنشئ UUID جديد
     */
    // protected static function booted()
    // {
    //     static::creating(function ($model) {
    //         if (empty($model->uuid)) {
    //             $model->uuid = (string) Str::uuid();
    //         }
    //     });
    // }

    public function product(): BelongsTo
    {
        return $this->belongsTo(product::class, 'product_uuid', 'uuid');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(units::class, 'unit_uuid', 'uuid');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}