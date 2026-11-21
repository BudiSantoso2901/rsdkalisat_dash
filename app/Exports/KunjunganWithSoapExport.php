<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class KunjunganWithSoapExport implements WithMultipleSheets
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

    public function sheets(): array
    {
        return [

            // SHEET 1: DATA KUNJUNGAN LAMA
            new ErmExport(
                $this->start,
                $this->end,
                $this->jenis_pasien,
                $this->ruangan,
                $this->jenis_kunjungan,
                $this->dokter
            ),

            // SHEET 2: RIWAYAT SOAP
            new SoapExport(
                $this->start,
                $this->end,
                $this->jenis_pasien,
                $this->ruangan,
                $this->jenis_kunjungan,
                $this->dokter
            ),

        ];
    }
}