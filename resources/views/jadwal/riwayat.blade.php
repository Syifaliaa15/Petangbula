@extends('layouts.dashboard')

@section('title', 'Riwayat Jadwal Posyandu')
@section('page-title', 'Riwayat Jadwal Posyandu')
@section('page-subtitle', 'Lihat riwayat jadwal posyandu dari seluruh wilayah')

@section('content')
    <!-- FILTER -->
    <div class="card" style="margin-bottom: 20px;">
        <form method="GET" action="" style="display: flex; gap: 12px; align-items: end; flex-wrap: wrap;">
            <div class="form-group" style="margin-bottom: 0; min-width: 180px;">
                <label style="font-size: 0.8rem;">Filter RW</label>
                <select name="rw" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua RW</option>
                    @foreach($posyandus as $p)
                        <option value="{{ $p->rw }}" {{ request('rw') == $p->rw ? 'selected' : '' }}>
                            RW {{ $p->rw }} - {{ $p->nama_posyandu }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0; min-width: 160px;">
                <label style="font-size: 0.8rem;">Status</label>
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="today" {{ request('status') == 'today' ? 'selected' : '' }}>🔥 Hari Ini</option>
                    <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>📅 Akan Datang</option>
                    <option value="past" {{ request('status') == 'past' ? 'selected' : '' }}>✓ Sudah Berlalu</option>
                </select>
            </div>

            @if(request()->hasAny(['rw', 'status']))
                <a href="{{ route('jadwal.riwayat') }}" class="btn-secondary" style="padding: 12px 20px;">Reset Filter</a>
            @endif
        </form>
    </div>

    <!-- SUMMARY -->
    <div class="stat-grid" style="margin-bottom: 20px;">
        <div class="stat-card">
            <div>
                <span class="stat-label">Total Jadwal</span>
                <div class="stat-value">{{ $jadwals->count() }}</div>
                <div class="stat-sub">semua periode</div>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <span class="stat-label">Akan Datang</span>
                <div class="stat-value">{{ $jadwals->where('tanggal', '>=', now())->count() }}</div>
                <div class="stat-sub">jadwal mendatang</div>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <span class="stat-label">Sudah Berlalu</span>
                <div class="stat-value">{{ $jadwals->where('tanggal', '<', now())->count() }}</div>
                <div class="stat-sub">jadwal lampau</div>
            </div>
        </div>
    </div>

    <!-- TABEL -->
    <div class="card">
        <h2 class="card-title">📅 Daftar Jadwal Posyandu</h2>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Kegiatan</th>
                        <th>Lokasi</th>
                        <th>Posyandu</th>
                        <th>Dibuat Oleh</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwals as $index => $j)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td style="font-weight: 700;">{{ $j->tanggal->format('d M Y') }}</td>
                            <td style="font-size: 0.85rem;">
                                {{ \Carbon\Carbon::parse($j->waktu_mulai)->format('H:i') }}
                                @if($j->waktu_selesai)
                                    - {{ \Carbon\Carbon::parse($j->waktu_selesai)->format('H:i') }}
                                @endif
                            </td>
                            <td>
                                <div style="font-weight: 700; color: #831843;">{{ $j->kegiatan }}</div>
                                @if($j->keterangan)
                                    <div style="font-size: 0.75rem; color: #9ca3af;">{{ Str::limit($j->keterangan, 50) }}</div>
                                @endif
                            </td>
                            <td style="font-size: 0.85rem;">{{ $j->lokasi }}</td>
                            <td>
                                @if($j->posyandu)
                                    <span class="badge badge-info">{{ $j->posyandu->nama_posyandu }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td style="font-size: 0.8rem;">{{ $j->kader->name ?? '-' }}</td>
                            <td>
                                @if($j->tanggal->isToday())
                                    <span class="badge badge-warning">🔥 Hari Ini</span>
                                @elseif($j->tanggal->isFuture())
                                    <span class="badge badge-success">📅 Akan Datang</span>
                                @else
                                    <span class="badge badge-secondary">✓ Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 60px; color: #9ca3af;">
                                <div style="font-size: 2rem; margin-bottom: 8px;">📅</div>
                                Belum ada riwayat jadwal posyandu.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection