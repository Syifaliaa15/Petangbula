@extends('layouts.dashboard')

@section('title', 'Edit Data Ibu Hamil')
@section('page-title', 'Edit Data Ibu Hamil')
@section('page-subtitle', 'Perbarui informasi data ibu hamil')

@section('content')
    <div class="card" style="max-width: 900px; margin: 0 auto;">
        <form action="{{ route('ibu-hamil.update', $ibuHamil->id) }}" method="POST">
            @csrf @method('PUT')

            <div class="form-grid">
                <div>
                    <h3 style="color: #831843; font-size: 1rem; font-weight: 700; margin-bottom: 16px; border-bottom: 2px solid #fbcfe8; padding-bottom: 8px;">
                        🤰 Data Ibu
                    </h3>

                    <div class="form-group">
                        <label>NIK (16 Digit) <span class="required">*</span></label>
                        <input type="text" name="nik" class="form-control" value="{{ old('nik', $ibuHamil->nik) }}" maxlength="16" required>
                    </div>

                    <div class="form-group">
                        <label>Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama', $ibuHamil->nama) }}" required>
                    </div>

                    <div style="display: flex; gap: 16px;">
                        <div class="form-group" style="flex: 1;">
                            <label>Umur (Tahun) <span class="required">*</span></label>
                            <input type="number" name="umur" class="form-control" value="{{ old('umur', $ibuHamil->umur) }}" min="15" max="60" required>
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label>Hamil Ke- <span class="required">*</span></label>
                            <input type="number" name="hamil_ke" class="form-control" value="{{ old('hamil_ke', $ibuHamil->hamil_ke) }}" min="1" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Kelompok Dasawisma</label>
                        <input type="text" name="klmp_dasawisma" class="form-control" value="{{ old('klmp_dasawisma', $ibuHamil->klmp_dasawisma) }}">
                    </div>
                </div>

                <div>
                    <h3 style="color: #831843; font-size: 1rem; font-weight: 700; margin-bottom: 16px; border-bottom: 2px solid #fbcfe8; padding-bottom: 8px;">
                        📅 Data Kehamilan & Alamat
                    </h3>

                    <div class="form-group">
                        <label>Tanggal Pendaftaran / HPHT <span class="required">*</span></label>
                        <input type="date" name="tanggal_pendaftaran" class="form-control" value="{{ old('tanggal_pendaftaran', $ibuHamil->tanggal_pendaftaran ? $ibuHamil->tanggal_pendaftaran->format('Y-m-d') : '') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Nama Suami</label>
                        <input type="text" name="nama_suami" class="form-control" value="{{ old('nama_suami', $ibuHamil->nama_suami) }}">
                    </div>

                    <div class="form-group">
                        <label>Alamat (RT & Nama Dusun)</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="text" name="rt" class="form-control" value="{{ old('rt', $ibuHamil->rt) }}" placeholder="RT" style="width: 100px; text-align: center;">
                            <input type="text" name="alamat" class="form-control" value="{{ old('alamat', $ibuHamil->alamat) }}" placeholder="Nama Dusun / Jalan" style="flex: 1;">
                        </div>
                        <small style="color: #9ca3af; font-size: 0.75rem; display: block; margin-top: 6px;">
                            📍 RW otomatis terisi dari posyandu Anda.
                        </small>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #fbcfe8;">
                <button type="submit" class="btn-primary" style="flex: 1;">💾 Perbarui Data</button>
                <a href="{{ route('ibu-hamil.index') }}" class="btn-secondary" style="flex: 1;">Batal</a>
            </div>
        </form>
    </div>
@endsection