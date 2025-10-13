<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class AbsenceRequest extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable,SoftDeletes;

    //
    protected $table = 'absence_requests';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function day()
    {
        return $this->hasMany(AbsenceDay::class, 'absence_id');
    }

    public function approval()
    {
        return $this->hasMany(AbsenceApproval::class, 'absence_id');
    }

    public function getFormattedDateAttribute()
    {
        if ($this->from_date == $this->from_date) {
            return Carbon::parse($this->from_date)->format('d/m/Y'); // không có ngày thì thôi
        }

        return Carbon::parse($this->from_date)->format('d/m/Y').' - '.Carbon::parse($this->to_date)->format('d/m/Y');
    }
}
