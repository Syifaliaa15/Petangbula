@extends('layouts.dashboard')

@section('title', 'Tambah Data Balita')
@section('page-title', 'Registrasi Balita Baru')
@section('page-subtitle', 'Isi formulir berikut untuk mendaftarkan anak ke dalam sistem')

@section('content')
    <div class="card" style="max-width: 900px; margin: 0 auto;">
        <form action="{{ route('balita.store') }}" method="POST">
            @csrf

            <div class="form-grid">
                <!-- Kolom Kiri -->
                <div>
                    <h3 style="color: #831843; font-size: 1rem; font-weight: 700; margin-bottom: 16px; border-bottom: 2px solid #fbcfe8; padding-bottom: 8px;">
                        👶 Data Anak
                    </h3>

                    <div class="form-group">
                        <label>Nama Lengkap Balita <span class="required">*</span></label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required placeholder="Contoh: Budi Santoso">
                        @error('nama') <small style="color: #ef4444;">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-group">
                        <label>NIK (Opsional)</label>
                        <input type="text" name="nik" class="form-control" value="{{ old('nik') }}" placeholder="16 digit NIK">
                    </div>

                    <div class="form-group">
                        <label>Tanggal Lahir <span class="required">*</span></label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}" max="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Jenis Kelamin <span class="required">*</span></label>
                        <select name="jenis_kelamin" class="form-control" required>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div>
                    <h3 style="color: #831843; font-size: 1rem; font-weight: 700; margin-bottom: 16px; border-bottom: 2px solid #fbcfe8; padding-bottom: 8px;">
                        👨‍👩‍👦 Data Orang Tua & Alamat
                    </h3>

                    <div class="form-group">
                        <label>Nama Ibu Kandung <span class="required">*</span></label>
                        <input type="text" name="nama_ibu" class="form-control" value="{{ old('nama_ibu') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Nama Ayah (Opsional)</label>
                        <input type="text" name="nama_ayah" class="form-control" value="{{ old('nama_ayah') }}">
                    </div>

                    <div class="form-group">
                        <label>Alamat (RT & Nama Dusun)</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="text" name="rt" class="form-control" value="{{ old('rt') }}" placeholder="RT" style="width: 100px; text-align: center;">
                            <input type="text" name="alamat" class="form-control" value="{{ old('alamat') }}" placeholder="Nama Dusun / Jalan" style="flex: 1;">
                        </div>
                        <small style="color: #9ca3af; font-size: 0.75rem; display: block; margin-top: 6px;">
                            📍 RW otomatis terisi dari posyandu Anda.
                        </small>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #fbcfe8;">
                <button type="submit" class="btn-primary" style="flex: 1;">💾 Simpan & Registrasi</button>
                <a href="{{ route('balita.index') }}" class="btn-secondary" style="flex: 1;">Batal</a>
            </div>
        </form>
    </div>
@endsection