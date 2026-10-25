@extends('layouts.dashboard')

@section('title', 'Riwayat Jadwal Posyandu')
@section('page-title', 'Riwayat Jadwal Posyandu')
@section('page-subtitle', 'Lihat riwayat jadwal posyandu dari seluruh wilayah')

@section('content')
    <!-- Filter -->
    <div class="bg-white border border-kia-100 rounded-2xl p-5 shadow-sm mb-6 w-full">
        <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-kia-800 mb-1.5 uppercase tracking-wide">Filter RW / Posyandu</label>
                <select name="rw" class="w-full px-3 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none" onchange="this.form.submit()">
                    <option value="">Semua Posyandu</option>
                    @foreach($posyandus as $p)
                        <option value="{{ $p->rw }}" {{ request('rw') == $p->rw ? 'selected' : '' }}>
                            RW {{ $p->rw }} - {{ $p->nama_posyandu }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-kia-800 mb-1.5 uppercase tracking-wide">Status Waktu</label>
                <select name="status" class="w-full px-3 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="today" {{ request('status') == 'today' ? 'selected' : '' }}>🔥 Hari Ini</option>
                    <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>📅 Akan Datang</option>
                    <option value="past" {{ request('status') == 'past' ? 'selected' : '' }}>✓ Sudah Berlalu</option>
                </select>
            </div>

            <div>
                @if(request()->hasAny(['rw', 'status']))
                    <a href="{{ route('jadwal.riwayat') }}" class="w-full block text-center px-4 py-2.5 bg-kia-100 text-kia-700 font-bold rounded-xl hover:bg-kia-200 transition-colors">Reset Filter</a>
                @endif
            </div>
        </form>
    </div>

    <!-- List Jadwal -->
    <div class="space-y-4 w-full">
        @forelse($jadwals as $j)
            <div class="bg-white border border-kia-100 rounded-2xl shadow-sm overflow-hidden flex flex-col md:flex-row w-full">
                <!-- Tanggal -->
                <div class="bg-kia-50/50 p-4 flex flex-row md:flex-col items-center justify-center md:w-32 border-b md:border-b-0 md:border-r border-kia-100 text-kia-700">
                    <span class="text-3xl font-extrabold mr-2 md:mr-0">{{ $j->tanggal->format('d') }}</span>
                    <span class="text-sm font-bold uppercase tracking-wider">{{ $j->tanggal->translatedFormat('M Y') }}</span>
                </div>

                <!-- Detail -->
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

                <!-- Info Tambahan -->
                <div class="p-4 bg-gray-50/50 md:border-l border-kia-100 flex flex-col justify-center text-xs">
                    <span class="text-gray-400 font-bold block mb-0.5">POSYANDU</span>
                    <span class="font-bold text-kia-900 mb-2">{{ $j->posyandu->nama_posyandu ?? '-' }}</span>
                    <span class="text-gray-400 font-bold block mb-0.5">DIBUAT OLEH</span>
                    <span class="font-medium text-gray-700">{{ $j->kader->name ?? '-' }}</span>
                </div>
            </div>
        @empty
            <div class="bg-white border border-kia-100 rounded-2xl p-12 text-center text-gray-500 shadow-sm">
                <div class="text-4xl mb-3">📅</div>
                <p class="font-bold">Belum ada riwayat jadwal posyandu.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $jadwals->links() }}
    </div>
@endsection