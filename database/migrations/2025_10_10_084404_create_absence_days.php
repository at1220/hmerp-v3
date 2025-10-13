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
        Schema::create('absence_days', function (Blueprint $table) {
            $table->id();
            $table->string('status')->comment('Trạng thái');
            $table->foreignId('absence_id')->comment('id đơn');
            $table->date('date')->comment('Ngày Nghi');
            $table->string('part_of_day')->comment('Thời gian nghỉ');
            $table->string('leave_type')->comment('Loại nghỉ phép');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absence_days');
    }
};
