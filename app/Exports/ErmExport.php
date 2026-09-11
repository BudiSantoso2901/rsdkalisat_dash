<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ErmExport implements FromCollection, WithHeadings
{
    protected $start;
    protected $end;
    protected $jenis_pasien;
    protected $ruangan;
    protected $jenis_kunjungan;
    protected $dokter;

    public function __construct(
        $start,
        $end,
        $jenis_pasien,
        $ruangan,
        $jenis_kunjungan,
        $dokter
    ) {
        $this->start = $start;
        $this->end = $end;
        $this->jenis_pasien = $jenis_pasien;
        $this->ruangan = $ruangan;
        $this->jenis_kunjungan = $jenis_kunjungan;
        $this->dokter = $dokter;
    }

    public function collection()
    {
        $start = Carbon::parse($this->start)->startOfDay();
        $end = Carbon::parse($this->end)->endOfDay();

        $jenis = $this->jenis_kunjungan ?: 'rajal';

        $query = DB::table('tr_pxregistrations as t')
            ->join('patient_types as pt', 't.type_id', '=', 'pt.id')
            ->join('patients as p', 't.patient_id', '=', 'p.id')
            ->join('users as u', 't.dokter_id', '=', 'u.id')
            ->leftJoin('sections as s3', 't.section_id', '=', 's3.id')

            ->select([
                't.checkout_date',
                't.schedule_date',
                't.reg_date',
                't.selesai_date',

                't.inpatient_status',
                't.parent_id',

                't.bpjs_sep',
                't.numb as no_registrasi',

                'p.nrm',
                'p.name as nama_pasien',

                'u.name as nama_dokter',

                's3.title as ruangan',
                'pt.title as penjamin',

                't.biaya',

                DB::raw("
                    CASE
                        WHEN t.bpjs_sep IS NOT NULL
                        AND t.bpjs_sep <> ''
                        THEN '✔'
                        ELSE '❌'
                    END AS cek_sep
                "),

                DB::raw("
                    CASE
                        WHEN t.rm_diagnosa IS NOT NULL
                        AND t.rm_diagnosa <> ''
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
                        WHEN t.bpjs_sep IS NOT NULL
                        AND t.bpjs_sep <> ''
                        AND t.rm_diagnosa IS NOT NULL
                        AND t.rm_diagnosa <> ''
                        AND t.rm_closing_date IS NOT NULL
                        AND t.bayar_date IS NOT NULL
                        AND t.farmasi_panggil_time IS NOT NULL
                        AND t.radiographer_id IS NOT NULL
                        AND t.analyst_id IS NOT NULL
                        THEN 'LENGKAP'
                        ELSE 'BELUM LENGKAP'
                    END AS status_erm
                ")
            ])

            ->where('t.status', 1)
            ->where('t.parent_id', '0')
            ->whereIn(
                't.source_reg',
                ['ADMISI', 'MJKN', 'NULL']
            );

        /*
        |--------------------------------------------------------------------------
        | JENIS KUNJUNGAN
        |--------------------------------------------------------------------------
        */

        if ($jenis === 'ranap') {

            $query
                ->whereBetween(
                    't.checkout_date',
                    [$start, $end]
                )
                ->where('t.inpatient_status', 1)
                ->orderBy('t.checkout_date', 'ASC');

        } elseif ($jenis === 'igd') {

            $query
                ->whereBetween(
                    't.reg_date',
                    [$start, $end]
                )
                ->where('t.inpatient_status', 0)
                ->whereIn(
                    's3.title',
                    ['IGD 24 JAM', 'PONEK']
                )
                ->orderBy('t.reg_date', 'ASC');

        } else {

            $query
                ->whereBetween(
                    't.schedule_date',
                    [$start, $end]
                )
                ->where('t.inpatient_status', 0)
                ->whereNotIn(
                    's3.title',
                    ['IGD 24 JAM', 'PONEK']
                )
                ->orderBy('t.schedule_date', 'ASC');
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER JENIS PASIEN
        |--------------------------------------------------------------------------
        */

        if (!empty($this->jenis_pasien)) {

            $jenisPasien = is_array($this->jenis_pasien)
                ? $this->jenis_pasien
                : explode(',', $this->jenis_pasien);

            $query->whereIn('pt.title', $jenisPasien);
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER RUANGAN
        |--------------------------------------------------------------------------
        */

        if (!empty($this->ruangan)) {
            $query->where(
                't.section_id',
                $this->ruangan
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER DOKTER
        |--------------------------------------------------------------------------
        */

        if (!empty($this->dokter)) {

            $dokter = is_array($this->dokter)
                ? $this->dokter
                : explode(',', $this->dokter);

            $query->whereIn('t.dokter_id', $dokter);
        }

        /*
        |--------------------------------------------------------------------------
        | SUSUN KOLOM EXCEL
        |--------------------------------------------------------------------------
        */

        return $query
            ->get()
            ->map(function ($row) use ($jenis) {

                $tanggal = function ($value) {
                    return $value
                        ? Carbon::parse($value)->format('d-m-Y H:i')
                        : '-';
                };

                /*
                |--------------------------------------------------------------------------
                | RAWAT JALAN
                |--------------------------------------------------------------------------
                */

                if ($jenis === 'rajal') {

                    return [
                        // Kolom utama
                        $tanggal($row->schedule_date),
                        $row->nrm ?? '-',
                        $row->nama_pasien ?? '-',
                        $row->nama_dokter ?? '-',
                        $row->ruangan ?? '-',
                        $row->penjamin ?? '-',
                        $tanggal($row->selesai_date),
                        $row->no_registrasi ?? '-',
                        $tanggal($row->checkout_date),
                        $row->bpjs_sep ?? '-',

                        // Kolom lainnya tetap ditampilkan
                        $tanggal($row->reg_date),
                        $row->inpatient_status ?? '-',
                        $row->parent_id ?? '-',
                        $row->biaya ?? 0,
                        $row->cek_sep ?? '❌',
                        $row->cek_diagnosa ?? '❌',
                        $row->cek_resume_medis ?? '❌',
                        $row->cek_billing ?? '❌',
                        $row->cek_obat ?? '❌',
                        $row->cek_radiologi ?? '❌',
                        $row->cek_laboratorium ?? '❌',
                        $row->status_erm ?? '-'
                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | RAWAT INAP / IGD
                |--------------------------------------------------------------------------
                */

                return [
                    // Kolom utama
                    $tanggal($row->reg_date),
                    $row->bpjs_sep ?? '-',
                    $tanggal($row->selesai_date),
                    $row->no_registrasi ?? '-',
                    $row->nrm ?? '-',
                    $row->nama_pasien ?? '-',
                    $row->nama_dokter ?? '-',
                    $row->ruangan ?? '-',
                    $row->penjamin ?? '-',
                    $tanggal($row->checkout_date),

                    // Kolom lainnya tetap ditampilkan
                    $tanggal($row->schedule_date),
                    $row->inpatient_status ?? '-',
                    $row->parent_id ?? '-',
                    $row->biaya ?? 0,
                    $row->cek_sep ?? '❌',
                    $row->cek_diagnosa ?? '❌',
                    $row->cek_resume_medis ?? '❌',
                    $row->cek_billing ?? '❌',
                    $row->cek_obat ?? '❌',
                    $row->cek_radiologi ?? '❌',
                    $row->cek_laboratorium ?? '❌',
                    $row->status_erm ?? '-'
                ];
            });
    }

    public function headings(): array
    {
        $jenis = $this->jenis_kunjungan ?: 'rajal';

        if ($jenis === 'rajal') {

            return [
                'Schedule Date',
                'NRM',
                'Nama Pasien',
                'Nama Dokter',
                'Ruangan',
                'Penjamin',
                'Selesai Date',
                'No Registrasi',
                'Checkout Date',
                'No SEP',

                'Reg Date',
                'Inpatient ID',
                'Parent ID',
                'Biaya',
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

        return [
            'Reg Date',
            'No SEP',
            'Selesai Date',
            'No Registrasi',
            'NRM',
            'Nama Pasien',
            'Nama Dokter',
            'Ruangan',
            'Penjamin',
            'Checkout Date',

            'Schedule Date',
            'Inpatient ID',
            'Parent ID',
            'Biaya',
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