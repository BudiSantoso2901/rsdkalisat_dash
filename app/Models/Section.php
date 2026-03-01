<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $table = 'sections';

    protected $primaryKey = 'idx';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'id',
        'property_code',
        'category_id',
        'specialist_id',
        'rj_non_bpjs',
        'role_lab',
        'for_transfer',
        'loket_id_admisi',
        'loket_id_admisi_bkp',
        'code',
        'prefix',
        'ranap',
        'title',
        'bpjs_title',
        'aliases',
        'description',
        'role',
        'autorate',
        'isverification_rm',
        'ordering',
        'latest_sync',
        'estimation_time',
        'ruang',
        'ss_location_id',
        'ss_response',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'ranap' => 'integer',
        'for_transfer' => 'integer',
        'isverification_rm' => 'integer',
        'ordering' => 'integer',
        'status' => 'integer',
        'estimation_time' => 'datetime:H:i:s',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */


    // Registrations (tr_pxregistrations)
    public function registrations()
    {
        return $this->hasMany(tr_pxregistrations::class, 'section_id', 'id');
    }
    public function rmElectronics()
    {
        return $this->hasMany(RmElectronic::class, 'section_id', 'id');
    }
    public function schedules()
    {
        return $this->hasMany(RsvSchedule::class, 'section_id', 'id');
    }
}
