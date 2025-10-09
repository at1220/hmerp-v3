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
        Schema::create('order_container_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id');
            // thông tin điểm lên hàng
            $table->date('pick_up_date')->comment('ngày lên hàng');
            $table->time('pick_up_time')->nullable()->comment('giờ lên hàng');
            $table->string('pick_up_point')->comment('Điểm len hàng');
            $table->string('pick_up_link')->nullable()->comment('link bản đồ nhận');
            $table->enum('pick_up_method', ['none', 'hands', 'forklift', 'other'])->default('none')->comment('Hình thức lên hàng');
            $table->string('sender_name')->nullable()->comment('người lấy hàng');
            $table->string('sender_phone')->nullable()->comment('sđt người lấy hàng');
            // thông tin điểm xuống
            $table->date('delivery_date')->nullable()->comment('ngày xuống hàng');
            $table->time('delivery_time')->nullable()->comment('giờ xuống hàng');
            $table->string('delivery_point')->comment('Điểm len xuống');
            $table->string('delivery_link')->nullable()->comment('link bản đồ giao');
            $table->enum('delivery_method', ['none', 'hands', 'forklift', 'other'])->default('none')->comment('Hình thức xuống hàng');
            $table->string('receiver_name')->nullable()->comment('người lấy hàng');
            $table->string('receiver_phone')->nullable()->comment('sđt người lấy hàng');
            // thông tin chuyến
            $table->float('distance')->nullable()->default(0)->comment('Khoảng cách');
            $table->string('item_name')->comment('Loại hàng');
            $table->boolean('has_cash')->default(false)->comment('thu tiền mặt');
            $table->float('trip_number')->nullable()->default(0)->comment('Số chuyến');
            $table->enum('type_cont', ['20ft', '40ft', '45ft'])->default('40ft')->comment('Loại cont');
            $table->string('cont_number')->comment('số cont');
            $table->string('bill_or_booking')->comment('số bill/booking');
            $table->enum('movement_type', ['import', 'export'])->comment('Loại hình');
            $table->string('container_action')->comment('Lấy trả rỗng');
            // ghi chú
            $table->text('note')->nullable()->comment('Ghi chú');
            $table->text('sale_note')->nullable()->comment('Ghi chú kd');
            $table->text('dispatch_note')->nullable()->comment('Ghi chú đv');
            $table->text('accountant_note')->nullable()->comment('Ghi chú KT');
            $table->text('sale_note_driver')->nullable()->comment('Ghi chú kd cho tx');
            $table->text('customer_note')->nullable()->comment('Ghi chú khách hàng');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_container_info');
    }
};
