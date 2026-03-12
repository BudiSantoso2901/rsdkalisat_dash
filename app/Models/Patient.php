<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patients';
    protected $primaryKey = 'id';

    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = true;

    protected $fillable = [
        'id',
        'property_code',
        'nrm',
        'name',
        'gender',
        'birth_date',
        'phone',
        'address',
        'bpjs_numb',
        'patient_typeid',
        'reg_date',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'reg_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke jenis pasien
    public function type()
    {
        return $this->belongsTo(PatientType::class, 'patient_typeid', 'id');
    }

    // Relasi ke registrasi
    public function registrations()
    {
        return $this->hasMany(tr_pxregistrations::class, 'patient_id', 'id');
    }
}
