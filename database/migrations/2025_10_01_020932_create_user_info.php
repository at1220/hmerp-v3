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
        Schema::create('customer_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade');
            $table->string('short_name')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_company')->nullable();
            $table->jsonb('cared_by')->nullable();
            $table->boolean('has_contact')->nullable();
            $table->integer('date_of_payment')->nullable()->default(7)->comment('Số ngày quá hạn thanh toán');
            $table->enum('noiti', ['green', 'yellow', 'red'])->nullable()->default('green')->comment('trạng thái cảnh báo nợ');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('staff_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade');
            $table->string('phone');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('identity_number')->comment('cccd or cmnd');
            $table->date('date_insurance')->comment('ngày cấp');
            $table->string('issuance_place')->comment('nơi cấp');
            $table->date('has_contact')->nullable();
            $table->date('birthday')->comment('ngày sinh');
            $table->string('birthplace')->comment('nơi sinh');
            $table->string('native_land')->comment('quê quán');
            $table->string('household')->comment('hộ khẩu thường trú');
            $table->string('staying')->comment('tam trú')->nullable();
            $table->enum('marital_status', ['unmarried', 'married'])->comment('Tình trạng hôn nhân')->default('unmarried');
            $table->string('nationality')->comment('Quốc tịch')->nullable();
            $table->string('folk')->comment('Dân tộc')->nullable();
            $table->string('religion')->comment('Tôn giáo')->nullable();
            $table->enum('level', [
                'secondary',
                'highschool',
                'intermediate',
                'college',
                'bachelor',
                'master',
                'doctorate',
            ])->comment('Trình độ học vấn')->default('highschool');
            $table->enum('type_contact', [
                'fulltime',
                'probation',
                'parttime',
                'intern',
                'partner',
            ])->comment('Loại hợp đồng')->default('fulltime');
            $table->enum('position', [
                'chairman',
                'ceo',
                'deputy_ceo',
                'manager',
                'deputy_manager',
                'staff',
                'leader',
            ])->comment('Chức vụ')->default('staff');
            $table->float('paid_leave')->default(0)->comment('Số ngày nghỉ phép được hưởng trong năm');
            $table->float('insurance_salary_month')->default(0)->comment('Mức đóng bhxh');
            $table->date('date_in')->comment('Ngày vào công ty');
            $table->date('date_out')->nullable()->comment('Ngày nghỉ việc');
            $table->enum('company', [
                'hm', 'homis', 'homex', 'hp',
            ])->default('hm')->comment('Công ty');
            $table->enum('payment_account', [
                'hm', 'homis', 'homex', 'hp',
            ])->nullable()->comment('Tk cty thanh toán');
            $table->jsonb('personnel_management')->nullable()->comment('Quản lý nhân sự');
            $table->enum('driving_licences', [
                'b1', 'b2', 'c', 'd', 'e', 'f', 'fb2', 'fc', 'fd', 'fe',
            ])->nullable()->comment('Bằng lái xe');
            $table->boolean('outside_truck')->default(false)->comment('Chạy xe ngoài');
            $table->boolean('has_evaluate')->default(true)->comment('Đánh giá tháng ');
            $table->integer('personal_deduction')->nullable()->comment('Số người phụ thuộc giảm trừ gia cảnh');
            $table->enum('department', [
                'sale_1', 'sale_2', 'sale_3', 'hr', 'accountant', 'dev', 'markerting',
                'dispatcher', 'parking_1', 'parking_2', 'parking_3', 'legal', 'garage',

            ])->nullable()->comment('Phòng ban');
            $table->timestamps();
            $table->softDeletes();
        });
        // bảng ds liên hệ phụ hoặc người thân
        Schema::create('contact_person', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade');
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('staff_salary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade');
            $table->float('gross_salary')->default(0)->nullable();
            $table->float('basic_salary')->default(0)->nullable();
            $table->float('probationary_salary')->default(0)->nullable()->comment('Lương thử việc');
            $table->float('part_time_salary')->default(0)->nullable()->comment('Lương part time');
            $table->float('meal_allowance')->default(0)->nullable();
            $table->float('metro_allowance')->default(0)->nullable();
            $table->float('phone_allowance')->default(0)->nullable();
            $table->float('other_allowance')->default(0)->nullable();
            $table->float('door_allowance')->default(0)->nullable()->comment('pc mở cổng cho quản lý bãi');
            $table->float('weekend_allowance')->default(0)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_info');
        Schema::dropIfExists('staff_info');
        Schema::dropIfExists('contact_person');
    }
};
