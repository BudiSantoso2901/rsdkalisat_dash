@extends('layouts.layouts')

@section('content')
    @php
        $total = ($rawatJalan ?? 0) + ($rawatInap ?? 0) + ($igd ?? 0);
        $totalPasien = ($pasienBaru ?? 0) + ($pasienLama ?? 0);

        $pct = fn($value, $base) => $base > 0 ? round(($value / $base) * 100, 1) : 0;
    @endphp

    <style>
        .dash {
            --blue: #4f9cf9;
            --green: #24c875;
            --red: #ff6464;
            --text: #273444;
            --muted: #8290a3;
            --border: #e8edf3;
            padding-bottom: 30px;
        }

        .dash-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(31, 45, 61, .04);
        }

        /* HEADER */
        .dash-header {
            padding: 22px 24px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .dash-header h4 {
            margin: 0 0 4px;
            font-size: 22px;
            font-weight: 700;
            color: var(--text);
        }

        .dash-header p,
        .subtitle {
            margin: 0;
            color: var(--muted);
            font-size: 11px;
        }

        .period-form,
        .schedule-filter {
            display: flex;
            align-items: end;
            gap: 8px;
        }

        .period-field {
            min-width: 140px;
        }

        .period-field label {
            display: block;
            margin-bottom: 4px;
            color: var(--muted);
            font-size: 11px;
        }

        .period-form .form-control,
        .schedule-filter .form-control {
            height: 38px;
            border-radius: 8px;
            border-color: var(--border);
            font-size: 12px;
        }

        .period-form .btn,
        .schedule-filter .btn {
            height: 38px;
            border-radius: 8px;
            font-size: 12px;
        }

        /* KPI */
        .metric {
            height: 100%;
            padding: 18px;
            transition: .2s;
        }

        .metric:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(31, 45, 61, .07);
        }

        .metric-main {
            display: flex;
            justify-content: space-between;
            align-items: start;
        }

        .metric-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--muted);
        }

        .metric-value {
            margin-top: 4px;
            font-size: 27px;
            font-weight: 700;
            color: var(--text);
        }

        .metric-info {
            margin-top: 10px;
            color: var(--muted);
            font-size: 11px;
        }

        .metric-icon {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 11px;
            font-size: 20px;
        }

        .i-primary {
            color: var(--blue);
            background: #eef6ff
        }

        .i-blue {
            color: #3498db;
            background: #eef7fd
        }

        .i-green {
            color: var(--green);
            background: #ebfbf3
        }

        .i-red {
            color: var(--red);
            background: #fff0f0
        }

        /* SECTION */
        .section-body {
            padding: 22px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            gap: 12px;
            margin-bottom: 16px;
        }

        .section-title {
            margin: 0;
            color: var(--text);
            font-size: 15px;
            font-weight: 700;
        }

        .section-badge {
            padding: 5px 10px;
            border-radius: 20px;
            background: #eef6ff;
            color: var(--blue);
            font-size: 10px;
            font-weight: 600;
            white-space: nowrap;
        }

        /* CHART */
        .poli-chart {
            position: relative;
            width: 100%;
            height: 470px;
        }

        .doctor-chart {
            position: relative;
            height: 430px;
        }

        .patient-panel {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .patient-chart {
            position: relative;
            height: 300px;
            margin-top: 5px;
        }

        .patient-list {
            margin-top: auto;
            display: grid;
            gap: 8px;
        }

        .patient-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 9px;
            font-size: 12px;
        }

        .patient-left {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #536273;
        }

        .dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
        }

        .dot-new {
            background: var(--green)
        }

        .dot-old {
            background: #a7dfbf
        }

        .patient-item strong,
        .patient-item small {
            display: block;
            text-align: right;
        }

        .patient-item strong {
            color: var(--text)
        }

        .patient-item small {
            color: var(--muted)
        }

        /* JADWAL */
        .schedule-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 18px;
        }

        .schedule-filter .form-control {
            width: 145px;
        }

        .schedule-table {
            margin: 0;
        }

        .schedule-table th {
            padding: 12px;
            background: #f8fafc;
            color: #667587;
            font-size: 11px;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        .schedule-table td {
            padding: 14px 12px;
            color: #687789;
            font-size: 12px;
            border-color: #edf1f5;
            vertical-align: middle;
        }

        .doctor-name {
            color: #4c5a69;
            font-weight: 600;
        }

        .quota {
            width: 120px;
            height: 5px;
            overflow: hidden;
            background: #e6eaee;
            border-radius: 10px;
        }

        .quota .progress-bar {
            height: 100%;
        }

        .status {
            display: inline-block;
            padding: 5px 8px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 600;
            white-space: nowrap;
        }

        .available {
            color: #159455;
            background: #e8f9f0;
        }

        .warning-status {
            color: #a86b00;
            background: #fff3d5;
        }

        .full-status {
            color: #d94343;
            background: #ffeded;
        }

        /* PAGINATION */
        .schedule-pagination .pagination {
            margin: 0;
            gap: 4px;
        }

        .schedule-pagination .page-link {
            min-width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 7px !important;
            border: 1px solid var(--border);
            color: #687789;
            font-size: 12px;
            box-shadow: none;
        }

        .schedule-pagination .page-item.active .page-link {
            background: var(--blue);
            border-color: var(--blue);
            color: #fff;
        }

        .schedule-pagination .page-item.disabled .page-link {
            color: #b9c2cc;
            background: #f8fafc;
        }

        @media(max-width:991px) {
            .dash-header {
                align-items: stretch;
                flex-direction: column;
            }

            .period-form {
                width: 100%
            }

            .period-field {
                flex: 1
            }

            .patient-chart {
                height: 270px
            }
        }

        @media(max-width:767px) {
            .dash-header {
                padding: 17px
            }

            .period-form,
            .schedule-head {
                align-items: stretch;
                flex-direction: column;
            }

            .period-form .btn {
                width: 100%
            }

            .schedule-filter {
                width: 100%;
                flex-wrap: wrap;
            }

            .schedule-filter .form-control {
                flex: 1;
                width: auto;
            }

            .schedule-filter .btn {
                flex: 1 1 100%;
            }

            .section-body {
                padding: 17px
            }

            .poli-chart {
                height: 440px
            }

            .doctor-chart {
                height: 380px
            }

            .schedule-pagination {
                align-items: flex-start !important;
                flex-direction: column;
            }
        }
    </style>


    <div class="content">
        <div class="container-fluid dash">

            {{-- HEADER --}}
            <div class="dash-card dash-header">

                <div>
                    <h4>Dashboard Pelayanan</h4>

                    <p>
                        Ringkasan aktivitas pelayanan rumah sakit periode
                        {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }}
                        {{ $tahun }}
                    </p>
                </div>


                <form method="GET" action="{{ route('dashboard') }}" class="period-form">

                    @if (request('tanggal_mulai'))
                        <input type="hidden" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                    @endif

                    @if (request('tanggal_selesai'))
                        <input type="hidden" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">
                    @endif


                    <div class="period-field">
                        <label>Bulan</label>

                        <select name="bulan" class="form-control">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>

                                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>


                    <div class="period-field">
                        <label>Tahun</label>

                        <select name="tahun" class="form-control">
                            @for ($t = now()->year; $t >= now()->year - 20; $t--)
                                <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>

                                    {{ $t }}
                                </option>
                            @endfor
                        </select>
                    </div>


                    <button class="btn btn-primary">
                        <i class="bi bi-filter me-1"></i>
                        Tampilkan
                    </button>

                </form>

            </div>


            {{-- KPI --}}
            @php
                $metrics = [
                    ['Total Kunjungan', $total, 'Seluruh jenis pelayanan', 'bi-people', 'i-primary'],
                    [
                        'Rawat Jalan',
                        $rawatJalan ?? 0,
                        $pct($rawatJalan ?? 0, $total) . '% dari total kunjungan',
                        'bi-hospital',
                        'i-blue',
                    ],
                    [
                        'Rawat Inap',
                        $rawatInap ?? 0,
                        $pct($rawatInap ?? 0, $total) . '% dari total kunjungan',
                        'bi-building',
                        'i-green',
                    ],
                    [
                        'IGD & PONEK',
                        $igd ?? 0,
                        $pct($igd ?? 0, $total) . '% dari total kunjungan',
                        'bi-heart-pulse',
                        'i-red',
                    ],
                ];
            @endphp


            <div class="row g-3 mb-3">

                @foreach ($metrics as [$label, $value, $info, $icon, $class])
                    <div class="col-xl-3 col-md-6">
                        <div class="dash-card metric">

                            <div class="metric-main">

                                <div>
                                    <div class="metric-label">
                                        {{ $label }}
                                    </div>

                                    <div class="metric-value">
                                        {{ number_format($value) }}
                                    </div>
                                </div>

                                <div class="metric-icon {{ $class }}">
                                    <i class="bi {{ $icon }}"></i>
                                </div>

                            </div>

                            <div class="metric-info">
                                {{ $info }}
                            </div>

                        </div>
                    </div>
                @endforeach

            </div>


            {{-- POLI + PASIEN --}}
            <div class="row g-3 mb-3 align-items-stretch">

                {{-- KUNJUNGAN PER POLI --}}
                <div class="col-xl-8 d-flex">

                    <div class="dash-card w-100">
                        <div class="section-body">

                            <div class="section-header">

                                <div>
                                    <h5 class="section-title">
                                        Kunjungan Per Poli
                                    </h5>

                                    <div class="subtitle">
                                        Jumlah kunjungan masing-masing poli
                                    </div>
                                </div>

                                <span class="section-badge">
                                    {{ $kunjunganPerPoli->count() }} Poli
                                </span>

                            </div>


                            @if ($kunjunganPerPoli->count())
                                <div class="poli-chart">
                                    <canvas id="chartPoli"></canvas>
                                </div>
                            @else
                                <div class="text-center text-muted py-5">
                                    Tidak ada data kunjungan poli
                                </div>
                            @endif

                        </div>
                    </div>

                </div>


                {{-- PASIEN BARU & LAMA --}}
                <div class="col-xl-4 d-flex">

                    <div class="dash-card w-100">
                        <div class="section-body patient-panel">

                            <div class="section-header">

                                <div>
                                    <h5 class="section-title">
                                        Pasien Baru & Lama
                                    </h5>

                                    <div class="subtitle">
                                        Komposisi status pasien
                                    </div>
                                </div>

                                <span class="section-badge">
                                    {{ number_format($totalPasien) }}
                                </span>

                            </div>


                            @if ($totalPasien > 0)
                                <div class="patient-chart">
                                    <canvas id="chartPasien"></canvas>
                                </div>
                            @else
                                <div class="text-center text-muted py-5">
                                    Tidak ada data pasien
                                </div>
                            @endif


                            <div class="patient-list">

                                <div class="patient-item">

                                    <div class="patient-left">
                                        <span class="dot dot-new"></span>
                                        Pasien Baru
                                    </div>

                                    <div>
                                        <strong>
                                            {{ number_format($pasienBaru ?? 0) }}
                                        </strong>

                                        <small>
                                            {{ $pct($pasienBaru ?? 0, $totalPasien) }}%
                                        </small>
                                    </div>

                                </div>


                                <div class="patient-item">

                                    <div class="patient-left">
                                        <span class="dot dot-old"></span>
                                        Pasien Lama
                                    </div>

                                    <div>
                                        <strong>
                                            {{ number_format($pasienLama ?? 0) }}
                                        </strong>

                                        <small>
                                            {{ $pct($pasienLama ?? 0, $totalPasien) }}%
                                        </small>
                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                </div>

            </div>


            {{-- STATISTIK DOKTER --}}
            <div class="dash-card mb-3">

                <div class="section-body">

                    <div class="section-header">

                        <div>
                            <h5 class="section-title">
                                Statistik Kunjungan Dokter
                            </h5>

                            <div class="subtitle">
                                10 dokter dengan kunjungan terbanyak
                            </div>
                        </div>

                        <span class="section-badge">
                            Top 10
                        </span>

                    </div>


                    @if ($pxdokter->count())
                        <div class="doctor-chart">
                            <canvas id="chartDokter"></canvas>
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            Tidak ada data kunjungan dokter
                        </div>
                    @endif

                </div>

            </div>


            {{-- JADWAL DOKTER --}}
            <div class="dash-card">

                <div class="section-body">

                    <div class="schedule-head">

                        <div>

                            <h5 class="section-title">
                                Jadwal Dokter
                            </h5>

                            <div class="subtitle">
                                {{ $tanggalMulai->format('d M Y') }}
                                -
                                {{ $tanggalSelesai->format('d M Y') }}
                            </div>

                        </div>


                        <form method="GET" action="{{ route('dashboard') }}" class="schedule-filter">

                            <input type="hidden" name="bulan" value="{{ $bulan }}">

                            <input type="hidden" name="tahun" value="{{ $tahun }}">


                            <input type="date" name="tanggal_mulai"
                                value="{{ request('tanggal_mulai', $tanggalMulai->format('Y-m-d')) }}"
                                class="form-control">


                            <input type="date" name="tanggal_selesai"
                                value="{{ request('tanggal_selesai', $tanggalSelesai->format('Y-m-d')) }}"
                                class="form-control">


                            <button class="btn btn-primary">

                                <i class="bi bi-filter"></i>

                                Filter

                            </button>

                        </form>

                    </div>


                    <div id="jadwalContainer">

                        @include('Page.partials.jadwal-table', [
                            'jadwal' => $jadwal,
                        ])

                    </div>

                </div>

            </div>

        </div>
    </div>
@endsection


@push('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const poliLabels = @json($kunjunganPerPoli->pluck('nama_poli')->values());

        const poliData = @json($kunjunganPerPoli->pluck('total')->values());

        const poliKuota = @json($kunjunganPerPoli->pluck('total_kuota')->values());

        const dokterLabels = @json($pxdokter->take(10)->pluck('nama_dokter')->values());

        const dokterData = @json($pxdokter->take(10)->pluck('total_pasien')->values());

        const pasienBaru = {{ $pasienBaru ?? 0 }};
        const pasienLama = {{ $pasienLama ?? 0 }};

        const number = value =>
            new Intl.NumberFormat('id-ID').format(value);

        Chart.defaults.font.family =
            "'Inter','Segoe UI',Arial,sans-serif";

        Chart.defaults.color = '#7B8794';


        /* POLI */
        const poliEl = document.getElementById('chartPoli');

        const quotaLabelPlugin = {
            id: 'quotaLabelPlugin',

            afterDatasetsDraw(chart) {
                const {
                    ctx
                } = chart;
                const meta = chart.getDatasetMeta(0);

                ctx.save();
                ctx.font = '600 11px Inter, Segoe UI, Arial';
                ctx.fillStyle = '#536273';
                ctx.textBaseline = 'middle';

                meta.data.forEach((bar, index) => {
                    const kunjungan = Number(poliData[index] ?? 0);
                    const kuota = Number(poliKuota[index] ?? 0);

                    ctx.fillText(
                        `${number(kunjungan)} / ${number(kuota)}`,
                        bar.x + 8,
                        bar.y
                    );
                });

                ctx.restore();
            }
        };

        if (poliEl) {
            new Chart(poliEl, {
                type: 'bar',

                plugins: [quotaLabelPlugin],

                data: {
                    labels: poliLabels,

                    datasets: [{
                        data: poliData,
                        backgroundColor: '#20BFA9',
                        hoverBackgroundColor: '#159F90',
                        borderRadius: 6,
                        borderSkipped: false,
                        barThickness: 20,
                        maxBarThickness: 22
                    }]
                },

                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,

                    layout: {
                        padding: {
                            right: 100
                        }
                    },

                    interaction: {
                        mode: 'index',
                        axis: 'y',
                        intersect: false
                    },

                    plugins: {
                        legend: {
                            display: false
                        },

                        tooltip: {
                            mode: 'index',
                            axis: 'y',
                            intersect: false,
                            displayColors: false,
                            padding: 12,
                            backgroundColor: '#263442',

                            callbacks: {
                                title: items => items[0].label,

                                label: ctx => {
                                    const kuota =
                                        Number(poliKuota[ctx.dataIndex] ?? 0);

                                    return [
                                        'Kunjungan: ' + number(ctx.raw),
                                        'Kuota: ' + number(kuota)
                                    ];
                                }
                            }
                        }
                    },

                    scales: {
                        x: {
                            beginAtZero: true,

                            border: {
                                display: false
                            },

                            grid: {
                                color: '#EDF1F5'
                            },

                            ticks: {
                                callback: value => number(value)
                            }
                        },

                        y: {
                            border: {
                                display: false
                            },

                            grid: {
                                display: false
                            },

                            ticks: {
                                autoSkip: false,
                                padding: 8,
                                color: '#536273',

                                font: {
                                    size: 14
                                }
                            }
                        }
                    }
                }
            });
        }


        /* DOKTER */
        const dokterEl =
            document.getElementById('chartDokter');

        if (dokterEl) {

            new Chart(dokterEl, {

                type: 'bar',

                data: {
                    labels: dokterLabels,

                    datasets: [{
                        data: dokterData,
                        backgroundColor: '#24C875',
                        hoverBackgroundColor: '#19A963',
                        borderRadius: 7,
                        borderSkipped: false,
                        barThickness: 24
                    }]
                },

                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,

                    interaction: {
                        mode: 'index',
                        axis: 'y',
                        intersect: false
                    },

                    plugins: {
                        legend: {
                            display: false
                        },

                        tooltip: {
                            mode: 'index',
                            axis: 'y',
                            intersect: false,
                            displayColors: false,
                            padding: 12,
                            backgroundColor: '#263442',

                            callbacks: {
                                title: items =>
                                    items[0].label,

                                label: ctx =>
                                    'Jumlah: ' +
                                    number(ctx.raw) +
                                    ' pasien'
                            }
                        }
                    },

                    scales: {
                        x: {
                            beginAtZero: true,

                            border: {
                                display: false
                            },

                            grid: {
                                color: '#EDF1F5'
                            },

                            ticks: {
                                callback: value =>
                                    number(value)
                            }
                        },

                        y: {
                            border: {
                                display: false
                            },

                            grid: {
                                display: false
                            },

                            ticks: {
                                color: '#536273',

                                font: {
                                    size: 13
                                }
                            }
                        }
                    }
                }

            });
        }


        /* PASIEN BARU & LAMA */
        const pasienEl =
            document.getElementById('chartPasien');

        if (pasienEl) {

            new Chart(pasienEl, {

                type: 'doughnut',

                data: {
                    labels: [
                        'Pasien Baru',
                        'Pasien Lama'
                    ],

                    datasets: [{
                        data: [
                            pasienBaru,
                            pasienLama
                        ],

                        backgroundColor: [
                            '#24C875',
                            '#A7DFBF'
                        ],

                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',

                    plugins: {
                        legend: {
                            display: false
                        },

                        tooltip: {
                            displayColors: false,

                            callbacks: {
                                label: ctx => {

                                    const total =
                                        pasienBaru +
                                        pasienLama;

                                    const persen = total ?
                                        (
                                            ctx.raw /
                                            total *
                                            100
                                        ).toFixed(1) :
                                        0;

                                    return (
                                        ctx.label +
                                        ': ' +
                                        number(ctx.raw) +
                                        ' (' +
                                        persen +
                                        '%)'
                                    );
                                }
                            }
                        }
                    }
                }

            });
        }

        /* =========================================================
        AJAX PAGINATION JADWAL DOKTER
        ========================================================= */

        document.addEventListener('click', async function(e) {

            const link = e.target.closest(
                '#jadwalContainer .pagination a'
            );

            if (!link) return;

            e.preventDefault();

            const container =
                document.getElementById('jadwalContainer');

            if (!container) return;


            /* Loading effect */
            container.style.opacity = '.45';
            container.style.pointerEvents = 'none';


            try {

                const response = await fetch(link.href, {

                    method: 'GET',

                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }

                });


                if (!response.ok) {
                    throw new Error(
                        'Gagal mengambil data jadwal'
                    );
                }


                const html =
                    await response.text();


                /* Ganti tabel saja */
                container.innerHTML = html;


                /* Update URL tanpa reload */
                window.history.replaceState({},
                    '',
                    link.href
                );


                /* Scroll halus ke tabel */
                container.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });


            } catch (error) {

                console.error(error);

                alert(
                    'Gagal memuat halaman jadwal dokter.'
                );

            } finally {

                container.style.opacity = '1';
                container.style.pointerEvents = 'auto';

            }

        });
    </script>
@endpush
