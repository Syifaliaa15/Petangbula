<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="Sat, 01 Jan 2000 00:00:00 GMT">
    <title>Login - PETANGBULA</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .login-body {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            min-height: 100vh;
        }
        .login-card {
            background: #fff;
            border-radius: 24px;
            padding: 40px 32px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 40px rgba(236, 72, 153, 0.15);
            border: 1px solid #fbcfe8;
            animation: fadeInUp 0.6s ease-out forwards;
        }
        .login-logo-wrapper {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #ec4899, #f43f5e);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }
        .login-logo-wrapper svg { width: 40px; height: 40px; fill: #fff; }
        .login-title {
            text-align: center; color: #831843; font-size: 1.8rem;
            font-weight: 800; margin-bottom: 4px;
        }
        .login-subtitle {
            text-align: center; color: #6b7280;
            font-size: 0.9rem; margin-bottom: 24px;
        }
        .input-icon {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%); width: 20px; height: 20px;
            fill: #9ca3af; transition: fill 0.2s;
        }
        .form-group:focus-within .input-icon { fill: #ec4899; }
        .back-link {
            display: flex; align-items: center; justify-content: center;
            gap: 8px; margin-top: 24px; color: #6b7280;
            font-size: 0.9rem; font-weight: 600;
            transition: color 0.2s; text-decoration: none;
        }
        .back-link:hover { color: #ec4899; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .alert-login-error {
            background: #fee2e2; border-left: 4px solid #ef4444;
            padding: 16px; border-radius: 12px; margin-bottom: 20px;
        }
        .alert-login-error h4 {
            color: #b91c1c; font-size: 0.95rem;
            margin-bottom: 8px; display: flex;
            align-items: center; gap: 8px;
        }
        .alert-login-error ul {
            margin: 0; padding-left: 24px;
            color: #7f1d1d; font-size: 0.9rem; font-weight: 600;
        }
    </style>
</head>
<body class="login-body">

    <div class="login-card">
        <div class="login-logo-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
        </div>

        <h1 class="login-title">PETANGBULA</h1>
        <p class="login-subtitle">Masuk untuk mengakses sistem posyandu</p>

        {{-- LOGIN ERROR --}}
        @if ($errors->any())
            <div class="alert-login-error">
                <h4>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="#ef4444">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                    Login Gagal
                </h4>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="alert-login-error">
                <h4>⚠️ Peringatan</h4>
                <ul><li>{{ session('error') }}</li></ul>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success" style="background: #d1fae5; color: #065f46; padding: 12px 16px; border-radius: 10px; margin-bottom: 16px; font-size: 0.9rem;">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" data-no-unsaved>
            @csrf

            <div class="form-group">
                <label for="username">Username</label>
                <div style="position: relative;">
                    <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                    <input type="text" id="username" name="username"
                           value="{{ old('username') }}"
                           class="form-control"
                           style="padding-left: 44px;"
                           placeholder="Masukkan username"
                           required autofocus autocomplete="username">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div style="position: relative;">
                    <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                    </svg>
                    <input type="password" id="password" name="password"
                           class="form-control"
                           style="padding-left: 44px;"
                           placeholder="Masukkan password"
                           required autocomplete="current-password">
                </div>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 8px;">
                Masuk
            </button>
        </form>

        <a href="{{ route('home') }}" class="back-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
            </svg>
            Kembali ke Beranda
        </a>
    </div>

    <script>
    // ==========================================
    // ANTI BACK BUTTON DI HALAMAN LOGIN
    // ==========================================
    (function() {
        // Jika halaman login muncul dari BFCache (user klik back),
        // paksa reload untuk memastikan form benar-benar bersih
        window.addEventListener('pageshow', function(event) {
            if (event.persisted === true) {
                // Halaman dari BFCache → cek dulu, user masih login?
                fetch('{{ route("auth.check") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.authenticated === true) {
                        // Masih login → redirect ke dashboard sesuai role
                        window.location.replace('{{ route("home") }}');
                    } else {
                        // Tidak login → refresh halaman login
                        window.location.reload();
                    }
                })
                .catch(function() {
                    window.location.reload();
                });
            }
        });

        // Blokir back button
        window.history.pushState(null, null, window.location.href);
        window.addEventListener('popstate', function() {
            window.history.pushState(null, null, window.location.href);
        });
    })();
</script>

</body>
</html>