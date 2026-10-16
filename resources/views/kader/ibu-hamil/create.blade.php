@extends('layouts.dashboard')

@section('title', 'Registrasi Ibu Hamil')
@section('page-title', 'Registrasi Ibu Hamil Baru')
@section('page-subtitle', 'Lengkapi data awal untuk membuat rekam medis 40 minggu')

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

        <form action="{{ route('ibu-hamil.store') }}" method="POST">
            @csrf

            <div class="form-grid">
                <!-- Kolom Kiri -->
                <div>
                    <h3 style="color: #831843; font-size: 1rem; font-weight: 700; margin-bottom: 16px; border-bottom: 2px solid #fbcfe8; padding-bottom: 8px;">
                        🤰 Data Ibu
                    </h3>

                    <div class="form-group">
                        <label>NIK (16 Digit) <span class="required">*</span></label>
                        <input type="text" name="nik" class="form-control" value="{{ old('nik') }}" maxlength="16" required placeholder="16 digit NIK">
                    </div>

                    <div class="form-group">
                        <label>Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required placeholder="Nama sesuai KTP">
                    </div>

                    <div style="display: flex; gap: 16px;">
                        <div class="form-group" style="flex: 1;">
                            <label>Umur (Tahun) <span class="required">*</span></label>
                            <input type="number" name="umur" class="form-control" value="{{ old('umur') }}" min="15" max="60" required>
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label>Hamil Ke- <span class="required">*</span></label>
                            <input type="number" name="hamil_ke" class="form-control" value="{{ old('hamil_ke') }}" min="1" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Kelompok Dasawisma</label>
                        <input type="text" name="klmp_dasawisma" class="form-control" value="{{ old('klmp_dasawisma') }}" placeholder="Contoh: Dasawisma Melati 2">
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div>
                    <h3 style="color: #831843; font-size: 1rem; font-weight: 700; margin-bottom: 16px; border-bottom: 2px solid #fbcfe8; padding-bottom: 8px;">
                        📅 Data Kehamilan & Alamat
                    </h3>

                    <div class="form-group">
                        <label>Tanggal Pendaftaran / HPHT <span class="required">*</span></label>
                        <input type="date" name="tanggal_pendaftaran" class="form-control" value="{{ old('tanggal_pendaftaran') }}" max="{{ date('Y-m-d') }}" required>
                        <small style="color: #9ca3af; font-size: 0.75rem;">Hari Pertama Haid Terakhir</small>
                    </div>

                    <div class="form-group">
                        <label>Nama Suami</label>
                        <input type="text" name="nama_suami" class="form-control" value="{{ old('nama_suami') }}" placeholder="Nama lengkap suami">
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
                <button type="submit" class="btn-primary" style="flex: 1;">💾 Simpan & Buat Rekam 40 Minggu</button>
                <a href="{{ route('ibu-hamil.index') }}" class="btn-secondary" style="flex: 1;">Batal</a>
            </div>
        </form>
    </div>
@endsection