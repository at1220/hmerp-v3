<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_billing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id');
            $table->float('price')->comment('Giá cước');
            $table->float('vat_rate_price')->nullable()->comment('số vat Giá cước');
            $table->float('truckload_price')->nullable()->comment('Giá bốc xếp');
            $table->float('vat_rate_truckload')->nullable()->comment('số vat Giá bx');
            $table->float('price_back')->nullable()->comment('Giá quay đầu');
            $table->float('vat_rate_price_back')->nullable()->comment('số vat Giá quay đầu');
            $table->float('total_amount_service')->nullable()->comment('số tiền tổng giá dịch vụ');
            $table->float('vat_amount_service')->nullable()->comment('số tiền vat Giá dịch vụ');
            $table->float('lift_fee')->nullable()->comment('phí nâng');
            $table->string('lift_fee_invoice')->nullable()->comment('Số hoá đơn phí nâng');
            $table->float('lower_fee')->nullable()->comment('phí hạ');
            $table->string('lower_fee_invoce')->nullable()->comment('Số hoá đơn phí hạ');
            $table->float('weighing_fee')->nullable()->comment('phí cân');
            $table->string('weighing_fee_invoice')->nullable()->comment('Số hoá đơn phí cân');
            $table->float('betting_fee')->nullable()->comment('phí cược');
            $table->string('betting_fee_invoice')->nullable()->comment('Số hoá đơn phí cược');
            $table->float('cleaning_fee')->nullable()->comment('phí vệ sinh');
            $table->string('cleaning_fee_invoice')->nullable()->comment('Số hoá đơn phí cược');
            $table->float('price_cash')->nullable()->comment('thu tiền mặt không vat (hàng chành)');
            $table->boolean('invoice_issued')->nullable()->comment('Xuất hoá đơn hay không');
            $table->float('total_paid')->nullable()->comment('Tổng khách thanh toán');
            $table->float('total_price')->nullable()->comment('Tổng giá cước');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_billing');
    }
};
