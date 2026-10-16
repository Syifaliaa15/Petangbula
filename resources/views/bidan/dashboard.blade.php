@extends('layouts.dashboard')

@section('title', 'Dashboard Bidan')
@section('page-title', 'Dashboard Bidan Desa')
@section('page-subtitle', 'Pantau dan validasi data kesehatan dari seluruh posyandu')

@section('sidebar-menu')
    <a href="{{ route('bidan.dashboard') }}" class="menu-item {{ request()->routeIs('bidan.dashboard') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
        Dashboard
    </a>
    <a href="{{ route('bidan.validasi.balita') }}" class="menu-item {{ request()->routeIs('bidan.validasi.balita') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
        Validasi Data Balita
    </a>
    <a href="{{ route('bidan.validasi.bumil') }}" class="menu-item {{ request()->routeIs('bidan.validasi.bumil') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
        Validasi Data Bumil
    </a>
    <a href="{{ route('bidan.darurat') }}" class="menu-item {{ request()->routeIs('bidan.darurat') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
        Pemeriksaan Darurat
    </a>
    <a href="{{ route('bidan.laporan.posyandu') }}" class="menu-item {{ request()->routeIs('bidan.laporan.posyandu') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
        Laporan Posyandu
    </a>
    <a href="{{ route('bidan.laporan.darurat') }}" class="menu-item {{ request()->routeIs('bidan.laporan.darurat') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
        Laporan Darurat
    </a>
    <a href="{{ route('kader.jadwal.index') }}" class="menu-item {{ request()->routeIs('jadwal.*') ? 'active' : '' }}">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
    Jadwal Posyandu
    </a>
@endsection

@section('content')
    <div class="stat-grid">
        <div class="stat-card">
            <div>
                <span class="stat-label">Total Balita</span>
                <div class="stat-value">{{ $totalBalita }}</div>
                <div class="stat-sub">di seluruh posyandu</div>
            </div>
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <span class="stat-label">Total Ibu Hamil</span>
                <div class="stat-value">{{ $totalIbuHamil }}</div>
                <div class="stat-sub">ibu hamil aktif</div>
            </div>
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
            </div>
        </div>

        <div class="stat-card" style="background: linear-gradient(135deg, #fef3c7, #fde68a); border-color: #fbbf24;">
            <div>
                <span class="stat-label" style="color: #92400e;">⏳ Menunggu Validasi</span>
                <div class="stat-value" style="color: #78350f;">{{ $totalPending }}</div>
                <div class="stat-sub" style="color: #92400e;">data perlu diverifikasi</div>
            </div>
            <div class="stat-icon" style="background: #fde68a;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#92400e"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
            </div>
        </div>
    </div>

    <div class="card">
        <h2 class="card-title">📊 Selamat Datang, Bidan!</h2>
        <p style="color: #6b7280; font-size: 0.9rem; line-height: 1.7;">
            Anda dapat memvalidasi data pemeriksaan yang dikirim oleh Kader dari seluruh posyandu.
            Klik menu <strong>"Validasi Data Balita"</strong> atau <strong>"Validasi Data Bumil"</strong>
            untuk memulai proses verifikasi.
        </p>
    </div>
@endsection