<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RmElectronic extends Model
{
    use HasFactory;

    protected $table = 'rm_electronics';

    // Primary key bukan id
    protected $primaryKey = 'idx';

    // Karena primary key auto increment bigint
    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'property_code',
        'patient_id',
        'erm_id',
        'reg_id',
        'numb',
        'nomor_surat',
        'title',
        'sub_title',
        'description',
        'type',
        'kategori',
        'size',
        'role',
        'form',
        'path',
        'erm_json',
        'section_id',
        'dokter_id',
        'status',
        'created_by',
        'updated_by',
        'pdf',
        'pdf_created',
        'pdf_updated',
        'status_publish',
        'date_publish'
    ];

    protected $casts = [
        'erm_json' => 'array',
        'json_hasil_lab' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'pdf_created' => 'datetime',
        'pdf_updated' => 'datetime',
        'date_publish' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // Relasi ke tr_pxregistrations
    public function registration()
    {
        return $this->belongsTo(tr_pxregistrations::class, 'reg_id', 'id');
    }

    // Relasi ke sections
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }
}
