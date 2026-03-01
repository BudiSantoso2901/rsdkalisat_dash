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
                                Rawat Jalan vs Rawat Inap
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
                                Pasien Baru vs Lama
                            </h5>

                            <canvas id="chartPasien"></canvas>
                        </div>
                    </div>
                </div>

            </div>


            {{-- ================== ROW 2 ================== --}}
            <div class="row mt-3">

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

                            <h5 class="mb-3">Jadwal Dokter Hari Ini</h5>

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
                                                $kapasitas = $item->kapasitaspasien ?? 0;
                                                $terisi = $item->total_pasien ?? 0;
                                                $persen = $kapasitas > 0 ? ($terisi / $kapasitas) * 100 : 0;
                                            @endphp

                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><strong>{{ $item->name }}</strong></td>
                                                <td>{{ $item->nama_poli }}</td>
                                                <td>{{ $item->open_time }} - {{ $item->closed_time }}</td>

                                                <td style="min-width:130px">
                                                    <div class="small fw-semibold mb-1">
                                                        {{ $terisi }} / {{ $kapasitas }}
                                                    </div>
                                                    <div class="progress" style="height:6px;">
                                                        <div class="progress-bar
                                                    {{ $persen >= 100 ? 'bg-danger' : ($persen >= 80 ? 'bg-warning' : 'bg-success') }}"
                                                            style="width: {{ $persen }}%">
                                                        </div>
                                                    </div>
                                                </td>

                                                <td>
                                                    @if ($terisi < $kapasitas)
                                                        <span class="badge bg-success">Tersedia</span>
                                                    @else
                                                        <span class="badge bg-danger">Penuh</span>
                                                    @endif
                                                </td>
                                            </tr>

                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">
                                                    Tidak ada jadwal hari ini
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
