@extends('layouts.layouts')

@section('content')
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <h4 class="mt-0 header-title">Data Registrasi Pasien</h4>

                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered dt-responsive table-responsive nowrap"
                                    style="width:100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Tanggal Registrasi</th>
                                            <th>Jenis Kartu</th>
                                            <th>No Kartu</th>
                                            <th>NRM</th>
                                            <th>No RM Elektronik</th>
                                            <th>Tanggal Checkout</th>
                                            <th>Jam Checkout</th>
                                            <th>No BPJS</th>
                                            <th>No SEP</th>
                                            <th>Kode Poli</th>
                                            <th>Prefix</th>
                                            <th>Biaya</th>
                                            <th>Bayar BPJS</th>
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
                stateSave: true,

                ajax: {
                    url: "{{ route('getData') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').length ? $('#start_date').val() : '';
                        d.end_date = $('#end_date').length ? $('#end_date').val() : '';
                    }
                },

                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'reg_date',
                        name: 'tr_pxregistrations.reg_date'
                    },
                    {
                        data: 'idcardtype',
                        name: 'tr_pxregistrations.idcardtype'
                    },
                    {
                        data: 'idcardnumb',
                        name: 'tr_pxregistrations.idcardnumb'
                    },
                    {
                        data: 'nrm',
                        name: 'tr_pxregistrations.nrm'
                    },
                    {
                        data: 'numb',
                        name: 'rm_electronics.numb'
                    },
                    {
                        data: 'checkout_date',
                        name: 'tr_pxregistrations.checkout_date'
                    },
                    {
                        data: 'checkout_time',
                        name: 'tr_pxregistrations.checkout_time'
                    },
                    {
                        data: 'bpjs_numb',
                        name: 'tr_pxregistrations.bpjs_numb'
                    },
                    {
                        data: 'bpjs_sep',
                        name: 'tr_pxregistrations.bpjs_sep'
                    },
                    {
                        data: 'code',
                        name: 'sections.code'
                    },
                    {
                        data: 'prefix',
                        name: 'sections.prefix'
                    },
                    {
                        data: 'biaya',
                        name: 'tr_pxregistrations.biaya',
                        className: 'text-end',
                        render: function(data) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(data ?? 0);
                        }
                    },
                    {
                        data: 'bayar_bpjs',
                        name: 'tr_pxregistrations.bayar_bpjs',
                        className: 'text-end',
                        render: function(data) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(data ?? 0);
                        }
                    }
                ],

                order: [
                    [1, 'desc']
                ],

                pageLength: 10,

                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],

                language: {
                    processing: "Memuat data...",
                    search: "",
                    searchPlaceholder: "Cari pasien / NRM / SEP...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: {
                        previous: "‹",
                        next: "›"
                    }
                }

            });


            // =========================
            // OPTIONAL FILTER HANDLER
            // =========================

            if ($('#start_date').length && $('#end_date').length) {
                $('#start_date, #end_date').on('change', function() {
                    table.ajax.reload();
                });
            }

            if ($('#perPage').length) {
                $('#perPage').on('change', function() {
                    table.page.len(this.value).draw();
                });
            }

            if ($('#reset').length) {
                $('#reset').on('click', function() {
                    $('#start_date').val('');
                    $('#end_date').val('');
                    table.search('').columns().search('').draw();
                });
            }

        });
    </script>
@endpush
