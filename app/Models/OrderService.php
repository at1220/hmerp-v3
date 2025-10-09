<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;

class OrderService extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, \OwenIt\Auditing\Auditable,SoftDeletes;

    protected $guarded = [];

    protected $table = 'order_services';

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
