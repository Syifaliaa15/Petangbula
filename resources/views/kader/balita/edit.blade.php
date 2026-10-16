@extends('layouts.dashboard')

@section('title', 'Edit Data Balita')
@section('page-title', 'Edit Data Balita')
@section('page-subtitle', 'Perbarui informasi balita')

@section('content')
    <div class="card" style="max-width: 900px; margin: 0 auto;">
        <form action="{{ route('balita.update', $balita->id) }}" method="POST">
            @csrf @method('PUT')

            <div class="form-grid">
                <div>
                    <h3 style="color: #831843; font-size: 1rem; font-weight: 700; margin-bottom: 16px; border-bottom: 2px solid #fbcfe8; padding-bottom: 8px;">
                        👶 Data Anak
                    </h3>

                    <div class="form-group">
                        <label>Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama', $balita->nama) }}" required>
                    </div>

                    <div class="form-group">
                        <label>NIK (Opsional)</label>
                        <input type="text" name="nik" class="form-control" value="{{ old('nik', $balita->nik) }}">
                    </div>

                    <div class="form-group">
                        <label>Tanggal Lahir <span class="required">*</span></label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $balita->tanggal_lahir->format('Y-m-d')) }}" required>
                    </div>

                    <div class="form-group">
                        <label>Jenis Kelamin <span class="required">*</span></label>
                        <select name="jenis_kelamin" class="form-control" required>
                            <option value="L" {{ old('jenis_kelamin', $balita->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $balita->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>

                <div>
                    <h3 style="color: #831843; font-size: 1rem; font-weight: 700; margin-bottom: 16px; border-bottom: 2px solid #fbcfe8; padding-bottom: 8px;">
                        👨‍👩‍👦 Data Orang Tua & Alamat
                    </h3>

                    <div class="form-group">
                        <label>Nama Ibu Kandung <span class="required">*</span></label>
                        <input type="text" name="nama_ibu" class="form-control" value="{{ old('nama_ibu', $balita->nama_ibu) }}" required>
                    </div>

                    <div class="form-group">
                        <label>Nama Ayah (Opsional)</label>
                        <input type="text" name="nama_ayah" class="form-control" value="{{ old('nama_ayah', $balita->nama_ayah) }}">
                    </div>

                    <div class="form-group">
                        <label>Alamat (RT & Nama Dusun)</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="text" name="rt" class="form-control" value="{{ old('rt', $balita->rt) }}" placeholder="RT" style="width: 100px; text-align: center;">
                            <input type="text" name="alamat" class="form-control" value="{{ old('alamat', $balita->alamat) }}" placeholder="Nama Dusun / Jalan" style="flex: 1;">
                        </div>
                        <small style="color: #9ca3af; font-size: 0.75rem; display: block; margin-top: 6px;">
                            📍 RW otomatis terisi dari posyandu Anda.
                        </small>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #fbcfe8;">
                <button type="submit" class="btn-primary" style="flex: 1;">💾 Perbarui Data</button>
                <a href="{{ route('balita.index') }}" class="btn-secondary" style="flex: 1;">Batal</a>
            </div>
        </form>
    </div>
@endsection