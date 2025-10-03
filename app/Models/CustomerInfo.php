<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class CustomerInfo extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable,SoftDeletes;

    //
    protected $guarded = [];

    protected $table = 'customer_info';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($customer) {
            $customer->phone = $customer->user->email;
        });

    }
}
