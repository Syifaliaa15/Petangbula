@extends('layouts.dashboard')

@section('title', 'Kelola Data Balita')
@section('page-title', 'Kelola Data Balita')
@section('page-subtitle', 'Manajemen data tumbuh kembang balita di wilayah Anda')

@section('content')
    <!-- HEADER + TOMBOL TAMBAH -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="color: #831843; font-size: 1.2rem; font-weight: 800;">Daftar Balita</h2>
            <p style="color: #6b7280; font-size: 0.85rem; margin-top: 4px;">
                Total: <strong>{{ $balitas->total() }}</strong> anak
                @if(auth()->user()->isKader() && auth()->user()->posyandu)
                    | Wilayah: <strong>{{ auth()->user()->posyandu->nama_posyandu }}</strong>
                @endif
            </p>
        </div>
        <a href="{{ route('balita.create') }}" class="btn-add">+ Tambah Data Balita</a>
    </div>

    <!-- FILTER + SEARCH -->
    <div class="card" style="padding: 16px; margin-bottom: 20px;">
        <form method="GET" action="" style="display: flex; gap: 12px; align-items: end; flex-wrap: wrap;">
            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
                <label style="font-size: 0.8rem;">🔍 Cari (Nama / NIK / Nama Ibu)</label>
                <input type="text" name="search" class="form-control"
                       value="{{ request('search') }}"
                       placeholder="Ketik untuk mencari...">
            </div>

            <div class="form-group" style="margin-bottom: 0; min-width: 140px;">
                <label style="font-size: 0.8rem;">Jenis Kelamin</label>
                <select name="jk" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    <option value="L" {{ request('jk') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ request('jk') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>

            @if(isset($posyandus) && $posyandus->count() > 0)
                <div class="form-group" style="margin-bottom: 0; min-width: 180px;">
                    <label style="font-size: 0.8rem;">Posyandu</label>
                    <select name="posyandu_id" class="form-control" onchange="this.form.submit()">
                        <option value="">Semua</option>
                        @foreach($posyandus as $p)
                            <option value="{{ $p->id }}" {{ request('posyandu_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_posyandu }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <button type="submit" class="btn-primary" style="padding: 12px 20px;">Cari</button>

            @if(request()->hasAny(['search', 'jk', 'posyandu_id']))
                <a href="{{ route('balita.index') }}" class="btn-secondary" style="padding: 12px 20px;">Reset</a>
            @endif
        </form>
    </div>

    <!-- TABEL -->
    <div class="responsive-table-wrapper">
        <table class="data-table responsive-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Balita & NIK</th>
                    <th>Nama Ibu</th>
                    <th>JK</th>
                    <th>Tanggal Lahir</th>
                    <th>Usia</th>
                    <th>Status Gizi (BB/U)</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($balitas as $index => $balita)
                    @php
                        $pemeriksaanTerakhir = $balita->pemeriksaan()->latest('tanggal_periksa')->first();
                    @endphp
                    <tr>
                        <td data-label="No">{{ $balitas->firstItem() + $index }}</td>
                        <td data-label="Nama">
                            <div style="font-weight: 700; color: #831843;">{{ $balita->nama }}</div>
                            <div style="font-size: 0.75rem; color: #9ca3af;">NIK: {{ $balita->nik ?? '-' }}</div>
                        </td>
                        <td data-label="Nama Ibu">{{ $balita->nama_ibu }}</td>
                        <td data-label="JK">
                            <span class="badge {{ $balita->jenis_kelamin == 'L' ? 'badge-info' : 'badge-warning' }}">
                                {{ $balita->jenis_kelamin == 'L' ? 'L' : 'P' }}
                            </span>
                        </td>
                        <td data-label="Tgl Lahir">{{ $balita->tanggal_lahir->format('d M Y') }}</td>
                        <td data-label="Usia" style="font-weight: 600;">{{ $balita->umur_lengkap }}</td>
                        <td data-label="Status Gizi">
                            @if($pemeriksaanTerakhir)
                                <span class="badge" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($pemeriksaanTerakhir->status_bb_u) }}; color: #4a1d3a;">
                                    {{ $pemeriksaanTerakhir->status_bb_u ?? '-' }}
                                </span>
                            @else
                                <span style="color: #9ca3af; font-size: 0.75rem;">Belum diperiksa</span>
                            @endif
                        </td>
                        <td data-label="Aksi" style="text-align: center; white-space: nowrap;">
                            <a href="{{ route('balita.show', $balita->id) }}" class="btn-action" style="background: #d1fae5; color: #065f46; margin-right: 4px;">📖</a>
                            <a href="{{ route('balita.edit', $balita->id) }}" class="btn-action btn-edit" style="margin-right: 4px;">✏️</a>
                            <form action="{{ route('balita.destroy', $balita->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin hapus data {{ $balita->nama }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action btn-delete">🗑️</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 60px; color: #9ca3af;">
                            <div style="font-size: 2rem; margin-bottom: 8px;">👶</div>
                            @if(request()->hasAny(['search', 'jk', 'posyandu_id']))
                                Tidak ada data yang cocok dengan filter.
                            @else
                                Belum ada data balita.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    @if($balitas->hasPages())
        <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div style="color: #6b7280; font-size: 0.85rem;">
                Menampilkan <strong>{{ $balitas->firstItem() }}</strong> - <strong>{{ $balitas->lastItem() }}</strong>
                dari <strong>{{ $balitas->total() }}</strong> data
            </div>
            <div>{{ $balitas->links() }}</div>
        </div>
    @endif
@endsection