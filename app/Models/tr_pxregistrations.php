<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tr_pxregistrations extends Model
{
    use HasFactory;
    protected $table = 'tr_pxregistrations';
    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = true; // karena ada created_at & updated_at

    /**
     * Kolom yang boleh diisi (disederhanakan ke yang umum dipakai di dashboard)
     * Kalau mau full semua kolom bisa pakai guarded = []
     */
    protected $fillable = [
        'id',
        'property_code',
        'patient_id',
        'nama_px',
        'nik',
        'nrm',
        'queue',
        'section_id',
        'schedule_id',
        'schedule_date',
        'reg_date',
        'reg_time',
        'dokter_id',
        'bpjs_numb',
        'bpjs_sep',
        'insurance_status',
        'status',
        'inpatient_status',
        'status_selesai',
        'status_bayar',
        'created_by',
        'updated_by',

    ];

    protected $casts = [
        'schedule_date' => 'date',
        'reg_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status' => 'integer',
        'status_selesai' => 'integer',
        'status_bayar' => 'integer',
        'inpatient_status' => 'integer',
        'insurance_status' => 'integer',
    ];
    public function rmElectronics()
    {
        return $this->hasMany(RmElectronic::class, 'reg_id', 'id');
    }
    public function schedule()
    {
        return $this->belongsTo(RsvSchedule::class, 'schedule_id', 'id');
    }
    public function dokter()
    {
        return $this->belongsTo(User::class, 'dokter_id', 'id');
    }
}
