<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class AbsenceApproval extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable,SoftDeletes;

    //
    protected $table = 'absence_approvals';

    protected $guarded = [];

    public function absence()
    {
        return $this->belongsTo(AbsenceRequest::class, 'absence_id');
    }

    public function approval()
    {
        return $this->belongsTo(User::class, 'approval_id');
    }
}
