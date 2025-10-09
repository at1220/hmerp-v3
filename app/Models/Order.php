<?php

namespace App\Models;

use App\Enum\Order\Type;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;

class Order extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, \OwenIt\Auditing\Auditable,SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'type' => Type::class,
    ];

    public function tripInfo()
    {
        return $this->hasOne(OrderTripInfo::class);
    }

    public function containerInfo()
    {
        return $this->hasOne(OrderContainerInfo::class);
    }

    public function bill()
    {
        return $this->hasOne(OrderBilling::class);
    }

    public function services()
    {
        return $this->hasMany(OrderService::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFormattedPickUpDateAttribute()
    {
        $date = null;
        $time = null;

        switch ($this->type) {
            case Type::Trip:
                $date = optional($this->tripInfo)->pick_up_date;
                $time = optional($this->tripInfo)->pick_up_time;
                break;

            case Type::Container:
                $date = optional($this->containerInfo)->pick_up_date;
                $time = optional($this->containerInfo)->pick_up_time;
                break;
        }

        if (! $date) {
            return null; // không có ngày thì thôi
        }

        // Nếu có giờ => nối ngày + giờ
        if ($time) {
            return \Carbon\Carbon::parse("{$date} {$time}")->format('H:i d/m/Y');
        }

        // Chỉ có ngày => chỉ hiển thị ngày
        return \Carbon\Carbon::parse($date)->format('d/m/Y');
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($order) {
            $order->tripInfo()->delete();
            $order->containerInfo()->delete();
            $order->hasOne()->delete();
        });
    }
}
