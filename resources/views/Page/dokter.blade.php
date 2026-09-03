@extends('layouts.layouts')
@section('content')
    <style>
        .doctor-page-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 18px;
        }

        .doctor-page-head h4 {
            margin: 0 0 4px;
            font-size: 20px;
            font-weight: 700;
            color: #273444;
        }

        .doctor-page-head p {
            margin: 0;
            color: #8290a3;
            font-size: 12px;
        }

        .doctor-filter {
            padding: 16px;
            margin-bottom: 18px;
            background: #f8fafc;
            border: 1px solid #e8edf3;
            border-radius: 10px;
        }

        .doctor-filter-field {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            min-width: 160px;
        }

        .doctor-filter-field label {
            height: 17px;
            margin-bottom: 5px;
        }

        /* Samakan Select2 dengan input Bulan & Tahun */
        .doctor-filter-field .select2-container {
            width: 100% !important;
            display: block;
        }

        .doctor-filter-field .select2-container .select2-selection--single {
            height: 38px !important;
            min-height: 38px !important;
            border: 1px solid #e1e7ed !important;
            border-radius: 8px !important;
        }

        .doctor-filter-field .select2-selection__rendered {
            height: 36px !important;
            line-height: 36px !important;
            padding-left: 12px !important;
            padding-right: 32px !important;
        }

        .doctor-filter-field .select2-selection__arrow {
            height: 36px !important;
            top: 1px !important;
        }

        .doctor-filter-inner {
            display: flex;
            align-items: end;
            gap: 10px;
            flex-wrap: wrap;
        }

        .doctor-filter-field.filter-poli {
            width: 300px;
            min-width: 300px;
        }

        .doctor-filter-field.filter-dokter {
            width: 300px;
            min-width: 300px;
        }

        /* Select2 mengikuti lebar field */
        .filter-poli .select2-container,
        .filter-dokter .select2-container {
            width: 100% !important;
        }

        /* Supaya teks panjang tidak bikin scroll horizontal */
        .select2-results__option {
            white-space: normal;
            word-break: normal;
        }

        .doctor-filter-field label {
            display: block;
            margin-bottom: 5px;
            font-size: 11px;
            font-weight: 600;
            color: #667587;
        }

        .doctor-filter .form-control {
            height: 38px;
            border-radius: 8px;
            border-color: #e1e7ed;
            font-size: 12px;
        }

        .doctor-filter .btn {
            height: 38px;
            border-radius: 8px;
            font-size: 12px;
            padding-left: 16px;
            padding-right: 16px;
        }

        #datatable thead th {
            background: #f8fafc;
            color: #667587;
            font-size: 12px;
            font-weight: 600;
            padding: 12px;
            white-space: nowrap;
            border-bottom: 1px solid #e8edf3;
        }

        #datatable tbody td {
            padding: 13px 12px;
            vertical-align: middle;
            font-size: 12px;
            color: #687789;
        }

        #datatable tbody tr:hover {
            background: #fbfcfd;
        }

        #datatable tbody td:nth-child(2) {
            color: #44505f;
            font-weight: 600;
        }

        #datatable td:nth-child(3) {
            min-width: 220px;
            white-space: normal;
        }

        .dataTables_filter input {
            border: 1px solid #e1e7ed !important;
            border-radius: 8px !important;
            min-height: 36px;
            font-size: 12px;
            padding: 6px 10px !important;
        }

        .dataTables_length select {
            border-radius: 7px;
            border-color: #e1e7ed;
            font-size: 12px;
        }

        .quota-display {
            width: 100%;
            max-width: 190px;
            margin: 0 auto;
        }

        .quota-number {
            margin-bottom: 8px;
            text-align: center;
            font-weight: 600;
            color: #63758a;
        }

        .quota {
            width: 100%;
            height: 8px;
            overflow: hidden;
            border-radius: 20px;
            background: #dfe4e8;
        }

        .quota .progress-bar {
            height: 100%;
            border-radius: 20px;
        }

        .doctor-status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-available {
            color: #159455;
            background: #e8f9f0;
        }

        .status-warning {
            color: #a86b00;
            background: #fff3d5;
        }

        .status-full {
            color: #d94343;
            background: #ffeded;
        }

        @media(max-width: 767px) {
            .doctor-page-head {
                align-items: flex-start;
                flex-direction: column;
            }

            .doctor-filter-inner {
                align-items: stretch;
                flex-direction: column;
            }

            .doctor-filter-field {
                width: 100%;
            }

            .doctor-filter-field.filter-poli,
            .doctor-filter-field.filter-dokter {
                width: 100%;
                min-width: 100%;
            }

            .doctor-filter .btn {
                width: 100%;
            }
        }
    </style>
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="doctor-page-head">

                                <div>
                                    <h4>
                                        Jadwal Dokter & Kuota
                                    </h4>

                                    <p>
                                        Rekap pasien dan kapasitas layanan dokter per bulan
                                    </p>
                                </div>

                            </div>

                            {{-- ================= FILTER ================= --}}
                            <div class="doctor-filter">

                                <div class="doctor-filter-inner">

                                    <div class="doctor-filter-field">

                                        <label>
                                            Bulan
                                        </label>

                                        <select id="bulan" class="form-control">

                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}"
                                                    {{ $i == now()->month ? 'selected' : '' }}>

                                                    {{ \Carbon\Carbon::create()->month($i)->locale('id')->translatedFormat('F') }}

                                                </option>
                                            @endfor

                                        </select>

                                    </div>


                                    <div class="doctor-filter-field">

                                        <label>
                                            Tahun
                                        </label>

                                        <select id="tahun" class="form-control">

                                            @for ($t = now()->year; $t >= now()->year - 20; $t--)
                                                <option value="{{ $t }}">
                                                    {{ $t }}
                                                </option>
                                            @endfor

                                        </select>

                                    </div>

                                    <div class="doctor-filter-field filter-poli">
                                        <label>Poli</label>

                                        <select id="poli" class="form-control">
                                            <option value="">Semua Poli</option>

                                            @foreach ($filterPoli as $poli)
                                                <option value="{{ $poli->id }}">
                                                    {{ $poli->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>


                                    <div class="doctor-filter-field filter-dokter">
                                        <label>Dokter</label>

                                        <select id="dokter" class="form-control">
                                            <option value="">Semua Dokter</option>
                                        </select>
                                    </div>


                                    <button id="filter" class="btn btn-primary">
                                        <i class="bi bi-filter me-1"></i>
                                        Tampilkan
                                    </button>


                                    <button id="reset" class="btn btn-light border">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                                        Reset
                                    </button>

                                </div>

                            </div>

                            {{-- TABLE --}}

                            <div class="table-responsive">

                                <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">

                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Dokter</th>
                                            <th>Poli</th>
                                            <th>Jam Layanan</th>
                                            <th>Pasien / Kuota</th>
                                            <th>Status</th>
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

            const semuaDokter = @json($filterDokter);


            $('#poli').select2({
                placeholder: 'Cari poli...',
                allowClear: true,
                width: '100%',
                minimumResultsForSearch: 0
            });

            $('#dokter').select2({
                placeholder: 'Cari dokter...',
                allowClear: true,
                width: '100%',
                minimumResultsForSearch: 0
            });

            $('#poli, #dokter').on('select2:open', function() {

                setTimeout(function() {

                    const searchInput =
                        document.querySelector(
                            '.select2-container--open .select2-search__field'
                        );

                    if (searchInput) {
                        searchInput.focus();
                    }

                }, 0);

            });

            function updateDokter(poliId = '') {

                const dokterSelect = $('#dokter');

                dokterSelect.empty();

                dokterSelect.append(
                    new Option('Semua Dokter', '')
                );


                const dokterUnik = new Map();


                semuaDokter.forEach(function(item) {

                    if (
                        !poliId ||
                        String(item.section_id) === String(poliId)
                    ) {

                        dokterUnik.set(
                            String(item.id),
                            item.name
                        );

                    }

                });


                dokterUnik.forEach(function(nama, id) {

                    dokterSelect.append(
                        new Option(nama, id)
                    );

                });


                dokterSelect.val('').trigger('change');
            }

            // Isi dokter saat halaman pertama dibuka
            updateDokter();


            // Saat poli berubah, sesuaikan pilihan dokter
            $('#poli').on('change', function() {

                updateDokter(
                    $(this).val()
                );

            });

            let table = $('#datatable').DataTable({

                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,

                dom: 'lrtip',

                columnDefs: [{
                        targets: 0,
                        width: '5%'
                    },
                    {
                        targets: 1,
                        width: '30%'
                    },
                    {
                        targets: 2,
                        width: '24%'
                    },
                    {
                        targets: 3,
                        width: '15%'
                    },
                    {
                        targets: 4,
                        width: '16%'
                    },
                    {
                        targets: 5,
                        width: '10%'
                    }
                ],

                ajax: {
                    url: "{{ route('getJadwalDokter') }}",

                    data: function(d) {
                        d.bulan = $('#bulan').val();
                        d.tahun = $('#tahun').val();

                        d.poli = $('#poli').val();
                        d.dokter = $('#dokter').val();
                    }
                },

                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_dokter',
                        name: 'u.name'
                    },
                    {
                        data: 'nama_poli',
                        name: 'sec.title'
                    },
                    {
                        data: 'jam',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,

                        render: function(data, type, row) {

                            const pasien = Number(row.total_pasien ?? 0);
                            const kuota = Number(row.total_kuota ?? 0);

                            const persen = kuota > 0 ?
                                Math.min(Math.round((pasien / kuota) * 100), 100) :
                                0;

                            let warna = 'bg-success';

                            if (persen >= 100) {
                                warna = 'bg-danger';
                            } else if (persen >= 80) {
                                warna = 'bg-warning';
                            }

                            return `
                                <div class="quota-display">

                                    <div class="quota-number">
                                        ${pasien} / ${kuota}
                                    </div>

                                    <div class="quota">
                                        <div
                                            class="progress-bar ${warna}"
                                            style="width:${persen}%">
                                        </div>
                                    </div>

                                </div>
                            `;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center align-middle',


                        render: function(data, type, row) {

                            const pasien = Number(row.total_pasien ?? 0);
                            const kuota = Number(row.total_kuota ?? 0);

                            const persen = kuota > 0 ?
                                (pasien / kuota) * 100 :
                                0;

                            if (persen >= 100) {
                                return `
                                    <span class="doctor-status status-full">
                                        Penuh
                                    </span>
                                `;
                            }

                            if (persen >= 80) {
                                return `
                                    <span class="doctor-status status-warning">
                                        Hampir Penuh
                                    </span>
                                `;
                            }

                            return `
                                <span class="doctor-status status-available">
                                    Tersedia
                                </span>
                            `;
                        }
                    }
                ],

                order: [
                    [1, 'asc']
                ],

                pageLength: 10,

                language: {

                    processing: "Memuat data...",

                    search: "",

                    searchPlaceholder: "Cari dokter atau poli...",

                    lengthMenu: "Tampilkan _MENU_ data",

                    info: "_START_–_END_ dari _TOTAL_ dokter",

                    infoEmpty: "Tidak ada data",

                    zeroRecords: "Dokter tidak ditemukan",

                    emptyTable: "Tidak ada jadwal dokter pada periode ini",

                    paginate: {
                        previous: "‹",
                        next: "›"
                    }

                }

            });
            // Saat pilih tanggal mulai
            // $('#start_date').on('change', function() {

            //     let startDate = $(this).val();

            //     if (startDate) {
            //         // Set minimal tanggal akhir = tanggal mulai
            //         $('#end_date').attr('min', startDate);

            //         // Jika end_date lebih kecil dari start_date → reset
            //         if ($('#end_date').val() < startDate) {
            //             $('#end_date').val('');
            //         }
            //     }
            // });

            // Optional: kalau mau tanggal akhir juga membatasi tanggal mulai
            // $('#end_date').on('change', function() {

            //     let endDate = $(this).val();

            //     if (endDate) {
            //         $('#start_date').attr('max', endDate);
            //     }
            // });

            // FILTER BUTTON
            $('#filter').click(function() {
                table.ajax.reload();
            });

            // RESET BUTTON
            $('#reset').click(function() {

                const sekarang = new Date();

                $('#bulan').val(sekarang.getMonth() + 1);
                $('#tahun').val(sekarang.getFullYear());

                $('#poli')
                    .val('')
                    .trigger('change');

                updateDokter();

                table.ajax.reload();

            });

        });
    </script>
@endpush
