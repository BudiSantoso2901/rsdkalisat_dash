<div class="table-responsive">

    <table class="table table-hover schedule-table">

        <thead>
            <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>Dokter</th>
                <th>Poli</th>
                <th>Jam</th>
                <th>Kuota</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>

        @forelse($jadwal as $index => $item)

            @php
                $kapasitas = (int) ($item->kapasitaspasien ?? 0);
                $terisi = (int) ($item->total_pasien ?? 0);

                $persen = $kapasitas > 0
                    ? min(round(($terisi / $kapasitas) * 100), 100)
                    : 0;

                if ($persen >= 100) {
                    $warna = 'bg-danger';
                    $status = 'Penuh';
                    $badge = 'full-status';

                } elseif ($persen >= 80) {
                    $warna = 'bg-warning';
                    $status = 'Hampir Penuh';
                    $badge = 'warning-status';

                } else {
                    $warna = 'bg-success';
                    $status = 'Tersedia';
                    $badge = 'available';
                }
            @endphp


            <tr>

                <td>
                    {{ $jadwal->firstItem() + $index }}
                </td>

                <td>
                    {{ \Carbon\Carbon::parse($item->tanggal_jadwal)->format('d/m/Y') }}
                </td>

                <td>
                    <span class="doctor-name">
                        {{ $item->nama_dokter ?? '-' }}
                    </span>
                </td>

                <td>
                    {{ $item->nama_poli ?? '-' }}
                </td>

                <td>

                    @if($item->open_time && $item->closed_time)

                        {{ \Carbon\Carbon::parse($item->open_time)->format('H:i') }}
                        -
                        {{ \Carbon\Carbon::parse($item->closed_time)->format('H:i') }}

                    @else
                        -
                    @endif

                </td>

                <td style="min-width:150px">

                    <div class="mb-1">
                        {{ $terisi }} / {{ $kapasitas }}
                    </div>

                    <div class="quota">

                        <div
                            class="progress-bar {{ $warna }}"
                            style="width:{{ $persen }}%">
                        </div>

                    </div>

                </td>

                <td>

                    <span class="status {{ $badge }}">
                        {{ $status }}
                    </span>

                </td>

            </tr>


        @empty

            <tr>
                <td
                    colspan="7"
                    class="text-center text-muted py-4">

                    Tidak ada jadwal pada periode ini

                </td>
            </tr>

        @endforelse

        </tbody>

    </table>

</div>


@if($jadwal->hasPages())

    <div class="
        schedule-pagination
        d-flex
        justify-content-between
        align-items-center
        mt-3
        flex-wrap
        gap-2
    ">

        <small class="text-muted">

            Menampilkan
            {{ $jadwal->firstItem() }}
            -
            {{ $jadwal->lastItem() }}

            dari
            {{ $jadwal->total() }}

            jadwal

        </small>


        <div>
            {{ $jadwal->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>

    </div>

@endif