<?php

namespace App\Http\Controllers;

use App\Models\tr_pxregistrations;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            $end   = Carbon::parse($request->end_date)->endOfDay();

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
        /*
    |--------------------------------------------------------------------------
    | FILTER BULAN & TAHUN
    |--------------------------------------------------------------------------
    */
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        $today = Carbon::today();

        /*
    |--------------------------------------------------------------------------
    | 1️⃣ JADWAL DOKTER (TETAP HARI INI)
    |--------------------------------------------------------------------------
    */
        $jadwal = DB::table('rsv_schedules as s')
            ->join('sections as sec', 's.section_id', '=', 'sec.id')
            ->join('users as u', 's.dokter_id', '=', 'u.id')
            ->leftJoin('tr_pxregistrations as r', function ($join) {
                $join->on('r.schedule_id', '=', 's.id')
                    ->where('r.status_batal', 0);
            })
            ->selectRaw('
            s.id,
            u.name,
            sec.title as nama_poli,
            s.open_time,
            s.closed_time,
            s.kapasitaspasien,
            COUNT(r.id) as total_pasien
        ')
            ->whereDate('s.date', $today)
            ->groupBy(
                's.id',
                'u.name',
                'sec.title',
                's.open_time',
                's.closed_time',
                's.kapasitaspasien'
            )
            ->orderBy('s.open_time')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | 2️⃣ KUNJUNGAN PER POLI (BULANAN)
    |--------------------------------------------------------------------------
    */
        $kunjunganPerPoli = DB::table('tr_pxregistrations as r')
            ->join('sections as s', 'r.section_id', '=', 's.id')
            ->selectRaw('
            s.title as nama_poli,
            COUNT(r.id) as total
        ')
            ->whereMonth('r.schedule_date', $bulan)
            ->whereYear('r.schedule_date', $tahun)
            ->where('r.status_batal', 0)
            ->groupBy('s.title')
            ->orderByDesc('total')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | 3️⃣ RAWAT JALAN VS INAP (BULANAN)
    |--------------------------------------------------------------------------
    */
        $rawatJalan = DB::table('tr_pxregistrations')
            ->whereMonth('schedule_date', $bulan)
            ->whereYear('schedule_date', $tahun)
            ->where('status_batal', 0)
            ->where('inpatient_status', 0)
            ->count();

        $rawatInap = DB::table('tr_pxregistrations')
            ->whereMonth('schedule_date', $bulan)
            ->whereYear('schedule_date', $tahun)
            ->where('status_batal', 0)
            ->where('inpatient_status', 1)
            ->count();

        /*
    |--------------------------------------------------------------------------
    | 4️⃣ PASIEN BARU VS LAMA (BULANAN)
    |--------------------------------------------------------------------------
    */
        $pasienBaru = DB::table('tr_pxregistrations')
            ->whereMonth('schedule_date', $bulan)
            ->whereYear('schedule_date', $tahun)
            ->where('status_batal', 0)
            ->where('first_regstatus', 1)
            ->count();

        $pasienLama = DB::table('tr_pxregistrations')
            ->whereMonth('schedule_date', $bulan)
            ->whereYear('schedule_date', $tahun)
            ->where('status_batal', 0)
            ->where('first_regstatus', 0)
            ->count();

        /*
    |--------------------------------------------------------------------------
    | RETURN VIEW
    |--------------------------------------------------------------------------
    */
        return view('Page.dashboard', compact(
            'jadwal',
            'kunjunganPerPoli',
            'rawatJalan',
            'rawatInap',
            'pasienBaru',
            'pasienLama',
            'bulan',
            'tahun'
        ));
    }
    public function getJadwalDokter(Request $request)
    {
        $query = DB::table('rsv_schedules')
            ->join('sections', 'rsv_schedules.section_id', '=', 'sections.id')
            ->join('users', 'rsv_schedules.dokter_id', '=', 'users.id')
            ->select([
                'rsv_schedules.title as nama_dokter',
                'users.name',
                'sections.code',
                'sections.prefix',
                'sections.title as nama_poli',
                'rsv_schedules.kodesubspesialis',
                'rsv_schedules.date',
                'rsv_schedules.open_time',
                'rsv_schedules.closed_time',
                'rsv_schedules.kuotajkn',
                'rsv_schedules.kuotanonjkn',
                'rsv_schedules.kapasitaspasien'
            ]);

        /*
    |--------------------------------------------------------------------------
    | DEFAULT: HARI INI SAJA (AGAR RINGAN)
    |--------------------------------------------------------------------------
    */
        if (!$request->filled('start_date') && !$request->filled('end_date')) {
            $query->whereDate('rsv_schedules.date', Carbon::today());
        }

        /*
    |--------------------------------------------------------------------------
    | FILTER RANGE TANGGAL
    |--------------------------------------------------------------------------
    */
        if ($request->filled('start_date') && $request->filled('end_date')) {

            $start = Carbon::parse($request->start_date)->startOfDay();
            $end   = Carbon::parse($request->end_date)->endOfDay();

            $query->whereBetween('rsv_schedules.date', [$start, $end]);
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->editColumn('date', function ($row) {
                return $row->date
                    ? Carbon::parse($row->date)->translatedFormat('l') . '<br>' .
                    Carbon::parse($row->date)->translatedFormat('d F Y')
                    : '-';
            })
            ->rawColumns(['date'])

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

            ->make(true);
    }
    // public function getData(Request $request)
    // {
    //     $query = tr_pxregistrations::select([
    //         'nama_px',
    //         'nik',
    //         'bpjs_sep',
    //         'reg_date',
    //         'reg_time',
    //         'section_id',
    //         'dokter_id',
    //         'status_selesai'
    //     ]);

    //     return DataTables::of($query)

    //         ->addIndexColumn() // untuk kolom No

    //         ->editColumn('status_selesai', function ($row) {
    //             if ($row->status_selesai == 1) {
    //                 return '<span class="badge bg-success">Selesai</span>';
    //             } else {
    //                 return '<span class="badge bg-warning">Proses</span>';
    //             }
    //         })

    //         ->editColumn('reg_date', function ($row) {
    //             return \Carbon\Carbon::parse($row->reg_date)->format('d-m-Y');
    //         })

    //         ->rawColumns(['status_selesai'])
    //         ->make(true);
    // }
}
