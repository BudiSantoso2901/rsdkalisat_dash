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
        return view('Page.dokter');
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
        $bulan = (int) ($request->bulan ?? now()->month);
        $tahun = (int) ($request->tahun ?? now()->year);

        // Range bulan untuk seluruh statistik dashboard
        $awalBulan = Carbon::createFromDate($tahun, $bulan, 1)->startOfDay();
        $akhirBulan = $awalBulan->copy()->endOfMonth()->endOfDay();

        // Range khusus jadwal dokter
        $tanggalMulai = $request->tanggal_mulai
            ? Carbon::parse($request->tanggal_mulai)->startOfDay()
            : Carbon::today()->startOfDay();

        $tanggalSelesai = $request->tanggal_selesai
            ? Carbon::parse($request->tanggal_selesai)->endOfDay()
            : Carbon::today()->endOfDay();

        /*
        |--------------------------------------------------------------------------
        | JADWAL DOKTER
        |--------------------------------------------------------------------------
        */
        $jadwal = DB::table('rsv_schedules as s')
            ->join('sections as sec', 's.section_id', '=', 'sec.id')
            ->join('users as u', 's.dokter_id', '=', 'u.id')

            ->leftJoin('tr_pxregistrations as r', function ($join) use ($tanggalMulai, $tanggalSelesai) {
                $join->on('r.section_id', '=', 's.section_id')
                    ->on('r.dokter_id', '=', 's.dokter_id')

                    // Batasi data pasien hanya ke range yang diminta
                    ->whereBetween('r.schedule_date', [$tanggalMulai, $tanggalSelesai])

                    // Pasien harus berada pada tanggal jadwal yang sama
                    ->whereRaw('r.schedule_date >= s.date')
                    ->whereRaw('r.schedule_date < DATE_ADD(s.date, INTERVAL 1 DAY)')

                    ->where('r.status', 1)
                    ->where('r.parent_id', '0')
                    ->where('r.status_batal', 0);
            })

            ->select(
                's.id',
                's.date as tanggal_jadwal',
                'u.name as nama_dokter',
                'sec.title as nama_poli',
                's.open_time',
                's.closed_time',
                's.kapasitaspasien',
                DB::raw('COUNT(r.id) as total_pasien')
            )

            ->whereBetween('s.date', [$tanggalMulai, $tanggalSelesai])

            ->groupBy(
                's.id',
                's.date',
                'u.name',
                'sec.title',
                's.open_time',
                's.closed_time',
                's.kapasitaspasien'
            )

            ->orderBy('s.date')
            ->orderBy('s.open_time')
            ->paginate(10)
            ->withQueryString();

        // Kalau request AJAX tabel jadwal, jangan jalankan statistik dashboard
        if ($request->ajax()) {
            return view('Page.partials.jadwal-table', compact('jadwal'));
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
                $awalBulan->toDateString(),
                $akhirBulan->toDateString()
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

            ->whereBetween('t.schedule_date', [$awalBulan, $akhirBulan])

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

            ->whereBetween('t.schedule_date', [$awalBulan, $akhirBulan])

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
            ->whereBetween('t.checkout_date', [$awalBulan, $akhirBulan])
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

            ->whereBetween('t.reg_date', [$awalBulan, $akhirBulan])

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
            ->whereBetween('t.schedule_date', [$awalBulan, $akhirBulan])
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

            ->whereBetween('t.schedule_date', [$awalBulan, $akhirBulan])

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
        $pxdokter = DB::table('tr_pxregistrations as t')
            ->join('users as u', 't.dokter_id', '=', 'u.id')
            ->join('sections as s', 't.section_id', '=', 's.id')

            ->select(
                'u.name as nama_dokter',
                's.title as nama_poli',
                DB::raw('COUNT(t.id) as total_pasien')
            )

            ->whereBetween('t.schedule_date', [$awalBulan, $akhirBulan])

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
            'jadwal',
            'tanggalMulai',
            'tanggalSelesai',
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
        $tanggalMulai = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::today()->startOfDay();

        $tanggalSelesai = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::today()->endOfDay();

        /*
        |--------------------------------------------------------------------------
        | AGREGASI PASIEN PER TANGGAL + DOKTER + POLI
        |--------------------------------------------------------------------------
        |
        | Sebelum join ke jadwal, pasien dihitung dulu per tanggal.
        | Ini mencegah total seluruh range masuk ke setiap tanggal jadwal.
        |
        */
        $registrasi = DB::table('tr_pxregistrations as r')
            ->selectRaw('
            DATE(r.schedule_date) as tanggal,
            r.section_id,
            r.dokter_id,
            COUNT(r.id) as total_pasien
        ')
            ->whereBetween('r.schedule_date', [
                $tanggalMulai,
                $tanggalSelesai
            ])
            ->where('r.status', 1)
            ->where('r.parent_id', '0')
            ->where('r.status_batal', 0)
            ->groupByRaw('
            DATE(r.schedule_date),
            r.section_id,
            r.dokter_id
        ');

        /*
        |--------------------------------------------------------------------------
        | JADWAL DOKTER
        |--------------------------------------------------------------------------
        */
        $query = DB::table('rsv_schedules as s')
            ->join('sections as sec', 's.section_id', '=', 'sec.id')
            ->join('users as u', 's.dokter_id', '=', 'u.id')

            ->leftJoinSub($registrasi, 'r', function ($join) {
                $join->on('r.section_id', '=', 's.section_id')
                    ->on('r.dokter_id', '=', 's.dokter_id')
                    ->on('r.tanggal', '=', DB::raw('DATE(s.date)'));
            })

            ->select([
                's.id',
                's.title as nama_dokter',
                'u.name',
                'sec.code',
                'sec.prefix',
                'sec.title as nama_poli',
                's.kodesubspesialis',
                's.date',
                's.open_time',
                's.closed_time',
                's.kuotajkn',
                's.kuotanonjkn',
                's.kapasitaspasien',

                DB::raw('COALESCE(r.total_pasien, 0) as total_pasien')
            ])

            ->whereBetween('s.date', [
                $tanggalMulai->toDateString(),
                $tanggalSelesai->toDateString()
            ])

            ->orderBy('s.date')
            ->orderBy('s.open_time');

        /*
        |--------------------------------------------------------------------------
        | DATATABLE
        |--------------------------------------------------------------------------
        */
        return DataTables::of($query)

            ->addIndexColumn()

            ->editColumn('date', function ($row) {
                return $row->date
                    ? Carbon::parse($row->date)->translatedFormat('l') . '<br>' .
                    Carbon::parse($row->date)->translatedFormat('d F Y')
                    : '-';
            })

            ->editColumn('open_time', function ($row) {
                return $row->open_time
                    ? Carbon::parse($row->open_time)->format('H:i')
                    : '-';
            })

            ->editColumn('closed_time', function ($row) {
                return $row->closed_time
                    ? Carbon::parse($row->closed_time)->format('H:i')
                    : '-';
            })

            ->addColumn('kuota_progress', function ($row) {

                $kapasitas = (int) ($row->kapasitaspasien ?? 0);
                $terisi = (int) ($row->total_pasien ?? 0);

                $persen = $kapasitas > 0
                    ? min(round(($terisi / $kapasitas) * 100), 100)
                    : 0;

                $warna = $persen >= 100
                    ? 'bg-danger'
                    : ($persen >= 80 ? 'bg-warning' : 'bg-success');

                return '
                <div style="min-width:130px">
                    <div class="fw-semibold mb-1">
                        ' . $terisi . ' / ' . $kapasitas . '
                    </div>

                    <div class="progress" style="height:6px;">
                        <div class="progress-bar ' . $warna . '"
                             style="width:' . $persen . '%">
                        </div>
                    </div>
                </div>';
            })

            ->rawColumns(['date', 'kuota_progress'])
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
    // public function getKunjunganPoli(Request $request)
    // {

    //     /*
    // |--------------------------------------------------------------------------
    // | FILTER TANGGAL
    // |--------------------------------------------------------------------------
    // */

    //     if ($request->filled('start_date') && $request->filled('end_date')) {

    //         $tanggalMulai   = Carbon::parse($request->start_date)->startOfDay();
    //         $tanggalSelesai = Carbon::parse($request->end_date)->endOfDay();
    //     } else {

    //         $tanggalMulai   = Carbon::today()->startOfDay();
    //         $tanggalSelesai = Carbon::today()->endOfDay();
    //     }


    //     /*
    // |--------------------------------------------------------------------------
    // | QUERY UTAMA
    // |--------------------------------------------------------------------------
    // */

    //     $query = DB::table('tr_pxregistrations as t')
    //         ->join('patient_types as pt', 't.type_id', '=', 'pt.id')
    //         ->join('patients as p', 't.patient_id', '=', 'p.id')
    //         ->join('users as u', 't.dokter_id', '=', 'u.id')
    //         ->leftJoin('sections as s3', 't.section_id', '=', 's3.id')

    //         ->select(
    //             't.schedule_date',
    //             't.source_reg',
    //             't.reg_date',
    //             't.selesai_date',
    //             't.checkout_date',
    //             't.numb as no_registrasi',
    //             't.inpatient_status',
    //             'u.name as nama_dokter',
    //             't.status_batal',
    //             's3.title as ruangan',
    //             'p.nrm',
    //             'p.name as nama_pasien',
    //             'pt.title as penjamin',
    //             't.bpjs_sep',
    //             't.bayar_date',
    //             't.biaya',
    //             't.rm_diagnosa'
    //         )

    //         ->whereBetween('t.schedule_date', [
    //             $tanggalMulai,
    //             $tanggalSelesai
    //         ])

    //         ->whereIn('t.source_reg', ['ADMISI', 'MJKN', 'NULL'])

    //         ->where('t.inpatient_status', 0)
    //         ->where('t.status', 1)
    //         ->where('t.parent_id', 0)

    //         ->where('s3.title', '!=', 'IGD 24 JAM');


    //     /*
    // |--------------------------------------------------------------------------
    // | FILTER JENIS PASIEN
    // |--------------------------------------------------------------------------
    // */

    //     if ($request->filled('jenis_pasien')) {

    //         $query->where('pt.title', $request->jenis_pasien);
    //     }


    //     /*
    // |--------------------------------------------------------------------------
    // | FILTER POLI / RUANGAN
    // |--------------------------------------------------------------------------
    // */

    //     if ($request->filled('ruangan')) {

    //         $query->where('s3.id', $request->ruangan);
    //     }


    //     /*
    // |--------------------------------------------------------------------------
    // | ORDER
    // |--------------------------------------------------------------------------
    // */

    //     $query->orderBy('t.schedule_date', 'asc');


    //     /*
    // |--------------------------------------------------------------------------
    // | DATATABLES
    // |--------------------------------------------------------------------------
    // */

    //     return DataTables::of($query)

    //         ->addIndexColumn()

    //         /*
    //     |--------------------------------------------------------------------------
    //     | GLOBAL SEARCH
    //     |--------------------------------------------------------------------------
    //     */

    //         ->filter(function ($query) use ($request) {

    //             if ($request->has('search')) {

    //                 $search = $request->get('search')['value'];

    //                 if ($search != '') {

    //                     $query->where(function ($q) use ($search) {

    //                         $q->where('p.name', 'like', "%{$search}%")
    //                             ->orWhere('p.nrm', 'like', "%{$search}%")
    //                             ->orWhere('u.name', 'like', "%{$search}%")
    //                             ->orWhere('s3.title', 'like', "%{$search}%")
    //                             ->orWhere('pt.title', 'like', "%{$search}%")
    //                             ->orWhere('t.numb', 'like', "%{$search}%");
    //                     });
    //                 }
    //             }
    //         })


    //         /*
    //     |--------------------------------------------------------------------------
    //     | FORMAT TANGGAL
    //     |--------------------------------------------------------------------------
    //     */

    //         ->editColumn('schedule_date', function ($row) {
    //             return $row->schedule_date
    //                 ? Carbon::parse($row->schedule_date)->format('d-m-Y H:i')
    //                 : '-';
    //         })

    //         ->editColumn('reg_date', function ($row) {
    //             return $row->reg_date
    //                 ? Carbon::parse($row->reg_date)->format('d-m-Y H:i')
    //                 : '-';
    //         })

    //         ->editColumn('checkout_date', function ($row) {
    //             return $row->checkout_date
    //                 ? Carbon::parse($row->checkout_date)->format('d-m-Y H:i')
    //                 : '-';
    //         })

    //         ->editColumn('bayar_date', function ($row) {
    //             return $row->bayar_date
    //                 ? Carbon::parse($row->bayar_date)->format('d-m-Y H:i')
    //                 : '-';
    //         })


    //         /*
    //     |--------------------------------------------------------------------------
    //     | STATUS BATAL
    //     |--------------------------------------------------------------------------
    //     */

    //         ->editColumn('status_batal', function ($row) {

    //             if ($row->status_batal == 1) {
    //                 return '<span class="badge bg-danger">Batal</span>';
    //             }

    //             return '<span class="badge bg-success">Aktif</span>';
    //         })


    //         ->rawColumns(['status_batal'])

    //         ->make(true);
    // }
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

        $jenisKunjungan = $request->jenis_kunjungan ?? 'rajal';

        /*
        |--------------------------------------------------------------------------
        | PILIH KOLOM TANGGAL
        |--------------------------------------------------------------------------
        */
        $dateColumn = match ($jenisKunjungan) {
            'ranap' => 'checkout_date',
            'igd' => 'reg_date',
            default => 'schedule_date',
        };

        /*
        |--------------------------------------------------------------------------
        | FILTER REGISTRASI DULU
        |--------------------------------------------------------------------------
        | Data diperkecil sebelum join ke tabel lain.
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
            ->whereBetween($dateColumn, [$tanggalMulai, $tanggalSelesai])
            ->whereIn('source_reg', ['ADMISI', 'MJKN', 'NULL'])
            ->where('status', 1)
            ->where('parent_id', '0');

        /*
        |--------------------------------------------------------------------------
        | FILTER JENIS KUNJUNGAN
        |--------------------------------------------------------------------------
        */
        if ($jenisKunjungan === 'ranap') {
            $registrasi->where('inpatient_status', 1);
        } else {
            $registrasi->where('inpatient_status', 0);
        }

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

        /*
        |--------------------------------------------------------------------------
        | FILTER RAJAL / IGD
        |--------------------------------------------------------------------------
        */
        if ($jenisKunjungan === 'rajal') {

            $query->whereNotIn('s3.title', [
                'IGD 24 JAM',
                'PONEK'
            ]);

        } elseif ($jenisKunjungan === 'igd') {

            $query->whereIn('s3.title', [
                'IGD 24 JAM',
                'PONEK'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER JENIS PASIEN
        |--------------------------------------------------------------------------
        */
        if ($request->filled('jenis_pasien')) {

            $jenis = is_array($request->jenis_pasien)
                ? $request->jenis_pasien
                : explode(',', $request->jenis_pasien);

            $query->whereIn('pt.title', $jenis);
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

            $query->whereIn('t.dokter_id', $dokter);
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER POLI
        |--------------------------------------------------------------------------
        */
        if ($request->filled('ruangan')) {
            $query->where('t.section_id', $request->ruangan);
        }

        /*
        |--------------------------------------------------------------------------
        | ORDER
        |--------------------------------------------------------------------------
        */
        $query->orderBy("t.$dateColumn", 'asc');

        /*
        |--------------------------------------------------------------------------
        | DATATABLE
        |--------------------------------------------------------------------------
        */
        return DataTables::of($query)

            ->addIndexColumn()

            ->filter(function ($query) use ($request) {

                $search = $request->input('search.value');

                if (!$search) {
                    return;
                }

                $query->where(function ($q) use ($search) {

                    $q->where('p.name', 'like', "%{$search}%")
                        ->orWhere('p.nrm', 'like', "%{$search}%")
                        ->orWhere('u.name', 'like', "%{$search}%")
                        ->orWhere('s3.title', 'like', "%{$search}%")
                        ->orWhere('pt.title', 'like', "%{$search}%")
                        ->orWhere('t.numb', 'like', "%{$search}%");
                });
            })

            ->editColumn('schedule_date', function ($row) {
                return $row->schedule_date
                    ? Carbon::parse($row->schedule_date)->format('d-m-Y H:i')
                    : '-';
            })

            ->editColumn('reg_date', function ($row) {
                return $row->reg_date
                    ? Carbon::parse($row->reg_date)->format('d-m-Y H:i')
                    : '-';
            })

            ->editColumn('checkout_date', function ($row) {
                return $row->checkout_date
                    ? Carbon::parse($row->checkout_date)->format('d-m-Y H:i')
                    : '-';
            })

            ->editColumn('bayar_date', function ($row) {
                return $row->bayar_date
                    ? Carbon::parse($row->bayar_date)->format('d-m-Y H:i')
                    : '-';
            })

            ->editColumn('status_batal', function ($row) {

                return $row->status_batal == 1
                    ? '<span class="badge bg-danger">Batal</span>'
                    : '<span class="badge bg-success">Aktif</span>';
            })

            ->rawColumns(['status_batal'])

            ->make(true);
    }
    public function exportExcel(Request $request)
    {

        $start = $request->start_date;
        $end = $request->end_date;
        $jenis = $request->jenis_pasien;
        $ruangan = $request->ruangan;
        $jenis_kunjungan = $request->jenis_kunjungan;
        $dokter = $request->dokter;

        return Excel::download(
            new ErmExport($start, $end, $jenis, $ruangan, $jenis_kunjungan, $dokter),
            'kunjungan_poli.xlsx'
        );
    }
}
