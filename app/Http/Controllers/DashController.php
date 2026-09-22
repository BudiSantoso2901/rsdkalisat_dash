<?php

namespace App\Http\Controllers;

use App\Exports\ErmExport;
use App\Models\tr_pxregistrations;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;


class DashController extends Controller
{
    //

    public function view_dokter()
    {
        $filterPoli = DB::table('rsv_schedules as s')
            ->join('sections as sec', 's.section_id', '=', 'sec.id')
            ->select(
                'sec.id',
                'sec.title'
            )
            ->distinct()
            ->orderBy('sec.title')
            ->get();


        $filterDokter = DB::table('rsv_schedules as s')
            ->join('users as u', 's.dokter_id', '=', 'u.id')
            ->select(
                'u.id',
                'u.name',
                's.section_id'
            )
            ->whereNotNull('u.name')
            ->distinct()
            ->orderBy('u.name')
            ->get();


        return view('Page.dokter', compact(
            'filterPoli',
            'filterDokter'
        ));
    }
    public function view_tabel_pasien()
    {
        return view('Page.tabel');
    }
    public function getData(Request $request)
    {
        $query = DB::table('rm_electronics')
            ->join('tr_pxregistrations', 'rm_electronics.reg_id', '=', 'tr_pxregistrations.id')
            ->join('sections', 'tr_pxregistrations.section_id', '=', 'sections.id')
            ->select([
                'tr_pxregistrations.reg_date',
                'tr_pxregistrations.idcardtype',
                'tr_pxregistrations.idcardnumb',
                'tr_pxregistrations.nrm',
                'rm_electronics.numb',
                'tr_pxregistrations.checkout_date',
                'tr_pxregistrations.checkout_time',
                'tr_pxregistrations.bpjs_numb',
                'tr_pxregistrations.bpjs_sep',
                'sections.code',
                'sections.prefix',
                'tr_pxregistrations.biaya',
                'tr_pxregistrations.bayar_bpjs'
            ]);

        /*
    |--------------------------------------------------------------------------
    | FILTER TANGGAL
    |--------------------------------------------------------------------------
    */

        // 🔹 Filter tanggal tunggal
        if ($request->filled('tanggal')) {
            $query->whereDate(
                'tr_pxregistrations.reg_date',
                Carbon::parse($request->tanggal)->format('Y-m-d')
            );
        }

        // 🔹 Filter range tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {

            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();

            $query->whereBetween('tr_pxregistrations.reg_date', [
                $start,
                $end
            ]);
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->editColumn('reg_date', function ($row) {
                return $row->reg_date
                    ? Carbon::parse($row->reg_date)->format('d-m-Y')
                    : '-';
            })

            ->editColumn('checkout_date', function ($row) {
                return $row->checkout_date
                    ? Carbon::parse($row->checkout_date)->format('d-m-Y')
                    : '-';
            })

            ->make(true);
    }
    // public function view_dashboard()
    // {
    //     return view('Page.dashboard');
    // }
    public function jadwalDokterHariIni(Request $request)
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = (int) ($request->tahun ?? now()->year);

        // Tentukan periode berdasarkan filter
        if ($bulan === 'all') {
            $awalPeriode = Carbon::createFromDate($tahun, 1, 1)->startOfDay();
            $akhirPeriode = Carbon::createFromDate($tahun, 12, 31)->endOfDay();
        } else {
            $bulan = (int) $bulan;

            $awalPeriode = Carbon::createFromDate($tahun, $bulan, 1)->startOfDay();
            $akhirPeriode = $awalPeriode->copy()->endOfMonth()->endOfDay();
        }

        /*
        |--------------------------------------------------------------------------
        | BASE QUERY
        |--------------------------------------------------------------------------
        */
        $baseQuery = DB::table('tr_pxregistrations as t')
            ->whereIn('t.source_reg', ['ADMISI', 'MJKN', 'NULL'])
            ->where('t.status', 1)
            ->where('t.parent_id', '0')
            ->where('t.status_batal', 0);

        /*
        |--------------------------------------------------------------------------
        | KUNJUNGAN + KUOTA PER POLI
        |--------------------------------------------------------------------------
        */

        // Total kuota dari seluruh jadwal dokter per poli dalam bulan terpilih
        $kuotaPerPoli = DB::table('rsv_schedules as rs')
            ->select(
                'rs.section_id',
                DB::raw('SUM(COALESCE(rs.kapasitaspasien, 0)) as total_kuota')
            )
            ->whereBetween('rs.date', [
                $awalPeriode->toDateString(),
                $akhirPeriode->toDateString()
            ])
            ->groupBy('rs.section_id');


        // Total kunjungan + kuota setiap poli
        $kunjunganPerPoli = DB::table('tr_pxregistrations as t')
            ->join('sections as s', 't.section_id', '=', 's.id')

            ->leftJoinSub($kuotaPerPoli, 'q', function ($join) {
                $join->on('q.section_id', '=', 's.id');
            })

            ->selectRaw('
                s.id as section_id,
                s.title as nama_poli,
                COUNT(t.id) as total,
                COALESCE(q.total_kuota, 0) as total_kuota
            ')

            ->whereBetween('t.schedule_date', [$awalPeriode, $akhirPeriode])

            ->where('t.inpatient_status', 0)
            ->whereNotIn('s.title', ['IGD 24 JAM', 'PONEK'])
            ->whereIn('t.source_reg', ['ADMISI', 'MJKN', 'NULL'])
            ->where('t.status', 1)
            ->where('t.parent_id', '0')
            ->where('t.status_batal', 0)

            ->groupBy(
                's.id',
                's.title',
                'q.total_kuota'
            )

            ->orderByDesc('total')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | RAWAT JALAN
        |--------------------------------------------------------------------------
        */
        $rawatJalan = DB::table('tr_pxregistrations as t')
            ->join('sections as s', 't.section_id', '=', 's.id')

            ->whereBetween('t.schedule_date', [$awalPeriode, $akhirPeriode])

            ->where('t.inpatient_status', 0)
            ->where('s.title', '!=', 'IGD 24 JAM')
            ->whereIn('t.source_reg', ['ADMISI', 'MJKN', 'NULL'])
            ->where('t.status', 1)
            ->where('t.parent_id', '0')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | RAWAT INAP
        |--------------------------------------------------------------------------
        */
        $rawatInap = DB::table('tr_pxregistrations as t')
            ->whereBetween('t.checkout_date', [$awalPeriode, $akhirPeriode])
            ->where('t.inpatient_status', 1)
            ->whereIn('t.source_reg', ['ADMISI', 'MJKN', 'NULL'])
            ->where('t.status', 1)
            ->where('t.parent_id', '0')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | IGD + PONEK
        |--------------------------------------------------------------------------
        */
        $igd = DB::table('tr_pxregistrations as t')
            ->join('sections as s', 't.section_id', '=', 's.id')

            ->whereBetween('t.reg_date', [$awalPeriode, $akhirPeriode])

            ->where('t.inpatient_status', 0)
            ->whereIn('s.title', ['IGD 24 JAM', 'PONEK'])
            ->whereIn('t.source_reg', ['ADMISI', 'MJKN', 'NULL'])
            ->where('t.status', 1)
            ->where('t.parent_id', '0')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | PASIEN BARU VS LAMA
        | Sebelumnya 2 query, sekarang hanya 1 query
        |--------------------------------------------------------------------------
        */
        $pasienStatus = (clone $baseQuery)
            ->whereBetween('t.schedule_date', [$awalPeriode, $akhirPeriode])
            ->selectRaw('
            SUM(CASE WHEN t.first_regstatus = 1 THEN 1 ELSE 0 END) AS baru,
            SUM(CASE WHEN t.first_regstatus = 0 THEN 1 ELSE 0 END) AS lama
        ')
            ->first();

        $pasienBaru = (int) ($pasienStatus->baru ?? 0);
        $pasienLama = (int) ($pasienStatus->lama ?? 0);

        /*
        |--------------------------------------------------------------------------
        | JENIS PASIEN
        |--------------------------------------------------------------------------
        */
        $jenisPasien = DB::table('tr_pxregistrations as t')
            ->join('patient_types as pt', 't.type_id', '=', 'pt.id')
            ->selectRaw('pt.title, COUNT(t.id) as total')

            ->whereBetween('t.schedule_date', [$awalPeriode, $akhirPeriode])

            ->whereIn('t.source_reg', ['ADMISI', 'MJKN', 'NULL'])
            ->where('t.status', 1)
            ->where('t.parent_id', 0)

            ->groupBy('pt.title')
            ->pluck('total', 'pt.title');

        /*
        |--------------------------------------------------------------------------
        | KUNJUNGAN DOKTER
        |--------------------------------------------------------------------------
        */
        $kuotaPerDokter = DB::table('rsv_schedules as rs')
            ->select(
                'rs.dokter_id',
                'rs.section_id',
                DB::raw('SUM(COALESCE(rs.kapasitaspasien, 0)) as total_kuota')
            )
            ->whereBetween('rs.date', [
                $awalPeriode->toDateString(),
                $akhirPeriode->toDateString()
            ])
            ->groupBy('rs.dokter_id', 'rs.section_id');

        $pxdokter = DB::table('tr_pxregistrations as t')
            ->join('users as u', 't.dokter_id', '=', 'u.id')
            ->join('sections as s', 't.section_id', '=', 's.id')

            ->leftJoinSub($kuotaPerDokter, 'q', function ($join) {
                $join->on('q.dokter_id', '=', 't.dokter_id')
                    ->on('q.section_id', '=', 't.section_id');
            })

            ->select(
                'u.name as nama_dokter',
                's.title as nama_poli',
                DB::raw('COUNT(t.id) as total_pasien'),
                DB::raw('MAX(COALESCE(q.total_kuota, 0)) as total_kuota')

            )

            ->whereBetween('t.schedule_date', [$awalPeriode, $akhirPeriode])

            ->where('t.inpatient_status', 0)
            ->whereNotIn('s.title', ['IGD 24 JAM', 'PONEK'])
            ->whereIn('t.source_reg', ['ADMISI', 'MJKN', 'NULL'])
            ->where('t.status', 1)
            ->where('t.parent_id', 0)
            ->where('t.status_batal', 0)

            ->groupBy('u.name', 's.title')
            ->orderByDesc('total_pasien')
            ->get();

        $labelsDokter = [];
        $dataDokter = [];

        foreach ($pxdokter as $item) {
            $labelsDokter[] = $item->nama_dokter . ' (' . $item->nama_poli . ')';
            $dataDokter[] = $item->total_pasien;
        }

        return view('Page.dashboard', compact(
            'kunjunganPerPoli',
            'rawatJalan',
            'rawatInap',
            'igd',
            'pasienBaru',
            'pasienLama',
            'bulan',
            'tahun',
            'jenisPasien',
            'pxdokter',
            'labelsDokter',
            'dataDokter'
        ));
    }

    public function getJadwalDokter(Request $request)
    {
        $bulan = (int) ($request->bulan ?? now()->month);
        $tahun = (int) ($request->tahun ?? now()->year);

        $awalBulan = Carbon::createFromDate(
            $tahun,
            $bulan,
            1
        )->startOfDay();

        $akhirBulan = $awalBulan
            ->copy()
            ->endOfMonth()
            ->endOfDay();

        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : null;

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : null;

        /*
         * Validasi hanya boleh dalam
         * bulan + tahun terpilih
         */
        if ($startDate && $endDate) {

            $validStart =
                $startDate->month === $bulan &&
                $startDate->year === $tahun;

            $validEnd =
                $endDate->month === $bulan &&
                $endDate->year === $tahun;

            if (!$validStart || !$validEnd) {

                return response()->json([
                    'message' =>
                    'Rentang tanggal harus berada dalam bulan dan tahun terpilih.'
                ], 422);
            }


            if ($startDate->gt($endDate)) {

                return response()->json([
                    'message' =>
                    'Tanggal mulai tidak boleh setelah tanggal selesai.'
                ], 422);
            }
        }

        $awalPeriode =
            ($startDate && $endDate)
            ? $startDate
            : $awalBulan;

        $akhirPeriode =
            ($startDate && $endDate)
            ? $endDate
            : $akhirBulan;

        /*
     |--------------------------------------------------------------------------
     | TOTAL PASIEN PER DOKTER + POLI DALAM 1 BULAN
     |--------------------------------------------------------------------------
     | Mengikuti filter Statistik Kunjungan Dokter di Dashboard.
     | 1 row = 1 dokter + 1 poli.
     */

        $pasienPerDokter = DB::table('tr_pxregistrations as r')
            ->join('sections as sec_reg', 'r.section_id', '=', 'sec_reg.id')

            ->selectRaw('
                r.dokter_id,
                r.section_id,
                COUNT(r.id) as total_pasien
            ')

            ->whereBetween('r.schedule_date', [
                $awalPeriode,
                $akhirPeriode
            ])

            ->where('r.inpatient_status', 0)

            ->whereNotIn('sec_reg.title', [
                'IGD 24 JAM',
                'PONEK'
            ])

            ->whereIn('r.source_reg', [
                'ADMISI',
                'MJKN',
                'NULL'
            ])

            ->where('r.status', 1)
            ->where('r.parent_id', 0)
            ->where('r.status_batal', 0)

            ->groupBy(
                'r.dokter_id',
                'r.section_id'
            );


        /*
        |--------------------------------------------------------------------------
        | JADWAL + KUOTA PER DOKTER + POLI
        |--------------------------------------------------------------------------
        */

        $query = DB::table('rsv_schedules as s')

            ->join('sections as sec', 's.section_id', '=', 'sec.id')
            ->join('users as u', 's.dokter_id', '=', 'u.id')

            ->leftJoinSub($pasienPerDokter, 'p', function ($join) {

                $join->on(
                    'p.dokter_id',
                    '=',
                    's.dokter_id'
                )

                    ->on(
                        'p.section_id',
                        '=',
                        's.section_id'
                    );
            })

            ->selectRaw('
                s.dokter_id,
                s.section_id,

                u.name as nama_dokter,
                sec.title as nama_poli,

                COUNT(DISTINCT DATE(s.date)) as total_hari_layanan,

                SUM(COALESCE(s.kapasitaspasien, 0)) as total_kuota,

                COALESCE(MAX(p.total_pasien), 0) as total_pasien
            ')

            ->whereBetween('s.date', [
                $awalPeriode->toDateString(),
                $akhirPeriode->toDateString()
            ])

            ->when($request->filled('poli'), function ($query) use ($request) {
                $query->where('s.section_id', $request->poli);
            })

            ->when($request->filled('dokter'), function ($query) use ($request) {
                $query->where('s.dokter_id', $request->dokter);
            })

            ->groupBy(
                's.dokter_id',
                's.section_id',
                'u.name',
                'sec.title'
            )

            ->orderBy('u.name');
        /*
          |--------------------------------------------------------------------------
          | DATATABLE
          |--------------------------------------------------------------------------
          */

        return DataTables::of($query)
            ->addIndexColumn()
            ->make(true);
    }
    public function view_kunjungan_poli()
    {
        $ruangan = DB::table('sections')
            ->where('title', '!=', 'IGD 24 JAM')
            ->orderBy('title', 'asc')
            ->get();

        // 🔥 TAMBAHAN QUERY DOKTER
        $dokter = DB::table('users')
            ->whereNotNull('name')
            ->orderBy('name', 'asc')
            ->get();

        return view('Page.kunjungan_poli', compact('ruangan', 'dokter'));
    }
    public function getKunjunganPoli(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | FILTER TANGGAL
    |--------------------------------------------------------------------------
    */
        $tanggalMulai = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::today()->startOfDay();

        $tanggalSelesai = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::today()->endOfDay();

        /*
    |--------------------------------------------------------------------------
    | JENIS KUNJUNGAN
    |--------------------------------------------------------------------------
    | all   = Semua
    | rajal = Rawat Jalan
    | ranap = Rawat Inap
    | igd   = IGD & PONEK
    |--------------------------------------------------------------------------
    */
        $jenisKunjungan = $request->input('jenis_kunjungan', 'all');

        /*
    |--------------------------------------------------------------------------
    | VALIDASI JENIS KUNJUNGAN
    |--------------------------------------------------------------------------
    */
        if (!in_array($jenisKunjungan, ['all', 'rajal', 'ranap', 'igd'])) {
            $jenisKunjungan = 'all';
        }

        /*
    |--------------------------------------------------------------------------
    | QUERY REGISTRASI DASAR
    |--------------------------------------------------------------------------
    */
        $registrasi = DB::table('tr_pxregistrations')
            ->select([
                'schedule_date',
                'reg_date',
                'checkout_date',
                'selesai_date',
                'numb',
                'inpatient_status',
                'dokter_id',
                'section_id',
                'patient_id',
                'type_id',
                'status_batal',
                'bpjs_sep',
                'bayar_date',
                'biaya',
                'rm_diagnosa',
                'source_reg',
            ])
            ->whereIn('source_reg', [
                'ADMISI',
                'MJKN',
                'NULL'
            ])
            ->where('status', 1)
            ->where('parent_id', '0');

        /*
    |--------------------------------------------------------------------------
    | QUERY UTAMA
    |--------------------------------------------------------------------------
    */
        $query = DB::query()
            ->fromSub($registrasi, 't')

            ->join('patient_types as pt', 't.type_id', '=', 'pt.id')
            ->join('patients as p', 't.patient_id', '=', 'p.id')
            ->join('users as u', 't.dokter_id', '=', 'u.id')
            ->leftJoin('sections as s3', 't.section_id', '=', 's3.id')

            ->select([
                't.schedule_date',
                't.reg_date',
                't.checkout_date',
                't.selesai_date',
                't.numb as no_registrasi',
                't.inpatient_status',
                'u.name as nama_dokter',
                't.status_batal',
                's3.title as ruangan',
                'p.nrm',
                'p.name as nama_pasien',
                'pt.title as penjamin',
                't.bpjs_sep',
                't.bayar_date',
                't.biaya',
                't.rm_diagnosa',
                't.source_reg',
            ]);

        if ($jenisKunjungan === 'ranap') {

            // =========================
            // RAWAT INAP
            // =========================
            $query
                ->where('t.inpatient_status', 1)
                ->whereBetween('t.checkout_date', [
                    $tanggalMulai,
                    $tanggalSelesai
                ]);
        } elseif ($jenisKunjungan === 'igd') {

            // =========================
            // IGD / PONEK
            // =========================
            $query
                ->where('t.inpatient_status', 0)
                ->whereIn('s3.title', [
                    'IGD 24 JAM',
                    'PONEK'
                ])
                ->whereBetween('t.reg_date', [
                    $tanggalMulai,
                    $tanggalSelesai
                ]);
        } elseif ($jenisKunjungan === 'rajal') {

            // =========================
            // RAWAT JALAN
            // =========================
            $query
                ->where('t.inpatient_status', 0)
                ->whereNotIn('s3.title', [
                    'IGD 24 JAM',
                    'PONEK'
                ])
                ->whereBetween('t.schedule_date', [
                    $tanggalMulai,
                    $tanggalSelesai
                ]);
        } else {

            // =========================
            // SEMUA JENIS KUNJUNGAN
            // =========================
            $query->where(function ($q) use ($tanggalMulai, $tanggalSelesai) {

                /*
            |--------------------------------------------------------------------------
            | 1. RAWAT INAP
            |--------------------------------------------------------------------------
            */
                $q->where(function ($ranap) use ($tanggalMulai, $tanggalSelesai) {

                    $ranap
                        ->where('t.inpatient_status', 1)
                        ->whereBetween('t.checkout_date', [
                            $tanggalMulai,
                            $tanggalSelesai
                        ]);
                })

                    /*
            |--------------------------------------------------------------------------
            | 2. IGD / PONEK
            |--------------------------------------------------------------------------
            */
                    ->orWhere(function ($igd) use ($tanggalMulai, $tanggalSelesai) {

                        $igd
                            ->where('t.inpatient_status', 0)
                            ->whereIn('s3.title', [
                                'IGD 24 JAM',
                                'PONEK'
                            ])
                            ->whereBetween('t.reg_date', [
                                $tanggalMulai,
                                $tanggalSelesai
                            ]);
                    })

                    /*
            |--------------------------------------------------------------------------
            | 3. RAWAT JALAN
            |--------------------------------------------------------------------------
            */
                    ->orWhere(function ($rajal) use ($tanggalMulai, $tanggalSelesai) {

                        $rajal
                            ->where('t.inpatient_status', 0)
                            ->whereNotIn('s3.title', [
                                'IGD 24 JAM',
                                'PONEK'
                            ])
                            ->whereBetween('t.schedule_date', [
                                $tanggalMulai,
                                $tanggalSelesai
                            ]);
                    });
            });
        }

        /*
    |--------------------------------------------------------------------------
    | FILTER JENIS PASIEN / PENJAMIN
    |--------------------------------------------------------------------------
    */
        if ($request->filled('jenis_pasien')) {

            $jenis = is_array($request->jenis_pasien)
                ? $request->jenis_pasien
                : explode(',', $request->jenis_pasien);

            // Hilangkan value kosong
            $jenis = array_filter($jenis);

            if (!empty($jenis)) {
                $query->whereIn('pt.title', $jenis);
            }
        }

        /*
    |--------------------------------------------------------------------------
    | FILTER DOKTER
    |--------------------------------------------------------------------------
    */
        if ($request->filled('dokter')) {

            $dokter = is_array($request->dokter)
                ? $request->dokter
                : explode(',', $request->dokter);

            // Hilangkan value kosong
            $dokter = array_filter($dokter);

            if (!empty($dokter)) {
                $query->whereIn('t.dokter_id', $dokter);
            }
        }

        /*
    |--------------------------------------------------------------------------
    | FILTER RUANGAN / POLI
    |--------------------------------------------------------------------------
    */
        if ($request->filled('ruangan')) {

            $query->where(
                't.section_id',
                $request->ruangan
            );
        }

        /*
    |--------------------------------------------------------------------------
    | ORDER DATA
    |--------------------------------------------------------------------------
    |
    | Jika ALL:
    |
    | RANAP       -> checkout_date
    | IGD/PONEK   -> reg_date
    | RAJAL       -> schedule_date
    |
    |--------------------------------------------------------------------------
    */
        if ($jenisKunjungan === 'all') {

            $query->orderByRaw("
            CASE
                WHEN t.inpatient_status = 1
                    THEN t.checkout_date

                WHEN t.inpatient_status = 0
                    AND s3.title IN ('IGD 24 JAM', 'PONEK')
                    THEN t.reg_date

                ELSE t.schedule_date
            END ASC
        ");
        } elseif ($jenisKunjungan === 'ranap') {

            $query->orderBy('t.checkout_date', 'asc');
        } elseif ($jenisKunjungan === 'igd') {

            $query->orderBy('t.reg_date', 'asc');
        } else {

            $query->orderBy('t.schedule_date', 'asc');
        }

        /*
    |--------------------------------------------------------------------------
    | DATATABLES
    |--------------------------------------------------------------------------
    */
        return DataTables::of($query)

            ->addIndexColumn()

            /*
        |--------------------------------------------------------------------------
        | FORMAT SCHEDULE DATE
        |--------------------------------------------------------------------------
        */
            ->editColumn('schedule_date', function ($row) {

                return $row->schedule_date
                    ? Carbon::parse($row->schedule_date)
                    ->format('d-m-Y H:i')
                    : '-';
            })

            /*
        |--------------------------------------------------------------------------
        | FORMAT REG DATE
        |--------------------------------------------------------------------------
        */
            ->editColumn('reg_date', function ($row) {

                return $row->reg_date
                    ? Carbon::parse($row->reg_date)
                    ->format('d-m-Y H:i')
                    : '-';
            })

            /*
        |--------------------------------------------------------------------------
        | FORMAT CHECKOUT DATE
        |--------------------------------------------------------------------------
        */
            ->editColumn('checkout_date', function ($row) {

                return $row->checkout_date
                    ? Carbon::parse($row->checkout_date)
                    ->format('d-m-Y H:i')
                    : '-';
            })

            /*
        |--------------------------------------------------------------------------
        | FORMAT BAYAR DATE
        |--------------------------------------------------------------------------
        */
            ->editColumn('bayar_date', function ($row) {

                return $row->bayar_date
                    ? Carbon::parse($row->bayar_date)
                    ->format('d-m-Y H:i')
                    : '-';
            })

            /*
        |--------------------------------------------------------------------------
        | STATUS BATAL
        |--------------------------------------------------------------------------
        */
            ->editColumn('status_batal', function ($row) {

                return $row->status_batal == 1

                    ? '<span class="badge bg-danger">
                        Batal
                   </span>'

                    : '<span class="badge bg-success">
                        Aktif
                   </span>';
            })

            ->rawColumns([
                'status_batal'
            ])

            ->make(true);
    }
    public function exportExcel(Request $request)
    {
        $start = $request->input('start_date');
        $end = $request->input('end_date');
        $jenis = $request->input('jenis_pasien');
        $ruangan = $request->input('ruangan');
        $jenis_kunjungan = $request->input('jenis_kunjungan', 'all');
        $dokter = $request->input('dokter');

        if (!in_array($jenis_kunjungan, [
            'all',
            'rajal',
            'ranap',
            'igd'
        ])) {
            $jenis_kunjungan = 'all';
        }

        return Excel::download(
            new ErmExport(
                $start,
                $end,
                $jenis,
                $ruangan,
                $jenis_kunjungan,
                $dokter
            ),
            'kunjungan_poli.xlsx'
        );
    }
}
