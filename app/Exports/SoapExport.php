<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class SoapExport implements FromQuery, WithHeadings, WithMapping, WithTitle
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

    /**
     * Memilih registrasi utama dengan filter yang sama
     * seperti ErmExport.
     */
    protected function rootRegistrationsQuery(): Builder
    {
        $start = Carbon::parse($this->start)->startOfDay();
        $end = Carbon::parse($this->end)->endOfDay();
        $jenis = $this->jenis_kunjungan ?: 'rajal';

        $query = DB::table('tr_pxregistrations as t')
            ->join('patient_types as pt', 't.type_id', '=', 'pt.id')
            ->join('patients as p', 't.patient_id', '=', 'p.id')
            ->join('users as u', 't.dokter_id', '=', 'u.id')
            ->leftJoin('sections as s3', 't.section_id', '=', 's3.id')
            ->select('t.id')
            ->where('t.status', 1)
            ->where('t.parent_id', '0')
            ->whereIn('t.source_reg', ['ADMISI', 'MJKN', 'NULL']);

        if ($jenis === 'ranap') {

            $query
                ->whereBetween('t.checkout_date', [$start, $end])
                ->where('t.inpatient_status', 1);

        } elseif ($jenis === 'igd') {

            $query
                ->whereBetween('t.reg_date', [$start, $end])
                ->where('t.inpatient_status', 0)
                ->whereIn('s3.title', ['IGD 24 JAM', 'PONEK']);

        } else {

            $query
                ->whereBetween('t.schedule_date', [$start, $end])
                ->where('t.inpatient_status', 0)
                ->whereNotIn('s3.title', ['IGD 24 JAM', 'PONEK']);
        }

        // Filter jenis pasien
        if (!empty($this->jenis_pasien)) {

            $jenisPasien = is_array($this->jenis_pasien)
                ? $this->jenis_pasien
                : explode(',', $this->jenis_pasien);

            $query->whereIn('pt.title', $jenisPasien);
        }

        // Filter ruangan
        if (!empty($this->ruangan)) {
            $query->where('t.section_id', $this->ruangan);
        }

        // Filter dokter
        if (!empty($this->dokter)) {

            $dokter = is_array($this->dokter)
                ? $this->dokter
                : explode(',', $this->dokter);

            $query->whereIn('t.dokter_id', $dokter);
        }

        return $query;
    }

    /**
     * Menelusuri registrasi utama dan seluruh turunannya.
     *
     * Hubungan:
     * child.parent_id = parent.id
     *
     * Recursive CTE untuk MariaDB 10.5.
     */
    protected function registrationChainSubquery(): Builder
    {
        $roots = $this->rootRegistrationsQuery();

        $rootSql = $roots->toSql();
        $bindings = $roots->getBindings();

        $sql = "(
            WITH RECURSIVE registration_chain
                (reg_id, root_id, depth) AS (

                SELECT
                    selected_roots.id AS reg_id,
                    selected_roots.id AS root_id,
                    0 AS depth
                FROM ({$rootSql}) AS selected_roots

                UNION ALL

                SELECT
                    child.id AS reg_id,
                    parent.root_id,
                    parent.depth + 1 AS depth
                FROM tr_pxregistrations AS child
                INNER JOIN registration_chain AS parent
                    ON child.parent_id = parent.reg_id

            ) CYCLE reg_id RESTRICT

            SELECT
                reg_id,
                root_id,
                depth
            FROM registration_chain

        ) AS registration_chain";

        return DB::query()->fromRaw($sql, $bindings);
    }

    /**
     * Query utama SOAP.
     */
    public function query(): Builder
    {
        $chain = $this->registrationChainSubquery();

        return DB::table('rm_cppt as c')

            ->joinSub(
                $chain,
                'registration_chain',
                function ($join) {
                    $join->on(
                        'registration_chain.reg_id',
                        '=',
                        'c.reg_id'
                    );
                }
            )

            ->join(
                'tr_pxregistrations as root_reg',
                'root_reg.id',
                '=',
                'registration_chain.root_id'
            )

            ->join(
                'tr_pxregistrations as soap_reg',
                'soap_reg.id',
                '=',
                'registration_chain.reg_id'
            )

            ->join(
                'patients as p',
                'p.id',
                '=',
                'root_reg.patient_id'
            )

            ->select([
                'root_reg.numb as no_registrasi_utama',
                'soap_reg.numb as no_registrasi_soap',

                'registration_chain.depth as transfer_depth',

                'p.nrm',
                'p.name as nama_pasien',

                'c.date as tanggal_cppt',
                'c.time as jam_cppt',

                'c.profesi',
                'c.ppa',

                'c.subyective as subjective',
                'c.obyective as objective',
                'c.asessment as assessment',

                'c.planning',
                'c.instruction',
                'c.instruction_obat',

                'c.dpjp_verified_status',

                'c.idx as cppt_idx',
            ])

            ->orderBy('root_reg.id')
            ->orderBy('c.date')
            ->orderBy('c.time')
            ->orderBy('c.idx');
    }

    /**
     * Mapping kolom Excel.
     */
    public function map($row): array
    {
        $tanggal = $row->tanggal_cppt
            ? Carbon::parse($row->tanggal_cppt)->format('d-m-Y')
            : '-';

        $statusVerifikasi = $row->dpjp_verified_status === null
            ? '-'
            : ((int) $row->dpjp_verified_status === 1
                ? 'Terverifikasi'
                : 'Belum Terverifikasi');

        return [
            $row->no_registrasi_utama ?? '-',
            $row->no_registrasi_soap ?? '-',

            (int) $row->transfer_depth === 0
                ? 'UTAMA'
                : 'TRANSFER',

            $row->nrm ?? '-',
            $row->nama_pasien ?? '-',

            $tanggal,
            $row->jam_cppt ?? '-',

            $row->profesi ?? '-',
            $row->ppa ?? '-',

            $row->subjective ?? '-',
            $row->objective ?? '-',
            $row->assessment ?? '-',

            $row->planning ?? '-',
            $row->instruction ?? '-',
            $row->instruction_obat ?? '-',

            $statusVerifikasi,
        ];
    }

    /**
     * Judul sheet.
     */
    public function title(): string
    {
        return 'RIWAYAT_SOAP';
    }

    /**
     * Header Excel.
     */
    public function headings(): array
    {
        return [
            'No Registrasi Utama',
            'No Registrasi SOAP',
            'Jenis Registrasi',
            'NRM',
            'Nama Pasien',
            'Tanggal CPPT',
            'Jam CPPT',
            'Profesi',
            'PPA',
            'Subjective',
            'Objective',
            'Assessment',
            'Planning',
            'Instruction',
            'Instruction Obat',
            'Status Verifikasi DPJP',
        ];
    }
}