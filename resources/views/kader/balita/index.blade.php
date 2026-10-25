@extends('layouts.dashboard')

@section('title', 'Kelola Data Balita')
@section('page-title', 'Kelola Data Balita')
@section('page-subtitle', 'Manajemen data tumbuh kembang balita')

@section('content')
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-extrabold text-kia-900 tracking-tight">Daftar Balita</h2>
            <p class="text-sm text-gray-500 mt-1 font-medium">
                Total: <span class="font-bold text-kia-600">{{ $balitas->total() }}</span> anak
                @if(auth()->user()->isKader() && auth()->user()->posyandu)
                    | Wilayah: <span class="font-bold text-kia-600">{{ auth()->user()->posyandu->nama_posyandu }}</span>
                @endif
            </p>
        </div>
        <a href="{{ route('balita.create') }}" class="inline-flex items-center justify-center gap-2 w-full md:w-auto px-6 py-3 bg-gradient-to-r from-kia-500 to-kia-600 text-white rounded-xl font-bold shadow-md hover:shadow-lg transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Tambah Data
        </a>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white border border-kia-100 rounded-2xl p-5 shadow-sm mb-6 w-full">
        <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-kia-800 mb-1.5 uppercase tracking-wide">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full px-3 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none" placeholder="Nama / NIK / Ibu">
            </div>
            <div>
                <label class="block text-xs font-bold text-kia-800 mb-1.5 uppercase tracking-wide">Jenis Kelamin</label>
                <select name="jk" class="w-full px-3 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none">
                    <option value="">Semua</option>
                    <option value="L" {{ request('jk') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ request('jk') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-kia-800 mb-1.5 uppercase tracking-wide">Posyandu</label>
                <select name="posyandu_id" class="w-full px-3 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none">
                    <option value="">Semua</option>
                    @foreach($posyandus ?? [] as $p)
                        <option value="{{ $p->id }}" {{ request('posyandu_id') == $p->id ? 'selected' : '' }}>{{ $p->nama_posyandu }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="w-full px-4 py-2.5 bg-kia-500 text-white text-sm font-bold rounded-xl hover:bg-kia-600 shadow-sm">Terapkan</button>
                @if(request()->hasAny(['search', 'jk', 'posyandu_id']))
                    <a href="{{ route('balita.index') }}" class="px-4 py-2.5 bg-white border border-kia-200 text-kia-700 text-sm font-bold rounded-xl hover:bg-kia-50 text-center">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- ========================================== -->
    <!-- TAMPILAN DESKTOP (TABEL) -->
    <!-- ========================================== -->
    <div class="hidden md:block bg-white border border-kia-100 rounded-2xl shadow-sm w-full overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="w-full min-w-[800px] text-left border-collapse">
                <thead class="bg-kia-50/80 border-b border-kia-100">
                    <tr>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase text-center w-16">No</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Nama Balita / NIK</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Nama Ibu</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase text-center">JK</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Usia</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Status Gizi (BB/U)</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-kia-50">
                    @forelse($balitas as $index => $balita)
                        @php
                            $p = $balita->pemeriksaan()->latest('tanggal_periksa')->first();
                            $status = $p->status_bb_u ?? '';
                            $badge = 'bg-gray-100 text-gray-600 border-gray-200';
                            if(str_contains($status, 'Normal')) $badge = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                            elseif(str_contains($status, 'Kurang') && !str_contains($status, 'Sangat')) $badge = 'bg-amber-50 text-amber-700 border-amber-200';
                            elseif(str_contains($status, 'Sangat') || str_contains($status, 'Buruk')) $badge = 'bg-red-50 text-red-700 border-red-200';
                            elseif(str_contains($status, 'Lebih')) $badge = 'bg-blue-50 text-blue-700 border-blue-200';
                        @endphp
                        <tr class="hover:bg-kia-50/50 transition-colors group">
                            <td class="px-6 py-4 text-center text-sm font-medium text-gray-500">{{ $balitas->firstItem() + $index }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-kia-900">{{ $balita->nama }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">NIK: {{ $balita->nik ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-medium">{{ $balita->nama_ibu }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold {{ $balita->jenis_kelamin == 'L' ? 'bg-blue-50 text-blue-600' : 'bg-kia-100 text-kia-600' }}">{{ $balita->jenis_kelamin }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-medium whitespace-nowrap">{{ $balita->umur_lengkap }}</td>
                            <td class="px-6 py-4">
                                @if($p)
                                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold border {{ $badge }}">{{ $status }}</span>
                                @else
                                    <span class="text-xs text-gray-400 italic">Belum diperiksa</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-1.5 opacity-80 group-hover:opacity-100">
                                    <a href="{{ route('balita.show', $balita->id) }}" class="p-2 text-kia-600 bg-kia-50 rounded-lg hover:bg-kia-500 hover:text-white" title="Buku KIA">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    </a>
                                    <a href="{{ route('balita.edit', $balita->id) }}" class="p-2 text-amber-600 bg-amber-50 rounded-lg hover:bg-amber-500 hover:text-white" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('balita.destroy', $balita->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-500 hover:text-white" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-12 text-gray-400">Data tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($balitas->hasPages())
            <div class="px-6 py-4 border-t border-kia-100 bg-white">{{ $balitas->links() }}</div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- TAMPILAN MOBILE (CARD) -->
    <!-- ========================================== -->
    <div class="block md:hidden space-y-4 w-full">
        @forelse($balitas as $balita)
            @php
                $p = $balita->pemeriksaan()->latest('tanggal_periksa')->first();
                $status = $p->status_bb_u ?? '';
                $badge = 'bg-gray-100 text-gray-600 border-gray-200';
                if(str_contains($status, 'Normal')) $badge = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                elseif(str_contains($status, 'Kurang') && !str_contains($status, 'Sangat')) $badge = 'bg-amber-50 text-amber-700 border-amber-200';
                elseif(str_contains($status, 'Sangat') || str_contains($status, 'Buruk')) $badge = 'bg-red-50 text-red-700 border-red-200';
                elseif(str_contains($status, 'Lebih')) $badge = 'bg-blue-50 text-blue-700 border-blue-200';
            @endphp
            
            <div class="bg-white border border-kia-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="bg-kia-50/50 px-4 py-3 border-b border-kia-100 flex justify-between items-center">
                    <div>
                        <h3 class="font-extrabold text-kia-900 text-lg">{{ $balita->nama }}</h3>
                        <p class="text-xs text-gray-500 font-medium">NIK: {{ $balita->nik ?? '-' }}</p>
                    </div>
                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold {{ $balita->jenis_kelamin == 'L' ? 'bg-blue-100 text-blue-700' : 'bg-kia-100 text-kia-700' }}">
                        {{ $balita->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                    </span>
                </div>
                
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-3 text-sm mb-4">
                        <div>
                            <span class="text-gray-400 text-[11px] font-bold block mb-0.5">NAMA IBU</span>
                            <span class="font-bold text-gray-800">{{ $balita->nama_ibu }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 text-[11px] font-bold block mb-0.5">USIA</span>
                            <span class="font-bold text-gray-800">{{ $balita->umur_lengkap }}</span>
                        </div>
                    </div>

                    <div class="bg-kia-50/50 border border-kia-100 rounded-xl p-3">
                        <span class="text-[11px] text-gray-500 font-bold block mb-1">STATUS BB/U TERAKHIR</span>
                        @if($p)
                            <span class="inline-block px-2.5 py-1 rounded-md text-xs font-bold border {{ $badge }}">{{ $status }}</span>
                        @else
                            <span class="text-xs text-gray-400 italic">Belum diperiksa</span>
                        @endif
                    </div>
                </div>

                <div class="bg-gray-50/50 border-t border-kia-100 p-3 flex gap-2">
                    <a href="{{ route('balita.show', $balita->id) }}" class="flex-1 py-2 text-center text-xs font-bold text-white bg-gradient-to-r from-kia-500 to-kia-600 rounded-lg">Buku KIA</a>
                    <a href="{{ route('balita.edit', $balita->id) }}" class="flex-1 py-2 text-center text-xs font-bold text-amber-700 bg-amber-100 rounded-lg">Edit</a>
                    <form action="{{ route('balita.destroy', $balita->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus data ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="py-2 px-4 text-xs font-bold text-red-700 bg-red-100 rounded-lg">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-gray-500 bg-white rounded-2xl border border-kia-100 shadow-sm">Data tidak ditemukan.</div>
        @endforelse
        
        <div class="mt-4">{{ $balitas->links() }}</div>
    </div>
@endsection