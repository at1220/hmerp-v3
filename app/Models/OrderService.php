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

    protected static function boot()
    {
        parent::boot();

        static::saved(function (self $service) {
            self::recalculateBilling($service->order_id);
        });

        static::deleted(function (self $service) {
            self::recalculateBilling($service->order_id);
        });

        static::restored(function (self $service) {
            self::recalculateBilling($service->order_id);
        });
    }

    protected static function recalculateBilling(int $orderId): void
    {
        // Lấy bản ghi billing
        $billing = OrderBilling::where('order_id', $orderId)->first();
        if (! $billing) {
            return;
        }

        // Lấy danh sách service còn tồn tại (chưa bị soft delete)
        $services = self::where('order_id', $orderId)->get();

        $totalService = 0;
        $vatService = 0;

        foreach ($services as $s) {
            $price = (float) $s->price;
            $vat = (float) $s->vat_rate;
            $totalService += $price;
            $vatService += $price * $vat / 100;
        }

        // Tính các thành phần trong billing
        $price = (float) $billing->price;
        $truckload = (float) $billing->truckload_price;
        $priceBack = (float) $billing->price_back;

        $vatPrice = 1 + ((float) $billing->vat_rate_price / 100);
        $vatTruckload = 1 + ((float) $billing->vat_rate_truckload / 100);
        $vatPriceBack = 1 + ((float) $billing->vat_rate_price_back / 100);

        // Tổng cộng
        $totalPrice = $price + $truckload + $priceBack + $totalService;
        $totalPaid = ($price * $vatPrice)
            + ($truckload * $vatTruckload)
            + ($priceBack * $vatPriceBack)
            + $totalService + $vatService;

        // Cập nhật lại
        $billing->update([
            'total_amount_service' => $totalService,
            'vat_amount_service' => $vatService,
            'total_price' => $totalPrice,
            'total_paid' => $totalPaid,
        ]);
    }
}
