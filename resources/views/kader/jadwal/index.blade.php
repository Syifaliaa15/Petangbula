@extends('layouts.dashboard')

@section('title', 'Jadwal Posyandu')
@section('page-title', 'Jadwal Posyandu')
@section('page-subtitle', 'Kelola jadwal kegiatan posyandu Anda')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="color: #831843; font-size: 1.2rem; font-weight: 800;">Daftar Jadwal Posyandu</h2>
            <p style="color: #6b7280; font-size: 0.85rem; margin-top: 4px;">
                Total: <strong>{{ $jadwals->count() }}</strong> jadwal
                @if(auth()->user()->posyandu)
                    | Wilayah: <strong>{{ auth()->user()->posyandu->nama_posyandu }}</strong>
                @endif
            </p>
        </div>
        <a href="{{ route('kader.jadwal.create') }}" class="btn-add">+ Tambah Jadwal</a>
    </div>

    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Kegiatan</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th style="text-align: center;">Aksi</th>
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
                        @if($j->tanggal->isToday())
                            <span class="badge badge-warning">🔥 Hari Ini</span>
                        @elseif($j->tanggal->isFuture())
                            <span class="badge badge-success">📅 Akan Datang</span>
                        @else
                            <span class="badge badge-secondary">✓ Selesai</span>
                        @endif
                    </td>
                    <td style="text-align: center; white-space: nowrap;">
                        <a href="{{ route('kader.jadwal.edit', $j->id) }}" class="btn-action btn-edit" style="margin-right: 4px;">✏️ Edit</a>
                        <form action="{{ route('kader.jadwal.destroy', $j->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin hapus jadwal ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-action btn-delete">🗑️</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 60px; color: #9ca3af;">
                        <div style="font-size: 2rem; margin-bottom: 8px;">📅</div>
                        Belum ada jadwal posyandu.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection