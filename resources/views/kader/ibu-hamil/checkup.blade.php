@extends('layouts.dashboard')

@section('title', 'Input Pemeriksaan Minggu Ke-' . $target_minggu)
@section('page-title', 'Input Pemeriksaan Mingguan')
@section('page-subtitle', $ibuHamil->nama . ' | Minggu Ke-' . $target_minggu)

@section('content')
    <div class="card" style="max-width: 900px; margin: 0 auto;">

        @if($errors->any())
            <div class="alert alert-error" style="flex-direction: column; align-items: flex-start;">
                <strong>⚠️ Terdapat kesalahan:</strong>
                <ul style="margin: 4px 0 0 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($pemeriksaan && $pemeriksaan->status_review === 'butuh_perbaikan')
            <div class="alert alert-warning">
                <strong>📝 Alasan Koreksi Medis dari Bidan:</strong><br>
                <em>"{{ $pemeriksaan->catatan_bidan }}"</em>
            </div>
        @endif

        <form action="{{ route('ibu-hamil.storeCheckup', $ibuHamil->id) }}" method="POST">
            @csrf
            <input type="hidden" name="target_minggu" value="{{ $target_minggu }}">

            <div class="form-grid">
                <div>
                    <div class="form-group">
                        <label>Berat Badan (Kg) <span class="required">*</span></label>
                        <input type="number" step="0.01" name="bb" class="form-control"
                            value="{{ old('bb', $pemeriksaan ? $pemeriksaan->bb : '') }}"
                            placeholder="Contoh: 55.5" required>
                    </div>

                    <div class="form-group">
                        <label>Lingkar Lengan Atas / LiLA (Cm) <span class="required">*</span></label>
                        <input type="number" step="0.1" name="lila" class="form-control"
                            value="{{ old('lila', $pemeriksaan ? $pemeriksaan->lila : '') }}"
                            placeholder="Contoh: 24.5" required>
                        <small style="color: #9ca3af; font-size: 0.75rem;">* &lt; 23.5 cm berisiko KEK</small>
                    </div>

                    <div class="form-group">
                        <label>Hemoglobin / HB (g/dL)</label>
                        <input type="number" step="0.1" name="hemoglobin" class="form-control"
                            value="{{ old('hemoglobin', $pemeriksaan ? $pemeriksaan->hemoglobin : '') }}"
                            placeholder="Contoh: 11.5">
                        <small style="color: #9ca3af; font-size: 0.75rem;">* &lt; 11.0 g/dL indikasi Anemia</small>
                    </div>
                </div>

                <div>
                    <div class="form-group">
                        <label>Tablet Tambah Darah (Bungkus) <span class="required">*</span></label>
                        <input type="number" name="tablet_tambah_darah" class="form-control"
                            value="{{ old('tablet_tambah_darah', $pemeriksaan ? $pemeriksaan->tablet_tambah_darah : '0') }}"
                            min="0" required>
                    </div>

                    <div class="form-group">
                        <label>Menerima PMT Pemulihan? <span class="required">*</span></label>
                        <select name="pmt_pemulihan" class="form-control" required>
                            <option value="0" {{ old('pmt_pemulihan', $pemeriksaan ? $pemeriksaan->pmt_pemulihan : '0') == '0' ? 'selected' : '' }}>❌ Tidak Menerima</option>
                            <option value="1" {{ old('pmt_pemulihan', $pemeriksaan ? $pemeriksaan->pmt_pemulihan : '0') == '1' ? 'selected' : '' }}>✅ Ya, Menerima PMT</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tekanan Darah (Opsional)</label>
                        <input type="text" name="tekanan_darah" class="form-control"
                            value="{{ old('tekanan_darah', $pemeriksaan ? $pemeriksaan->tekanan_darah : '') }}"
                            placeholder="Contoh: 120/80">
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-top: 20px;">
                <label>Catatan / Keluhan</label>
                <textarea name="keterangan" class="form-control" rows="3" placeholder="Catatan tambahan (opsional)">{{ old('keterangan', $pemeriksaan ? $pemeriksaan->keterangan : '') }}</textarea>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid #fbcfe8;">
                <button type="submit" class="btn-primary" style="flex: 2;">
                    {{ $pemeriksaan ? '💾 Perbarui Data Pemeriksaan' : '💾 Kirim Hasil Penimbangan ke Bidan' }}
                </button>
                <a href="{{ route('ibu-hamil.show', $ibuHamil->id) }}" class="btn-secondary" style="flex: 1;">Batal</a>
            </div>
        </form>
    </div>
@endsection