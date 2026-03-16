<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\DataTables;

class RanapController extends Controller
{
    //
     public function ranap_get_data(Request $request)
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
                't.checkout_date',
                't.source_reg',
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
                't.biaya',
                't.rm_diagnosa'
            )

            ->whereBetween('t.checkout_date', [
                $tanggalMulai,
                $tanggalSelesai
            ])

            ->whereIn('t.source_reg', ['ADMISI', 'MJKN', 'NULL'])

            ->where('t.inpatient_status', 1)
            ->where('t.status', 1)
            ->where('t.parent_id', 0)

            ->where('s3.title', '!=', 'IGD 24 JAM');


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
    | FILTER POLI / RUANGAN
    |--------------------------------------------------------------------------
    */

        if ($request->filled('ruangan')) {

            $query->where('s3.id', $request->ruangan);
        }


        /*
    |--------------------------------------------------------------------------
    | ORDER
    |--------------------------------------------------------------------------
    */

        $query->orderBy('t.checkout_date', 'asc');


        /*
    |--------------------------------------------------------------------------
    | DATATABLES
    |--------------------------------------------------------------------------
    */

        return DataTables::of($query)

            ->addIndexColumn()

            /*
        |--------------------------------------------------------------------------
        | GLOBAL SEARCH
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

            ->editColumn('checkout_date', function ($row) {
                return $row->checkout_date
                    ? Carbon::parse($row->checkout_date)->format('d-m-Y H:i')
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
}
