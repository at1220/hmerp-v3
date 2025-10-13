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
        Schema::create('absence_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_id')->nullable()->comment('Người duyệt');
            $table->foreignId('absence_id')->comment('id đơn');
            $table->tinyInteger('level')->comment('Cấp độ');
            $table->string('status')->comment('trạng thái');
            $table->text('note')->nullable()->comment('Ghi chú');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absence_approvals');
    }
};
