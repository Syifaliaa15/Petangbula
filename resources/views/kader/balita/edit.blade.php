@extends('layouts.dashboard')

@section('hide_header') 
@endsection

@section('title', 'Edit Data Balita')
@section('page-title', 'Edit Data Balita')
@section('page-subtitle', 'Perbarui informasi data balita')

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

        <form action="{{ route('balita.update', $balita->id) }}" method="POST">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Kolom Kiri: Data Anak -->
                <div>
                    <h3 class="text-lg font-bold text-kia-900 border-b border-kia-200 pb-2 mb-4">Data Anak</h3>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-kia-900 mb-1.5">Nama Lengkap Balita <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama', $balita->nama) }}" required
                               class="w-full px-3 py-2 text-sm border border-kia-300 rounded-md focus:border-kia-500 focus:ring-1 focus:ring-kia-500 outline-none transition-all">
                        @error('nama') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-kia-900 mb-1.5">NIK (16 Digit)</label>
                        <input type="text" name="nik" value="{{ old('nik', $balita->nik) }}" maxlength="16" inputmode="numeric"
                               class="w-full px-3 py-2 text-sm border border-kia-300 rounded-md focus:border-kia-500 focus:ring-1 focus:ring-kia-500 outline-none transition-all">
                        @error('nik') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-kia-900 mb-1.5">Tanggal Lahir <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $balita->tanggal_lahir->format('Y-m-d')) }}" required
                               class="w-full px-3 py-2 text-sm border border-kia-300 rounded-md focus:border-kia-500 focus:ring-1 focus:ring-kia-500 outline-none transition-all">
                        @error('tanggal_lahir') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-kia-900 mb-1.5">Jenis Kelamin <span class="text-red-500">*</span></label>
                        <select name="jenis_kelamin" required class="w-full px-3 py-2 text-sm border border-kia-300 rounded-md focus:border-kia-500 focus:ring-1 focus:ring-kia-500 outline-none transition-all bg-white">
                            <option value="L" {{ old('jenis_kelamin', $balita->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $balita->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>

                <!-- Kolom Kanan: Data Orang Tua & Alamat -->
                <div>
                    <h3 class="text-lg font-bold text-kia-900 border-b border-kia-200 pb-2 mb-4">Data Orang Tua & Alamat</h3>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-kia-900 mb-1.5">Nama Ibu Kandung <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_ibu" value="{{ old('nama_ibu', $balita->nama_ibu) }}" required
                               class="w-full px-3 py-2 text-sm border border-kia-300 rounded-md focus:border-kia-500 focus:ring-1 focus:ring-kia-500 outline-none transition-all">
                        @error('nama_ibu') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-kia-900 mb-1.5">Nama Ayah (Opsional)</label>
                        <input type="text" name="nama_ayah" value="{{ old('nama_ayah', $balita->nama_ayah) }}"
                               class="w-full px-3 py-2 text-sm border border-kia-300 rounded-md focus:border-kia-500 focus:ring-1 focus:ring-kia-500 outline-none transition-all">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-kia-900 mb-1.5">Alamat (RT & Dusun)</label>
                        <div class="flex gap-2">
                            <input type="text" name="rt" value="{{ old('rt', $balita->rt) }}" placeholder="RT" class="w-1/3 px-3 py-2 text-sm border border-kia-300 rounded-md focus:border-kia-500 focus:ring-1 focus:ring-kia-500 outline-none transition-all text-center">
                            <input type="text" name="alamat" value="{{ old('alamat', $balita->alamat) }}" placeholder="Nama Dusun / Jalan" class="w-2/3 px-3 py-2 text-sm border border-kia-300 rounded-md focus:border-kia-500 focus:ring-1 focus:ring-kia-500 outline-none transition-all">
                        </div>
                        <p class="text-xs text-kia-500 mt-2 italic">* RW otomatis terisi sesuai Posyandu Anda.</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-kia-200 flex gap-3">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-kia-500 text-white rounded-md text-sm font-semibold hover:bg-kia-600 transition-colors shadow-sm">
                    Perbarui Data
                </button>
                <a href="{{ route('balita.index') }}" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-md text-sm font-semibold hover:bg-gray-200 transition-colors text-center border border-gray-300">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection