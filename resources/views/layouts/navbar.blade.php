 <!-- Topbar Start -->
 <div class="navbar-custom">
     <ul class="list-unstyled topnav-menu float-end mb-0">
         <li class="dropdown notification-list topbar-dropdown">
             <a class="nav-link dropdown-toggle nav-user me-0 waves-effect waves-light" data-bs-toggle="dropdown"
                 href="#" role="button" aria-haspopup="false" aria-expanded="false">
                 <img src="{{ asset('assets/dokter 2.png') }}" alt="user-image" class="rounded-circle">
             </a>
             <div class="dropdown-menu dropdown-menu-end profile-dropdown ">
                 <!-- item-->
                 <div class="dropdown-header noti-title">
                     <h6 class="text-overflow m-0">Welcome !</h6>
                 </div>

                 <!-- item-->
                 <a href="contacts-profile.html" class="dropdown-item notify-item">
                     <i class="fe-user"></i>
                     <span>My Account</span>
                 </a>

                 <!-- item-->
                 <a href="auth-lock-screen.html" class="dropdown-item notify-item">
                     <i class="fe-lock"></i>
                     <span>Lock Screen</span>
                 </a>

                 <div class="dropdown-divider"></div>

                 <!-- item-->
                 <a href="auth-logout.html" class="dropdown-item notify-item">
                     <i class="fe-log-out"></i>
                     <span>Logout</span>
                 </a>

             </div>
         </li>

         <li class="dropdown notification-list">
             <a href="javascript:void(0);" class="nav-link right-bar-toggle waves-effect waves-light">
                 <i class="fe-settings noti-icon"></i>
             </a>
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

     {{-- <ul class="list-unstyled topnav-menu topnav-menu-left mb-0">
         <li>
             <button class="button-menu-mobile disable-btn waves-effect">
                 <i class="fe-menu"></i>
             </button>
         </li>

         <li>
             <h4 class="page-title-main">Dashboard</h4>
         </li>

     </ul> --}}

     <div class="clearfix"></div>

 </div>
 <!-- end Topbar -->
