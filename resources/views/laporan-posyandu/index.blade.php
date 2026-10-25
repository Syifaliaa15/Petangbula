@extends('layouts.dashboard')

@section('title', 'Laporan Posyandu')
@section('page-title', 'Laporan Posyandu')
@section('page-subtitle', 'Rekapitulasi data yang sudah divalidasi Bidan')

@section('content')
    <!-- FILTER -->
    <div class="bg-white border border-kia-100 rounded-2xl p-5 shadow-sm mb-6 w-full">
        <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-kia-800 mb-1.5 uppercase tracking-wide">Bulan</label>
                <select name="bulan" class="w-full px-3 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none" onchange="this.form.submit()">
                    @foreach($bulanList as $num => $nama)
                        <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-kia-800 mb-1.5 uppercase tracking-wide">Tahun</label>
                <select name="tahun" class="w-full px-3 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none" onchange="this.form.submit()">
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <a href="{{ url()->current() }}" class="w-full px-4 py-2.5 bg-white border border-kia-200 text-kia-700 text-sm font-bold rounded-xl hover:bg-kia-50 text-center transition-colors">Reset Filter</a>
            </div>
        </form>
    </div>

    <!-- TOMBOL EXPORT -->
    <div class="bg-gradient-to-r from-kia-500 to-kia-600 rounded-2xl p-5 mb-6 flex flex-col md:flex-row justify-between items-center gap-3 shadow-md w-full">
        <div class="text-center md:text-left text-white">
            <strong class="block text-lg">Export Laporan</strong>
            <span class="text-sm text-kia-100">Periode {{ $bulanList[$bulan] ?? '-' }} {{ $tahun }}</span>
        </div>
        <div class="flex gap-2 w-full md:w-auto">
            <a href="{{ route('laporan-posyandu.pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="flex-1 md:flex-none px-4 py-2 bg-white text-red-600 rounded-lg font-bold hover:bg-gray-50 flex items-center justify-center gap-2 shadow-sm text-sm">
                📄 PDF
            </a>
            <a href="{{ route('laporan-posyandu.excel', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="flex-1 md:flex-none px-4 py-2 bg-white text-emerald-600 rounded-lg font-bold hover:bg-gray-50 flex items-center justify-center gap-2 shadow-sm text-sm">
                📊 Excel
            </a>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAMPILAN DESKTOP (TABEL) -->
    <!-- ========================================== -->
    <div class="hidden md:block bg-white border border-kia-100 rounded-2xl shadow-sm w-full overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="w-full min-w-[800px] text-left border-collapse">
                <thead class="bg-kia-50/80 border-b border-kia-100">
                    <tr>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Kategori</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Identitas</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Data Periksa</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Status Gizi</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Validasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-kia-50">
                    @forelse($laporans as $l)
                        @php $id = $l->identitas ?? []; $data = $l->data_pemeriksaan ?? []; $gizi = $l->status_gizi ?? []; @endphp
                        <tr class="hover:bg-kia-50/50 transition-colors">
                            <td class="px-6 py-4"><span class="px-2.5 py-1 bg-kia-100 text-kia-700 rounded-lg text-xs font-bold">{{ ucfirst($l->kategori) }}</span></td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-kia-900">{{ $id['nama'] ?? '-' }}</div>
                                <div class="text-xs text-gray-500">NIK: {{ $id['nik'] ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600">
                                @if($l->kategori === 'balita')
                                    BB: <strong class="text-gray-900">{{ $data['berat_badan'] ?? '-' }}</strong> kg | TB: <strong class="text-gray-900">{{ $data['tinggi_badan'] ?? '-' }}</strong> cm
                                @else
                                    BB: <strong class="text-gray-900">{{ $data['bb'] ?? '-' }}</strong> kg | LiLA: <strong class="text-gray-900">{{ $data['lila'] ?? '-' }}</strong> cm
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs">
                                @if($l->kategori === 'balita')
                                    <div class="mb-1 text-gray-700">BB/U: {{ $gizi['bb_u'] ?? '-' }}</div>
                                    <div class="text-gray-700">TB/U: {{ $gizi['tb_u'] ?? '-' }}</div>
                                @else
                                    <div class="mb-1 text-gray-700">KEK: {{ $gizi['kek'] ?? '-' }}</div>
                                    <div class="text-gray-700">HB: {{ $gizi['anemia'] ?? '-' }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <div class="text-emerald-600 font-bold">✅ Valid</div>
                                <div class="text-gray-400">{{ $l->tanggal_validasi?->format('d/m/y H:i') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-12 text-gray-400">Belum ada data laporan untuk periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAMPILAN MOBILE (CARD) -->
    <!-- ========================================== -->
    <div class="block md:hidden space-y-4 w-full">
        @forelse($laporans as $l)
            @php $id = $l->identitas ?? []; $data = $l->data_pemeriksaan ?? []; $gizi = $l->status_gizi ?? []; @endphp
            <div class="bg-white border border-kia-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="bg-kia-50/50 px-4 py-3 border-b border-kia-100 flex justify-between items-center">
                    <div>
                        <span class="text-[10px] font-bold text-kia-500 uppercase tracking-wider">{{ ucfirst($l->kategori) }}</span>
                        <h4 class="font-extrabold text-kia-900">{{ $id['nama'] ?? '-' }}</h4>
                    </div>
                    <span class="text-[10px] font-bold bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-lg">✅ Valid</span>
                </div>
                <div class="p-4 grid grid-cols-2 gap-3 text-sm">
                    @if($l->kategori === 'balita')
                        <div><span class="text-gray-400 text-[10px] font-bold block">BB</span> <span class="font-medium">{{ $data['berat_badan'] ?? '-' }} kg</span></div>
                        <div><span class="text-gray-400 text-[10px] font-bold block">TB</span> <span class="font-medium">{{ $data['tinggi_badan'] ?? '-' }} cm</span></div>
                        <div><span class="text-gray-400 text-[10px] font-bold block">BB/U</span> {{ $gizi['bb_u'] ?? '-' }}</div>
                        <div><span class="text-gray-400 text-[10px] font-bold block">TB/U</span> {{ $gizi['tb_u'] ?? '-' }}</div>
                    @else
                        <div><span class="text-gray-400 text-[10px] font-bold block">BB</span> <span class="font-medium">{{ $data['bb'] ?? '-' }} kg</span></div>
                        <div><span class="text-gray-400 text-[10px] font-bold block">LiLA</span> <span class="font-medium">{{ $data['lila'] ?? '-' }} cm</span></div>
                        <div><span class="text-gray-400 text-[10px] font-bold block">KEK</span> {{ $gizi['kek'] ?? '-' }}</div>
                        <div><span class="text-gray-400 text-[10px] font-bold block">HB</span> {{ $gizi['anemia'] ?? '-' }}</div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white border border-kia-100 rounded-2xl p-8 text-center text-gray-500 shadow-sm">Belum ada data laporan.</div>
        @endforelse
        
        <div class="mt-4">{{ $laporans->links() }}</div>
    </div>
@endsection