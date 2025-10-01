<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class ContactPerson extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable,SoftDeletes;

    //
    protected $guarded = [];

    protected $table = 'contact_person';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
