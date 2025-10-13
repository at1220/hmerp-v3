<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class AbsenceDay extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable,SoftDeletes;

    //
    protected $table = 'absence_days';

    protected $guarded = [];

    public function absence()
    {
        return $this->belongsTo(AbsenceRequest::class, 'absence_id');
    }

    protected static function boot()
    {
        parent::boot();

        // Khi lưu (create / update)
        static::saved(function ($day) {
            $day->updateAbsenceRequestTotal();
        });

        // Khi xóa mềm
        static::deleted(function ($day) {
            $day->updateAbsenceRequestTotal();
        });

        // Khi khôi phục
        static::restored(function ($day) {
            $day->updateAbsenceRequestTotal();
        });
    }

    public function updateAbsenceRequestTotal()
    {
        if (! $this->absence) {
            return;
        }

        // Lấy tất cả absence_days còn tồn tại (chưa bị soft delete)
        $total = $this->absence
            ->day()
            ->get()
            ->sum(function ($day) {
                return $day->part_of_day == 'day' ? 1 : 0.5;
            });

        $this->absence->update(['total_day' => $total]);
    }
}
