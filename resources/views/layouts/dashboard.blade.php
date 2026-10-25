<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <title>@yield('title', 'Dashboard') - PETANGBULA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js untuk Toast & Dropdown -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    @stack('styles')
</head>
<body class="font-sans bg-kia-100 text-text-dark antialiased flex h-screen overflow-hidden">

    <!-- ========================================== -->
    <!-- 1. SIDEBAR DESKTOP -->
    <!-- ========================================== -->
    <aside class="hidden lg:flex flex-col w-64 bg-white border-r border-kia-200 flex-shrink-0 h-full z-40">
        <div class="p-6 text-center border-b border-kia-200">
            <img src="{{ asset('images/Logo.png') }}" alt="Logo" class="h-12 mx-auto mb-3">
            <p class="text-[10px] font-bold text-kia-500 uppercase tracking-widest">{{ Auth::user()->role }}</p>
        </div>

        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            @if(Auth::user()->isKader())
                <a href="{{ route('kader.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('kader.dashboard') ? 'bg-kia-500 text-white shadow-sm' : 'text-text-medium hover:bg-kia-100 hover:text-kia-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('balita.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('balita.*') ? 'bg-kia-500 text-white shadow-sm' : 'text-text-medium hover:bg-kia-100 hover:text-kia-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Data Balita
                </a>
                <a href="{{ route('ibu-hamil.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('ibu-hamil.*') ? 'bg-kia-500 text-white shadow-sm' : 'text-text-medium hover:bg-kia-100 hover:text-kia-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    Data Ibu Hamil
                </a>
                <a href="{{ route('kader.jadwal.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('kader.jadwal.*') ? 'bg-kia-500 text-white shadow-sm' : 'text-text-medium hover:bg-kia-100 hover:text-kia-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Jadwal Posyandu
                </a>
                <a href="{{ route('kader.laporan.posyandu') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('kader.laporan.posyandu') ? 'bg-kia-500 text-white shadow-sm' : 'text-text-medium hover:bg-kia-100 hover:text-kia-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Laporan Posyandu
                </a>
            @elseif(Auth::user()->isBidan())
                <a href="{{ route('bidan.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('bidan.dashboard') ? 'bg-kia-500 text-white shadow-sm' : 'text-text-medium hover:bg-kia-100 hover:text-kia-700' }}"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>Dashboard</a>
                <a href="{{ route('bidan.validasi.balita') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('bidan.validasi.balita') ? 'bg-kia-500 text-white shadow-sm' : 'text-text-medium hover:bg-kia-100 hover:text-kia-700' }}"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Validasi Balita</a>
                <a href="{{ route('bidan.validasi.bumil') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('bidan.validasi.bumil') ? 'bg-kia-500 text-white shadow-sm' : 'text-text-medium hover:bg-kia-100 hover:text-kia-700' }}"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Validasi Bumil</a>
                <a href="{{ route('bidan.darurat') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('bidan.darurat') ? 'bg-kia-500 text-white shadow-sm' : 'text-text-medium hover:bg-kia-100 hover:text-kia-700' }}"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>Pemeriksaan Darurat</a>
                <a href="{{ route('jadwal.riwayat') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('jadwal.riwayat') ? 'bg-kia-500 text-white shadow-sm' : 'text-text-medium hover:bg-kia-100 hover:text-kia-700' }}"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Riwayat Jadwal</a>
                <a href="{{ route('bidan.laporan.posyandu') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('bidan.laporan.posyandu') ? 'bg-kia-500 text-white shadow-sm' : 'text-text-medium hover:bg-kia-100 hover:text-kia-700' }}"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>Laporan Posyandu</a>
                <a href="{{ route('bidan.laporan.darurat') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('bidan.laporan.darurat') ? 'bg-kia-500 text-white shadow-sm' : 'text-text-medium hover:bg-kia-100 hover:text-kia-700' }}"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>Laporan Darurat</a>
            @elseif(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-kia-500 text-white shadow-sm' : 'text-text-medium hover:bg-kia-100 hover:text-kia-700' }}"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>Dashboard</a>
                <a href="{{ route('admin.kelola-akun') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('admin.kelola-akun*') || request()->routeIs('admin.edit-user') ? 'bg-kia-500 text-white shadow-sm' : 'text-text-medium hover:bg-kia-100 hover:text-kia-700' }}"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>Kelola Akun</a>
                <a href="{{ route('jadwal.riwayat') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('jadwal.riwayat') ? 'bg-kia-500 text-white shadow-sm' : 'text-text-medium hover:bg-kia-100 hover:text-kia-700' }}"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Riwayat Jadwal</a>
                <a href="{{ route('admin.laporan-posyandu') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('admin.laporan-posyandu') ? 'bg-kia-500 text-white shadow-sm' : 'text-text-medium hover:bg-kia-100 hover:text-kia-700' }}"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>Laporan Posyandu</a>
                <a href="{{ route('admin.laporan-darurat') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('admin.laporan-darurat') ? 'bg-kia-500 text-white shadow-sm' : 'text-text-medium hover:bg-kia-100 hover:text-kia-700' }}"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>Laporan Darurat</a>
            @endif
        </nav>

        <form id="logout-form-desktop" action="{{ route('logout') }}" method="POST" class="p-4 border-t border-kia-200">
            @csrf
            <button type="button" onclick="konfirmasiLogout()" class="flex items-center justify-center gap-2 w-full px-3 py-2.5 rounded-md bg-red-50 text-red-600 text-sm font-semibold hover:bg-red-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Logout
            </button>
        </form>
    </aside>

    <!-- ========================================== -->
    <!-- 2. MAIN AREA WRAPPER -->
    <!-- ========================================== -->
    <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden relative">
        
        <!-- HEADER MOBILE -->
        <header class="lg:hidden flex items-center justify-between px-4 py-3 bg-white border-b border-kia-200 shadow-sm z-50 flex-shrink-0">
            <button onclick="toggleMobileSidebar()" class="p-2 text-kia-900 bg-kia-50 rounded-md hover:bg-kia-100 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>
            </button>
            <div class="font-extrabold text-kia-900 tracking-widest text-sm">PETANGBULA</div>
            <div class="w-8 h-8 rounded-full bg-kia-500 text-white flex items-center justify-center font-bold text-xs">
                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
            </div>
        </header>

        <!-- AREA SCROLL KONTEN -->
        <main class="flex-1 overflow-y-auto overflow-x-hidden">
            <div class="p-4 md:p-8 w-full">
                <!-- Header Nama Kader & Posyandu -->
                @if (!View::hasSection('hide_header'))
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                        <div>
                            <h1 class="text-2xl font-extrabold text-kia-900 tracking-tight">@yield('page-title', 'Dashboard')</h1>
                            <p class="text-sm text-text-medium mt-1">@yield('page-subtitle', '')</p>
                        </div>
                        <div class="flex items-center gap-3 bg-white px-4 py-2.5 rounded-lg border border-kia-200 shadow-sm">
                            <div class="w-10 h-10 rounded-full bg-kia-500 text-white flex items-center justify-center font-bold text-lg">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="text-left">
                                <div class="text-sm font-bold text-kia-900 leading-tight">{{ Auth::user()->name }}</div>
                                <div class="text-xs text-kia-500 uppercase font-semibold">{{ Auth::user()->posyandu->nama_posyandu ?? Auth::user()->role }}</div>
                            </div>
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- ========================================== -->
    <!-- 3. SIDEBAR MOBILE (Overlay) -->
    <!-- ========================================== -->
    <div id="mobile-sidebar-overlay" onclick="toggleMobileSidebar()" class="hidden fixed inset-0 bg-black/50 z-50 lg:hidden transition-opacity"></div>
    
    <aside id="mobile-sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white transform -translate-x-full transition-transform duration-300 flex flex-col lg:hidden">
        <div class="p-6 text-center border-b border-kia-200 relative">
            <button onclick="toggleMobileSidebar()" class="absolute top-2 right-2 p-2 text-gray-500 hover:text-red-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <img src="{{ asset('images/Logo.png') }}" alt="Logo" class="h-12 mx-auto mb-3">
            <p class="text-[10px] font-bold text-kia-500 uppercase tracking-widest">{{ Auth::user()->role }}</p>
        </div>
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            @if(Auth::user()->isKader())
                <a href="{{ route('kader.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold {{ request()->routeIs('kader.dashboard') ? 'bg-kia-500 text-white' : 'text-text-medium' }}">Dashboard</a>
                <a href="{{ route('balita.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold {{ request()->routeIs('balita.*') ? 'bg-kia-500 text-white' : 'text-text-medium' }}">Data Balita</a>
                <a href="{{ route('ibu-hamil.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold {{ request()->routeIs('ibu-hamil.*') ? 'bg-kia-500 text-white' : 'text-text-medium' }}">Data Ibu Hamil</a>
                <a href="{{ route('kader.jadwal.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold {{ request()->routeIs('kader.jadwal.*') ? 'bg-kia-500 text-white' : 'text-text-medium' }}">Jadwal Posyandu</a>
                <a href="{{ route('kader.laporan.posyandu') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold {{ request()->routeIs('kader.laporan.posyandu') ? 'bg-kia-500 text-white' : 'text-text-medium' }}">Laporan Posyandu</a>
            @elseif(Auth::user()->isBidan())
                <a href="{{ route('bidan.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold {{ request()->routeIs('bidan.dashboard') ? 'bg-kia-500 text-white' : 'text-text-medium' }}">Dashboard</a>
                <a href="{{ route('bidan.validasi.balita') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold {{ request()->routeIs('bidan.validasi.balita') ? 'bg-kia-500 text-white' : 'text-text-medium' }}">Validasi Balita</a>
                <a href="{{ route('bidan.validasi.bumil') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold {{ request()->routeIs('bidan.validasi.bumil') ? 'bg-kia-500 text-white' : 'text-text-medium' }}">Validasi Bumil</a>
                <a href="{{ route('bidan.darurat') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold {{ request()->routeIs('bidan.darurat') ? 'bg-kia-500 text-white' : 'text-text-medium' }}">Pemeriksaan Darurat</a>
                <a href="{{ route('jadwal.riwayat') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold {{ request()->routeIs('jadwal.riwayat') ? 'bg-kia-500 text-white' : 'text-text-medium' }}">Riwayat Jadwal</a>
                <a href="{{ route('bidan.laporan.posyandu') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold {{ request()->routeIs('bidan.laporan.posyandu') ? 'bg-kia-500 text-white' : 'text-text-medium' }}">Laporan Posyandu</a>
                <a href="{{ route('bidan.laporan.darurat') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold {{ request()->routeIs('bidan.laporan.darurat') ? 'bg-kia-500 text-white' : 'text-text-medium' }}">Laporan Darurat</a>
            @elseif(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold {{ request()->routeIs('admin.dashboard') ? 'bg-kia-500 text-white' : 'text-text-medium' }}">Dashboard</a>
                <a href="{{ route('admin.kelola-akun') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold {{ request()->routeIs('admin.kelola-akun*') ? 'bg-kia-500 text-white' : 'text-text-medium' }}">Kelola Akun</a>
                <a href="{{ route('jadwal.riwayat') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold {{ request()->routeIs('jadwal.riwayat') ? 'bg-kia-500 text-white' : 'text-text-medium' }}">Riwayat Jadwal</a>
                <a href="{{ route('admin.laporan-posyandu') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold {{ request()->routeIs('admin.laporan-posyandu') ? 'bg-kia-500 text-white' : 'text-text-medium' }}">Laporan Posyandu</a>
                <a href="{{ route('admin.laporan-darurat') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-semibold {{ request()->routeIs('admin.laporan-darurat') ? 'bg-kia-500 text-white' : 'text-text-medium' }}">Laporan Darurat</a>
            @endif
        </nav>
        <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="p-4 border-t border-kia-200">
            @csrf
            <button type="button" onclick="konfirmasiLogout()" class="flex items-center justify-center gap-2 w-full px-3 py-2.5 rounded-md bg-red-50 text-red-600 text-sm font-semibold">
                Logout
            </button>
        </form>
    </aside>

    <!-- ========================================== -->
    <!-- 4. TOAST NOTIFICATIONS (Alpine.js) -->
    <!-- ========================================== -->
    <div class="fixed top-24 right-4 md:right-8 z-[9999] flex flex-col gap-3 max-w-sm w-full pointer-events-none">
        
        <!-- Toast Validation Errors -->
        @if($errors->any())
        <div x-data="{ show: true }" x-show="show" x-cloak x-init="setTimeout(() => show = false, 6000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-8"
             class="pointer-events-auto bg-white border-l-4 border-red-500 shadow-xl rounded-xl p-4 flex items-start gap-3 relative overflow-hidden">
            <div class="text-red-500 mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="flex-1 text-sm font-medium text-gray-800">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @endif

        <!-- Toast Success -->
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-cloak x-init="setTimeout(() => show = false, 4000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-8"
             class="pointer-events-auto bg-white border-l-4 border-emerald-500 shadow-xl rounded-xl p-4 flex items-start gap-3 relative overflow-hidden">
            <div class="text-emerald-500 mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="flex-1 text-sm font-medium text-gray-800">
                {{ session('success') }}
            </div>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @endif

        <!-- Toast Error -->
        @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-cloak x-init="setTimeout(() => show = false, 5000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-8"
             class="pointer-events-auto bg-white border-l-4 border-red-500 shadow-xl rounded-xl p-4 flex items-start gap-3 relative overflow-hidden">
            <div class="text-red-500 mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="flex-1 text-sm font-medium text-gray-800">
                {{ session('error') }}
            </div>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @endif

    </div>

    <!-- ========================================== -->
    <!-- 5. SCRIPTS -->
    <!-- ========================================== -->
    <script>
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('mobile-sidebar');
            const overlay = document.getElementById('mobile-sidebar-overlay');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }

        function konfirmasiLogout() {
            if (confirm('Apakah Anda yakin ingin keluar dari sistem?\n\nAnda harus masuk kembali untuk mengakses.')) {
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
                    if (data.success) window.location.replace(data.redirect);
                    else document.getElementById('logout-form-desktop').submit();
                })
                .catch(function() { document.getElementById('logout-form-desktop').submit(); });
            }
        }
    </script>
    @stack('scripts')
</body>
</html>