@extends('layouts.dashboard')

@section('title', 'Laporan Darurat')
@section('page-title', 'Laporan Pemeriksaan Darurat')
@section('page-subtitle', 'Daftar kasus pemeriksaan darurat')

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
    <a href="{{ route('bidan.darurat') }}" class="menu-item">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
        Pemeriksaan Darurat
    </a>
    <a href="{{ route('bidan.laporan.posyandu') }}" class="menu-item">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
        Laporan Posyandu
    </a>
    <a href="{{ route('bidan.laporan.darurat') }}" class="menu-item active">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
        Laporan Darurat
    </a>
@endsection

@section('content')
    <div class="card">
        <h2 class="card-title">🚨 Daftar Laporan Pemeriksaan Darurat ({{ $laporans->count() }})</h2>

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
                        <th>Bidan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporans as $index => $l)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $l->tanggal->format('d M Y') }}</td>
                        <td style="font-weight: 700; color: #831843;">{{ $l->nama_pasien }}</td>
                        <td>
                            <span class="badge {{ $l->kategori == 'balita' ? 'badge-info' : 'badge-warning' }}">
                                {{ ucfirst($l->kategori) }}
                            </span>
                        </td>
                        <td style="font-size: 0.85rem;">{{ Str::limit($l->keluhan, 60) }}</td>
                        <td style="font-size: 0.85rem;">{{ Str::limit($l->tindakan ?? '-', 60) }}</td>
                        <td style="font-size: 0.8rem;">{{ $l->bidan->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 60px; color: #9ca3af;">
                            <div style="font-size: 2rem; margin-bottom: 8px;">📋</div>
                            Belum ada data laporan pemeriksaan darurat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection