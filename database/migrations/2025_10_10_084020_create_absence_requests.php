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
        Schema::create('absence_requests', function (Blueprint $table) {
            $table->id();
            $table->string('status')->comment('Trạng thái');
            $table->foreignId('user_id')->comment('Nhân viên nghỉ');
            $table->foreignId('created_by')->comment('Người tạo đơn');
            $table->date('from_date')->comment('Ngày bắt đầu');
            $table->date('to_date')->comment('Ngày kết thúc');
            $table->float('total_day')->comment('Tổng ngày nghỉ');
            $table->string('reason')->comment('Lý do');
            $table->text('description')->comment('Ghi chú thêm')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absence_requests');
    }
};
