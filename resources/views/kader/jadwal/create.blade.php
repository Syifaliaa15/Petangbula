@extends('layouts.dashboard')

@section('title', 'Tambah Jadwal Posyandu')
@section('page-title', 'Tambah Jadwal Posyandu')
@section('page-subtitle', 'Buat jadwal kegiatan posyandu baru')

@section('content')
    <div class="card" style="max-width: 800px; margin: 0 auto;">
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

        <form action="{{ route('kader.jadwal.store') }}" method="POST">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label>Tanggal <span class="required">*</span></label>
                    <input type="date" name="tanggal" class="form-control" 
                           value="{{ old('tanggal') }}" min="{{ date('Y-m-d') }}" required>
                </div>

                <div class="form-group">
                    <label>Kegiatan <span class="required">*</span></label>
                    <input type="text" name="kegiatan" class="form-control" 
                           value="{{ old('kegiatan') }}" placeholder="Contoh: Penimbangan Balita" required>
                </div>

                <div class="form-group">
                    <label>Waktu Mulai <span class="required">*</span></label>
                    <input type="time" name="waktu_mulai" class="form-control" 
                           value="{{ old('waktu_mulai', '08:00') }}" required>
                </div>

                <div class="form-group">
                    <label>Waktu Selesai</label>
                    <input type="time" name="waktu_selesai" class="form-control" 
                           value="{{ old('waktu_selesai', '11:00') }}">
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label>Lokasi <span class="required">*</span></label>
                    <input type="text" name="lokasi" class="form-control" 
                           value="{{ old('lokasi') }}" placeholder="Contoh: Balai RW 01" required>
                </div>
            </div>

            <div class="form-group">
                <label>Keterangan (Opsional)</label>
                <textarea name="keterangan" class="form-control" rows="3" 
                          placeholder="Catatan tambahan">{{ old('keterangan') }}</textarea>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #fbcfe8;">
                <button type="submit" class="btn-primary" style="flex: 1;">💾 Simpan Jadwal</button>
                <a href="{{ route('kader.jadwal.index') }}" class="btn-secondary" style="flex: 1;">Batal</a>
            </div>
        </form>
    </div>
@endsection