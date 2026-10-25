<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <title>Login - PETANGBULA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-kia-50 font-sans text-text-dark min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-sm border border-kia-200 p-8 relative overflow-hidden">
        
        <!-- Aksen Garis Atas -->
        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-kia-400 via-kia-500 to-kia-600"></div>

        <!-- Logo Icon -->
        <div class="w-20 h-20 bg-gradient-to-br from-kia-400 to-kia-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-md shadow-kia-200">
            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
        </div>

        <!-- Header Title -->
        <h1 class="text-2xl font-extrabold text-kia-900 text-center tracking-widest mb-1">PETANGBULA</h1>
        <p class="text-sm text-text-medium text-center mb-8">Masuk untuk mengakses sistem posyandu</p>

        <!-- Alert Error -->
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md mb-6">
                <div class="flex items-center gap-2 mb-1">
                    <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span class="text-sm font-bold text-red-700">Gagal Masuk</span>
                </div>
                <ul class="list-disc list-inside text-xs text-red-600 ml-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded-md mb-6 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        <!-- Form Login -->
        <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Input Username -->
            <div>
                <label for="username" class="block text-sm font-bold text-kia-900 mb-2">Username</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-kia-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" 
                           class="block w-full pl-10 pr-3 py-2.5 border border-kia-300 rounded-md bg-kia-50 text-text-dark focus:border-kia-500 focus:ring-2 focus:ring-kia-200 outline-none transition-all sm:text-sm" 
                           placeholder="Masukkan username" required autofocus>
                </div>
            </div>

            <!-- Input Password -->
            <div>
                <label for="password" class="block text-sm font-bold text-kia-900 mb-2">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-kia-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="password" id="password" name="password" 
                           class="block w-full pl-10 pr-3 py-2.5 border border-kia-300 rounded-md bg-kia-50 text-text-dark focus:border-kia-500 focus:ring-2 focus:ring-kia-200 outline-none transition-all sm:text-sm" 
                           placeholder="Masukkan password" required>
                </div>
            </div>

            <!-- Tombol Masuk -->
            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-kia-500 hover:bg-kia-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-kia-500 transition-all mt-2">
                Masuk
            </button>
        </form>

        <!-- Kembali ke Beranda -->
        <div class="mt-8 text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-text-medium hover:text-kia-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Beranda
            </a>
        </div>

    </div>

</body>
</html>