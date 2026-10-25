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

            <h3 class="text-lg font-bold text-kia-900 border-b border-kia-100 pb-3 mb-5">Data Pengukuran</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                <div>
                    <label class="block text-sm font-bold text-kia-900 mb-1.5">Berat Badan (Kg) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="bb" class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all" value="{{ old('bb', $pemeriksaan ? $pemeriksaan->bb : '') }}" placeholder="Contoh: 55.5" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-kia-900 mb-1.5">Lingkar Lengan Atas / LiLA (Cm) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.1" name="lila" class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all" value="{{ old('lila', $pemeriksaan ? $pemeriksaan->lila : '') }}" placeholder="Contoh: 24.5" required>
                    <p class="text-[11px] text-gray-500 mt-1 italic">* &lt; 23.5 cm berisiko KEK</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-kia-900 mb-1.5">Hemoglobin / HB (g/dL)</label>
                    <input type="number" step="0.1" name="hemoglobin" class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all" value="{{ old('hemoglobin', $pemeriksaan ? $pemeriksaan->hemoglobin : '') }}" placeholder="Contoh: 11.5">
                    <p class="text-[11px] text-gray-500 mt-1 italic">* &lt; 11.0 g/dL indikasi Anemia</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-kia-900 mb-1.5">Tekanan Darah (Opsional)</label>
                    <input type="text" name="tekanan_darah" class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all" value="{{ old('tekanan_darah', $pemeriksaan ? $pemeriksaan->tekanan_darah : '') }}" placeholder="Contoh: 120/80">
                </div>
            </div>

            <h3 class="text-lg font-bold text-kia-900 border-b border-kia-100 pb-3 mb-5">Suplementasi & Program</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                <div>
                    <label class="block text-sm font-bold text-kia-900 mb-1.5">Tablet Tambah Darah (Bungkus) <span class="text-red-500">*</span></label>
                    <input type="number" name="tablet_tambah_darah" class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all" value="{{ old('tablet_tambah_darah', $pemeriksaan ? $pemeriksaan->tablet_tambah_darah : '0') }}" min="0" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-kia-900 mb-1.5">Menerima PMT Pemulihan? <span class="text-red-500">*</span></label>
                    <select name="pmt_pemulihan" class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all" required>
                        <option value="0" {{ old('pmt_pemulihan', $pemeriksaan ? $pemeriksaan->pmt_pemulihan : '0') == '0' ? 'selected' : '' }}>❌ Tidak Menerima</option>
                        <option value="1" {{ old('pmt_pemulihan', $pemeriksaan ? $pemeriksaan->pmt_pemulihan : '0') == '1' ? 'selected' : '' }}>✅ Ya, Menerima PMT</option>
                    </select>
                </div>
            </div>

            <div class="mb-8">
                <label class="block text-sm font-bold text-kia-900 mb-1.5">Catatan / Keluhan</label>
                <textarea name="keterangan" class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all" rows="3" placeholder="Catatan tambahan (opsional)">{{ old('keterangan', $pemeriksaan ? $pemeriksaan->keterangan : '') }}</textarea>
            </div>

            <div class="pt-6 border-t border-kia-100 flex flex-col md:flex-row gap-3">
                <button type="submit" class="w-full md:w-auto px-8 py-3 bg-gradient-to-r from-kia-500 to-kia-600 text-white rounded-xl font-bold shadow-md hover:shadow-lg transition-all">
                    {{ $pemeriksaan ? '💾 Perbarui' : '💾 Simpan' }}
                </button>
                <a href="{{ route('ibu-hamil.show', $ibuHamil->id) }}" class="w-full md:w-auto px-8 py-3 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition-colors text-center border border-gray-200">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection