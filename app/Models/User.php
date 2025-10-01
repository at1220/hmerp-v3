<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements Auditable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, \OwenIt\Auditing\Auditable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
            'first_password' => 'hashed',
            'roles' => 'array',
        ];
    }

    protected $casts = [
        'permissions' => 'array',
    ];

    public function staffInfo()
    {
        return $this->hasOne(StaffInfo::class);
    }

    public function customerInfo()
    {
        return $this->hasOne(CustomerInfo::class);
    }

    public function contactPersons()
    {
        return $this->hasMany(ContactPerson::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            $plainPassword = Str::random(10);
            $user->first_password = Hash::make($plainPassword);
            if (empty($user->role)) {
                $user->role = 'admin';
            }
        });
        static::deleting(function ($user) {
            $user->customerInfo()->delete();
            $user->staffInfo()->delete();
            $user->contactPersons()->delete();
        });
    }
}
