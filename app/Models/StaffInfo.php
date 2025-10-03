<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class StaffInfo extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable,SoftDeletes;

    //
    protected $guarded = [];

    protected $table = 'staff_info';

    protected $casts = [
        'personnel_management' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($staff) {
            $staff->phone = $staff->user->email;
        });

    }
}
