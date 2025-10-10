<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;

class OrderBilling extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, \OwenIt\Auditing\Auditable,SoftDeletes;

    protected $table = 'order_billing';

    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(fn (self $billing) => $billing->recalculateTotals());
    }

    public function recalculateTotals(): void
    {
        // Lấy tất cả service thuộc order này
        $services = \App\Models\OrderService::where('order_id', $this->order_id)->get();

        $totalService = 0;
        $vatService = 0;

        foreach ($services as $s) {
            $price = (float) $s->price;
            $vat = (float) $s->vat_rate;
            $totalService += $price;
            $vatService += $price * $vat / 100;
        }

        $price = (float) $this->price;
        $truckload = (float) $this->truckload_price;
        $priceBack = (float) $this->price_back;

        $vatPrice = 1 + ((float) $this->vat_rate_price / 100);
        $vatTruckload = 1 + ((float) $this->vat_rate_truckload / 100);
        $vatPriceBack = 1 + ((float) $this->vat_rate_price_back / 100);

        $totalPrice = $price + $truckload + $priceBack + $totalService;
        $totalPaid = ($price * $vatPrice)
            + ($truckload * $vatTruckload)
            + ($priceBack * $vatPriceBack)
            + $totalService + $vatService;

        // Cập nhật lại các cột tổng
        $this->updateQuietly([
            'total_amount_service' => $totalService,
            'vat_amount_service' => $vatService,
            'total_price' => $totalPrice,
            'total_paid' => $totalPaid,
        ]);
    }
}
