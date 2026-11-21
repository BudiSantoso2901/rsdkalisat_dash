<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RmCppt extends Model
{
    protected $table = 'rm_cppt';

    protected $primaryKey = 'idx';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'dpjp_verified_at' => 'datetime',
        'dpjp_verified_status' => 'integer',
    ];
}