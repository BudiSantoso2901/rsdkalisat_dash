<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class ErmExport implements FromCollection, WithHeadings
{
    protected $start;
    protected $end;
    protected $jenis_pasien;
    protected $ruangan;
    protected $jenis_kunjungan;
    protected $dokter; // 🔥 TAMBAHAN

    public function __construct($start, $end, $jenis_pasien, $ruangan, $jenis_kunjungan, $dokter)
    {
        $this->start = $start;
        $this->end   = $end;
        $this->jenis_pasien = $jenis_pasien;
        $this->ruangan = $ruangan;
        $this->jenis_kunjungan = $jenis_kunjungan;
        $this->dokter = $dokter; // 🔥 TAMBAHAN
    }

    public function collection()
    {
        $start = Carbon::parse($this->start)->startOfDay();
        $end   = Carbon::parse($this->end)->endOfDay();

        $query = DB::table('tr_pxregistrations as t')

            ->select(
                't.checkout_date',
                't.schedule_date',
                't.inpatient_status',
                't.parent_id',
                't.bpjs_sep',
                't.reg_date',
                't.selesai_date',
                't.numb as no_registrasi',
                'p.nrm',
                'p.name as nama_pasien',
                'u.name as nama_dokter',
                's3.title as ruangan',
                'pt.title as penjamin',


                DB::raw("CASE WHEN t.bpjs_sep IS NOT NULL AND t.bpjs_sep <> '' THEN '✔' ELSE '❌' END AS cek_sep"),
                DB::raw("CASE WHEN t.rm_diagnosa IS NOT NULL AND t.rm_diagnosa <> '' THEN '✔' ELSE '❌' END AS cek_diagnosa"),
                DB::raw("CASE WHEN t.rm_closing_date IS NOT NULL THEN '✔' ELSE '❌' END AS cek_resume_medis"),
                DB::raw("CASE WHEN t.bayar_date IS NOT NULL THEN '✔' ELSE '❌' END AS cek_billing"),
                DB::raw("CASE WHEN t.farmasi_panggil_time IS NOT NULL THEN '✔' ELSE '❌' END AS cek_obat"),
                DB::raw("CASE WHEN t.radiographer_id IS NOT NULL THEN '✔' ELSE '❌' END AS cek_radiologi"),
                DB::raw("CASE WHEN t.analyst_id IS NOT NULL THEN '✔' ELSE '❌' END AS cek_laboratorium"),

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
            ->where('t.parent_id', 0)
            ->whereIn('t.source_reg', ['ADMISI', 'MJKN', 'NULL'])

            ->where(function ($q) {
                $q->where('s3.title', 'not like', '%LAB%')
                    ->where('s3.title', 'not like', '%RADIOLOGI%');
            });

        /*
        |------------------------------------------------------------------
        | FILTER JENIS KUNJUNGAN
        |------------------------------------------------------------------
        */
        if ($this->jenis_kunjungan == 'rajal') {

            $query->whereBetween('t.schedule_date', [$start, $end])
                ->where('t.inpatient_status', 0)
                ->where('s3.title', '!=', 'IGD 24 JAM')
                ->orderBy('t.schedule_date', 'ASC');
        } elseif ($this->jenis_kunjungan == 'ranap') {

            $query->whereBetween('t.checkout_date', [$start, $end])
                ->where('t.inpatient_status', 1)
                ->orderBy('t.checkout_date', 'ASC');
        } elseif ($this->jenis_kunjungan == 'igd') {

            $query->whereBetween('t.reg_date', [$start, $end])
                ->whereIn('s3.title', ['IGD 24 JAM', 'PONEK'])
                ->orderBy('t.reg_date', 'ASC');
        }

        /*
        |------------------------------------------------------------------
        | FILTER JENIS PASIEN
        |------------------------------------------------------------------
        */
        if (!empty($this->jenis_pasien)) {

            $jenis = $this->jenis_pasien;

            if (!is_array($jenis)) {
                $jenis = explode(',', $jenis);
            }

            $query->whereIn('pt.title', $jenis);
        }

        /*
        |------------------------------------------------------------------
        | FILTER RUANGAN
        |------------------------------------------------------------------
        */
        if (!empty($this->ruangan)) {
            $query->where('t.section_id', $this->ruangan);
        }

        /*
        |------------------------------------------------------------------
        | FILTER DOKTER 🔥
        |------------------------------------------------------------------
        */
        if (!empty($this->dokter)) {

            $dokter = $this->dokter;

            if (!is_array($dokter)) {
                $dokter = explode(',', $dokter);
            }

            $query->whereIn('t.dokter_id', $dokter);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Checkout Date',
            'Schedule Date',
            'Inpatient ID',
            'Parent ID',
            'No SEP',
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
