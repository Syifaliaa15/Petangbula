@extends('layouts.dashboard')

@section('title', 'Input Pemeriksaan - ' . $balita->nama)
@section('page-title', 'Input Pemeriksaan Bulanan')
@section('page-subtitle', $balita->nama . ' | Bulan Ke-' . $target_bulan)

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
                <strong>📝 Catatan Koreksi dari Bidan:</strong><br>
                <em>"{{ $pemeriksaan->catatan_bidan }}"</em>
            </div>
        @endif

        <form action="{{ route('balita.storeCheckup', $balita->id) }}" method="POST">
            @csrf
            <input type="hidden" name="target_bulan" value="{{ $target_bulan }}">

            <div class="form-grid">
                <div>
                    <div class="form-group">
                        <label>Tanggal Pemeriksaan <span class="required">*</span></label>
                        <input type="date" name="tanggal_periksa" class="form-control"
                            value="{{ $pemeriksaan ? $pemeriksaan->tanggal_periksa->format('Y-m-d') : date('Y-m-d') }}"
                            max="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Berat Badan (kg) <span class="required">*</span></label>
                        <input type="number" step="0.01" name="berat_badan" class="form-control"
                            value="{{ $pemeriksaan ? $pemeriksaan->berat_badan : old('berat_badan') }}"
                            placeholder="Contoh: 8.5" min="0.5" max="50" required>
                    </div>

                    <div class="form-group">
                        <label>Tinggi/Panjang Badan (cm) <span class="required">*</span></label>
                        <input type="number" step="0.1" name="tinggi_badan" class="form-control"
                            value="{{ $pemeriksaan ? $pemeriksaan->tinggi_badan : old('tinggi_badan') }}"
                            placeholder="Contoh: 75.0" min="30" max="150" required>
                    </div>
                </div>

                <div>
                    <div class="form-group">
                        <label>Lingkar Kepala (cm)</label>
                        <input type="number" step="0.1" name="lingkar_kepala" class="form-control"
                            value="{{ $pemeriksaan ? $pemeriksaan->lingkar_kepala : old('lingkar_kepala') }}"
                            placeholder="Contoh: 45.0" min="20" max="70">
                    </div>

                    <div class="alert alert-success" style="font-size: 0.85rem;">
                    <strong>Info:</strong> Usia balita saat ini <strong>{{ $balita->umur_lengkap }}</strong>.
                        Target input: <strong>Bulan ke-{{ $target_bulan }}</strong>.
                    </div>
                </div>
            </div>

            <!-- IMUNISASI -->
            <div style="margin-top: 24px; padding-top: 20px; border-top: 2px solid #fbcfe8;">
                <label style="display: block; font-weight: 800; color: #831843; margin-bottom: 12px;">
                    💉 Imunisasi yang Diberikan
                </label>
                @php
                    $imunisasiList = ['HB-0', 'BCG', 'Polio 1', 'DPT-HB-Hib 1', 'Polio 2', 'DPT-HB-Hib 2', 'Polio 3', 'DPT-HB-Hib 3', 'Polio 4', 'IPV', 'Campak/MR'];
                    $imunisasiSudah = $pemeriksaan ? ($pemeriksaan->imunisasi ?? []) : [];
                @endphp
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 10px;">
                    @foreach($imunisasiList as $vaksin)
                    <label style="display: flex; align-items: center; gap: 8px; padding: 10px 14px; background: #fff; border: 2px solid #fbcfe8; border-radius: 10px; cursor: pointer; font-size: 0.85rem; font-weight: 600; color: #831843;">
                        <input type="checkbox" name="imunisasi[]" value="{{ $vaksin }}"
                            {{ in_array($vaksin, $imunisasiSudah) ? 'checked' : '' }}
                            style="width: 16px; height: 16px; accent-color: #ec4899;">
                        {{ $vaksin }}
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- VITAMIN A -->
            <div style="margin-top: 20px;">
                <label style="display: flex; align-items: center; gap: 12px; padding: 14px; background: linear-gradient(135deg, #fef3c7, #fde68a); border: 2px solid #fbbf24; border-radius: 12px; cursor: pointer;">
                    <input type="checkbox" name="vitamin_a" value="1" {{ ($pemeriksaan && $pemeriksaan->vitamin_a) ? 'checked' : '' }}
                        style="width: 18px; height: 18px; accent-color: #f59e0b;">
                    <span style="font-weight: 700; color: #92400e;">💊 Vitamin A diberikan</span>
                </label>
            </div>

            <!-- KETERANGAN -->
            <div class="form-group" style="margin-top: 20px;">
                <label>Catatan / Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3" placeholder="Catatan tambahan (opsional)">{{ $pemeriksaan ? $pemeriksaan->keterangan : old('keterangan') }}</textarea>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid #fbcfe8;">
                <button type="submit" class="btn-primary" style="flex: 2;">
                    {{ $pemeriksaan ? '💾 Perbarui Data' : '💾 Simpan Pemeriksaan' }}
                </button>
                <a href="{{ route('balita.show', $balita->id) }}" class="btn-secondary" style="flex: 1;">Batal</a>
            </div>
        </form>
    </div>
@endsection