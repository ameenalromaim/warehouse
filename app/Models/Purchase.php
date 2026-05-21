<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    // use Concerns\HasUuidColumn;
    use Concerns\ScopedByBranch;
    use Concerns\Syncable;

    protected $table = 'purchase';

    protected $fillable = [
        'uuid', // مهم جداً
        'supplier_uuid',
        'date',
        'invoice_number',
        'type_location',
        'user_id',
        'updated_by_device',
        'note',
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
     * إذا جاء UUID من Flutter استخدمه
     * إذا لم يأتِ، أنشئ UUID جديد
     */
    // protected static function booted()
    // {
    //     static::creating(function ($model) {
    //         if (empty($model->uuid)) {
    //             $model->uuid = (string) \Illuminate\Support\Str::uuid();
    //         }
    //     });
    // }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(suppliers::class, 'supplier_uuid', 'uuid');
    }

    public function items(): HasMany
    {
        return $this->hasMany(purchaseitem::class, 'purchase_uuid', 'uuid');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}