<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="Sat, 01 Jan 2000 00:00:00 GMT">
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ time() }}">
    @stack('styles')
</head>
    <body>
    <script>
        // ==========================================
        // HYBRID BACK BUTTON SYSTEM
        // ==========================================
        (function() {
            // Ambil URL dashboard aktif (untuk deteksi user di dashboard)
            var dashboardUrls = [
                '{{ route("kader.dashboard") }}',
                '{{ route("bidan.dashboard") }}',
                '{{ route("admin.dashboard") }}'
            ];
            var currentUrl = window.location.href.split('?')[0]; // strip query string
            var isDashboard = dashboardUrls.some(function(url) {
                return currentUrl.indexOf(url) !== -1;
            });

            // ==========================================
            // 1. HANDLE BFCACHE (halaman dari cache)
            // ==========================================
            window.addEventListener('pageshow', function(event) {
                if (event.persisted === true) {
                    // Cek apakah user masih login ke server
                    fetch('{{ route("auth.check") }}', {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin'
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.authenticated !== true) {
                            window.location.replace('{{ route("login") }}');
                        } else {
                            window.location.reload();
                        }
                    })
                    .catch(function() {
                        window.location.replace('{{ route("login") }}');
                    });
                }
            });

            // ==========================================
            // 2. HANDLE BACK BUTTON (popstate)
            // ==========================================
            window.history.pushState(null, null, window.location.href);

            window.addEventListener('popstate', function() {
                // Kalau user di DASHBOARD → logout otomatis
                if (isDashboard) {
                    var confirmed = confirm(
                        'Apakah Anda yakin ingin keluar?\n\n' +
                        'Menekan tombol kembali dari halaman Dashboard akan otomatis keluar dari sistem.\n' +
                        'Klik OK untuk keluar, atau Batal untuk tetap di halaman ini.'
                    );

                    if (confirmed) {
                        // Kirim request logout ke server
                        fetch('{{ route("logout.ajax") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        })
                        .then(function(r) { return r.json(); })
                        .then(function(data) {
                            if (data.success) {
                                window.location.replace(data.redirect);
                            } else {
                                window.location.replace('{{ route("login") }}');
                            }
                        })
                        .catch(function() {
                            window.location.replace('{{ route("login") }}');
                        });
                    } else {
                        // User batal → push state kembali (biar tidak keluar)
                        window.history.pushState(null, null, window.location.href);
                    }
                } else {
                    // User di halaman LAIN → navigasi biasa
                    // Cek apakah ada form yang belum disimpan
                    var hasUnsavedForm = checkUnsavedForm();
                    
                    if (hasUnsavedForm) {
                        var confirmed = confirm(
                            'Ada perubahan yang belum disimpan.\n\n' +
                            'Yakin ingin keluar tanpa menyimpan?'
                        );
                        if (!confirmed) {
                            window.history.pushState(null, null, window.location.href);
                            return;
                        }
                    }
                    // Lanjut navigasi back normal
                    window.history.back();
                }
            });

            // ==========================================
            // 3. DETEKSI FORM BELUM DISIMPAN
            // ==========================================
            function checkUnsavedForm() {
                var forms = document.querySelectorAll('form:not([data-no-unsaved])');
                for (var i = 0; i < forms.length; i++) {
                    var form = forms[i];
                    var inputs = form.querySelectorAll('input, textarea, select');
                    for (var j = 0; j < inputs.length; j++) {
                        var input = inputs[j];
                        // Skip CSRF, method, submit
                        if (input.type === 'hidden' || input.type === 'submit') continue;
                        
                        // Cek apakah ada value yang diisi tapi beda dengan default
                        if (input.value && input.defaultValue !== input.value) {
                            return true;
                        }
                    }
                }
                return false;
            }

            // ==========================================
            // 4. BLOKIR SHORTCUT BACK KEYBOARD
            // ==========================================
            document.addEventListener('keydown', function(e) {
                // Ctrl + Shift + Backspace (Chrome shortcut)
                if (e.ctrlKey && e.shiftKey && e.key === 'Backspace') {
                    e.preventDefault();
                }
            });

            // ==========================================
            // 5. KONFIRMASI SAAT TUTUP TAB (kalau ada form belum disimpan)
            // ==========================================
            window.addEventListener('beforeunload', function(e) {
                if (!isDashboard && checkUnsavedForm()) {
                    e.preventDefault();
                    e.returnValue = '';
                    return '';
                }
            });

        })();
    </script>

    <div class="dashboard-wrapper">

        <!-- MOBILE MENU TOGGLE -->
        <button class="menu-toggle" onclick="toggleSidebar()">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
            </svg>
        </button>

        <!-- OVERLAY (hanya muncul saat sidebar terbuka di mobile) -->
        <div id="sidebar-overlay" onclick="toggleSidebar()" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 49;"></div>

        <!-- SIDEBAR -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <img src="{{ asset('images/Logo.png') }}" alt="Logo Petangbula">
                <h2>PETANGBULA</h2>
                <p>{{ Auth::user()->role ?? 'guest' }}</p>
            </div>

            <nav class="sidebar-nav">
                @if(Auth::user()->isKader())
                    <a href="{{ route('kader.dashboard') }}" class="menu-item {{ request()->routeIs('kader.dashboard') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('balita.index') }}" class="menu-item {{ request()->routeIs('balita.*') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
                        Kelola Data Balita
                    </a>
                    <a href="{{ route('ibu-hamil.index') }}" class="menu-item {{ request()->routeIs('ibu-hamil.*') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
                        Kelola Data Bumil
                    </a>
                    <a href="{{ route('kader.jadwal.index') }}" class="menu-item {{ request()->routeIs('kader.jadwal.*') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
                        Jadwal Posyandu
                    </a>
                    <a href="{{ route('kader.laporan.posyandu') }}" class="menu-item {{ request()->routeIs('kader.laporan.posyandu') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                        Laporan Posyandu
                    </a>
                @elseif(Auth::user()->isBidan())
                    <a href="{{ route('bidan.dashboard') }}" class="menu-item {{ request()->routeIs('bidan.dashboard') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('bidan.validasi.balita') }}" class="menu-item {{ request()->routeIs('bidan.validasi.balita') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        Validasi Data Balita
                    </a>
                    <a href="{{ route('bidan.validasi.bumil') }}" class="menu-item {{ request()->routeIs('bidan.validasi.bumil') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        Validasi Data Bumil
                    </a>
                    <a href="{{ route('bidan.darurat') }}" class="menu-item {{ request()->routeIs('bidan.darurat') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                        Pemeriksaan Darurat
                    </a>
                    <a href="{{ route('jadwal.riwayat') }}" class="menu-item {{ request()->routeIs('jadwal.riwayat') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
                        Riwayat Jadwal Posyandu
                    </a>
                    <a href="{{ route('bidan.laporan.posyandu') }}" class="menu-item {{ request()->routeIs('bidan.laporan.posyandu') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                        Laporan Posyandu
                    </a>
                    <a href="{{ route('bidan.laporan.darurat') }}" class="menu-item {{ request()->routeIs('bidan.laporan.darurat') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                        Laporan Darurat
                    </a>
                @elseif(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.kelola-akun') }}" class="menu-item {{ request()->routeIs('admin.kelola-akun*') || request()->routeIs('admin.edit-user') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        Kelola Akun
                    </a>
                    <a href="{{ route('jadwal.riwayat') }}" class="menu-item {{ request()->routeIs('jadwal.riwayat') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
                        Riwayat Jadwal Posyandu
                    </a>
                    <a href="{{ route('admin.laporan-posyandu') }}" class="menu-item {{ request()->routeIs('admin.laporan-posyandu') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                        Laporan Posyandu
                    </a>
                    <a href="{{ route('admin.laporan-darurat') }}" class="menu-item {{ request()->routeIs('admin.laporan-darurat') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                        Laporan Darurat
                    </a>
                @endif
            </nav>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" data-no-unsaved style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #fbcfe8;">
                @csrf
                <button type="button" onclick="konfirmasiLogout()" class="menu-item" style="color: #b91c1c; background: #fee2e2; width: 100%;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                    </svg>
                    Logout
                </button>
            </form>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">
            <div class="content-header">
                <div>
                    <h1>@yield('page-title', 'Dashboard')</h1>
                    <p>@yield('page-subtitle', '')</p>
                </div>
                <div class="user-info">
                    <div class="avatar">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="details">
                        <div class="name">{{ Auth::user()->name ?? 'User' }}</div>
                        <div class="role">
                            @if(Auth::user()->posyandu)
                                {{ Auth::user()->posyandu->nama_posyandu }}
                            @else
                                {{ ucfirst(Auth::user()->role ?? '-') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        // ==========================================
        // TOGGLE SIDEBAR MOBILE
        // ==========================================
        function toggleSidebar() {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('sidebar-overlay');

            sidebar.classList.toggle('open');

            if (sidebar.classList.contains('open')) {
                overlay.style.display = 'block';
                document.body.style.overflow = 'hidden';
            } else {
                overlay.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }

        // Tutup sidebar otomatis saat link diklik (di mobile)
        document.querySelectorAll('.sidebar-nav .menu-item').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 900) {
                    var sidebar = document.getElementById('sidebar');
                    if (sidebar.classList.contains('open')) {
                        toggleSidebar();
                    }
                }
            });
        });

        function konfirmasiLogout() {
            if (confirm('Apakah Anda yakin ingin keluar dari sistem?\n\nAnda harus masuk kembali untuk mengakses.')) {
                // Kirim AJAX logout
                fetch('{{ route("logout.ajax") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        window.location.replace(data.redirect);
                    } else {
                        // Fallback: submit form manual
                        document.getElementById('logout-form').submit();
                    }
                })
                .catch(function() {
                    // Fallback: submit form manual
                    document.getElementById('logout-form').submit();
                });
            }
        }
    </script>
    @stack('scripts')
</body>
</html>