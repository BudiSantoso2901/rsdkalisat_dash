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

        $tanggalMulai   = $request->tanggal_mulai
            ? Carbon::parse($request->tanggal_mulai)->startOfDay()
            : Carbon::today()->startOfDay();

        $tanggalSelesai = $request->tanggal_selesai
            ? Carbon::parse($request->tanggal_selesai)->endOfDay()
            : Carbon::today()->endOfDay();

        /*
    |--------------------------------------------------------------------------
    | 1️⃣ JADWAL DOKTER (TETAP HARI INI)
    |--------------------------------------------------------------------------
    */

        $jadwal = DB::table('rsv_schedules as s')
            ->join('sections as sec', 's.section_id', '=', 'sec.id')
            ->join('users as u', 's.dokter_id', '=', 'u.id')

            ->leftJoin('tr_pxregistrations as r', function ($join) use ($tanggalMulai, $tanggalSelesai) {
                $join->on('r.section_id', '=', 's.section_id')
                    ->on('r.dokter_id', '=', 's.dokter_id')
                    ->whereBetween('r.schedule_date', [$tanggalMulai, $tanggalSelesai])
                    ->where('r.status', 1);
            })

            ->join('patients as p', 'r.patient_id', '=', 'p.id')
            ->join('patient_types as pt', 'r.type_id', '=', 'pt.id')

            ->select(
                's.id',
                'u.name as nama_dokter',
                'sec.title as nama_poli',
                's.open_time',
                's.closed_time',
                's.kapasitaspasien',
                DB::raw('COUNT(r.id) as total_pasien')
            )

            ->whereBetween('s.date', [
                $tanggalMulai->toDateString(),
                $tanggalSelesai->toDateString()
            ])

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
        // dd($jadwal);
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
| 5️⃣ JENIS PASIEN (BULANAN)
|--------------------------------------------------------------------------
*/

        $jenisPasien = DB::table('tr_pxregistrations as r')
            ->join('patients as p', 'r.patient_id', '=', 'p.id')
            ->join('patient_types as pt', 'p.patient_typeid', '=', 'pt.id')
            ->selectRaw('
        pt.title,
        COUNT(r.id) as total
    ')
            ->whereMonth('r.schedule_date', $bulan)
            ->whereYear('r.schedule_date', $tahun)
            ->where('r.status', 1)
            ->where('r.status_batal', 0)
            ->groupBy('pt.title')
            ->pluck('total', 'pt.title');

        $asuransi     = $jenisPasien['ASURANSI'] ?? 0;
        $bpjsNonPbi   = $jenisPasien['BPJS NON PBI'] ?? 0;
        $bpjsPbi      = $jenisPasien['BPJS PBI'] ?? 0;
        $bpjsUhc      = $jenisPasien['BPJS UHC'] ?? 0;
        $jpk          = $jenisPasien['JPK'] ?? 0;
        $pegawai      = $jenisPasien['PEGAWAI'] ?? 0;
        $spm          = $jenisPasien['SPM'] ?? 0;
        $umum         = $jenisPasien['UMUM'] ?? 0;
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
            'tahun',
            'asuransi',
            'bpjsNonPbi',
            'bpjsPbi',
            'bpjsUhc',
            'jpk',
            'pegawai',
            'spm',
            'umum',
            'tanggalMulai',
            'tanggalSelesai'
        ));
    }
    public function getJadwalDokter(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | TANGGAL FILTER
    |--------------------------------------------------------------------------
    */

        if ($request->filled('start_date') && $request->filled('end_date')) {

            $tanggalMulai   = Carbon::parse($request->start_date)->startOfDay();
            $tanggalSelesai = Carbon::parse($request->end_date)->endOfDay();
        } else {

            $tanggalMulai   = Carbon::today()->startOfDay();
            $tanggalSelesai = Carbon::today()->endOfDay();
        }

        $query = DB::table('rsv_schedules as s')
            ->join('sections as sec', 's.section_id', '=', 'sec.id')
            ->join('users as u', 's.dokter_id', '=', 'u.id')

            ->leftJoin('tr_pxregistrations as r', function ($join) use ($tanggalMulai, $tanggalSelesai) {

                $join->on('r.section_id', '=', 's.section_id')
                    ->on('r.dokter_id', '=', 's.dokter_id')
                    ->whereBetween('r.schedule_date', [$tanggalMulai, $tanggalSelesai])
                    ->where('r.status', 1);
            })

            ->leftJoin('patients as p', 'r.patient_id', '=', 'p.id')
            ->leftJoin('patient_types as pt', 'r.type_id', '=', 'pt.id')

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
                DB::raw('COUNT(r.id) as total_pasien')
            ])

            ->groupBy(
                's.id',
                's.title',
                'u.name',
                'sec.code',
                'sec.prefix',
                'sec.title',
                's.kodesubspesialis',
                's.date',
                's.open_time',
                's.closed_time',
                's.kuotajkn',
                's.kuotanonjkn',
                's.kapasitaspasien'
            );

        /*
    |--------------------------------------------------------------------------
    | FILTER TANGGAL JADWAL
    |--------------------------------------------------------------------------
    */

        if (!$request->filled('start_date') && !$request->filled('end_date')) {

            $query->whereDate('s.date', Carbon::today());
        } else {

            $query->whereBetween('s.date', [
                Carbon::parse($request->start_date)->toDateString(),
                Carbon::parse($request->end_date)->toDateString()
            ]);
        }

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

                $persen = $kapasitas > 0 ? round(($terisi / $kapasitas) * 100) : 0;
                $persen = min($persen, 100);

                $warna = 'bg-success';

                if ($persen >= 100) {
                    $warna = 'bg-danger';
                } elseif ($persen >= 80) {
                    $warna = 'bg-warning';
                }

                return '
            <div style="min-width:130px">
                <div class="fw-semibold mb-1">
                    ' . $terisi . ' / ' . $kapasitas . '
                </div>
                <div class="progress" style="height:6px;">
                    <div class="progress-bar ' . $warna . '"
                        style="width: ' . $persen . '%">
                    </div>
                </div>
            </div>';
            })

            ->rawColumns(['date', 'kuota_progress'])

            ->make(true);
    }
    public function view_kunjungan_poli()
    {
        return view('Page.kunjungan_poli');
    }
    public function getKunjunganPoli(Request $request)
    {

        /*
    |--------------------------------------------------------------------------
    | FILTER TANGGAL
    |--------------------------------------------------------------------------
    */

        if ($request->filled('start_date') && $request->filled('end_date')) {

            $tanggalMulai   = Carbon::parse($request->start_date)->startOfDay();
            $tanggalSelesai = Carbon::parse($request->end_date)->endOfDay();
        } else {

            $tanggalMulai   = Carbon::today()->startOfDay();
            $tanggalSelesai = Carbon::today()->endOfDay();
        }


        /*
    |--------------------------------------------------------------------------
    | QUERY UTAMA
    |--------------------------------------------------------------------------
    */

        $query = DB::table('tr_pxregistrations as t')
            ->join('patient_types as pt', 't.type_id', '=', 'pt.id')
            ->join('patients as p', 't.patient_id', '=', 'p.id')
            ->join('users as u', 't.dokter_id', '=', 'u.id')
            ->leftJoin('sections as s3', 't.section_id', '=', 's3.id')

            ->select(
                't.id',
                't.checkout_date',
                't.reg_date',
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
                't.source_reg',
                't.rm_diagnosa',
                't.rm_kodediagnosa',
                't.rm_closing_date',
                't.biaya'
            )

            ->where('t.status', 1)

            ->whereBetween('t.checkout_date', [
                $tanggalMulai,
                $tanggalSelesai
            ]);


        /*
    |--------------------------------------------------------------------------
    | FILTER JENIS PASIEN
    |--------------------------------------------------------------------------
    */

        if ($request->filled('jenis_pasien')) {

            $query->where('pt.title', $request->jenis_pasien);
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
        | SEARCH GLOBAL (ANTI ERROR)
        |--------------------------------------------------------------------------
        */

            ->filter(function ($query) use ($request) {

                if ($request->has('search')) {

                    $search = $request->get('search')['value'];

                    if ($search != '') {

                        $query->where(function ($q) use ($search) {

                            $q->where('p.name', 'like', "%{$search}%")
                                ->orWhere('p.nrm', 'like', "%{$search}%")
                                ->orWhere('u.name', 'like', "%{$search}%")
                                ->orWhere('s3.title', 'like', "%{$search}%")
                                ->orWhere('pt.title', 'like', "%{$search}%")
                                ->orWhere('t.numb', 'like', "%{$search}%");
                        });
                    }
                }
            })


            /*
        |--------------------------------------------------------------------------
        | FORMAT TANGGAL
        |--------------------------------------------------------------------------
        */

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


            /*
        |--------------------------------------------------------------------------
        | STATUS BATAL
        |--------------------------------------------------------------------------
        */

            ->editColumn('status_batal', function ($row) {

                if ($row->status_batal == 1) {

                    return '<span class="badge bg-danger">Batal</span>';
                }

                return '<span class="badge bg-success">Aktif</span>';
            })


            ->rawColumns(['status_batal'])

            ->make(true);
    }
    public function exportExcel(Request $request)
    {

        $start = $request->start_date;
        $end   = $request->end_date;
        $jenis = $request->jenis_pasien;

        return Excel::download(
            new ErmExport($start, $end, $jenis),
            'kunjungan_poli.xlsx'
        );
    }
}
