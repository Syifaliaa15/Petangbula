@extends('layouts.dashboard')

@section('hide_header') 
@endsection

@section('title', 'Registrasi Ibu Hamil')
@section('page-title', 'Registrasi Ibu Hamil Baru')
@section('page-subtitle', 'Lengkapi data awal untuk rekam medis 40 minggu')

@section('content')
    <!-- Header halaman dibuat super ringkas -->
    <div class="mb-4">
        <h1 class="text-xl font-extrabold text-kia-900">Registrasi Ibu Hamil Baru</h1>
    </div>

    <div class="bg-white border border-kia-200 rounded-lg shadow-sm p-5 max-w-4xl mx-auto">
        
        <!-- ALERT ERROR (Dikecilkan) -->
        @if ($errors->any())
            <div class="bg-red-50 text-red-700 text-xs px-3 py-2 rounded-md mb-4 border-l-4 border-red-500 flex items-start gap-2">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <div>
                    @foreach ($errors->all() as $error)
                        <div class="font-medium">{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <form action="{{ route('ibu-hamil.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kolom Kiri: Data Ibu -->
                <div>
                    <h3 class="text-sm font-bold text-kia-900 border-b border-kia-200 pb-2 mb-3">Data Ibu</h3>

                    <div class="mb-3">
                        <label class="block text-xs font-bold text-kia-800 mb-1">NIK (16 Digit) <span class="text-red-500">*</span></label>
                        <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16" inputmode="numeric" required placeholder="Masukkan 16 digit NIK"
                               class="w-full px-3 py-1.5 text-sm border border-kia-300 rounded-md focus:border-kia-500 focus:ring-1 focus:ring-kia-500 outline-none transition-all">
                    </div>

                    <div class="mb-3">
                        <label class="block text-xs font-bold text-kia-800 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Nama sesuai KTP"
                               class="w-full px-3 py-1.5 text-sm border border-kia-300 rounded-md focus:border-kia-500 focus:ring-1 focus:ring-kia-500 outline-none transition-all">
                    </div>

                    <div class="flex gap-3 mb-3">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-kia-800 mb-1">Umur (Tahun) <span class="text-red-500">*</span></label>
                            <input type="number" name="umur" value="{{ old('umur') }}" min="15" max="50" required
                                   class="w-full px-3 py-1.5 text-sm border border-kia-300 rounded-md focus:border-kia-500 focus:ring-1 focus:ring-kia-500 outline-none transition-all">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-kia-800 mb-1">Hamil Ke- <span class="text-red-500">*</span></label>
                            <input type="number" name="hamil_ke" value="{{ old('hamil_ke') }}" min="1" max="10" required
                                   class="w-full px-3 py-1.5 text-sm border border-kia-300 rounded-md focus:border-kia-500 focus:ring-1 focus:ring-kia-500 outline-none transition-all">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="block text-xs font-bold text-kia-800 mb-1">Kelompok Dasawisma</label>
                        <input type="text" name="klmp_dasawisma" value="{{ old('klmp_dasawisma') }}" placeholder="Contoh: Dasawisma Melati 2"
                               class="w-full px-3 py-1.5 text-sm border border-kia-300 rounded-md focus:border-kia-500 focus:ring-1 focus:ring-kia-500 outline-none transition-all">
                    </div>
                </div>

                <!-- Kolom Kanan: Data Kehamilan & Alamat -->
                <div>
                    <h3 class="text-sm font-bold text-kia-900 border-b border-kia-200 pb-2 mb-3">Data Kehamilan & Alamat</h3>

                    <div class="mb-3">
                        <label class="block text-xs font-bold text-kia-800 mb-1">Tanggal Pendaftaran / HPHT <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_pendaftaran" value="{{ old('tanggal_pendaftaran') }}" max="{{ date('Y-m-d') }}" required
                               class="w-full px-3 py-1.5 text-sm border border-kia-300 rounded-md focus:border-kia-500 focus:ring-1 focus:ring-kia-500 outline-none transition-all">
                        <p class="text-[11px] text-kia-500 mt-1 italic">Hari Pertama Haid Terakhir</p>
                    </div>

                    <div class="mb-3">
                        <label class="block text-xs font-bold text-kia-800 mb-1">Nama Suami</label>
                        <input type="text" name="nama_suami" value="{{ old('nama_suami') }}" placeholder="Nama lengkap suami"
                               class="w-full px-3 py-1.5 text-sm border border-kia-300 rounded-md focus:border-kia-500 focus:ring-1 focus:ring-kia-500 outline-none transition-all">
                    </div>

                    <div class="mb-3">
                        <label class="block text-xs font-bold text-kia-800 mb-1">Alamat (RT & Dusun)</label>
                        <!-- Perbaikan Grid Alamat -->
                        <div class="grid grid-cols-4 gap-2">
                            <input type="text" name="rt" value="{{ old('rt') }}" placeholder="RT" class="col-span-1 px-3 py-1.5 text-sm border border-kia-300 rounded-md focus:border-kia-500 focus:ring-1 focus:ring-kia-500 outline-none transition-all text-center">
                            <input type="text" name="alamat" value="{{ old('alamat') }}" placeholder="Nama Dusun / Jalan" class="col-span-3 px-3 py-1.5 text-sm border border-kia-300 rounded-md focus:border-kia-500 focus:ring-1 focus:ring-kia-500 outline-none transition-all w-full">
                        </div>
                        <p class="text-[11px] text-kia-500 mt-1 italic">* RW terisi otomatis.</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-kia-200 flex gap-2">
                <!-- Tombol diperpendek -->
                <button type="submit" class="px-6 py-2 bg-kia-500 text-white rounded-md text-sm font-semibold hover:bg-kia-600 transition-colors shadow-sm">
                    Simpan
                </button>
                <a href="{{ route('ibu-hamil.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-semibold hover:bg-gray-200 transition-colors text-center border border-gray-300">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection