<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientType extends Model
{
    use HasFactory;

    protected $table = 'patient_types';
    protected $primaryKey = 'id';

    public $incrementing = false; // karena id char uuid
    protected $keyType = 'string';

    public $timestamps = true;

    protected $fillable = [
        'id',
        'property_code',
        'title',
        'description',
        'param',
        'jkn',
        'bpjs_jkn',
        'default',
        'pegawai',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'jkn' => 'integer',
        'bpjs_jkn' => 'integer',
        'default' => 'integer',
        'pegawai' => 'integer',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke pasien
    public function patients()
    {
        return $this->hasMany(Patient::class, 'patient_typeid', 'id');
    }
}
