<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use Concerns\HasUuidColumn, Concerns\Syncable, HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    public const ROLE_SUPER_ADMIN = 'super_admin';

    public const ROLE_BRANCH_USER = 'branch_user';

    protected $fillable = [
        'name',
        'type_location',
        'email',
        'phone',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'deleted_at' => 'datetime',
            'synced_at' => 'datetime',
            'version' => 'integer',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isBranchUser(): bool
    {
        return $this->role === self::ROLE_BRANCH_USER;
    }

    /**
     * مفتاح الفرع لفلترة البيانات؛ null للسوبر أدمن (= كل الفروع).
     */
    public function branchScopeKey(): ?string
    {
        if ($this->isSuperAdmin()) {
            return null;
        }

        $loc = trim((string) $this->type_location);

        return $loc !== '' ? $loc : null;
    }

    public function canAccessBranchRecord(mixed $recordLocation): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $userLoc = $this->branchScopeKey();

        if ($userLoc === null) {
            return false;
        }

        return (string) $recordLocation === $userLoc;
    }
}
