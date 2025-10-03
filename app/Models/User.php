<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
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
            'roles' => 'array',
        ];
    }

    protected $casts = [
        'permissions' => 'array',
    ];

    public function staff()
    {
        return $this->hasOne(StaffInfo::class);
    }

    public function customer()
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
            $user->first_password = Str::random(10);
            if (empty($user->role)) {
                $user->role = 'admin';
            }
        });
        static::deleting(function ($user) {
            $user->staff()->delete();
            $user->customer()->delete();
            $user->contactPersons()->delete();
        });
    }
}
