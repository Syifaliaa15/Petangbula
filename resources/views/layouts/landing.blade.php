<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <title>@yield('title', 'PETANGBULA - Pemantauan Tumbuh Kembang Ibu Hamil dan Balita')">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans bg-kia-50 text-text-dark antialiased">

    {{-- NAVBAR --}}
    <header class="sticky top-0 z-50 bg-kia-50 border-b border-kia-200">
        <div class="max-w-7xl mx-auto px-4 md:px-6 py-3 md:py-4 flex items-center justify-between gap-3 md:gap-6">
            
            <a href="{{ route('home') }}" class="flex-shrink-0">
                <img src="{{ asset('images/Logo.png') }}" alt="Petangbula" class="h-8 md:h-11 w-auto">
            </a>

            <nav class="hidden md:flex gap-7 items-center flex-1 justify-center">
                <a href="#home" class="nav-link text-sm text-text-medium hover:text-kia-500 font-medium transition-colors">Beranda</a>
                <a href="#edukasi" class="nav-link text-sm text-text-medium hover:text-kia-500 font-medium transition-colors">Informasi & Edukasi</a>
                <a href="#jadwal" class="nav-link text-sm text-text-medium hover:text-kia-500 font-medium transition-colors">Jadwal Posyandu</a>
                <a href="#cek-gizi" class="nav-link text-sm text-text-medium hover:text-kia-500 font-medium transition-colors">Cek Gizi</a>
            </nav>

            <div class="flex items-center gap-2 md:gap-3">
                @guest
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-3 md:px-5 py-2 border-2 border-kia-500 text-kia-500 rounded-md text-xs md:text-sm font-semibold hover:bg-kia-500 hover:text-white transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                        <span class="hidden sm:inline">Login</span>
                    </a>
                @else
                    <div class="hidden sm:block text-right leading-tight">
                        <div class="text-xs md:text-sm font-bold text-kia-900">{{ Auth::user()->name }}</div>
                        <div class="text-[10px] md:text-xs text-kia-500 uppercase font-semibold tracking-wide">{{ Auth::user()->role }}</div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-3 md:px-5 py-2 bg-kia-500 text-white rounded-md text-xs md:text-sm font-semibold hover:bg-kia-600 transition-all">
                            Keluar
                        </button>
                    </form>
                @endguest
            </div>
        </div>
    </header>

    @yield('content')

    {{-- FOOTER --}}
    <footer class="bg-kia-100 border-t border-kia-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 md:px-6 py-5 text-center text-xs md:text-sm text-text-medium">
            &copy; {{ date('Y') }} PETANGBULA — Posyandu Desa Sindangpanon. Hak cipta dilindungi.
        </div>
    </footer>

    @stack('scripts')
</body>
</html>