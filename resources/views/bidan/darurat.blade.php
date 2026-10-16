@extends('layouts.dashboard')

@section('title', 'Pemeriksaan Darurat')
@section('page-title', 'Pemeriksaan Darurat')
@section('page-subtitle', 'Catat dan kelola kasus pemeriksaan darurat')

@section('sidebar-menu')
    <a href="{{ route('bidan.dashboard') }}" class="menu-item">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
        Dashboard
    </a>
    <a href="{{ route('bidan.validasi.balita') }}" class="menu-item">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
        Validasi Data Balita
    </a>
    <a href="{{ route('bidan.validasi.bumil') }}" class="menu-item">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
        Validasi Data Bumil
    </a>
    <a href="{{ route('bidan.darurat') }}" class="menu-item active">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
        Pemeriksaan Darurat
    </a>
    <a href="{{ route('bidan.laporan.posyandu') }}" class="menu-item">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
        Laporan Posyandu
    </a>
    <a href="{{ route('bidan.laporan.darurat') }}" class="menu-item">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
        Laporan Darurat
    </a>
@endsection

@section('content')
    <div class="card">
        <h2 class="card-title">🚨 Form Pemeriksaan Darurat</h2>
        <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 20px;">
            Catat kasus pemeriksaan darurat yang membutuhkan penanganan segera.
        </p>

        <form action="{{ route('bidan.darurat.store') }}" method="POST">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label>Nama Pasien <span class="required">*</span></label>
                    <input type="text" name="nama_pasien" class="form-control" value="{{ old('nama_pasien') }}" required placeholder="Nama lengkap">
                    @error('nama_pasien') <small style="color: #ef4444;">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Kategori <span class="required">*</span></label>
                    <select name="kategori" class="form-control" required>
                        <option value="balita" {{ old('kategori') == 'balita' ? 'selected' : '' }}>Balita</option>
                        <option value="bumil" {{ old('kategori') == 'bumil' ? 'selected' : '' }}>Ibu Hamil</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-control">
                        <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tanggal Pemeriksaan <span class="required">*</span></label>
                    <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label>Keluhan / Diagnosis <span class="required">*</span></label>
                <textarea name="keluhan" class="form-control" rows="3" required placeholder="Deskripsi keluhan atau diagnosis">{{ old('keluhan') }}</textarea>
                @error('keluhan') <small style="color: #ef4444;">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label>Tindakan / Penanganan</label>
                <textarea name="tindakan" class="form-control" rows="3" placeholder="Tindakan yang telah diberikan">{{ old('tindakan') }}</textarea>
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" class="btn-primary">💾 Simpan Data Darurat</button>
            </div>
        </form>
    </div>

    <!-- RIWAYAT -->
    <div class="card">
        <h2 class="card-title">📋 Riwayat Pemeriksaan Darurat ({{ $riwayat->count() }})</h2>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Pasien</th>
                        <th>Kategori</th>
                        <th>Keluhan</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $index => $r)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $r->tanggal->format('d M Y') }}</td>
                        <td style="font-weight: 700; color: #831843;">{{ $r->nama_pasien }}</td>
                        <td>
                            <span class="badge {{ $r->kategori == 'balita' ? 'badge-info' : 'badge-warning' }}">
                                {{ ucfirst($r->kategori) }}
                            </span>
                        </td>
                        <td style="font-size: 0.85rem;">{{ Str::limit($r->keluhan, 50) }}</td>
                        <td style="font-size: 0.85rem;">{{ Str::limit($r->tindakan ?? '-', 50) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #9ca3af;">
                            Belum ada data pemeriksaan darurat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection