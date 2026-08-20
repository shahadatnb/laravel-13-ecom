<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'phone', 'address', 'avatar', 'bio', 'date_of_birth', 'gender', 'timezone', 'locale', 'type'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    use HasRoles;

    public const TYPE_ADMIN = 'admin';

    public const TYPE_STAFF = 'staff';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Scope a query to only include admin users.
     */
    public function scopeAdmins($query): void
    {
        $query->where('type', self::TYPE_ADMIN);
    }

    /**
     * Scope a query to only include staff users.
     */
    public function scopeStaff($query): void
    {
        $query->where('type', self::TYPE_STAFF);
    }

    /**
     * Scope a query to only include admin/staff users.
     */
    public function scopeAdminStaff($query): void
    {
        $query->whereIn('type', [self::TYPE_ADMIN, self::TYPE_STAFF]);
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->type === self::TYPE_ADMIN;
    }

    /**
     * Check if user is staff.
     */
    public function isStaff(): bool
    {
        return $this->type === self::TYPE_STAFF;
    }


    /**
     * Get the customer profile for the user.
     */
    public function customerProfile(): HasMany
    {
        return $this->hasMany(CustomerProfile::class, 'user_id');
    }

    /**
     * Get wallet transactions for the user (admin-created transactions).
     */
    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class, 'created_by');
    }

    /**
     * Get addresses manually assigned to this admin/staff user.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class, 'user_id');
    }
}
