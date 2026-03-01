<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RsvSchedule extends Model
{
    protected $table = 'rsv_schedules';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'title',
        'section_id',
        'kodesubspesialis',
        'date',
        'open_time',
        'closed_time',
        'kapasitaspasien'
    ];

    /**
     * Relasi ke Section
     */
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }
    public function dokter()
    {
        return $this->belongsTo(User::class, 'dokter_id', 'id');
    }
    public function registrations()
    {
        return $this->hasMany(tr_pxregistrations::class, 'schedule_id', 'id')
            ->where('status_batal', 0);
    }
    /**
     * Scope untuk ambil data sesuai kebutuhan
     */
    public function scopeWithSectionData($query)
    {
        return $query->join('sections', 'rsv_schedules.section_id', '=', 'sections.id')
            ->select([
                'rsv_schedules.title as nama_dokter',
                'sections.code',
                'sections.prefix',
                'sections.title as section_title',
                'rsv_schedules.kodesubspesialis',
                'rsv_schedules.date',
                'rsv_schedules.open_time',
                'rsv_schedules.closed_time',
                'rsv_schedules.kapasitaspasien'
            ]);
    }
}
