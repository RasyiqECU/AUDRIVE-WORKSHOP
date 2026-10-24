<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'is_active',
        'role',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // ── Relationships ──

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    public function assignedWorkOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class, 'assigned_mechanic_id');
    }

    public function mechanicSchedules(): HasMany
    {
        return $this->hasMany(MechanicSchedule::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function salesCommissions(): HasMany
    {
        return $this->hasMany(SalesCommission::class);
    }

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeMechanics($query)
    {
        return $query->where('role', 'mechanic');
    }

    public function scopeSales($query)
    {
        return $query->where('role', 'sales');
    }

    // ── Helpers ──

    public function isMechanic(): bool
    {
        return $this->role === 'mechanic';
    }

    public function isSales(): bool
    {
        return $this->role === 'sales';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super-admin';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Ambil profil customer yang sudah ada, atau buat baru HANYA jika user adalah customer.
     * Jangan pernah auto-create customer profile untuk staff/admin.
     *
     * @throws \LogicException jika dipanggil pada user non-customer
     */
    public function getOrCreateCustomerProfile(): Customer
    {
        if (!$this->isCustomer()) {
            throw new \LogicException("Cannot create customer profile for user with role: {$this->role}");
        }

        return $this->customer ?? Customer::create(['user_id' => $this->id]);
    }

    public function assignRole(string $role): self
    {
        $this->forceFill(['role' => $role])->save();
        return $this;
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }
}