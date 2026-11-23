<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class DashboardUser extends Authenticatable
{
    protected $table = 'dashboard_users';

    protected $fillable = [
        'nama',
        'username',
        'password',
        'role',
        'profesi',
        'status',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'status' => 'boolean',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];
}