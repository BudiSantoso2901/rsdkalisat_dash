<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login Dashboard | RSD Kalisat</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo_kop.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --blue: #67a9e8;
            --blue-dark: #23649f;
            --blue-light: #eaf5ff;
            --pink: #f2cbd8;
            --navy: #183b5b;
            --text: #34495e;
            --muted: #8495a7;
            --white: #ffffff;
            --border: #e3edf5;
            --danger: #d94c62;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 10% 15%, rgba(103, 169, 232, .18), transparent 28%),
                radial-gradient(circle at 90% 85%, rgba(242, 203, 216, .30), transparent 30%),
                linear-gradient(135deg, #f4f9ff 0%, #edf6ff 50%, #fff7fa 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px;
            overflow-x: hidden;
        }

        .background-circle {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            filter: blur(1px);
        }

        .circle-one {
            width: 330px;
            height: 330px;
            background: rgba(103, 169, 232, .10);
            top: -130px;
            right: -80px;
        }

        .circle-two {
            width: 260px;
            height: 260px;
            background: rgba(242, 203, 216, .18);
            bottom: -110px;
            left: -90px;
        }

        .login-container {
            width: 100%;
            max-width: 1040px;
            min-height: 620px;
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            background: var(--white);
            border: 1px solid rgba(255, 255, 255, .8);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 25px 70px rgba(40, 91, 135, .13);
            position: relative;
            z-index: 1;
        }

        /* LEFT PANEL */

        .brand-header {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .brand-logo-wrapper {
            width: 100%;
            max-width: 390px;
            min-height: auto;
            padding: 0;

            display: flex;
            align-items: center;
            justify-content: flex-start;

            background: transparent;
            border-radius: 0;
            box-shadow: none;
        }

        .brand-logo-image {
            display: block;
            width: 100%;
            max-width: 390px;
            height: auto;
            max-height: 90px;
            object-fit: contain;
            object-position: left center;
        }

        .brand-panel {
            padding: 54px 48px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: white;
            background: linear-gradient(145deg, #4c9bdc 0%, #75b8ed 55%, #a5d4f4 100%);
        }

        .brand-panel::before,
        .brand-panel::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, .10);
        }

        .brand-panel::before {
            width: 400px;
            height: 400px;
            right: -180px;
            top: -130px;
        }

        .brand-panel::after {
            width: 300px;
            height: 300px;
            left: -140px;
            bottom: -140px;
        }

        .brand-content,
        .brand-footer {
            position: relative;
            z-index: 1;
        }

        .brand-header {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .brand-logo {
            width: 62px;
            height: 62px;
            background: rgba(255, 255, 255, .95);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(22, 77, 122, .13);
            color: var(--blue-dark);
            flex-shrink: 0;
        }

        .brand-logo svg {
            width: 36px;
            height: 36px;
        }

        .brand-name {
            font-family: 'Poppins', sans-serif;
            font-size: 19px;
            font-weight: 700;
            line-height: 1.4;
        }

        .brand-subtitle {
            font-size: 10px;
            letter-spacing: 1.5px;
            font-weight: 600;
            opacity: .85;
            margin-top: 3px;
        }

        .brand-main {
            margin-top: 36px;
        }

        .brand-tag {
            display: inline-block;
            padding: 8px 14px;
            border: 1px solid rgba(255, 255, 255, .45);
            background: rgba(255, 255, 255, .14);
            border-radius: 30px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            margin-bottom: 22px;
        }

        .brand-main h1 {
            font-family: 'Poppins', sans-serif;
            font-size: clamp(30px, 3.4vw, 42px);
            line-height: 1.25;
            font-weight: 700;
            max-width: 440px;
            margin-bottom: 18px;
        }

        .brand-main h1 span {
            color: #eaf5ff;
        }

        .brand-main p {
            font-size: 14px;
            line-height: 1.9;
            color: rgba(255, 255, 255, .92);
            max-width: 390px;
        }

        .feature-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 38px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 12px;
            font-weight: 500;
            color: rgba(255, 255, 255, .95);
        }

        .feature-check {
            width: 24px;
            height: 24px;
            border-radius: 8px;
            background: rgba(255, 255, 255, .18);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
        }

        .brand-footer {
            font-size: 11px;
            color: rgba(255, 255, 255, .85);
            padding-top: 35px;
        }

        /* RIGHT PANEL */

        .form-panel {
            padding: 60px 60px;
            display: flex;
            justify-content: center;
            flex-direction: column;
        }

        .form-heading {
            margin-bottom: 35px;
        }

        .form-heading .eyebrow {
            color: var(--blue-dark);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .form-heading h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 29px;
            color: var(--navy);
            font-weight: 700;
            margin-bottom: 10px;
        }

        .form-heading p {
            color: var(--muted);
            font-size: 13px;
            line-height: 1.7;
        }

        .form-group {
            margin-bottom: 21px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 9px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper input {
            width: 100%;
            height: 52px;
            padding: 0 45px 0 16px;
            border: 1px solid var(--border);
            background: #fafdff;
            border-radius: 12px;
            outline: none;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            color: var(--navy);
            transition: .2s ease;
        }

        .input-wrapper input::placeholder {
            color: #b1bfcc;
        }

        .input-wrapper input:focus {
            border-color: var(--blue);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(103, 169, 232, .12);
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #90a8be;
            display: flex;
            align-items: center;
            pointer-events: none;
        }

        .input-icon svg {
            width: 19px;
            height: 19px;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);

            width: 36px;
            height: 36px;

            display: flex;
            align-items: center;
            justify-content: center;

            border: none;
            background: transparent;
            color: #8ba2b8;

            cursor: pointer;
            padding: 6px;

            transition: color .2s ease;
        }

        .toggle-password:hover {
            color: #23649f;
        }

        .toggle-password .eye-icon {
            display: none;
            width: 21px;
            height: 21px;
        }

        .toggle-password .eye-icon.active {
            display: block;
        }

        .toggle-password svg {
            width: 20px;
            height: 20px;
        }

        .toggle-password svg[hidden] {
            display: none;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 3px 0 26px;
        }

        .remember {
            display: flex;
            gap: 8px;
            align-items: center;
            font-size: 11px;
            color: var(--muted);
            cursor: pointer;
        }

        .remember input {
            accent-color: var(--blue-dark);
        }

        .login-button {
            width: 100%;
            height: 52px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(100deg, #3987ca, #67a9e8);
            color: white;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: .4px;
            cursor: pointer;
            box-shadow: 0 9px 20px rgba(65, 137, 199, .23);
            transition: .2s ease;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(65, 137, 199, .30);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .alert-error {
            padding: 13px 15px;
            border: 1px solid #f5cbd2;
            background: #fff5f6;
            color: var(--danger);
            border-radius: 10px;
            margin-bottom: 22px;
            font-size: 12px;
            line-height: 1.6;
        }

        .alert-success {
            padding: 13px 15px;
            border: 1px solid #bde5d1;
            background: #f1fbf5;
            color: #247a50;
            border-radius: 10px;
            margin-bottom: 22px;
            font-size: 12px;
            line-height: 1.6;
        }

        .secure-note {
            display: flex;
            gap: 9px;
            align-items: center;
            justify-content: center;
            margin-top: 27px;
            font-size: 10px;
            color: #9aabba;
        }

        .secure-note svg {
            width: 14px;
            height: 14px;
        }

        .footer-mobile {
            display: none;
        }

        @media (max-width: 850px) {
            .login-container {
                max-width: 530px;
                min-height: auto;
                grid-template-columns: 1fr;
            }

            .brand-panel {
                padding: 28px 32px;
                min-height: 235px;
            }

            .brand-main {
                margin-top: 24px;
            }

            .brand-main h1 {
                font-size: 27px;
            }

            .brand-main p,
            .feature-list,
            .brand-footer {
                display: none;
            }

            .form-panel {
                padding: 42px 38px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 14px;
            }

            .login-container {
                border-radius: 20px;
            }

            .brand-panel {
                min-height: 190px;
                padding: 23px;
            }

            .brand-logo {
                width: 48px;
                height: 48px;
                border-radius: 14px;
            }

            .brand-logo svg {
                width: 29px;
                height: 29px;
            }

            .brand-name {
                font-size: 15px;
            }

            .brand-subtitle {
                font-size: 8px;
            }

            .brand-main {
                margin-top: 18px;
            }

            .brand-main h1 {
                font-size: 24px;
                margin-bottom: 0;
            }

            .brand-tag {
                margin-bottom: 12px;
                font-size: 9px;
            }

            .form-panel {
                padding: 35px 24px;
            }

            .form-heading h2 {
                font-size: 25px;
            }

            .footer-mobile {
                display: block;
                text-align: center;
                font-size: 10px;
                color: #9aabba;
                margin-top: 25px;
            }
        }
    </style>
</head>

<body>

    <div class="background-circle circle-one"></div>
    <div class="background-circle circle-two"></div>

    <div class="login-container">

        <!-- PANEL IDENTITAS RUMAH SAKIT -->
        <section class="brand-panel">

            <div class="brand-content">

                <div class="brand-header">

                    <div class="brand-logo-wrapper">

                        <img src="{{ asset('assets/images/images-removebg-preview.png') }}" alt="RSD Kalisat Jember"
                            class="brand-logo-image">

                    </div>

                </div>

                <div class="brand-main">

                    <div class="brand-tag">
                        MINI SIMRS
                    </div>

                    <h1>
                        Sistem Informasi
                        <span>Manajemen Rumah Sakit</span>
                    </h1>

                    <p>
                        Platform ini berfungsi melihat dan monitoring data
                        pelayanan Rumah Sakit Daerah Kalisat
                        untuk mendukung pengambilan keputusan
                        yang cepat dan tepat.
                    </p>

                    <div class="feature-list">
                        <div class="feature-item">
                            <span class="feature-check">✓</span>
                            Informasi pelayanan terintegrasi
                        </div>

                        <div class="feature-item">
                            <span class="feature-check">✓</span>
                            Monitoring data secara terpusat
                        </div>

                    </div>

                </div>

            </div>

            <div class="brand-footer">
                © {{ date('Y') }} IT Rumah Sakit Daerah Kalisat V2
                · Kabupaten Jember
            </div>

        </section>


        <!-- PANEL FORM LOGIN -->
        <section class="form-panel">

            <div class="form-heading">

                <div class="eyebrow">Welcome Back</div>

                <h2>Selamat Datang</h2>

                <p>
                    Silakan masuk menggunakan akun dashboard
                    yang telah diberikan oleh administrator.
                </p>

            </div>


            <!-- PESAN ERROR -->
            @if ($errors->any())
                <div class="alert-error">
                    <strong>Login gagal!</strong>
                    <br>
                    {{ $errors->first() }}
                </div>
            @endif


            <!-- PESAN SUKSES -->
            @if (session('status'))
                <div class="alert-success">
                    {{ session('status') }}
                </div>
            @endif


            <form method="POST" action="{{ route('login.process') }}">

                @csrf

                <div class="form-group">

                    <label for="username">USERNAME</label>

                    <div class="input-wrapper">

                        <input type="text" id="username" name="username" value="{{ old('username') }}"
                            placeholder="Masukkan username" autocomplete="username" maxlength="80" required autofocus>

                        <span class="input-icon">
                            <svg viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.7" />
                                <path d="M4 21C4.5 16.5 7.3 14 12 14C16.7 14 19.5 16.5 20 21" stroke="currentColor"
                                    stroke-width="1.7" stroke-linecap="round" />
                            </svg>
                        </span>

                    </div>

                </div>


                <div class="form-group">

                    <label for="password">PASSWORD</label>

                    <div class="input-wrapper">

                        <input type="password" id="password" name="password" placeholder="Masukkan password"
                            autocomplete="current-password" required>

                        <button type="button" class="toggle-password" id="togglePassword"
                            aria-label="Tampilkan password" title="Tampilkan password">

                            <svg id="eyeOpen" class="eye-icon active" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>

                            <svg id="eyeClosed" class="eye-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M3 3l18 18" />
                                <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8" />
                                <path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a17 17 0 0 1-3.2 3.9" />
                                <path d="M6.2 6.2C3.5 8.1 2 12 2 12s3.6 7 10 7c1.1 0 2.2-.2 3.2-.6" />
                            </svg>
                        </button>

                    </div>

                </div>


                <div class="form-options">

                    <label class="remember">
                        <input type="checkbox" name="remember" value="1">
                        Ingat saya
                    </label>

                </div>


                <button type="submit" class="login-button">
                    MASUK KE DASHBOARD
                </button>

            </form>


            <div class="secure-note">
                <svg viewBox="0 0 24 24" fill="none">
                    <rect x="5" y="10" width="14" height="11" rx="2" stroke="currentColor"
                        stroke-width="1.7" />
                    <path d="M8 10V7C8 4.8 9.8 3 12 3C14.2 3 16 4.8 16 7V10" stroke="currentColor" stroke-width="1.7"
                        stroke-linecap="round" />
                </svg>

                Akses aman dan terlindungi
            </div>

            <div class="footer-mobile">
                © {{ date('Y') }} RSD Kalisat · Kabupaten Jember
            </div>

        </section>

    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            const eyeOpen = document.getElementById('eyeOpen');
            const eyeClosed = document.getElementById('eyeClosed');

            // Pastikan seluruh elemen tersedia
            if (!togglePassword || !passwordInput || !eyeOpen || !eyeClosed) {
                console.error('Elemen toggle password tidak ditemukan.');
                return;
            }

            // Fungsi mengatur tampilan password dan ikon
            function setPasswordVisibility(isVisible) {

                passwordInput.type = isVisible ? 'text' : 'password';

                // Password tersembunyi = ikon mata terbuka
                eyeOpen.classList.toggle('active', !isVisible);

                // Password terlihat = ikon mata tercoret
                eyeClosed.classList.toggle('active', isVisible);

                togglePassword.setAttribute(
                    'aria-label',
                    isVisible ? 'Sembunyikan password' : 'Tampilkan password'
                );

                togglePassword.setAttribute(
                    'title',
                    isVisible ? 'Sembunyikan password' : 'Tampilkan password'
                );

                togglePassword.setAttribute(
                    'aria-pressed',
                    String(isVisible)
                );
            }

            // Kondisi awal
            setPasswordVisibility(false);

            // Event klik tombol mata
            togglePassword.addEventListener('click', function() {

                const isCurrentlyHidden = passwordInput.type === 'password';

                setPasswordVisibility(isCurrentlyHidden);

            });

        });
    </script>

</body>

</html>
