@extends('layouts.dashboard')

@section('title', 'Jadwal Posyandu')
@section('page-title', 'Jadwal Posyandu')
@section('page-subtitle', 'Kelola jadwal kegiatan posyandu Anda')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-extrabold text-kia-900 tracking-tight">Daftar Jadwal</h2>
            <p class="text-sm text-gray-500 mt-1 font-medium">Total: <span class="font-bold text-kia-600">{{ $jadwals->count() }}</span> jadwal</p>
        </div>
        <a href="{{ route('kader.jadwal.create') }}" class="inline-flex items-center justify-center gap-2 w-full md:w-auto px-6 py-3 bg-gradient-to-r from-kia-500 to-kia-600 text-white rounded-xl font-bold shadow-md hover:shadow-lg transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Tambah Jadwal
        </a>
    </div>

    <div class="space-y-4 w-full">
        @forelse($jadwals as $j)
            <div class="bg-white border border-kia-100 rounded-2xl shadow-sm overflow-hidden flex flex-col md:flex-row w-full">
                <div class="bg-kia-50/50 p-4 flex flex-row md:flex-col items-center justify-center md:w-32 border-b md:border-b-0 md:border-r border-kia-100 text-kia-700">
                    <span class="text-3xl font-extrabold mr-2 md:mr-0">{{ $j->tanggal->format('d') }}</span>
                    <span class="text-sm font-bold uppercase tracking-wider">{{ $j->tanggal->translatedFormat('M Y') }}</span>
                </div>

                <div class="p-4 flex-1">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-2 gap-2">
                        <h3 class="font-bold text-kia-900 text-lg">{{ $j->kegiatan }}</h3>
                        @if($j->tanggal->isToday())
                            <span class="px-2.5 py-1 bg-amber-100 text-amber-700 border border-amber-200 rounded-md text-xs font-bold">🔥 Hari Ini</span>
                        @elseif($j->tanggal->isFuture())
                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-md text-xs font-bold">📅 Akan Datang</span>
                        @else
                            <span class="px-2.5 py-1 bg-gray-100 text-gray-600 border border-gray-200 rounded-md text-xs font-bold">✓ Selesai</span>
                        @endif
                    </div>
                    
                    <div class="flex flex-col md:flex-row gap-2 md:gap-6 text-sm text-gray-600 mt-3">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-kia-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ \Carbon\Carbon::parse($j->waktu_mulai)->format('H:i') }} - {{ $j->waktu_selesai ? \Carbon\Carbon::parse($j->waktu_selesai)->format('H:i') : 'Selesai' }}
                        </span>
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-kia-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $j->lokasi }}
                        </span>
                    </div>
                    @if($j->keterangan)
                        <div class="mt-3 p-2 bg-gray-50 rounded border border-gray-100 text-xs text-gray-500 italic">
                            "{{ $j->keterangan }}"
                        </div>
                    @endif
                </div>

                <div class="p-4 bg-gray-50/50 md:border-l border-kia-100 flex items-center justify-end gap-2">
                    <a href="{{ route('kader.jadwal.edit', $j->id) }}" class="px-3 py-2 bg-white border border-amber-200 text-amber-600 rounded-lg text-xs font-bold hover:bg-amber-50 transition-colors shadow-sm">✏️ Edit</a>
                    <form action="{{ route('kader.jadwal.destroy', $j->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus jadwal ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-3 py-2 bg-white border border-red-200 text-red-600 rounded-lg text-xs font-bold hover:bg-red-50 transition-colors shadow-sm">🗑️ Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white border border-kia-100 rounded-2xl p-12 text-center text-gray-500 shadow-sm">
                <div class="text-4xl mb-3">📅</div>
                <p class="font-bold">Belum ada jadwal posyandu.</p>
            </div>
        @endforelse
    </div>
@endsection