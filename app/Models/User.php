<?php

namespace App\Models;


use Filament\Models\Contracts\FilamentUser;
use Filament\Panel as FilamentPanel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasRoles, SoftDeletes;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'fee'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        // 'profile_photo_url',
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
        ];
    }

    public function canAccessPanel(FilamentPanel $panel): bool
    {
        // return true;
        return $this->hasRole(["Secretary", "Member", "Accountant"]);
    }
    public function isSecretary(): bool
    {
        return $this->hasRole("Secretary");
    }
    public function isAccountant(): bool
    {
        return $this->hasRole("Accountant");
    }
    public function isBoth(): bool
    {
        return $this->hasRole(["Accountant", "Secretary"]);
    }
    public function isMember(): bool
    {
        return $this->hasRole("Member");
    }
}