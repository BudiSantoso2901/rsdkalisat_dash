@extends('layouts.layouts')

@section('content')
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">

                    <div class="card">
                        <div class="card-body">

                            <h4 class="mt-0 header-title mb-3">
                                Data Kunjungan Pasien Poli
                            </h4>

                            {{-- ================= FILTER ================= --}}
                            <div class="row mb-3">

                                <div class="col-md-3">
                                    <label>Range Tanggal</label>
                                    <input type="text" id="tanggal_range" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label>Jenis Pasien</label>
                                    <select id="jenis_pasien" class="form-control">

                                        <option value="">Semua</option>
                                        <option value="ASURANSI">ASURANSI</option>
                                        <option value="BPJS NON PBI">BPJS NON PBI</option>
                                        <option value="BPJS PBI">BPJS PBI</option>
                                        <option value="BPJS UHC">BPJS UHC</option>
                                        <option value="JPK">JPK</option>
                                        <option value="PEGAWAI">PEGAWAI</option>
                                        <option value="SPM">SPM</option>
                                        <option value="UMUM">UMUM</option>

                                    </select>
                                </div>

                                <div class="col-md-3 d-flex align-items-end">

                                    <button id="filter" class="btn btn-primary me-2">
                                        Filter
                                    </button>

                                    <button id="reset" class="btn btn-secondary me-2">
                                        Reset
                                    </button>
                                    <button id="export_excel" class="btn btn-success me-2">
                                        Export Excel
                                    </button>

                                </div>

                            </div>
                            {{-- ================= END FILTER ================= --}}

                            <div class="table-responsive">

                                <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">

                                    <thead class="table-light">

                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Tanggal Checkout</th>
                                            <th>Tanggal Registrasi</th>
                                            <th>Tanggal Selesai</th>
                                            <th>Dokter</th>
                                            <th>Poli</th>
                                            <th>NRM</th>
                                            <th>Nama Pasien</th>
                                            <th>Penjamin</th>
                                            <th>SEP</th>
                                            <th>Sumber</th>
                                            <th>Diagnosa</th>
                                            <th>Biaya</th>
                                        </tr>

                                    </thead>

                                    <tbody></tbody>

                                </table>

                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {

            /*
            |--------------------------------------------------------------------------
            | DATE RANGE PICKER
            |--------------------------------------------------------------------------
            */

            $('#tanggal_range').daterangepicker({

                locale: {
                    format: 'YYYY-MM-DD'
                },

                startDate: moment(),
                endDate: moment()

            });


            /*
            |--------------------------------------------------------------------------
            | DATATABLE
            |--------------------------------------------------------------------------
            */

            let table = $('#datatable').DataTable({

                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,

                ajax: {
                    url: "{{ route('getDataPoli') }}",
                    data: function(d) {

                        let range = $('#tanggal_range').val();

                        if (range) {

                            let tanggal = range.split(' - ');

                            d.start_date = tanggal[0];
                            d.end_date = tanggal[1];

                        }

                        d.jenis_pasien = $('#jenis_pasien').val();

                    }
                },

                columns: [

                    {
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'checkout_date'
                    },
                    {
                        data: 'reg_date'
                    },
                    {
                        data: 'selesai_date'
                    },

                    {
                        data: 'nama_dokter'
                    },

                    {
                        data: 'ruangan'
                    },

                    {
                        data: 'nrm'
                    },

                    {
                        data: 'name'
                    },

                    {
                        data: 'penjamin'
                    },

                    {
                        data: 'bpjs_sep'
                    },

                    {
                        data: 'source_reg'
                    },

                    {
                        data: 'rm_diagnosa'
                    }, {
                        data: 'biaya'
                    }

                ],

                order: [
                    [1, 'desc']
                ],

                pageLength: 10,

                language: {
                    processing: "Memuat data...",
                    searchPlaceholder: "Cari pasien / poli...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: {
                        previous: "‹",
                        next: "›"
                    }
                }

            });


            /*
            |--------------------------------------------------------------------------
            | FILTER
            |--------------------------------------------------------------------------
            */

            $('#filter').click(function() {

                table.ajax.reload();

            });


            /*
            |--------------------------------------------------------------------------
            | RESET
            |--------------------------------------------------------------------------
            */

            $('#reset').click(function() {

                $('#jenis_pasien').val('');

                $('#tanggal_range').data('daterangepicker').setStartDate(moment());
                $('#tanggal_range').data('daterangepicker').setEndDate(moment());

                table.ajax.reload();

            });
            /*
    |--------------------------------------------------------------------------
    | EXPORT EXCEL
    |--------------------------------------------------------------------------
    */

            $('#export_excel').click(function() {

                let range = $('#tanggal_range').val();
                let jenis_pasien = $('#jenis_pasien').val();

                let start = '';
                let end = '';

                if (range) {

                    let tanggal = range.split(' - ');

                    start = tanggal[0];
                    end = tanggal[1];
                }

                let url = "{{ route('export.excel') }}";

                url += "?start_date=" + start +
                    "&end_date=" + end +
                    "&jenis_pasien=" + jenis_pasien;

                window.open(url, '_blank');

            });


        });
    </script>
@endpush
