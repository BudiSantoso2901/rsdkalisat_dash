<!-- ========== Left Sidebar Start ========== -->
<div class="left-side-menu">

    <div class="h-100" data-simplebar>
        <!--- Sidemenu -->
        <div id="sidebar-menu">

            <ul id="side-menu">

                <li>
                    <a href="{{ route('dashboard') }}">
                        <i class="mdi mdi-view-dashboard-outline"></i>
                        <span> Dashboard </span>
                    </a>
                </li>

                <li class="menu-title mt-2">Informasi</li>

                <li>
                    <a href="{{ route('dokter') }}">
                        <i class="mdi mdi-doctor"></i>
                        <span> Dokter </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('tabel') }}">
                        <i class="mdi mdi-table"></i>
                        <span>Pasien</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('kunjungan_poli') }}">
                        <i class="mdi mdi-chart-bar"></i>
                        <span>Kunjungan Poli</span>
                    </a>
                </li>
            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>
<!-- Left Sidebar End -->
