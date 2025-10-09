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
        Schema::create('order_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id');
            $table->string('service_id')->comment('loại dịch vụ');
            $table->float('price')->comment('Giá cước');
            $table->float('vat_rate')->nullable()->comment('số vat Giá cước');
            $table->text('note')->nullable()->comment('số vat Giá quay đầu');
            $table->string('invoice_number')->nullable()->comment('số tiền tổng giá dịch vụ');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_services');
    }
};
