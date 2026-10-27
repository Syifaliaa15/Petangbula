@extends('layouts.dashboard')

@section('title', 'Input Pemeriksaan - ' . $balita->nama)
@section('page-title', 'Input Pemeriksaan Bulanan')
@section('page-subtitle', $balita->nama . ' | Bulan Ke-' . $target_bulan)

@section('content')
    <div class="max-w-3xl mx-auto">
        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mb-6 shadow-sm">
                <div class="font-bold mb-1">Terdapat kesalahan:</div>
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($pemeriksaan && $pemeriksaan->status_review === 'butuh_perbaikan')
            <div class="bg-amber-50 border-l-4 border-amber-500 text-amber-700 p-4 rounded-xl mb-6 shadow-sm">
                <strong>📝 Catatan Koreksi dari Bidan:</strong><br>
                <em>"{{ $pemeriksaan->catatan_bidan }}"</em>
            </div>
        @endif

        <form action="{{ route('balita.storeCheckup', $balita->id) }}" method="POST" class="bg-white border border-kia-100 rounded-2xl shadow-sm p-6 md:p-8">
            @csrf
            <input type="hidden" name="target_bulan" value="{{ $target_bulan }}">

            <!-- Info Alert -->
            <div class="bg-kia-50/50 border border-kia-100 text-kia-800 p-4 rounded-xl mb-6 text-sm flex items-start gap-3">
                <svg class="w-5 h-5 text-kia-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    Usia balita saat ini <strong>{{ $balita->umur_lengkap }}</strong>.<br>
                    Target input: <strong>Bulan ke-{{ $target_bulan }}</strong>.
                </div>
            </div>

            <!-- Data Pengukuran -->
            <h3 class="text-lg font-bold text-kia-900 border-b border-kia-100 pb-3 mb-5">Data Pengukuran</h3>
            
            {{-- Pilihan Cara Ukur --}}
            <div class="mb-5 bg-kia-50/50 p-4 rounded-xl border border-kia-200">
                <label class="block text-sm font-bold text-kia-900 mb-2">Cara Ukur Tinggi Badan <span class="text-red-500">*</span></label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="cara_ukur" value="tidur" {{ old('cara_ukur', $pemeriksaan->cara_ukur ?? 'tidur') == 'tidur' ? 'checked' : '' }} required class="w-4 h-4 accent-kia-500">
                        <span class="text-sm text-gray-700">Telentang (Panjang Badan / PB)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="cara_ukur" value="berdiri" {{ old('cara_ukur', $pemeriksaan->cara_ukur ?? '') == 'berdiri' ? 'checked' : '' }} required class="w-4 h-4 accent-kia-500">
                        <span class="text-sm text-gray-700">Berdiri (Tinggi Badan / TB)</span>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                <div>
                    <label class="block text-sm font-bold text-kia-900 mb-1.5">Tanggal Pemeriksaan <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_periksa" class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all" value="{{ $pemeriksaan ? $pemeriksaan->tanggal_periksa->format('Y-m-d') : date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-kia-900 mb-1.5">Berat Badan (kg) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="berat_badan" class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all" value="{{ $pemeriksaan ? $pemeriksaan->berat_badan : old('berat_badan') }}" placeholder="Contoh: 8.5" min="0.5" max="50" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-kia-900 mb-1.5">Tinggi/Panjang Badan (cm) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.1" name="tinggi_badan" class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all" value="{{ $pemeriksaan ? $pemeriksaan->tinggi_badan : old('tinggi_badan') }}" placeholder="Contoh: 75.0" min="30" max="150" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-kia-900 mb-1.5">Lingkar Kepala / LIKA (cm) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.1" name="lingkar_kepala" class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all" value="{{ $pemeriksaan ? $pemeriksaan->lingkar_kepala : old('lingkar_kepala') }}" placeholder="Contoh: 45.0" min="20" max="70" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-kia-900 mb-1.5">Lingkar Lengan Atas / LILA (cm) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.1" name="lila" class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all" value="{{ $pemeriksaan ? $pemeriksaan->lila : old('lila') }}" placeholder="Contoh: 15.0" min="10" max="30" required>
                </div>
            </div>

            <!-- Imunisasi -->
            <h3 class="text-lg font-bold text-kia-900 border-b border-kia-100 pb-3 mb-5">💉 Imunisasi yang Diberikan</h3>
            @php
                $imunisasiList = ['HB-0', 'BCG', 'Polio 1', 'DPT-HB-Hib 1', 'Polio 2', 'DPT-HB-Hib 2', 'Polio 3', 'DPT-HB-Hib 3', 'Polio 4', 'IPV', 'Campak/MR'];
                $imunisasiSudah = $pemeriksaan ? ($pemeriksaan->imunisasi ?? []) : [];
            @endphp
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-8">
                @foreach($imunisasiList as $vaksin)
                    <label class="flex items-center gap-2.5 p-3 border border-kia-200 rounded-xl cursor-pointer hover:bg-kia-50 transition-colors text-sm font-medium text-gray-700">
                        <input type="checkbox" name="imunisasi[]" value="{{ $vaksin }}" {{ in_array($vaksin, $imunisasiSudah) ? 'checked' : '' }} class="w-4 h-4 accent-kia-500 rounded">
                        {{ $vaksin }}
                    </label>
                @endforeach
            </div>


            <!-- Suplementasi -->
            <h3 class="text-lg font-bold text-kia-900 border-b border-kia-100 pb-3 mb-5">💊 Suplementasi & Program</h3>
            <div class="mb-8 flex flex-col gap-3">
                <label class="flex items-center gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl cursor-pointer hover:bg-amber-100 transition-colors">
                    <input type="checkbox" name="vitamin_a" value="1" {{ ($pemeriksaan && $pemeriksaan->vitamin_a) ? 'checked' : '' }} class="w-5 h-5 accent-amber-500 rounded">
                    <span class="font-bold text-amber-800">Vitamin A diberikan pada bulan ini</span>
                </label>
                
                <label class="flex items-center gap-3 p-4 bg-kia-50 border border-kia-200 rounded-xl cursor-pointer hover:bg-kia-100 transition-colors">
                    <input type="checkbox" name="asi_eksklusif" value="1" {{ ($pemeriksaan && $pemeriksaan->asi_eksklusif) ? 'checked' : '' }} class="w-5 h-5 accent-kia-500 rounded">
                    <span class="font-bold text-kia-800">Mendapatkan ASI Eksklusif</span>
                </label>
            </div>

            <!-- Catatan -->
            <div class="mb-8">
                <label class="block text-sm font-bold text-kia-900 mb-1.5">Catatan / Keterangan (Opsional)</label>
                <textarea name="keterangan" class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all" rows="3" placeholder="Catatan tambahan...">{{ $pemeriksaan ? $pemeriksaan->keterangan : old('keterangan') }}</textarea>
            </div>

            <!-- Tombol Aksi -->
            <div class="pt-6 border-t border-kia-100 flex flex-col md:flex-row gap-3">
                <button type="submit" class="w-full md:w-auto px-8 py-3 bg-gradient-to-r from-kia-500 to-kia-600 text-white rounded-xl font-bold shadow-md hover:shadow-lg transition-all">
                    {{ $pemeriksaan ? '💾 Perbarui Data' : '💾 Simpan' }}
                </button>
                <a href="{{ route('balita.show', $balita->id) }}" class="w-full md:w-auto px-8 py-3 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition-colors text-center border border-gray-200">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection