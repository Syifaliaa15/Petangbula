@extends('layouts.dashboard')

@section('title', 'Laporan Darurat')
@section('page-title', 'Laporan Pemeriksaan Darurat')
@section('page-subtitle', 'Daftar seluruh kasus pemeriksaan darurat yang tercatat')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-extrabold text-kia-900 tracking-tight">Riwayat Darurat</h2>
            <p class="text-sm text-gray-500 mt-1 font-medium">Total: <span class="font-bold text-kia-600">{{ $laporans->count() }}</span> kasus</p>
        </div>
    </div>

    @if($laporans->count() > 0)
        <!-- ========================================== -->
        <!-- TAMPILAN DESKTOP (TABEL) -->
        <!-- ========================================== -->
        <div class="hidden md:block bg-white border border-kia-100 rounded-2xl shadow-sm w-full overflow-hidden">
            <div class="overflow-x-auto w-full">
                <table class="w-full min-w-[800px] text-left border-collapse">
                    <thead class="bg-kia-50/80 border-b border-kia-100">
                        <tr>
                            <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase w-16 text-center">No</th>
                            <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Tanggal</th>
                            <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Pasien</th>
                            <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Keluhan</th>
                            <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Tindakan</th>
                            <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Bidan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-kia-50">
                        @foreach($laporans as $index => $l)
                        <tr class="hover:bg-kia-50/50 transition-colors">
                            <td class="px-6 py-4 text-center text-sm font-medium text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-kia-900 whitespace-nowrap">{{ $l->tanggal->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-kia-900">{{ $l->nama_pasien }}</div>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold {{ $l->kategori == 'balita' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                    {{ ucfirst($l->kategori) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ Str::limit($l->keluhan, 60) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ Str::limit($l->tindakan ?? '-', 60) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $l->bidan->name ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAMPILAN MOBILE (CARD) -->
        <!-- ========================================== -->
        <div class="block md:hidden space-y-4 w-full">
            @foreach($laporans as $l)
                <div class="bg-white border border-kia-100 rounded-2xl shadow-sm overflow-hidden">
                    <div class="bg-kia-50/50 px-4 py-3 border-b border-kia-100 flex justify-between items-center">
                        <div>
                            <h3 class="font-extrabold text-kia-900">{{ $l->nama_pasien }}</h3>
                            <p class="text-xs text-gray-500 font-medium">{{ $l->tanggal->format('d M Y') }}</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-md text-xs font-bold {{ $l->kategori == 'balita' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                            {{ ucfirst($l->kategori) }}
                        </span>
                    </div>
                    <div class="p-4 text-sm">
                        <div class="mb-3">
                            <span class="text-gray-400 text-[10px] font-bold block mb-0.5">KELUHAN</span>
                            <p class="text-gray-800">{{ $l->keluhan }}</p>
                        </div>
                        <div class="mb-3">
                            <span class="text-gray-400 text-[10px] font-bold block mb-0.5">TINDAKAN</span>
                            <p class="text-gray-800">{{ $l->tindakan ?? '-' }}</p>
                        </div>
                        <div class="pt-3 border-t border-gray-100 flex justify-between items-center text-xs">
                            <span class="text-gray-500">Oleh: {{ $l->bidan->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white border border-kia-100 rounded-2xl p-12 text-center text-gray-500 shadow-sm">
            <div class="text-5xl mb-4">📋</div>
            <h3 class="text-lg font-bold text-kia-900 mb-1">Belum Ada Data</h3>
            <p class="text-sm">Belum ada kasus pemeriksaan darurat yang dilaporkan.</p>
        </div>
    @endif
@endsection