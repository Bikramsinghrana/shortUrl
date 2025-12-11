<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
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
        return $this->hasRole('SuperAdmin');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('Admin');
    }

    public function isMember(): bool
    {
        return $this->hasRole('Member');
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