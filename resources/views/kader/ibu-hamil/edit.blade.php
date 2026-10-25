@extends('layouts.dashboard')

@section('hide_header') 
@endsection

@section('title', 'Edit Data Ibu Hamil')
@section('page-title', 'Edit Data Ibu Hamil')
@section('page-subtitle', 'Perbarui informasi data ibu hamil')

@section('content')
    <div class="bg-white border border-kia-200 rounded-lg shadow-sm p-6 max-w-4xl mx-auto">
        
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md mb-6">
                <ul class="list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('ibu-hamil.update', $ibuHamil->id) }}" method="POST">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Kolom Kiri: Data Ibu -->
                <div>
                    <h3 class="text-lg font-bold text-kia-900 border-b border-kia-200 pb-2 mb-4">Data Ibu</h3>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-kia-900 mb-1.5">NIK (16 Digit) <span class="text-red-500">*</span></label>
                        <input type="text" name="nik" value="{{ old('nik', $ibuHamil->nik) }}" maxlength="16" inputmode="numeric" required
                               class="w-full px-3 py-2 text-sm border border-kia-300 rounded-md focus:border-kia-500 focus:ring-1 focus:ring-kia-500 outline-none transition-all">
                        @error('nik') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-kia-900 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama', $ibuHamil->nama) }}" required
                               class="w-full px-3 py-2 text-sm border border-kia-300 rounded-md focus:border-kia-500 focus:ring-1 focus:ring-kia-500 outline-none transition-all">
                        @error('nama') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex gap-4 mb-4">
                        <div class="flex-1">
                            <label class="block text-sm font-bold text-kia-900 mb-1.5">Umur (Tahun) <span class="text-red-500">*</span></label>
                            <input type="number" name="umur" value="{{ old('umur', $ibuHamil->umur) }}" min="15" max="50" required
                                   class="w-full px-3 py-2 text-sm border border-kia-300 rounded-md focus:border-kia-500 focus:ring-1 focus:ring-kia-500 outline-none transition-all">
                            @error('umur') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-bold text-kia-900 mb-1.5">Hamil Ke- <span class="text-red-500">*</span></label>
                            <input type="number" name="hamil_ke" value="{{ old('hamil_ke', $ibuHamil->hamil_ke) }}" min="1" max="10" required
                                   class="w-full px-3 py-2 text-sm border border-kia-300 rounded-md focus:border-kia-500 focus:ring-1 focus:ring-kia-500 outline-none transition-all">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-kia-900 mb-1.5">Kelompok Dasawisma</label>
                        <input type="text" name="klmp_dasawisma" value="{{ old('klmp_dasawisma', $ibuHamil->klmp_dasawisma) }}"
                               class="w-full px-3 py-2 text-sm border border-kia-300 rounded-md focus:border-kia-500 focus:ring-1 focus:ring-kia-500 outline-none transition-all">
                    </div>
                </div>

                <!-- Kolom Kanan: Data Kehamilan & Alamat -->
                <div>
                    <h3 class="text-lg font-bold text-kia-900 border-b border-kia-200 pb-2 mb-4">Data Kehamilan & Alamat</h3>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-kia-900 mb-1.5">Tanggal Pendaftaran / HPHT <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_pendaftaran" value="{{ old('tanggal_pendaftaran', $ibuHamil->tanggal_pendaftaran ? $ibuHamil->tanggal_pendaftaran->format('Y-m-d') : '') }}" required
                               class="w-full px-3 py-2 text-sm border border-kia-300 rounded-md focus:border-kia-500 focus:ring-1 focus:ring-kia-500 outline-none transition-all">
                        @error('tanggal_pendaftaran') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-kia-900 mb-1.5">Nama Suami</label>
                        <input type="text" name="nama_suami" value="{{ old('nama_suami', $ibuHamil->nama_suami) }}"
                               class="w-full px-3 py-2 text-sm border border-kia-300 rounded-md focus:border-kia-500 focus:ring-1 focus:ring-kia-500 outline-none transition-all">
                        @error('nama_suami') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-kia-900 mb-1.5">Alamat (RT & Dusun)</label>
                        <div class="flex gap-2">
                            <input type="text" name="rt" value="{{ old('rt', $ibuHamil->rt) }}" placeholder="RT" class="w-1/3 px-3 py-2 text-sm border border-kia-300 rounded-md focus:border-kia-500 focus:ring-1 focus:ring-kia-500 outline-none transition-all text-center">
                            <input type="text" name="alamat" value="{{ old('alamat', $ibuHamil->alamat) }}" placeholder="Nama Dusun / Jalan" class="w-2/3 px-3 py-2 text-sm border border-kia-300 rounded-md focus:border-kia-500 focus:ring-1 focus:ring-kia-500 outline-none transition-all">
                        </div>
                        <p class="text-xs text-kia-500 mt-2 italic">* RW otomatis terisi sesuai Posyandu Anda.</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-kia-200 flex gap-3">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-kia-500 text-white rounded-md text-sm font-semibold hover:bg-kia-600 transition-colors shadow-sm">
                    Perbarui Data
                </button>
                <a href="{{ route('ibu-hamil.index') }}" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-md text-sm font-semibold hover:bg-gray-200 transition-colors text-center border border-gray-300">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection