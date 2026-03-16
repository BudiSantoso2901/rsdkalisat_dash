<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ErmExport implements FromCollection, WithHeadings
{
    protected $start;
    protected $end;
    protected $jenis;

    public function __construct($start, $end, $jenis)
    {
        $this->start = $start;
        $this->end   = $end;
        $this->jenis = $jenis;
    }

    public function collection()
    {
        $query = DB::table('tr_pxregistrations as t')

            ->select(

                't.schedule_date',
                't.bpjs_sep',
                't.reg_date',
                't.selesai_date',
                't.numb as no_registrasi',
                'p.nrm',
                'p.name as nama_pasien',
                'u.name as nama_dokter',
                's3.title as ruangan',
                'pt.title as penjamin',

                DB::raw("
                    CASE
                        WHEN t.bpjs_sep IS NOT NULL AND t.bpjs_sep <> ''
                        THEN '✔'
                        ELSE '❌'
                    END AS cek_sep
                "),

                DB::raw("
                    CASE
                        WHEN t.rm_diagnosa IS NOT NULL AND t.rm_diagnosa <> ''
                        THEN '✔'
                        ELSE '❌'
                    END AS cek_diagnosa
                "),

                DB::raw("
                    CASE
                        WHEN t.rm_closing_date IS NOT NULL
                        THEN '✔'
                        ELSE '❌'
                    END AS cek_resume_medis
                "),

                DB::raw("
                    CASE
                        WHEN t.bayar_date IS NOT NULL
                        THEN '✔'
                        ELSE '❌'
                    END AS cek_billing
                "),

                DB::raw("
                    CASE
                        WHEN t.farmasi_panggil_time IS NOT NULL
                        THEN '✔'
                        ELSE '❌'
                    END AS cek_obat
                "),

                DB::raw("
                    CASE
                        WHEN t.radiographer_id IS NOT NULL
                        THEN '✔'
                        ELSE '❌'
                    END AS cek_radiologi
                "),

                DB::raw("
                    CASE
                        WHEN t.analyst_id IS NOT NULL
                        THEN '✔'
                        ELSE '❌'
                    END AS cek_laboratorium
                "),

                DB::raw("
                    CASE
                        WHEN t.bpjs_sep IS NOT NULL AND t.bpjs_sep <> ''
                        AND t.rm_diagnosa IS NOT NULL AND t.rm_diagnosa <> ''
                        AND t.rm_closing_date IS NOT NULL
                        AND t.bayar_date IS NOT NULL
                        AND t.farmasi_panggil_time IS NOT NULL
                        AND t.radiographer_id IS NOT NULL
                        AND t.analyst_id IS NOT NULL
                        THEN 'LENGKAP'
                        ELSE 'BELUM LENGKAP'
                    END AS status_erm
                ")
            )

            ->join('patient_types as pt', 't.type_id', '=', 'pt.id')
            ->join('patients as p', 't.patient_id', '=', 'p.id')
            ->join('users as u', 't.dokter_id', '=', 'u.id')
            ->leftJoin('sections as s3', 't.section_id', '=', 's3.id')

            ->where('t.status', 1)

            /*
            |--------------------------------------------------------------------------
            | HILANGKAN POLI LAB & RADIOLOGI
            |--------------------------------------------------------------------------
            */

            ->where(function ($q) {
                $q->where('s3.title', 'not like', '%LAB%')
                    ->where('s3.title', 'not like', '%RADIOLOGI%');
            })

            /*
            |--------------------------------------------------------------------------
            | FILTER TANGGAL
            |--------------------------------------------------------------------------
            */

            ->whereBetween('t.schedule_date', [
                $this->start . ' 00:00:00',
                $this->end . ' 23:59:59'
            ])

            /*
            |--------------------------------------------------------------------------
            | FILTER JENIS PASIEN
            |--------------------------------------------------------------------------
            */

            ->when($this->jenis, function ($q) {
                $q->where('pt.title', $this->jenis);
            })

            ->orderBy('t.reg_date', 'ASC');

        return $query->get();
    }

    public function headings(): array
    {
        return [

            'Schedule Date',
            'No.SEP',
            'Reg Date',
            'Selesai Date',
            'No Registrasi',
            'NRM',
            'Nama Pasien',
            'Nama Dokter',
            'Ruangan',
            'Penjamin',
            'SEP',
            'Diagnosa',
            'Resume Medis',
            'Billing',
            'Obat',
            'Radiologi',
            'Laboratorium',
            'Status ERM'
        ];
    }
}
