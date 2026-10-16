@extends('layouts.dashboard')

@section('title', 'Laporan Darurat')
@section('page-title', 'Laporan Pemeriksaan Darurat')
@section('page-subtitle', 'Daftar kasus pemeriksaan darurat dari seluruh posyandu')

@section('sidebar-menu')
    <a href="{{ route('admin.dashboard') }}" class="menu-item">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
        Dashboard
    </a>
    <a href="{{ route('admin.kelola-akun') }}" class="menu-item">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
        Kelola Akun
    </a>
    <a href="{{ route('kader.jadwal.index') }}" class="menu-item">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
        Jadwal Posyandu
    </a>
    <a href="{{ route('admin.laporan-posyandu') }}" class="menu-item">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
        Laporan Posyandu
    </a>
    <a href="{{ route('admin.laporan-darurat') }}" class="menu-item active">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
        Laporan Darurat
    </a>
@endsection

@section('content')
    <div class="card">
        <h2 class="card-title">🚨 Daftar Laporan Pemeriksaan Darurat ({{ $laporans->count() }})</h2>

        @if($laporans->count() > 0)
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
                        @foreach($laporans as $index => $l)
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 60px; color: #9ca3af;">
                <div style="font-size: 3rem; margin-bottom: 12px;">📋</div>
                <div style="font-weight: 700; color: #831843; margin-bottom: 4px;">Belum Ada Data</div>
                <div style="font-size: 0.85rem;">Belum ada laporan pemeriksaan darurat.</div>
            </div>
        @endif
    </div>
@endsection