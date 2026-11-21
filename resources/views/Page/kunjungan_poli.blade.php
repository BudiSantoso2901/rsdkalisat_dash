@extends('layouts.layouts')

@section('content')
    <style>
        /* =========================================
           FILTER GRID - 3 KOLOM
        ========================================= */

        .kunjungan-filter-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            column-gap: 22px;
            row-gap: 12px;
            align-items: end;
            width: 100%;
        }

        .kunjungan-filter-grid .filter-item {
            min-width: 0;
            width: 100%;
        }

        .kunjungan-filter-grid .filter-item label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #6c757d;
        }

        .kunjungan-filter-grid .filter-item>.form-control {
            width: 100%;
            height: 38px;
            box-sizing: border-box;
            font-size: 14px;
        }

        /* =========================================
           SELECT2 SINGLE - RUANGAN DAN DOKTER
        ========================================= */

        .kunjungan-filter-grid .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            height: 38px !important;
            border: 1px solid #ced4da !important;
            border-radius: 4px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 12px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }

        /* =========================================
           SELECT2 MULTIPLE - JENIS PASIEN
        ========================================= */

        #jenis_pasien+.select2-container {
            display: block !important;
            width: 100% !important;
        }

        #jenis_pasien+.select2-container .select2-selection--multiple {
            box-sizing: border-box !important;
            width: 100% !important;
            height: 38px !important;
            min-height: 38px !important;
            padding: 2px 7px !important;
            background: #fff !important;
            border: 1px solid #ced4da !important;
            border-radius: 4px !important;
            overflow: hidden !important;
        }

        #jenis_pasien+.select2-container .select2-selection--multiple .select2-selection__rendered {
            display: flex !important;
            align-items: center !important;
            flex-wrap: nowrap !important;
            width: 100% !important;
            height: 32px !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow-x: auto !important;
            overflow-y: hidden !important;
            white-space: nowrap !important;
        }

        #jenis_pasien+.select2-container .select2-selection--multiple .select2-selection__choice {
            float: none !important;
            flex: 0 0 auto !important;
            margin: 0 5px 0 0 !important;
            padding: 3px 7px !important;
            font-size: 12px !important;
        }

        #jenis_pasien+.select2-container .select2-selection--multiple .select2-search--inline {
            float: none !important;
            flex: 1 0 70px !important;
            min-width: 70px !important;
        }

        #jenis_pasien+.select2-container .select2-selection--multiple .select2-search__field {
            width: 100% !important;
            height: 28px !important;
            margin: 0 !important;
            padding: 0 3px !important;
            font-size: 14px !important;
            line-height: 28px !important;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
        }

        /* =========================================
           SEARCH PASIEN
        ========================================= */

        #search_pasien {
            width: 100%;
            height: 38px;
            box-sizing: border-box;
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 6px 12px;
            font-size: 14px;
        }

        #search_pasien:focus {
            border-color: #80bdff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, .15);
        }

        #search_pasien::placeholder {
            color: #a0a7ae;
        }

        #search_pasien_btn {
            min-width: 125px;
        }

        /* RESPONSIVE */
        @media (max-width: 767px) {
            .kunjungan-filter-grid {
                grid-template-columns: 1fr;
                row-gap: 12px;
            }
        }

        /* DATE RANGE - hanya tampilkan 1 kalender */
        .daterangepicker .drp-calendar.right {
            display: none !important;
        }

        .daterangepicker .drp-calendar.left {
            border-left: 0 !important;
        }

        /* Bedakan Pilih Manual dari preset */
        .daterangepicker .ranges li:last-child {
            margin-top: 8px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;

            font-weight: 700;
            color: #3578c6;
            letter-spacing: .2px;
        }

        /* Saat Pilih Manual aktif */
        .daterangepicker .ranges li:last-child.active {
            color: #fff;
        }

        .soap-value {
            white-space: pre-wrap;
            overflow-wrap: anywhere;
            line-height: 1.6;
        }

        /* STATUS VERIFIKASI CPPT */
        .soap-verification-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 2px 5px;
            border-radius: 7px;
            font-size: 10px;
            font-weight: 600;
            white-space: nowrap;
            line-height: 1.2;
            border: 1px solid transparent;
        }

        .soap-verification-badge.is-verified {
            color: #166534;
            background: #dcfce7;
            border-color: #bbf7d0;
        }

        .soap-verification-badge.is-unverified {
            color: #92400e;
            background: #fef3c7;
            border-color: #fde68a;
        }

        .soap-verification-badge.is-unknown {
            color: #475569;
            background: #f1f5f9;
            border-color: #e2e8f0;
        }

        .erm-json-content {
            white-space: pre-wrap;
            overflow-wrap: anywhere;
            word-break: break-word;
            max-height: 420px;
            overflow-y: auto;
            background: #f8f9fa;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 15px;
            font-size: 12px;
            line-height: 1.6;
        }

        .erm-document-card {
            border-left: 4px solid #3578c6;
        }

        .erm-document-card {
            border: 1px solid #e5eaf0;
            border-left: 4px solid #3997d8;
            border-radius: 8px;
            height: 100%;
            transition: .2s ease;
        }

        .erm-document-card:hover {
            box-shadow: 0 4px 14px rgba(0, 0, 0, .08);
        }

        .erm-field-box {
            background: #f8fafc;
            border: 1px solid #e9edf2;
            border-radius: 7px;
            padding: 12px;
            height: 100%;
        }

        .erm-field-label {
            color: #718096;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .erm-field-value {
            color: #334155;
            font-size: 13px;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
            line-height: 1.6;
        }

        .erm-nested-box {
            border: 1px solid #e5eaf0;
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 12px;
            background: #fff;
        }

        .erm-group-title {
            font-weight: 700;
            font-size: 14px;
            color: #3578c6;
            margin-bottom: 12px;
        }

        .erm-empty {
            background: #f8fafc;
            padding: 14px;
            border-radius: 7px;
            color: #8793a1;
            font-size: 13px;
        }

        .erm-viewer-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .erm-document-viewer {
            border: 1px solid #e5eaf0;
            border-radius: 9px;
            overflow: hidden;
            background: #fff;
        }

        .erm-document-viewer-header {
            padding: 16px 20px;
            background: #f8fbff;
            border-bottom: 1px solid #e5eaf0;
        }

        .erm-document-content-scroll {
            max-height: 58vh;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 20px;
            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
        }

        .erm-detail-section {
            margin-bottom: 20px;
        }

        .erm-detail-section-title {
            font-size: 14px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 14px;
        }

        .erm-field-box {
            background: #f8fafc;
            border: 1px solid #e9edf2;
            border-radius: 7px;
            padding: 12px;
            min-height: 70px;
            height: auto;
        }

        .erm-field-label {
            color: #718096;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .erm-field-value {
            color: #334155;
            font-size: 13px;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
            line-height: 1.6;
        }

        .erm-collapsible {
            border: 1px solid #e5eaf0;
            border-radius: 8px;
            margin-bottom: 12px;
            background: #fff;
        }

        .erm-collapsible summary {
            cursor: pointer;
            padding: 13px 15px;
            font-size: 13px;
            font-weight: 600;
            color: #3578c6;
            outline: none;
        }

        .erm-collapsible summary:hover {
            background: #f8fbff;
        }

        .erm-collapsible-content {
            padding: 12px 15px;
            border-top: 1px solid #e5eaf0;
        }

        .erm-nested-box {
            border: 1px solid #e5eaf0;
            border-radius: 7px;
            padding: 12px;
            margin-bottom: 10px;
            background: #fff;
        }

        .erm-empty {
            background: #f8fafc;
            padding: 14px;
            border-radius: 7px;
            color: #8793a1;
            font-size: 13px;
        }

        /* Preview gambar tanda tangan / gambar ERM */
        .erm-image-preview {
            display: block;
            width: 100%;
            max-width: 280px;
            max-height: 160px;
            min-height: 70px;
            object-fit: contain;
            object-position: left center;
            padding: 8px;
            background: #fff;
            border: 1px solid #e5eaf0;
            border-radius: 7px;
        }

        @media (max-width: 575px) {
            .erm-document-content-scroll {
                max-height: 55vh;
                padding: 12px;
            }
        }


        /* TABEL KUNJUNGAN PASIEN */
        .kunjungan-table-wrapper {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
        }

        .dataTables_wrapper {
            width: 100%;
            max-width: 100%;
        }

        /* FILTER LAYOUT */
        .filter-label {
            font-weight: 600;
            margin-bottom: 6px;
            color: #6c757d;
        }

        #search_pasien {
            height: 38px;
            width: 100%;
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 6px 12px;
            font-size: 14px;
        }

        #search_pasien:focus {
            border-color: #80bdff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, .15);
        }

        #search_pasien::placeholder {
            color: #a0a7ae;
        }

        #search_pasien_btn {
            min-width: 125px;
        }

        @media (max-width: 767px) {
            .filter-grid .col-md-4 {
                margin-bottom: 12px;
            }
        }

        /* Tabel memiliki lebar tetap agar kolom tidak menyempit */
        #datatable {
            width: 2315px !important;
            min-width: 2315px !important;
            max-width: none !important;
            table-layout: fixed !important;
            margin-bottom: 0 !important;
            border-collapse: collapse;
        }

        /* Ukuran dan posisi sel */
        #datatable th,
        #datatable td {
            box-sizing: border-box;
            vertical-align: middle;
            padding: 10px;
            font-size: 13px;
        }

        /* Header */
        #datatable thead th {
            background: #f5f8fc;
            color: #334155;
            font-weight: 600;
            white-space: nowrap !important;
            height: 48px;
        }

        /* Isi tabel */
        #datatable tbody td {
            white-space: nowrap;
            line-height: 1.5;
        }

        /* Kolom dengan teks panjang */
        #datatable tbody td:nth-child(7),
        #datatable tbody td:nth-child(8),
        #datatable tbody td:nth-child(10),
        #datatable tbody td:nth-child(11),
        #datatable tbody td:nth-child(14) {
            white-space: normal;
            overflow-wrap: break-word;
            word-break: normal;
        }

        /* Tombol Detail */
        #datatable td:nth-child(2) {
            white-space: nowrap;
        }

        .btn-detail-soap {
            min-width: 85px;
            white-space: nowrap;
        }

        /* Lebar kolom */
        #datatable th:nth-child(1),
        #datatable td:nth-child(1) {
            width: 55px;
        }

        #datatable th:nth-child(2),
        #datatable td:nth-child(2) {
            width: 110px;
        }

        #datatable th:nth-child(3),
        #datatable td:nth-child(3) {
            width: 145px;
        }

        #datatable th:nth-child(4),
        #datatable td:nth-child(4) {
            width: 145px;
        }

        #datatable th:nth-child(5),
        #datatable td:nth-child(5) {
            width: 155px;
        }

        #datatable th:nth-child(6),
        #datatable td:nth-child(6) {
            width: 145px;
        }

        #datatable th:nth-child(7),
        #datatable td:nth-child(7) {
            width: 220px;
        }

        #datatable th:nth-child(8),
        #datatable td:nth-child(8) {
            width: 190px;
        }

        #datatable th:nth-child(9),
        #datatable td:nth-child(9) {
            width: 105px;
        }

        #datatable th:nth-child(10),
        #datatable td:nth-child(10) {
            width: 200px;
        }

        #datatable th:nth-child(11),
        #datatable td:nth-child(11) {
            width: 150px;
        }

        #datatable th:nth-child(12),
        #datatable td:nth-child(12) {
            width: 170px;
        }

        #datatable th:nth-child(13),
        #datatable td:nth-child(13) {
            width: 110px;
        }

        #datatable th:nth-child(14),
        #datatable td:nth-child(14) {
            width: 280px;
        }

        #datatable th:nth-child(15),
        #datatable td:nth-child(15) {
            width: 135px;
        }

        /* MODAL DETAIL PASIEN */
        #modalRiwayatSoap .modal-dialog {
            width: calc(100% - 30px);
            max-width: 1200px;
            height: calc(100vh - 30px);
            margin: 15px auto;
        }

        #modalRiwayatSoap .modal-content {
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border-radius: 10px;
        }

        /* HEADER SELALU TERLIHAT */
        #modalRiwayatSoap .modal-header {
            flex: 0 0 auto;
            position: relative;
            z-index: 10;
            background: #fff;
            border-bottom: 1px solid #e5eaf0;
        }

        /* BODY MENJADI SATU-SATUNYA AREA SCROLL */
        #modalRiwayatSoap .modal-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto !important;
            overflow-x: hidden;
            padding: 24px;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior-y: contain;
        }

        @media (max-width: 575px) {
            #modalRiwayatSoap .modal-dialog {
                width: calc(100% - 16px);
                height: calc(100vh - 16px);
                margin: 8px auto;
            }

            #modalRiwayatSoap .modal-body {
                padding: 15px;
            }
        }
    </style>
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

                            <div class="kunjungan-filter-grid mb-4">

                                {{-- BARIS 1 --}}
                                <div class="filter-item">
                                    <label for="jenis_kunjungan">Jenis Kunjungan</label>

                                    <select id="jenis_kunjungan" class="form-control">
                                        <option value="all" selected>Semua Jenis Kunjungan</option>
                                        <option value="rajal">Rawat Jalan</option>
                                        <option value="ranap">Rawat Inap</option>
                                        <option value="igd">IGD & PONEK</option>
                                    </select>
                                </div>

                                <div class="filter-item">
                                    <label for="tanggal_range">Tanggal Kunjungan</label>

                                    <input type="text" id="tanggal_range" class="form-control">
                                </div>

                                <div class="filter-item">
                                    <label for="ruangan">Ruangan</label>

                                    <select id="ruangan" class="form-control select2">
                                        <option value="">Semua Ruangan</option>

                                        @foreach ($ruangan as $s)
                                            <option value="{{ $s->id }}">
                                                {{ $s->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- BARIS 2 --}}
                                <div class="filter-item">
                                    <label for="jenis_pasien">Jenis Pasien</label>

                                    <select id="jenis_pasien" name="jenis_pasien[]" class="form-control" multiple>

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

                                <div class="filter-item">
                                    <label for="dokter">Dokter</label>

                                    <select id="dokter" class="form-control select2">
                                        <option value="">Semua Dokter</option>

                                        @foreach ($dokter as $d)
                                            <option value="{{ $d->id }}">
                                                {{ $d->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="filter-item">
                                    <label for="search_pasien">Cari Pasien</label>

                                    <input type="text" id="search_pasien" class="form-control"
                                        placeholder="Nama, NRM, dokter, atau poli..." autocomplete="off">
                                </div>

                            </div>

                            {{-- TOMBOL FILTER --}}

                            <div class="row mb-3">
                                <div class="col-12 d-flex flex-wrap align-items-center gap-2">

                                    <button id="filter" type="button" class="btn btn-primary">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>

                                    <button id="reset" type="button" class="btn btn-secondary">
                                        <i class="fas fa-redo"></i> Reset
                                    </button>

                                    <button id="export_excel" type="button" class="btn btn-success">
                                        <i class="fas fa-file-excel"></i> Export Excel
                                    </button>

                                    <button id="search_pasien_btn" type="button" class="btn btn-info text-white">
                                        <i class="fas fa-search"></i> Cari Pasien
                                    </button>

                                </div>
                            </div>

                            {{-- ================= END FILTER ================= --}}
                            
                            <div class="kunjungan-table-wrapper">

                                <table id="datatable" class="table table-bordered w-100">
                                    <thead class="table-light">

                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Detail</th>
                                            <th>Checkout Date</th>
                                            <th>Schedule Date</th>
                                            <th>Tanggal Registrasi</th>
                                            <th>Tanggal Selesai</th>
                                            <th>Dokter</th>
                                            <th>Poli</th>
                                            <th>NRM</th>
                                            <th>Nama Pasien</th>
                                            <th>Penjamin</th>
                                            <th>NO SEP</th>
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


    {{-- MODAL DETAIL PASIEN --}}
    <div class="modal fade" id="modalRiwayatSoap" tabindex="-1" role="dialog" aria-labelledby="modalRiwayatSoapLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-xl" role="document">

            <div class="modal-content">

                {{-- HEADER TETAP --}}
                <div class="modal-header">

                    <div>
                        <h5 class="modal-title" id="modalRiwayatSoapLabel">
                            <i class="fas fa-file-medical text-primary"></i>
                            Detail Pasien & Riwayat SOAP
                        </h5>

                        <small class="text-muted">
                            Riwayat catatan perkembangan pasien terintegrasi
                        </small>
                    </div>

                    <button type="button" class="close btn-close-soap" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>

                {{-- SEMUA ISI MODAL BERADA DI DALAM BODY --}}
                <div class="modal-body">

                    {{-- IDENTITAS PASIEN --}}
                    <div class="card border-primary mb-4">
                        <div class="card-body">

                            <h6 class="text-primary font-weight-bold mb-3">
                                IDENTITAS PASIEN
                            </h6>

                            <div id="soapPatientIdentity">
                                <span class="text-muted">
                                    Memuat identitas pasien...
                                </span>
                            </div>

                        </div>
                    </div>

                    {{-- DAFTAR DOKUMEN RM ELEKTRONIK --}}

                    <div id="ermListView">

                        <div class="d-flex justify-content-between align-items-center mb-3">

                            <h5 class="mb-0">
                                <i class="fas fa-file-medical-alt text-info"></i>
                                Dokumen Rekam Medis Elektronik
                            </h5>

                            <span id="ermTotalBadge" class="badge badge-info">
                                0 Dokumen
                            </span>

                        </div>

                        <div id="ermLoading" class="text-center py-4 d-none">
                            <div class="spinner-border text-info"></div>
                            <div class="mt-2 text-muted">
                                Memuat dokumen...
                            </div>
                        </div>

                        <div id="ermError" class="alert alert-danger d-none"></div>

                        <div id="ermDocumentList" class="row"></div>

                    </div>

                    {{-- RIWAYAT SOAP --}}
                    <div class="d-flex justify-content-between
                    align-items-center mb-3">

                        <h5 class="mb-0" id="soapSectionTitle">Riwayat SOAP / CPPT</h5>

                        <span class="badge badge-primary">
                            Catatan PPA
                        </span>

                    </div>

                    <div id="soapLoading" class="text-center py-4 d-none">
                        <div class="spinner-border text-primary"></div>
                        <div class="mt-2 text-muted">
                            Memuat riwayat SOAP...
                        </div>
                    </div>

                    <div id="soapError" class="alert alert-danger d-none"></div>

                    <div id="soapTimeline"></div>

                    {{-- PAGINATION SOAP DI TENGAH --}}
                    <div class="d-flex flex-column align-items-center mt-4 mb-4">

                        <small id="soapPaginationInfo" class="text-muted mb-2 text-center">
                        </small>

                        <div id="soapPagination" class="btn-group"></div>

                    </div>

                    <hr class="my-4">

                    {{-- DETAIL DOCUMENT VIEWER --}}

                    <div id="ermDetailView" class="d-none">

                        <div class="erm-viewer-toolbar">

                            <button type="button" id="btnBackErmList" class="btn btn-sm btn-outline-primary">

                                <i class="fas fa-arrow-left"></i>
                                Kembali ke Daftar

                            </button>

                            <span id="ermDetailKindBadge" class="badge badge-info">
                                Dokumen ERM
                            </span>

                        </div>


                        <div id="ermDetailLoading" class="text-center py-4 d-none">

                            <div class="spinner-border text-primary"></div>

                            <div class="mt-2 text-muted">
                                Membuka dokumen...
                            </div>

                        </div>


                        <div id="ermDetailError" class="alert alert-danger d-none"></div>


                        <div id="ermDetailContainer" class="erm-document-viewer d-none">

                            <div class="erm-document-viewer-header">

                                <h5 id="ermDetailTitle" class="mb-1">
                                    Detail Dokumen
                                </h5>

                                <small class="text-muted">
                                    Informasi dan isi Rekam Medis Elektronik
                                </small>

                            </div>


                            {{-- AREA SCROLL KHUSUS DOKUMEN --}}
                            <div id="ermDocumentContentScroll" class="erm-document-content-scroll">


                                {{-- INFORMASI DOKUMEN --}}
                                <div class="erm-detail-section">

                                    <h6 class="erm-detail-section-title">
                                        <i class="fas fa-info-circle text-primary"></i>
                                        Informasi Dokumen
                                    </h6>

                                    <div id="ermDetailMeta" class="row"></div>

                                </div>


                                <hr>


                                {{-- ISI DOKUMEN --}}
                                <div class="erm-detail-section">

                                    <h6 class="erm-detail-section-title">
                                        <i class="fas fa-file-medical text-primary"></i>
                                        Isi Dokumen
                                    </h6>

                                    <div id="ermReadableContent"></div>

                                </div>


                                {{-- HASIL LABORATORIUM --}}
                                <div id="ermLabSection" class="erm-detail-section">

                                    <hr>

                                    <h6 class="erm-detail-section-title">
                                        <i class="fas fa-flask text-info"></i>
                                        Hasil Laboratorium
                                    </h6>

                                    <div id="ermLabReadableContent"></div>

                                </div>

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

                startDate: moment(),
                endDate: moment(),

                linkedCalendars: false,

                // dropdown bulan + tahun saat pilih manual
                showDropdowns: true,

                locale: {
                    format: 'YYYY-MM-DD',
                    separator: ' - ',
                    applyLabel: 'Terapkan',
                    cancelLabel: 'Batal',
                    customRangeLabel: 'Pilih Manual'
                },

                ranges: {

                    'Hari Ini': [
                        moment(),
                        moment()
                    ],

                    'Bulan Ini': [
                        moment().startOf('month'),
                        moment().endOf('month')
                    ],

                    'Bulan Lalu': [
                        moment().subtract(1, 'month').startOf('month'),
                        moment().subtract(1, 'month').endOf('month')
                    ],

                    '3 Bulan Terakhir': [
                        moment().subtract(2, 'month').startOf('month'),
                        moment().endOf('month')
                    ],

                    '6 Bulan Terakhir': [
                        moment().subtract(5, 'month').startOf('month'),
                        moment().endOf('month')
                    ],

                    'Tahun Ini': [
                        moment().startOf('year'),
                        moment().endOf('year')
                    ],

                    'Tahun Lalu': [
                        moment().subtract(1, 'year').startOf('year'),
                        moment().subtract(1, 'year').endOf('year')
                    ]
                }

            });
            // Select2 untuk Ruangan dan Dokter
            $('.select2').select2({
                placeholder: "Cari poli...",
                allowClear: true,
                width: '100%'
            });

            // Select2 multi-select untuk Jenis Pasien
            $('#jenis_pasien').select2({
                placeholder: 'Pilih Jenis Pasien',
                width: '100%',
                closeOnSelect: false
            });

            /*
            |--------------------------------------------------------------------------
            | DATATABLE
            |--------------------------------------------------------------------------
            */

            let appliedPatientSearch = '';

            let table = $('#datatable').DataTable({

                processing: true,
                serverSide: true,

                // Penting: scrollbar hanya dari wrapper luar
                responsive: false,
                scrollX: false,
                scrollCollapse: false,
                autoWidth: false,

                dom: 'lrtip',
                searching: false,

                ajax: {
                    url: "{{ route('getDataPoli') }}",

                    data: function(d) {

                        const range = $('#tanggal_range').val();

                        if (range) {
                            const tanggal = range.split(' - ');

                            d.start_date = tanggal[0] || '';
                            d.end_date = tanggal[1] || '';
                        }

                        d.jenis_kunjungan =
                            $('#jenis_kunjungan').val() || 'all';

                        d.jenis_pasien =
                            $('#jenis_pasien').val() || [];

                        d.ruangan = $('#ruangan').val() || '';
                        d.dokter = $('#dokter').val() || '';

                        d.search_pasien = appliedPatientSearch;
                    }
                },

                columnDefs: [{
                        targets: 0,
                        width: '55px'
                    },
                    {
                        targets: 1,
                        width: '110px'
                    },
                    {
                        targets: 2,
                        width: '145px'
                    },
                    {
                        targets: 3,
                        width: '145px'
                    },
                    {
                        targets: 4,
                        width: '155px'
                    },
                    {
                        targets: 5,
                        width: '145px'
                    },
                    {
                        targets: 6,
                        width: '220px'
                    },
                    {
                        targets: 7,
                        width: '190px'
                    },
                    {
                        targets: 8,
                        width: '105px'
                    },
                    {
                        targets: 9,
                        width: '200px'
                    },
                    {
                        targets: 10,
                        width: '150px'
                    },
                    {
                        targets: 11,
                        width: '170px'
                    },
                    {
                        targets: 12,
                        width: '110px'
                    },
                    {
                        targets: 13,
                        width: '280px'
                    },
                    {
                        targets: 14,
                        width: '135px'
                    }
                ],

                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'registration_id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {

                            if (!data) return '-';
                            if (type !== 'display') return data;

                            const $button = $('<button>', {
                                type: 'button',
                                class: 'btn btn-sm btn-info btn-detail-soap'
                            });

                            $button.attr('data-reg-id', data);

                            $button.html(
                                '<i class="fas fa-file-medical"></i> Detail'
                            );

                            return $button.prop('outerHTML');
                        }
                    },
                    {
                        data: 'checkout_date'
                    },
                    {
                        data: 'schedule_date'
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
                        data: 'nama_pasien'
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
                    },
                    {
                        data: 'biaya',
                        render: function(data, type, row) {

                            if (data == null) return 'Rp 0';

                            return 'Rp ' +
                                parseInt(data, 10).toLocaleString('id-ID');
                        }
                    }
                ],

                order: [
                    [2, 'desc']
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
                    },
                    emptyTable: "Tidak ada data kunjungan",
                    zeroRecords: "Data tidak ditemukan"
                }

            });

            // Sesuaikan ukuran kolom setelah tabel selesai dimuat
            table.on('init.dt draw.dt', function() {
                table.columns.adjust();
            });


            $('#search_pasien_btn').on('click', function() {

                appliedPatientSearch =
                    ($('#search_pasien').val() || '').trim();

                table.ajax.reload();
            });

            // Enter juga menjalankan pencarian
            $('#search_pasien').on('keydown', function(e) {

                if (e.key === 'Enter') {

                    e.preventDefault();

                    $('#search_pasien_btn').trigger('click');
                }
            });

            /*
            |--------------------------------------------------------------------------
            | DETAIL PASIEN - RIWAYAT SOAP
            |--------------------------------------------------------------------------
            */

            let activeSoapRegistrationId = null;

            const soapUrlTemplate = @json(route('kunjungan-poli.riwayat-soap', [
                    'registrationId' => '__REG_ID__',
                ]));

            // URL dasar halaman kunjungan
            const ermBaseUrl = @json(url('/kunjungan-poli'));

            // URL daftar dokumen RM Elektronik
            const ermUrlTemplate =
                ermBaseUrl + '/__REG_ID__/riwayat-erm';

            // URL detail dokumen RM Elektronik
            const ermDetailUrlTemplate =
                ermBaseUrl + '/__REG_ID__/riwayat-erm/__DOC_ID__';


            // Membuat field SOAP dengan aman
            function createSoapField(label, value) {

                const $field = $('<div>')
                    .addClass('col-md-6 mb-3');

                $('<div>')
                    .addClass('text-muted small font-weight-bold mb-1')
                    .text(label)
                    .appendTo($field);

                $('<div>')
                    .addClass('soap-value')
                    .text(value || '—')
                    .appendTo($field);

                return $field;
            }


            // Menampilkan identitas pasien
            function renderSoapPatient(pasien) {

                const $container = $('#soapPatientIdentity').empty();

                const $row = $('<div>').addClass('row');

                const items = [
                    ['Nama Pasien', pasien.nama_pasien],
                    ['Nomor Rekam Medis', pasien.nrm],
                    ['Nomor Registrasi', pasien.no_registrasi],
                    ['Tanggal Registrasi', pasien.reg_date]
                ];

                items.forEach(function(item, index) {

                    const $field = $('<div>')
                        .addClass('col-md-6 mb-2');

                    $('<div>')
                        .addClass('text-muted small')
                        .text(item[0])
                        .appendTo($field);

                    $('<strong>')
                        .text(item[1] || '—')
                        .appendTo($field);

                    $row.append($field);
                });

                $container.append($row);
            }


            // Menampilkan satu catatan SOAP
            function renderSoapItems(items) {

                const $container = $('#soapTimeline').empty();

                if (!items.length) {

                    $container.html(`
            <div class="alert alert-info">
                Belum terdapat catatan SOAP pada rangkaian
                registrasi pasien ini.
            </div>
        `);

                    return;
                }

                items.forEach(function(item, index) {

                    const isMain = Number(item.depth) === 0;

                    const verificationValue = item.dpjp_verified_status;

                    const verified = Number(verificationValue) === 1;

                    const unknown =
                        verificationValue === null ||
                        verificationValue === undefined ||
                        verificationValue === '';

                    const $card = $('<div>')
                        .addClass('card shadow-sm mb-3');

                    if (index === 0) {
                        $card.attr('id', 'soapFirstItem');
                    }

                    const $body = $('<div>')
                        .addClass('card-body');

                    // Header catatan
                    const $header = $('<div>')
                        .addClass('d-flex justify-content-between flex-wrap mb-3');

                    const $heading = $('<div>');

                    $('<h6>')
                        .addClass('font-weight-bold mb-1')
                        .text(item.tanggal + ' ' + (item.jam || ''))
                        .appendTo($heading);

                    $('<small>')
                        .addClass('text-muted')
                        .text(
                            'Registrasi: ' + (item.no_registrasi || '—') +
                            ' | ' + (isMain ? 'UTAMA' : 'TRANSFER') +
                            ' | ' + (item.ruangan || '—')
                        )
                        .appendTo($heading);

                    const badgeClass = verified ?
                        'is-verified' :
                        unknown ?
                        'is-unknown' :
                        'is-unverified';

                    const badgeText = verified ?
                        'Terverifikasi DPJP' :
                        unknown ?
                        'Status Verifikasi Tidak Tersedia' :
                        'Belum Terverifikasi DPJP';

                    const $badge = $('<span>')
                        .addClass('soap-verification-badge')
                        .addClass(badgeClass)
                        .text(badgeText);

                    $header.append($heading, $badge);

                    $body.append($header);

                    // Profesi dan PPA
                    const $profession = $('<div>')
                        .addClass('mb-3');

                    $('<strong>')
                        .text('Profesi: ')
                        .appendTo($profession);

                    $profession.append(
                        $('<span>').text(item.profesi || '—')
                    );

                    $profession.append(' | ');

                    $('<strong>')
                        .text('PPA: ')
                        .appendTo($profession);

                    $profession.append(
                        $('<span>').text(item.ppa || '—')
                    );

                    $body.append($profession);

                    // Isi SOAP
                    const $row = $('<div>').addClass('row');

                    [
                        ['Subjective', item.subjective],
                        ['Objective', item.objective],
                        ['Assessment', item.assessment],
                        ['Planning', item.planning],
                        ['Instruction', item.instruction],
                        ['Instruction Obat', item.instruction_obat]
                    ].forEach(function(field) {

                        $row.append(
                            createSoapField(field[0], field[1])
                        );

                    });

                    $body.append($row);
                    $card.append($body);
                    $container.append($card);

                });
            }


            // Membuat tombol pagination
            function renderSoapPagination(meta) {

                $('#soapPaginationInfo').text(
                    'Menampilkan ' +
                    (meta.from || 0) + '–' +
                    (meta.to || 0) +
                    ' dari ' + meta.total + ' catatan SOAP'
                );

                const $pagination = $('#soapPagination').empty();

                const $prev = $('<button>')
                    .attr('type', 'button')
                    .addClass('btn btn-outline-primary btn-sm')
                    .text('‹ Sebelumnya')
                    .prop('disabled', meta.current_page <= 1)
                    .on('click', function() {
                        loadRiwayatSoap(meta.current_page - 1, true);
                    });

                const $next = $('<button>')
                    .attr('type', 'button')
                    .addClass('btn btn-outline-primary btn-sm')
                    .text('Berikutnya ›')
                    .prop('disabled', meta.current_page >= meta.last_page)
                    .on('click', function() {
                        loadRiwayatSoap(meta.current_page + 1, true);
                    });

                $pagination.append($prev, $next);
            }


            // AJAX mengambil SOAP
            function loadRiwayatSoap(page = 1, autoScroll = false) {

                if (!activeSoapRegistrationId) {
                    return;
                }

                $('#soapLoading').removeClass('d-none');
                $('#soapError').addClass('d-none').text('');
                $('#soapTimeline').empty();
                $('#soapPagination').empty();

                const url = soapUrlTemplate.replace(
                    '__REG_ID__',
                    encodeURIComponent(activeSoapRegistrationId)
                );

                $.ajax({

                    url: url,
                    type: 'GET',

                    data: {
                        page: page
                    },

                    success: function(response) {

                        $('#soapLoading').addClass('d-none');

                        // Tampilkan data SOAP
                        renderSoapPatient(response.pasien);
                        renderSoapItems(response.data);
                        renderSoapPagination(response.meta);

                        // AUTO SCROLL KE CATATAN PERTAMA HALAMAN BARU
                        if (autoScroll) {

                            const $modalBody = $('#modalRiwayatSoap .modal-body');
                            const $target = $('#soapFirstItem');

                            if ($target.length) {

                                const targetPosition =
                                    $modalBody.scrollTop() +
                                    $target.offset().top -
                                    $modalBody.offset().top -
                                    15;

                                $modalBody.stop(true).animate({
                                    scrollTop: Math.max(0, targetPosition)
                                }, 350);

                            } else {

                                const $title = $('#soapSectionTitle');

                                const titlePosition =
                                    $modalBody.scrollTop() +
                                    $title.offset().top -
                                    $modalBody.offset().top -
                                    15;

                                $modalBody.stop(true).animate({
                                    scrollTop: Math.max(0, titlePosition)
                                }, 350);

                            }
                        }

                    },

                    error: function(xhr) {

                        $('#soapLoading').addClass('d-none');

                        const message = xhr.responseJSON?.message ||
                            'Gagal mengambil riwayat SOAP pasien.';

                        $('#soapError')
                            .removeClass('d-none')
                            .text(message);

                    }

                });
            }

            // Format JSON agar mudah dibaca
            // Mengubah JSON string menjadi object
            function parseErmData(value) {

                let result = value;

                for (let i = 0; i < 3; i++) {

                    if (typeof result !== 'string') {
                        break;
                    }

                    try {
                        result = JSON.parse(result);
                    } catch (error) {
                        break;
                    }
                }

                return result;
            }


            // Nama field teknis yang tidak perlu ditampilkan
            const ermHiddenFields = [
                'mnCurrent',
                'id',
                'erm_id',
                'reg_id',
                'patient_id',
                'formNumb',
                'section_id',
                'dokter_id',
                'role',
                'path',
                'surkon_id',
                'invparent_reg',
                '_token'
            ];


            // Mengubah nama field menjadi label yang lebih mudah dibaca
            function ermFieldLabel(key) {

                const labels = {
                    namaPasien: 'Nama Pasien',
                    nrm: 'Nomor Rekam Medis',
                    jadwalPraktek: 'Jadwal Praktik',
                    tanggalKontrol: 'Tanggal Kontrol',
                    noSurat: 'Nomor Surat',
                    diagnosis: 'Diagnosis',
                    diagnosa: 'Diagnosis',
                    dokter: 'Dokter',
                    namaDokter: 'Nama Dokter',
                    namaPoli: 'Poli',
                    poli: 'Poli',
                    tanggal: 'Tanggal',
                    umur: 'Umur',
                    tekananDarah: 'Tekanan Darah',
                    instruksi: 'Instruksi',
                    tindakan: 'Tindakan',
                    terapi: 'Terapi',
                    catatan: 'Catatan',
                    obat: 'Obat',
                    subjective: 'Subjective',
                    objective: 'Objective',
                    assessment: 'Assessment',
                    planning: 'Planning',

                    name: 'Nama Pasien',
                    gender: 'Jenis Kelamin',
                    birth_date: 'Tanggal Lahir',
                    age: 'Usia',
                    section: 'Poli / Ruangan',
                    class: 'Kelas Perawatan',
                    reg_date: 'Tanggal Registrasi',
                    checkout_date: 'Tanggal Keluar',
                    bpjs_sep: 'Nomor SEP',
                    bpjs_numb: 'Nomor Peserta BPJS',
                    hak_kelas: 'Hak Kelas'
                };

                if (labels[key]) {
                    return labels[key];
                }

                return key
                    .replace(/([A-Z])/g, ' $1')
                    .replace(/_/g, ' ')
                    .replace(/\b\w/g, function(char) {
                        return char.toUpperCase();
                    });
            }


            // Mengubah JSON menjadi tampilan dokumen yang lebih ringkas

            function renderErmObject($container, object) {

                if (!object || typeof object !== 'object') {
                    return;
                }

                const $simpleRow = $('<div>').addClass('row');
                const $complexContainer = $('<div>').addClass('mt-2');

                Object.entries(object).forEach(function(entry) {

                    const key = entry[0];
                    const value = entry[1];

                    if (ermHiddenFields.includes(key)) {
                        return;
                    }

                    if (
                        value === null ||
                        value === undefined ||
                        value === ''
                    ) {
                        return;
                    }

                    if (
                        Array.isArray(value) &&
                        value.length === 0
                    ) {
                        return;
                    }

                    if (
                        typeof value === 'object' &&
                        !Array.isArray(value) &&
                        Object.keys(value).length === 0
                    ) {
                        return;
                    }

                    // Deteksi gambar Base64 (PNG, JPG, JPEG, GIF, WEBP)
                    const isImageData =
                        typeof value === 'string' &&
                        /^data:image\/(png|jpe?g|gif|webp);base64,/i.test(value.trim());

                    if (isImageData) {

                        const $field = $('<div>')
                            .addClass('col-12 col-md-6 mb-3');

                        const $box = $('<div>')
                            .addClass('erm-field-box');

                        $('<div>')
                            .addClass('erm-field-label')
                            .text(ermFieldLabel(key))
                            .appendTo($box);

                        const $img = $('<img>')
                            .attr('src', value.trim())
                            .attr('alt', ermFieldLabel(key))
                            .addClass('erm-image-preview');

                        $img.on('error', function() {
                            $(this).replaceWith(
                                $('<div>')
                                .addClass('text-danger small')
                                .text('Gambar tidak dapat ditampilkan.')
                            );
                        });

                        $box.append($img);
                        $field.append($box);
                        $simpleRow.append($field);

                        return;
                    }

                    // Data sederhana: langsung ditampilkan dalam grid
                    if (
                        typeof value !== 'object'
                    ) {

                        const $field = $('<div>')
                            .addClass('col-12 col-md-6 mb-3');

                        const $box = $('<div>')
                            .addClass('erm-field-box');

                        $('<div>')
                            .addClass('erm-field-label')
                            .text(ermFieldLabel(key))
                            .appendTo($box);

                        $('<div>')
                            .addClass('erm-field-value')
                            .text(
                                typeof value === 'boolean' ?
                                (value ? 'Ya' : 'Tidak') :
                                String(value)
                            )
                            .appendTo($box);

                        $field.append($box);
                        $simpleRow.append($field);

                        return;
                    }

                    // Data bertingkat: gunakan accordion
                    const $details = $('<details>')
                        .addClass('erm-collapsible');

                    const $summary = $('<summary>')
                        .text(ermFieldLabel(key));

                    const $content = $('<div>')
                        .addClass('erm-collapsible-content');

                    if (Array.isArray(value)) {

                        // Daftar berisi object
                        if (
                            value.some(item =>
                                item !== null &&
                                typeof item === 'object'
                            )
                        ) {

                            value.forEach(function(item) {

                                const $item = $('<div>')
                                    .addClass('erm-nested-box');

                                if (
                                    item &&
                                    typeof item === 'object' &&
                                    !Array.isArray(item)
                                ) {
                                    renderErmObject($item, item);
                                } else {
                                    $('<div>')
                                        .text(String(item ?? '—'))
                                        .appendTo($item);
                                }

                                $content.append($item);

                            });

                        } else {

                            // Daftar nilai biasa
                            const $list = $('<ul>')
                                .addClass('mb-0 pl-3');

                            value.forEach(function(item) {

                                $('<li>')
                                    .text(String(item))
                                    .appendTo($list);

                            });

                            $content.append($list);

                        }

                    } else {

                        // Object bertingkat
                        renderErmObject($content, value);

                    }

                    $details.append($summary, $content);
                    $complexContainer.append($details);

                });

                $container.append($simpleRow, $complexContainer);

            }


            // Menampilkan isi dokumen
            function renderErmReadable(selector, value, emptyMessage) {

                const $container = $(selector).empty();

                const parsed = parseErmData(value);

                if (
                    parsed === null ||
                    parsed === undefined ||
                    parsed === ''
                ) {
                    $container.html(
                        $('<div>')
                        .addClass('erm-empty')
                        .text(emptyMessage)
                    );
                    return;
                }

                if (
                    Array.isArray(parsed) &&
                    parsed.length === 0
                ) {
                    $container.html(
                        $('<div>')
                        .addClass('erm-empty')
                        .text(emptyMessage)
                    );
                    return;
                }

                if (Array.isArray(parsed)) {

                    parsed.forEach(function(item) {

                        const $item = $('<div>')
                            .addClass('erm-nested-box');

                        if (item && typeof item === 'object') {
                            renderErmObject($item, item);
                        } else {
                            $item.text(String(item));
                        }

                        $container.append($item);

                    });

                } else if (typeof parsed === 'object') {

                    renderErmObject($container, parsed);

                } else {

                    $container.html(
                        $('<div>')
                        .addClass('erm-field-box')
                        .text(String(parsed))
                    );

                }

                if (!$container.children().length) {
                    $container.html(
                        $('<div>')
                        .addClass('erm-empty')
                        .text('Belum ada informasi yang dapat ditampilkan.')
                    );
                }

            }


            // Menampilkan daftar dokumen ERM
            function renderErmDocuments(documents) {

                const $container = $('#ermDocumentList').empty();

                if (!documents || documents.length === 0) {

                    $container.html(`
            <div class="col-12">
                <div class="erm-empty text-center">
                    Belum ada dokumen RM Elektronik.
                </div>
            </div>
        `);

                    return;
                }

                documents.forEach(function(item) {

                    const $column = $('<div>')
                        .addClass('col-12 col-lg-6 mb-3');

                    const $card = $('<div>')
                        .addClass('card erm-document-card mb-0');

                    const $body = $('<div>')
                        .addClass('card-body');

                    $('<h6>')
                        .addClass('font-weight-bold')
                        .text(item.title || 'Dokumen RM')
                        .appendTo($body);

                    const info = [
                        item.kategori || item.type,
                        item.created_at,
                        item.ruangan,
                        item.nama_dokter
                    ].filter(Boolean).join(' | ');

                    $('<div>')
                        .addClass('small text-muted mb-3')
                        .text(info || 'Informasi dokumen tidak tersedia')
                        .appendTo($body);

                    const $button = $('<button>')
                        .attr('type', 'button')
                        .attr('data-document-id', item.document_id)
                        .addClass('btn btn-sm btn-outline-primary btn-lihat-erm')
                        .html('<i class="fas fa-eye"></i> Lihat Dokumen');

                    $body.append($button);
                    $card.append($body);
                    $column.append($card);

                    $container.append($column);

                });

            }

            // AJAX daftar dokumen ERM
            function loadRiwayatErm() {

                if (!activeSoapRegistrationId) {
                    return;
                }

                // Pastikan yang tampil adalah daftar dokumen
                $('#ermListView').removeClass('d-none');
                $('#ermDetailView').addClass('d-none');

                $('#ermDetailContainer').addClass('d-none');
                $('#ermDetailError').addClass('d-none').empty();
                $('#ermDetailLoading').addClass('d-none');

                $('#ermLoading').removeClass('d-none');
                $('#ermError').addClass('d-none').empty();
                $('#ermDocumentList').empty();

                $('#ermDetailContainer').addClass('d-none');
                $('#ermDetailError').addClass('d-none');

                const url = ermUrlTemplate.replace(
                    '__REG_ID__',
                    encodeURIComponent(activeSoapRegistrationId)
                );

                $.ajax({

                    url: url,
                    type: 'GET',

                    success: function(response) {

                        $('#ermLoading').addClass('d-none');

                        const documents = response.data || [];

                        renderErmDocuments(documents);

                        $('#ermTotalBadge').text(
                            documents.length + ' Dokumen'
                        );

                    },

                    error: function(xhr) {

                        $('#ermLoading').addClass('d-none');

                        const message = xhr.responseJSON?.message ||
                            'Gagal mengambil dokumen RM Elektronik.';

                        $('#ermError')
                            .removeClass('d-none')
                            .text(message);

                    }

                });

            }

            // Membuat informasi detail dokumen
            function appendErmMeta(label, value) {

                const $field = $('<div>')
                    .addClass('col-md-6 mb-2');

                $('<div>')
                    .addClass('text-muted small')
                    .text(label)
                    .appendTo($field);

                $('<strong>')
                    .text(value || '—')
                    .appendTo($field);

                $('#ermDetailMeta').append($field);
            }


            // AJAX mengambil isi detail dokumen
            function loadDetailErm(documentId) {

                if (!activeSoapRegistrationId || !documentId) {
                    return;
                }

                $('#ermDetailLoading').removeClass('d-none');
                $('#ermDetailContainer').addClass('d-none');
                $('#ermDetailError').addClass('d-none').empty();

                let url = ermDetailUrlTemplate.replace(
                    '__REG_ID__',
                    encodeURIComponent(activeSoapRegistrationId)
                );

                url = url.replace(
                    '__DOC_ID__',
                    encodeURIComponent(documentId)
                );

                $.ajax({

                    url: url,
                    type: 'GET',

                    success: function(response) {

                        $('#ermDetailLoading').addClass('d-none');

                        const data = response.data || {};

                        const form = String(data.form || '').toLowerCase();

                        let jenisDokumen = data.kategori || data.type || 'Dokumen ERM';

                        // Identifikasi jenis formulir
                        if (form === 'resume_medis') {
                            jenisDokumen = 'RESUME MEDIS';
                        } else if (form === 'surat_kontrol') {
                            jenisDokumen = 'SURAT KONTROL';
                        }

                        $('#ermDetailKindBadge').text(jenisDokumen);

                        $('#ermDetailTitle').text(
                            data.title || jenisDokumen
                        );

                        // Informasi dokumen
                        $('#ermDetailMeta').empty();

                        appendErmMeta('Nomor Registrasi', data.no_registrasi);
                        appendErmMeta('Nomor Surat', data.nomor_surat);
                        appendErmMeta('Jenis Dokumen', data.type);
                        appendErmMeta('Kategori', data.kategori);
                        appendErmMeta('Form', data.form);
                        appendErmMeta('Ruangan', data.ruangan);
                        appendErmMeta('Dokter', data.nama_dokter);
                        appendErmMeta('Tanggal Dibuat', data.created_at);

                        // Isi dokumen
                        renderErmReadable(
                            '#ermReadableContent',
                            data.erm_json,
                            'Isi dokumen belum tersedia.'
                        );

                        // Periksa ketersediaan hasil laboratorium
                        const labData = parseErmData(data.json_hasil_lab);

                        const hasLab = Array.isArray(labData) ?
                            labData.length > 0 :
                            (
                                labData &&
                                typeof labData === 'object' ?
                                Object.keys(labData).length > 0 :
                                !!labData
                            );

                        $('#ermLabSection').toggle(hasLab);

                        if (hasLab) {

                            renderErmReadable(
                                '#ermLabReadableContent',
                                labData,
                                'Tidak ada data hasil laboratorium.'
                            );

                        } else {

                            $('#ermLabReadableContent').empty();

                        }

                        // Tampilkan panel dokumen
                        $('#ermDetailContainer').removeClass('d-none');

                        // Mulai dari atas isi dokumen
                        $('#ermDocumentContentScroll').scrollTop(0);

                        // Arahkan area scroll modal ke detail dokumen
                        requestAnimationFrame(function() {

                            const $modalBody = $('#modalRiwayatSoap .modal-body');
                            const $target = $('#ermDetailView');

                            const targetPosition =
                                $modalBody.scrollTop() +
                                $target.offset().top -
                                $modalBody.offset().top -
                                15;

                            $modalBody.stop(true).animate({
                                scrollTop: Math.max(0, targetPosition)
                            }, 400);

                        });

                    },
                    error: function(xhr) {

                        $('#ermDetailLoading').addClass('d-none');

                        const message = xhr.responseJSON?.message ||
                            'Gagal mengambil detail dokumen RM Elektronik.';

                        $('#ermDetailError')
                            .removeClass('d-none')
                            .text(message);

                    }

                });

            }


            // Buka Document Viewer
            // Pilih dokumen ERM
            $(document).on('click', '.btn-lihat-erm', function() {

                const documentId = $(this).attr('data-document-id');

                // Daftar ERM tetap terlihat di atas
                $('#ermListView').removeClass('d-none');

                // Tampilkan viewer di bawah CPPT
                $('#ermDetailView').removeClass('d-none');

                $('#ermDetailContainer').addClass('d-none');
                $('#ermDetailError').addClass('d-none').empty();
                $('#ermDetailLoading').removeClass('d-none');

                $('#ermDocumentContentScroll').scrollTop(0);

                loadDetailErm(documentId);

            });

            // Tutup detail dan kembali ke daftar dokumen
            $(document).on('click', '#btnBackErmList', function() {

                $('#ermDetailView').addClass('d-none');
                $('#ermDetailContainer').addClass('d-none');

                $('#ermReadableContent').empty();
                $('#ermLabReadableContent').empty();

                // Kembalikan scroll ke daftar ERM di bagian atas
                const $modalBody = $('#modalRiwayatSoap .modal-body');
                const $target = $('#ermListView');

                const targetPosition =
                    $modalBody.scrollTop() +
                    $target.offset().top -
                    $modalBody.offset().top -
                    15;

                $modalBody.stop(true).animate({
                    scrollTop: Math.max(0, targetPosition)
                }, 400);

            });


            // Klik tombol Detail
            $(document).on('click', '.btn-detail-soap', function() {

                activeSoapRegistrationId = $(this).attr('data-reg-id');

                $('#modalRiwayatSoap').modal('show');

                $('#soapPatientIdentity').text('Memuat identitas pasien...');
                $('#soapTimeline').empty();
                $('#soapPaginationInfo').text('');

                loadRiwayatSoap(1);

                // Tambahan: mengambil daftar RM Elektronik
                loadRiwayatErm();

            });
            $(document).on('click', '.btn-close-soap', function() {
                $('#modalRiwayatSoap').modal('hide');
            });


            // Reset saat modal ditutup
            $('#modalRiwayatSoap').on('hidden.bs.modal', function() {

                activeSoapRegistrationId = null;

                $('#soapPatientIdentity').empty();
                $('#soapTimeline').empty();
                $('#soapPagination').empty();
                $('#soapPaginationInfo').empty();
                $('#soapError').addClass('d-none').empty();

                $('#ermDocumentList').empty();
                $('#ermTotalBadge').text('0 Dokumen');

                $('#ermDetailContainer').addClass('d-none');
                $('#ermDetailError').addClass('d-none').empty();
                $('#ermError').addClass('d-none').empty();
                $('#ermLoading').addClass('d-none');
                $('#ermDetailLoading').addClass('d-none');

                $('#ermListView').removeClass('d-none');
                $('#ermDetailView').addClass('d-none');

                $('#ermDetailContainer').addClass('d-none');
                $('#ermReadableContent').empty();
                $('#ermLabReadableContent').empty();

                $('#ermDocumentContentScroll').scrollTop(0);

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

            $('#reset').on('click', function() {
                $('#jenis_kunjungan').val('all');

                $('#jenis_pasien').val(null).trigger('change');
                $('#ruangan').val('').trigger('change');
                $('#dokter').val('').trigger('change');
                $('#search_pasien').val('');
                appliedPatientSearch = '';

                const datePicker = $('#tanggal_range').data('daterangepicker');
                datePicker.setStartDate(moment());
                datePicker.setEndDate(moment());

                table.ajax.reload();
            });


            /*
            |--------------------------------------------------------------------------
            | EXPORT EXCEL
            |--------------------------------------------------------------------------
            */
            $('#export_excel').on('click', function() {
                const range = $('#tanggal_range').val() || '';
                const jenisPasien = $('#jenis_pasien').val() || [];
                const ruangan = $('#ruangan').val() || '';
                const dokter = $('#dokter').val() || '';
                const jenisKunjungan = $('#jenis_kunjungan').val() || 'all';

                let start = '';
                let end = '';

                if (range) {
                    const tanggal = range.split(' - ');
                    start = tanggal[0] || '';
                    end = tanggal[1] || '';
                }

                const params = new URLSearchParams();
                params.append('start_date', start);
                params.append('end_date', end);
                params.append('jenis_kunjungan', jenisKunjungan);
                params.append('ruangan', ruangan);
                params.append('dokter', dokter);

                jenisPasien.forEach(function(value) {
                    if (value) {
                        params.append('jenis_pasien[]', value);
                    }
                });

                const exportUrl = "{{ route('export.excel') }}" + '?' + params.toString();
                window.open(exportUrl, '_blank');
            });

        });
    </script>
@endpush
