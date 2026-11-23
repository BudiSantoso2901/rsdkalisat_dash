<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class SimrsUser extends Authenticatable
{
    protected $connection = 'mysql';

    protected $table = 'users';

    // Primary key SIMRS adalah idx, bukan id.
    protected $primaryKey = 'idx';

    public $incrementing = true;

    protected $keyType = 'int';

    // Model khusus autentikasi baca saja.
    public $timestamps = false;

    protected $guarded = ['*'];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}