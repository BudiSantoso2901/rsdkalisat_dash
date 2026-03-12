@extends('layouts.layouts')

@section('content')
    <div class="content">
        <div class="container-fluid">

            {{-- ================= FILTER BULAN & TAHUN ================= --}}
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard') }}">
                        <div class="row align-items-end">

                            <div class="col-md-3">
                                <label class="form-label">Bulan</label>
                                <select name="bulan" class="form-control">
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Tahun</label>
                                <select name="tahun" class="form-control">
                                    @for ($t = now()->year; $t >= now()->year - 20; $t--)
                                        <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>
                                            {{ $t }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-2">
                                <button class="btn btn-primary w-100">Tampilkan</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>


            {{-- ================== ROW 1 ================== --}}
            <div class="row">

                {{-- RAWAT JALAN VS RAWAT INAP --}}
                <div class="col-xl-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">

                            <div class="mb-2">
                                <i class="bi bi-hospital fs-2 text-primary"></i>
                            </div>

                            <h5 class="fw-semibold">
                                Rawat Jalan dan Rawat Inap
                            </h5>

                            <canvas id="chartRawat"></canvas>
                        </div>
                    </div>
                </div>


                {{-- KUNJUNGAN PER POLI --}}
                <div class="col-xl-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">

                            <div class="mb-2">
                                <i class="bi bi-clipboard2-pulse fs-2 text-success"></i>
                            </div>

                            <h5 class="fw-semibold">
                                Kunjungan Per Poli
                            </h5>

                            <canvas id="chartPoli"></canvas>
                        </div>
                    </div>
                </div>


                {{-- PASIEN BARU VS LAMA --}}
                <div class="col-xl-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">

                            <div class="mb-2">
                                <i class="bi bi-people-fill fs-2 text-warning"></i>
                            </div>

                            <h5 class="fw-semibold">
                                Pasien Baru dan Pasien Lama
                            </h5>

                            <canvas id="chartPasien"></canvas>
                        </div>
                    </div>
                </div>

            </div>


            {{-- ================== ROW 2 ================== --}}
            <div class="row mt-3">
                {{-- JENIS PASIEN BULANAN --}}
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">

                            <h5 class="mb-4">
                                Jenis Pasien Bulan
                                {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }}
                                {{ $tahun }}
                            </h5>

                            @php
                                $dataJenis = [
                                    'ASURANSI' => $asuransi,
                                    'BPJS NON PBI' => $bpjsNonPbi,
                                    'BPJS PBI' => $bpjsPbi,
                                    'BPJS UHC' => $bpjsUhc,
                                    'JPK' => $jpk,
                                    'PEGAWAI' => $pegawai,
                                    'SPM' => $spm,
                                    'UMUM' => $umum,
                                ];

                                $totalJenis = array_sum($dataJenis);
                            @endphp

                            <div class="row">

                                @foreach ($dataJenis as $label => $value)
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <div class="border rounded p-3 text-center h-100">

                                            <div class="text-muted small mb-1">
                                                {{ $label }}
                                            </div>

                                            <div class="fs-4 fw-bold">
                                                {{ number_format($value) }}
                                            </div>

                                        </div>
                                    </div>
                                @endforeach

                            </div>

                            <div class="mt-3 pt-3 border-top d-flex justify-content-between">
                                <span class="fw-semibold">Total Semua Jenis</span>
                                <span class="fw-bold fs-5">
                                    {{ number_format($totalJenis) }}
                                </span>
                            </div>

                        </div>
                    </div>
                </div>
                {{-- STATISTIK BULANAN --}}
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">

                            <h5 class="mb-3">
                                Statistik Bulan {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }}
                                {{ $tahun }}
                            </h5>

                            @forelse ($kunjunganPerPoli as $poli)
                                <div class="d-flex justify-content-between border-bottom py-2">
                                    <span>{{ $poli->nama_poli }}</span>
                                    <strong>{{ $poli->total }}</strong>
                                </div>
                            @empty
                                <div class="text-center text-muted py-3">
                                    Tidak ada data kunjungan
                                </div>
                            @endforelse

                            <div class="mt-3 pt-2 border-top d-flex justify-content-between">
                                <span class="fw-semibold">Total Kunjungan</span>
                                <span class="fw-bold fs-5">
                                    {{ $rawatJalan + $rawatInap }}
                                </span>
                            </div>

                        </div>
                    </div>
                </div>


                {{-- JADWAL DOKTER HARI INI --}}
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-3">

                                <h5 class="mb-0">
                                    Jadwal Dokter
                                    <small class="text-muted">
                                        {{ $tanggalMulai->format('d M Y') }}
                                        -
                                        {{ $tanggalSelesai->format('d M Y') }}
                                    </small>
                                </h5>

                                <form method="GET" class="d-flex gap-2">

                                    <input type="date" name="tanggal_mulai"
                                        value="{{ request('tanggal_mulai', $tanggalMulai->format('Y-m-d')) }}"
                                        class="form-control form-control-sm">

                                    <input type="date" name="tanggal_selesai"
                                        value="{{ request('tanggal_selesai', $tanggalSelesai->format('Y-m-d')) }}"
                                        class="form-control form-control-sm">

                                    <button class="btn btn-primary btn-sm">
                                        Filter
                                    </button>

                                </form>

                            </div>

                            <div class="table-responsive">

                                <table class="table table-hover align-middle">

                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Dokter</th>
                                            <th>Poli</th>
                                            <th>Jam</th>
                                            <th>Kuota</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @forelse ($jadwal as $index => $item)
                                            @php
                                                $kapasitas = (int) ($item->kapasitaspasien ?? 0);
                                                $terisi = (int) ($item->total_pasien ?? 0);

                                                $persen = $kapasitas > 0 ? round(($terisi / $kapasitas) * 100) : 0;
                                                $persen = min($persen, 100);

                                                if ($persen >= 100) {
                                                    $warna = 'bg-danger';
                                                    $status = 'Penuh';
                                                    $badge = 'bg-danger';
                                                } elseif ($persen >= 80) {
                                                    $warna = 'bg-warning';
                                                    $status = 'Hampir Penuh';
                                                    $badge = 'bg-warning text-dark';
                                                } else {
                                                    $warna = 'bg-success';
                                                    $status = 'Tersedia';
                                                    $badge = 'bg-success';
                                                }
                                            @endphp

                                            <tr>

                                                <td>{{ $index + 1 }}</td>

                                                <td>
                                                    <strong>{{ $item->nama_dokter ?? '-' }}</strong>
                                                </td>

                                                <td>
                                                    {{ $item->nama_poli ?? '-' }}
                                                </td>

                                                <td>
                                                    {{ \Carbon\Carbon::parse($item->open_time)->format('H:i') }}
                                                    -
                                                    {{ \Carbon\Carbon::parse($item->closed_time)->format('H:i') }}
                                                </td>

                                                <td style="min-width:150px">

                                                    <div class="small fw-semibold mb-1">
                                                        {{ $terisi }} / {{ $kapasitas }}
                                                    </div>

                                                    <div class="progress" style="height:6px;">
                                                        <div class="progress-bar {{ $warna }}"
                                                            style="width: {{ $persen }}%">
                                                        </div>
                                                    </div>

                                                </td>

                                                <td>
                                                    <span class="badge {{ $badge }}">
                                                        {{ $status }}
                                                    </span>
                                                </td>

                                            </tr>

                                        @empty

                                            <tr>
                                                <td colspan="6" class="text-center text-muted">
                                                    Tidak ada jadwal pada periode ini
                                                </td>
                                            </tr>
                                        @endforelse

                                    </tbody>

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const rawatJalan = {{ $rawatJalan ?? 0 }};
        const rawatInap = {{ $rawatInap ?? 0 }};
        const pasienBaru = {{ $pasienBaru ?? 0 }};
        const pasienLama = {{ $pasienLama ?? 0 }};

        const poliLabels = {!! json_encode($kunjunganPerPoli->pluck('nama_poli')) !!};
        const poliData = {!! json_encode($kunjunganPerPoli->pluck('total')) !!};

        // Rawat Chart
        new Chart(document.getElementById('chartRawat'), {
            type: 'doughnut',
            data: {
                labels: ['Rawat Jalan', 'Rawat Inap'],
                datasets: [{
                    data: [rawatJalan, rawatInap],
                    backgroundColor: ['#36A2EB', '#FF6384']
                }]
            }
        });

        // Poli Chart
        new Chart(document.getElementById('chartPoli'), {
            type: 'bar',
            data: {
                labels: poliLabels,
                datasets: [{
                    label: 'Jumlah Pasien',
                    data: poliData,
                    backgroundColor: '#5b69bc'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Pasien Chart
        new Chart(document.getElementById('chartPasien'), {
            type: 'doughnut',
            data: {
                labels: ['Pasien Baru', 'Pasien Lama'],
                datasets: [{
                    data: [pasienBaru, pasienLama],
                    backgroundColor: ['#4CAF50', '#FFC107']
                }]
            }
        });
    </script>
@endpush
