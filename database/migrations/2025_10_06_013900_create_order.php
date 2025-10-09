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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->enum('type', ['trip', 'frozen', 'container', 'freight', 'crane'])->comment('Loại đơn');
            $table->string('status')->comment('trạng thái');
            $table->string('status_payment')->comment('trạng thái thanh toán');
            $table->foreignId('post_paid_term')->nullable()->comment('id bảng kê');
            $table->foreignId('created_by');
            $table->foreignId('staff_id');
            $table->foreignId('customer_id');
            $table->timestamps();
            $table->softDeletes();
        });
        //

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
