<!-- Topbar Start -->
<div class="navbar-custom">
    <ul class="list-unstyled topnav-menu float-end mb-0">

        <!-- PROFILE DROPDOWN -->
        <li class="dropdown notification-list topbar-dropdown">
            <a class="nav-link dropdown-toggle nav-user me-0 waves-effect waves-light"
                data-bs-toggle="dropdown"
                href="#"
                role="button"
                aria-haspopup="false"
                aria-expanded="false">

                <img src="{{ asset('assets/dokter 2.png') }}"
                    alt="user-image"
                    class="rounded-circle">
            </a>

            <div class="dropdown-menu dropdown-menu-end profile-dropdown">

                <!-- Welcome -->
                <div class="dropdown-header noti-title">
                    <h6 class="text-overflow m-0">Welcome !</h6>
                </div>

                <!-- My Account -->
                <a href="contacts-profile.html" class="dropdown-item notify-item">
                    <i class="fe-user"></i>
                    <span>My Account</span>
                </a>

                <!-- Lock Screen -->
                <a href="auth-lock-screen.html" class="dropdown-item notify-item">
                    <i class="fe-lock"></i>
                    <span>Lock Screen</span>
                </a>

                <div class="dropdown-divider"></div>

                <!-- LOGOUT LARAVEL -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf

                    <button type="submit"
                        class="dropdown-item notify-item"
                        style="border: none; background: transparent; width: 100%; text-align: left;">

                        <i class="fe-log-out"></i>
                        <span>Logout</span>
                    </button>
                </form>

            </div>
        </li>

    </ul>

    <!-- LOGO -->
    <div class="logo-box">
        <a href="index.html" class="logo logo-light text-center">
            <span class="logo-sm">
                <img src="{{ asset('assets/rsd.png') }}" alt="" height="44">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/rsd.png') }}" alt="" height="16">
            </span>
        </a>

        <a href="index.html" class="logo logo-dark text-center">
            <span class="logo-sm">
                <img src="{{ asset('assets/logo.png') }}" alt="" height="44">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/logo.png') }}" alt="" height="44">
            </span>
        </a>
    </div>

    <div class="clearfix"></div>
</div>
<!-- end Topbar -->