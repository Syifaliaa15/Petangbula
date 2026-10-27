@extends('layouts.dashboard')

@section('title', 'Input Pemeriksaan Minggu Ke-' . $target_minggu)
@section('page-title', 'Input Pemeriksaan Mingguan')
@section('page-subtitle', $ibuHamil->nama . ' | Minggu Ke-' . $target_minggu)

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
                <strong>📝 Alasan Koreksi Medis dari Bidan:</strong><br>
                <em>"{{ $pemeriksaan->catatan_bidan }}"</em>
            </div>
        @endif

        <form action="{{ route('ibu-hamil.storeCheckup', $ibuHamil->id) }}" method="POST" class="bg-white border border-kia-100 rounded-2xl shadow-sm p-6 md:p-8">
            @csrf
            <input type="hidden" name="target_minggu" value="{{ $target_minggu }}">

            <!-- DATA PENGUKURAN -->
            <h3 class="text-lg font-bold text-kia-900 border-b border-kia-100 pb-3 mb-4">Data Pengukuran dan Pemeriksaan</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <!-- Kolom Kiri -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-kia-900 mb-1">BB Sebelum Hamil (Kg) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.1" min="30" name="bb_pra_hamil" class="w-full px-3 py-2 text-sm border border-kia-200 rounded-lg bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all" value="{{ old('bb_pra_hamil', $pemeriksaan ? $pemeriksaan->bb_pra_hamil : '') }}" placeholder="Contoh: 50" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-kia-900 mb-1">BB Saat Ini (Kg) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.1" name="bb" class="w-full px-3 py-2 text-sm border border-kia-200 rounded-lg bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all" value="{{ old('bb', $pemeriksaan ? $pemeriksaan->bb : '') }}" placeholder="Contoh: 55" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-kia-900 mb-1">Lingkar Perut (Cm) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.1" name="lingkar_perut" class="w-full px-3 py-2 text-sm border border-kia-200 rounded-lg bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all" value="{{ old('lingkar_perut', $pemeriksaan ? $pemeriksaan->lingkar_perut : '') }}" placeholder="Contoh: 80" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-kia-900 mb-1">Tekanan Darah</label>
                        <input type="text" name="tekanan_darah" class="w-full px-3 py-2 text-sm border border-kia-200 rounded-lg bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all" value="{{ old('tekanan_darah', $pemeriksaan ? $pemeriksaan->tekanan_darah : '') }}" placeholder="Contoh: 120/80">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-kia-900 mb-1">Kategori TFU</label>
                        <select name="kategori_tfu" class="w-full px-3 py-2 text-sm border border-kia-200 rounded-lg bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all">
                            <option value="">-- Pilih --</option>
                            <option value="Sesuai Masa Kehamilan" {{ old('kategori_tfu', $pemeriksaan->kategori_tfu ?? '') == 'Sesuai Masa Kehamilan' ? 'selected' : '' }}>Sesuai Masa Kehamilan</option>
                            <option value="Tidak Sesuai" {{ old('kategori_tfu', $pemeriksaan->kategori_tfu ?? '') == 'Tidak Sesuai' ? 'selected' : '' }}>Tidak Sesuai</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-kia-900 mb-1">Kategori Janin</label>
                        <select name="kategori_janin" class="w-full px-3 py-2 text-sm border border-kia-200 rounded-lg bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all">
                            <option value="">-- Pilih --</option>
                            <option value="Normal" {{ old('kategori_janin', $pemeriksaan->kategori_janin ?? '') == 'Normal' ? 'selected' : '' }}>Normal</option>
                            <option value="Besar" {{ old('kategori_janin', $pemeriksaan->kategori_janin ?? '') == 'Besar' ? 'selected' : '' }}>Besar</option>
                            <option value="Kecil" {{ old('kategori_janin', $pemeriksaan->kategori_janin ?? '') == 'Kecil' ? 'selected' : '' }}>Kecil</option>
                        </select>
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-kia-900 mb-1">Tinggi Badan (cm) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.1" min="100" name="tinggi_badan" class="w-full px-3 py-2 text-sm border border-kia-200 rounded-lg bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all" value="{{ old('tinggi_badan', $pemeriksaan ? $pemeriksaan->tinggi_badan : '') }}" placeholder="Contoh: 155" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-kia-900 mb-1">Lingkar Lengan Atas / LiLA (Cm) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.1" name="lila" class="w-full px-3 py-2 text-sm border border-kia-200 rounded-lg bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all" value="{{ old('lila', $pemeriksaan ? $pemeriksaan->lila : '') }}" placeholder="Contoh: 24" required>
                        <p class="text-[10px] text-gray-500 mt-1 italic">* &lt; 23.5 cm berisiko KEK</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-kia-900 mb-1">Hemoglobin / HB (g/dL)</label>
                        <input type="number" step="0.1" name="hemoglobin" class="w-full px-3 py-2 text-sm border border-kia-200 rounded-lg bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all" value="{{ old('hemoglobin', $pemeriksaan ? $pemeriksaan->hemoglobin : '') }}" placeholder="Contoh: 11.5">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-kia-900 mb-1">TFU / Tinggi Fundus Uteri (Cm)</label>
                        <input type="number" step="0.1" name="tfu" class="w-full px-3 py-2 text-sm border border-kia-200 rounded-lg bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all" value="{{ old('tfu', $pemeriksaan ? $pemeriksaan->tfu : '') }}" placeholder="Contoh: 25">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-kia-900 mb-1">Taksiran Berat Janin (Gram)</label>
                        <input type="number" step="1" name="taksiran_bb_janin" class="w-full px-3 py-2 text-sm border border-kia-200 rounded-lg bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all" value="{{ old('taksiran_bb_janin', $pemeriksaan ? $pemeriksaan->taksiran_bb_janin : '') }}" placeholder="Contoh: 2500">
                    </div>
                </div>
            </div>

            <!-- SUPLEMENTASI & PROGRAM (Yang sempat hilang) -->
            <h3 class="text-lg font-bold text-kia-900 border-b border-kia-100 pb-3 mb-4 mt-8">Suplementasi dan Program</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <div>
                    <label class="block text-xs font-bold text-kia-900 mb-1">Tablet Tambah Darah (Strip) <span class="text-red-500">*</span></label>
                    <input type="number" name="tablet_tambah_darah" class="w-full px-3 py-2 text-sm border border-kia-200 rounded-lg bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all" value="{{ old('tablet_tambah_darah', $pemeriksaan ? $pemeriksaan->tablet_tambah_darah : '0') }}" min="0" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-kia-900 mb-1">Menerima PMT Pemulihan? <span class="text-red-500">*</span></label>
                    <select name="pmt_pemulihan" class="w-full px-3 py-2 text-sm border border-kia-200 rounded-lg bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all" required>
                        <option value="0" {{ old('pmt_pemulihan', $pemeriksaan ? $pemeriksaan->pmt_pemulihan : '0') == '0' ? 'selected' : '' }}>❌ Tidak Menerima</option>
                        <option value="1" {{ old('pmt_pemulihan', $pemeriksaan ? $pemeriksaan->pmt_pemulihan : '0') == '1' ? 'selected' : '' }}>✅ Ya, Menerima PMT</option>
                    </select>
                </div>
            </div>

            <!-- CATATAN -->
            <h3 class="text-lg font-bold text-kia-900 border-b border-kia-100 pb-3 mb-4 mt-8">Catatan Kader</h3>
            <div class="mb-8">
                <label class="block text-xs font-bold text-kia-900 mb-1">Catatan / Keluhan (Opsional)</label>
                <textarea name="keterangan" class="w-full px-3 py-2 text-sm border border-kia-200 rounded-lg bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all" rows="3" placeholder="Tuliskan jika ada keluhan atau catatan tambahan...">{{ old('keterangan', $pemeriksaan ? $pemeriksaan->keterangan : '') }}</textarea>
            </div>

            <!-- TOMBOL AKSI -->
            <div class="pt-6 border-t border-kia-100 flex flex-col md:flex-row gap-3">
                <button type="submit" class="w-full md:w-auto px-8 py-3 bg-gradient-to-r from-kia-500 to-kia-600 text-white rounded-xl font-bold shadow-md hover:shadow-lg transition-all text-center">
                    {{ $pemeriksaan ? '💾 Perbarui' : '💾 Simpan'}}
                </button>
                <a href="{{ route('ibu-hamil.show', $ibuHamil->id) }}" class="w-full md:w-auto px-8 py-3 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition-colors text-center border border-gray-200">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection