@extends('layouts.dashboard')

@section('title', 'Kelola Data Ibu Hamil')
@section('page-title', 'Kelola Data Ibu Hamil')
@section('page-subtitle', 'Manajemen data kesehatan ibu hamil')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="color: #831843; font-size: 1.2rem; font-weight: 800;">Daftar Ibu Hamil</h2>
            <p style="color: #6b7280; font-size: 0.85rem; margin-top: 4px;">
                Total: <strong>{{ $ibuHamils->total() }}</strong> ibu
            </p>
        </div>
        <a href="{{ route('ibu-hamil.create') }}" class="btn-add">+ Tambah Data Ibu Hamil</a>
    </div>

    <!-- FILTER + SEARCH -->
    <div class="card" style="padding: 16px; margin-bottom: 20px;">
        <form method="GET" action="" style="display: flex; gap: 12px; align-items: end; flex-wrap: wrap;">
            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
                <label style="font-size: 0.8rem;">🔍 Cari (Nama / NIK / Nama Suami)</label>
                <input type="text" name="search" class="form-control" 
                       value="{{ request('search') }}" 
                       placeholder="Ketik untuk mencari...">
            </div>

            <div class="form-group" style="margin-bottom: 0; min-width: 140px;">
                <label style="font-size: 0.8rem;">Hamil Ke-</label>
                <select name="hamil_ke" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    @for($i = 1; $i <= 10; $i++)
                        <option value="{{ $i }}" {{ request('hamil_ke') == $i ? 'selected' : '' }}>Ke-{{ $i }}</option>
                    @endfor
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

            @if(request()->hasAny(['search', 'hamil_ke', 'posyandu_id']))
                <a href="{{ route('ibu-hamil.index') }}" class="btn-secondary" style="padding: 12px 20px;">Reset</a>
            @endif
        </form>
    </div>

    <!-- TABEL -->
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama & NIK</th>
                    <th>Umur</th>
                    <th>Hamil Ke</th>
                    <th>Usia Kehamilan</th>
                    <th>Status Kesehatan</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ibuHamils as $index => $bumil)
                    @php
                        $pemeriksaanTerakhir = $bumil->pemeriksaan()->latest('tanggal_periksa')->first();
                    @endphp
                    <tr>
                        <td>{{ $ibuHamils->firstItem() + $index }}</td>
                        <td>
                            <div style="font-weight: 700; color: #831843;">{{ $bumil->nama }}</div>
                            <div style="font-size: 0.78rem; color: #9ca3af;">NIK: {{ $bumil->nik }}</div>
                        </td>
                        <td>{{ $bumil->umur ?? '-' }} th</td>
                        <td><span class="badge badge-warning">Ke-{{ $bumil->hamil_ke }}</span></td>
                        <td><span class="badge badge-success">{{ $bumil->usia_kehamilan_lengkap }}</span></td>
                        <td>
                            @if($pemeriksaanTerakhir)
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span class="badge" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($pemeriksaanTerakhir->status_kek) }}; color: #4a1d3a; font-size: 0.68rem;">
                                        KEK: {{ $pemeriksaanTerakhir->status_kek ?? '-' }}
                                    </span>
                                    <span class="badge" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($pemeriksaanTerakhir->status_anemia) }}; color: #4a1d3a; font-size: 0.68rem;">
                                        HB: {{ $pemeriksaanTerakhir->status_anemia ?? '-' }}
                                    </span>
                                </div>
                            @else
                                <span style="color: #9ca3af; font-size: 0.78rem;">Belum diperiksa</span>
                            @endif
                        </td>
                        <td style="text-align: center; white-space: nowrap;">
                            <a href="{{ route('ibu-hamil.show', $bumil->id) }}" class="btn-action" style="background: #d1fae5; color: #065f46; margin-right: 4px;">📖</a>
                            <a href="{{ route('ibu-hamil.edit', $bumil->id) }}" class="btn-action btn-edit" style="margin-right: 4px;">✏️</a>
                            <form action="{{ route('ibu-hamil.destroy', $bumil->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin hapus data {{ $bumil->nama }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action btn-delete">🗑️</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 60px; color: #9ca3af;">
                            <div style="font-size: 2rem; margin-bottom: 8px;">🤰</div>
                            @if(request()->hasAny(['search', 'hamil_ke', 'posyandu_id']))
                                Tidak ada data yang cocok.
                            @else
                                Belum ada data ibu hamil.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    @if($ibuHamils->hasPages())
        <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div style="color: #6b7280; font-size: 0.85rem;">
                Menampilkan <strong>{{ $ibuHamils->firstItem() }}</strong> - <strong>{{ $ibuHamils->lastItem() }}</strong>
                dari <strong>{{ $ibuHamils->total() }}</strong> data
            </div>
            <div>{{ $ibuHamils->links() }}</div>
        </div>
    @endif
@endsection