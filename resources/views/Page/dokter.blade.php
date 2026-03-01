@extends('layouts.layouts')
@section('content')
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <h4 class="mt-0 header-title mb-3">
                                Data Jadwal Dokter & Kuota
                            </h4>

                            {{-- ================= FILTER ================= --}}
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label>Tanggal Mulai</label>
                                    <input type="date" id="start_date" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label>Tanggal Akhir</label>
                                    <input type="date" id="end_date" class="form-control">
                                </div>

                                <div class="col-md-3 d-flex align-items-end">
                                    <button id="reset" class="btn btn-secondary me-2">
                                        Reset
                                    </button>
                                    <button id="filter" class="btn btn-primary me-2">
                                        Filter
                                    </button>
                                </div>
                            </div>
                            {{-- ================= END FILTER ================= --}}

                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">

                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Tanggal</th>
                                            {{-- <th>Dokter</th> --}}
                                            <th>Nama Dokter</th>
                                            <th>Kode Poli</th>
                                            <th>Prefix</th>
                                            <th>Nama Poli</th>
                                            <th>Sub Spesialis</th>
                                            <th>Jam Buka</th>
                                            <th>Jam Tutup</th>
                                            <th>Kapasitas Kuota</th>
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

            let table = $('#datatable').DataTable({

                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,

                ajax: {
                    url: "{{ route('getJadwalDokter') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },

                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'date'
                    },
                    // {
                    //     data: 'nama_dokter'
                    // },
                    {
                        data: 'name'
                    },
                    {
                        data: 'code'
                    },
                    {
                        data: 'prefix'
                    },
                    {
                        data: 'nama_poli'
                    },
                    {
                        data: 'kodesubspesialis'
                    },
                    {
                        data: 'open_time'
                    },
                    {
                        data: 'closed_time'
                    },
                    {
                        data: 'kapasitaspasien',
                        className: 'text-center'
                    },
                ],

                order: [
                    [1, 'asc']
                ],

                pageLength: 10,

                language: {
                    processing: "Memuat data...",
                    searchPlaceholder: "Cari dokter / poli...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: {
                        previous: "‹",
                        next: "›"
                    }
                }

            });
            // Saat pilih tanggal mulai
            $('#start_date').on('change', function() {

                let startDate = $(this).val();

                if (startDate) {
                    // Set minimal tanggal akhir = tanggal mulai
                    $('#end_date').attr('min', startDate);

                    // Jika end_date lebih kecil dari start_date → reset
                    if ($('#end_date').val() < startDate) {
                        $('#end_date').val('');
                    }
                }
            });

            // Optional: kalau mau tanggal akhir juga membatasi tanggal mulai
            $('#end_date').on('change', function() {

                let endDate = $(this).val();

                if (endDate) {
                    $('#start_date').attr('max', endDate);
                }
            });

            // FILTER BUTTON
            $('#filter').click(function() {
                table.ajax.reload();
            });

            // RESET BUTTON
            $('#reset').click(function() {
                $('#start_date').val('');
                $('#end_date').val('');
                table.ajax.reload();
            });

        });
    </script>
@endpush
