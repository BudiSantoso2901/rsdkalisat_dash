@extends('layouts.layouts')
@section('content')
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">
            <!-- end row -->

            <div class="row">
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mt-0">Informasi Kunjungan</h4>

                            <div class="widget-chart text-center">
                                <div id="morris-donut-example" dir="ltr" style="height: 245px;" class="morris-chart">
                                </div>
                            </div>
                            <ul class="list-inline chart-detail-list mb-0">
                                <li class="list-inline-item">
                                    <h5 style="color: #ff8acc;"><i class="fa fa-circle me-1"></i>Kunjungan IGD
                                    </h5>
                                </li>
                                <li class="list-inline-item">
                                    <h5 style="color: #5b69bc;"><i class="fa fa-circle me-1"></i>Kunjungan Rawat Jalan
                                    </h5>
                                </li>
                                <li class="list-inline-item">
                                    <h5 style="color: #202a63;"><i class="fa fa-circle me-1"></i>Kunjungan Rawat Inap
                                    </h5>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mt-0">Kunjungan IGD</h4>
                            <div id="morris-bar-example" dir="ltr" style="height: 280px;" class="morris-chart">
                            </div>
                        </div>
                    </div>
                </div><!-- end col -->

                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="dropdown float-end">
                                <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="mdi mdi-dots-vertical"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item">Action</a>
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item">Another action</a>
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item">Something else</a>
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
                                </div>
                            </div>
                            <h4 class="header-title mt-0">Kunjungan Rawat Inap dan Rawat Jalan</h4>
                            <div id="morris-line-example" dir="ltr" style="height: 280px;" class="morris-chart">
                            </div>
                        </div>
                    </div>
                </div><!-- end col -->
            </div><!-- end col -->


        </div>
        <!-- end row -->



        <div class="row">
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">

                        <!-- Dropdown kanan atas -->
                        <div class="dropdown float-end">
                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="mdi mdi-dots-vertical"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="javascript:void(0);" class="dropdown-item">Refresh</a>
                                <a href="javascript:void(0);" class="dropdown-item">Export</a>
                            </div>
                        </div>

                        <!-- Judul -->
                        <h4 class="header-title mb-3">Statistik Kunjungan</h4>

                        <!-- List Data -->
                        <ul class="list-group list-group-flush">

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-hospital me-2"></i> GAWAT DARURAT</span>
                                <span class="text-card-baru fw-bold">2</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-clipboard2-pulse me-2"></i> POLIKLINIK</span>
                                <span class="text-card-baru fw-bold">82</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-tools me-2"></i> PENUNJANG</span>
                                <span class="text-card-baru fw-bold">26</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-capsule me-2"></i> FARMASI</span>
                                <span class="text-card-baru fw-bold">0</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-heart-pulse me-2"></i> INTENSIVE UNIT</span>
                                <span class="text-card-baru fw-bold">1</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-building me-2"></i> RAWAT INAP</span>
                                <span class="text-card-baru fw-bold">1</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-building me-2"></i> RUANG OK</span>
                                <span class="text-card-baru fw-bold">1</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-building me-2"></i> GIZI</span>
                                <span class="text-card-baru fw-bold">1</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-building me-2"></i> APOTEK</span>
                                <span class="text-card-baru fw-bold">1</span>
                            </li>

                        </ul>

                        <!-- Total -->
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                            <span class="text-muted">
                                <i class="bi bi-people-fill me-2"></i> JUMLAH KUNJUNGAN
                            </span>
                            <span class="text-card-baru fw-bold fs-5">112</span>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <div class="dropdown float-end">
                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="mdi mdi-dots-vertical"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Action</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Another action</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Something else</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
                            </div>
                        </div>

                        <h4 class="header-title mt-0 mb-3">Jadwal Dokter Hari ini</h4>

                        <div class="table-responsive">
                            <table id="table-jadwal" class="table table-hover mb-0 w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Dokter</th>
                                        <th>Tanggal</th>
                                        <th>Poli</th>
                                        <th>Jam Praktek</th>
                                        <th>Kuota Pasien</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div><!-- end col -->

        </div>
        <!-- end row -->

    </div> <!-- container-fluid -->

    </div> <!-- content -->
@endsection
@push('script')
    <!--Morris Chart-->
    <script src="{{ asset('assets/libs/morris.js06/morris.min.js') }}"></script>
    <script src="{{ asset('assets/libs/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/dashboard.init.js') }}"></script>
    <script>
        $(document).ready(function() {

            $('#table-jadwal').DataTable({

                processing: true,
                serverSide: true,
                searching: false,
                lengthChange: false,
                pageLength: 7,
                ordering: false,
                info: false,

                ajax: {
                    url: "{{ route('dokter.hari_ini') }}",
                    type: "GET"
                },

                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },

                    {
                        data: 'name',
                        render: function(data) {
                            return `<strong>${data}</strong>`;
                        }
                    },

                    {
                        data: 'date',
                        render: function(data) {
                            let date = new Date(data);
                            return date.toLocaleDateString('id-ID', {
                                weekday: 'long',
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            });
                        }
                    },

                    {
                        data: null,
                        render: function(data, type, row) {
                            return `${row.code} - ${row.nama_poli}`;
                        }
                    },

                    {
                        data: null,
                        render: function(data, type, row) {
                            return `${row.open_time} - ${row.closed_time}`;
                        }
                    },

                    // 🔥 KUOTA PROGRESS FIX
                    {
                        data: null,
                        className: 'text-center',
                        render: function(data, type, row) {

                            let kapasitas = Number(row.kapasitaspasien ?? 0);
                            let terisi = Number(row.total_pasien ?? 0);

                            let persen = kapasitas > 0 ?
                                Math.round((terisi / kapasitas) * 100) :
                                0;

                            let warna = 'bg-success';

                            if (persen >= 100) {
                                warna = 'bg-danger';
                            } else if (persen >= 80) {
                                warna = 'bg-warning';
                            }

                            return `
                        <div style="min-width:130px">
                            <div class="fw-semibold mb-1">
                                ${terisi} / ${kapasitas}
                            </div>
                            <div class="progress" style="height:6px;">
                                <div class="progress-bar ${warna}"
                                     role="progressbar"
                                     style="width: ${persen}%">
                                </div>
                            </div>
                        </div>
                    `;
                        }
                    },

                    {
                        data: null,
                        className: 'text-center',
                        render: function(data, type, row) {

                            let sisa = Number(row.kapasitaspasien ?? 0) -
                                Number(row.total_pasien ?? 0);

                            if (sisa > 0) {
                                return `<span class="badge bg-success">Tersedia</span>`;
                            } else {
                                return `<span class="badge bg-danger">Penuh</span>`;
                            }
                        }
                    }
                ],

                language: {
                    processing: "Memuat..."
                }

            });

        });
    </script>
@endpush
