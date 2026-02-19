<?php

namespace App\Models;

use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'is_active',
        'invited_by',
        'invitation_token',
        'invitation_status',
        'invited_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'invited_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function shortUrls(): HasMany
    {
        return $this->hasMany(ShortUrl::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function invitedUsers(): HasMany
    {
        return $this->hasMany(User::class, 'invited_by');
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(RoleEnum::SUPER_ADMIN->value);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(RoleEnum::ADMIN->value);
    }

    public function isMember(): bool
    {
        return $this->hasRole(RoleEnum::MEMBER->value);
    }

    /**
     * Parent admin (the user who invited this user)
     */
    public function parent()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Direct child admins (users invited by this user)
     */
    public function children()
    {
        return $this->hasMany(User::class, 'invited_by');
    }

    /**
     * Recursively get all descendant users
     */
    public function descendants()
    {
        $descendants = collect([$this]);

        foreach ($this->children as $child) {
            $descendants = $descendants->merge($child->descendants());
        }

        return $descendants;
    }

    /**
     * Return only the descendant IDs 
     */
    public function descendantIds()
    {
        return $this->descendants()->pluck('id')->unique()->values();
    }
}