@extends('layouts.dashboard')

@section('title', 'Laporan Posyandu')
@section('page-title', 'Laporan Posyandu')
@section('page-subtitle', 'Rekapitulasi data yang sudah divalidasi Bidan')

@section('content')
    <!-- FILTER -->
    <div class="bg-white border border-kia-100 rounded-2xl p-4 shadow-sm mb-6 w-full">
        <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
            <div>
                <label class="block text-[10px] font-bold text-kia-800 mb-1 uppercase tracking-wider">Tipe Laporan</label>
                <select name="tipe_periode" class="w-full text-xs px-3 py-2 border border-kia-200 rounded-lg bg-kia-50/50 focus:bg-white focus:ring-1 focus:ring-kia-500 outline-none" onchange="this.form.submit()">
                    <option value="bulanan" {{ $tipe_periode == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                    <option value="tahunan" {{ $tipe_periode == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                </select>
            </div>
            
            <div class="{{ $tipe_periode == 'tahunan' ? 'opacity-50 pointer-events-none' : '' }}">
                <label class="block text-[10px] font-bold text-kia-800 mb-1 uppercase tracking-wider">Bulan</label>
                <select name="bulan" class="w-full text-xs px-3 py-2 border border-kia-200 rounded-lg bg-kia-50/50 focus:bg-white focus:ring-1 focus:ring-kia-500 outline-none" onchange="this.form.submit()">
                    @foreach($bulanList as $num => $nama)
                        <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-kia-800 mb-1 uppercase tracking-wider">Tahun</label>
                <select name="tahun" class="w-full text-xs px-3 py-2 border border-kia-200 rounded-lg bg-kia-50/50 focus:bg-white focus:ring-1 focus:ring-kia-500 outline-none" onchange="this.form.submit()">
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            @if(isset($posyandus) && (auth()->user()->isBidan() || auth()->user()->isAdmin()))
            <div>
                <label class="block text-[10px] font-bold text-kia-800 mb-1 uppercase tracking-wider">Posyandu</label>
                <select name="posyandu_id" class="w-full text-xs px-3 py-2 border border-kia-200 rounded-lg bg-kia-50/50 focus:bg-white focus:ring-1 focus:ring-kia-500 outline-none" onchange="this.form.submit()">
                    <option value="">Semua Posyandu</option>
                    @foreach($posyandus as $p)
                        <option value="{{ $p->id }}" {{ ($posyanduId ?? null) == $p->id ? 'selected' : '' }}>{{ $p->nama_posyandu }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block text-[10px] font-bold text-kia-800 mb-1 uppercase tracking-wider">Kategori</label>
                <select name="kategori" class="w-full text-xs px-3 py-2 border border-kia-200 rounded-lg bg-kia-50/50 focus:bg-white focus:ring-1 focus:ring-kia-500 outline-none" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    <option value="balita" {{ $kategori == 'balita' ? 'selected' : '' }}>Balita</option>
                    <option value="bumil" {{ $kategori == 'bumil' ? 'selected' : '' }}>Ibu Hamil</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-kia-800 mb-1 uppercase tracking-wider">Status Gizi</label>
                <select name="status_gizi" class="w-full text-xs px-3 py-2 border border-kia-200 rounded-lg bg-kia-50/50 focus:bg-white focus:ring-1 focus:ring-kia-500 outline-none" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="negatif" {{ $status_gizi == 'negatif' ? 'selected' : '' }}>Hanya Gizi Bermasalah</option>
                </select>
            </div>
            
            <div class="lg:col-span-6 flex justify-end mt-1">
                <a href="{{ url()->current() }}" class="text-xs text-kia-600 hover:text-kia-800 font-bold underline">Reset Filter</a>
            </div>
        </form>
    </div>

    <!-- TOMBOL EXPORT -->
    <div class="bg-gradient-to-r from-kia-50 to-white border border-kia-200 rounded-xl p-4 mb-6 flex flex-col md:flex-row justify-between items-center gap-3">
        <div class="text-center md:text-left">
            <strong class="text-kia-900 block">Export Laporan</strong>
            <span class="text-xs text-gray-500">
                Periode: {{ $tipe_periode == 'tahunan' ? 'Tahun '.$tahun : ($bulanList[$bulan] ?? '-').' '.$tahun }}
            </span>
        </div>
        <div class="flex gap-2 w-full md:w-auto">
            <a href="{{ route('laporan-posyandu.pdf', request()->all()) }}" class="flex-1 md:flex-none px-4 py-2 bg-white text-red-600 rounded-lg font-bold hover:bg-gray-50 flex items-center justify-center gap-2 shadow-sm text-sm border border-red-200">
                📄 PDF
            </a>
            <a href="{{ route('laporan-posyandu.excel', request()->all()) }}" class="flex-1 md:flex-none px-4 py-2 bg-white text-emerald-600 rounded-lg font-bold hover:bg-gray-50 flex items-center justify-center gap-2 shadow-sm text-sm border border-emerald-200">
                📊 Excel
            </a>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAMPILAN DESKTOP (TABEL) -->
    <!-- ========================================== -->
    <div class="hidden md:block bg-white border border-kia-100 rounded-2xl shadow-sm w-full overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="w-full min-w-[1200px] text-left border-collapse">
                <thead class="bg-kia-50/80 border-b border-kia-100">
                    <tr>
                        <th class="px-4 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Kategori</th>
                        <th class="px-4 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Posyandu & Identitas</th>
                        <th class="px-4 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Data Periksa</th>
                        <th class="px-4 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Status Gizi</th>
                        <th class="px-4 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Tindakan Bidan</th>
                        <th class="px-4 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Validasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-kia-50">
                    @forelse($laporans as $l)
                        @php 
                            $id = $l->identitas ?? []; 
                            $data = $l->data_pemeriksaan ?? []; 
                            $gizi = $l->status_gizi ?? []; 
                            $imun = $l->imunisasi ?? [];
                        @endphp
                        <tr class="hover:bg-kia-50/50 transition-colors">
                            <td class="px-4 py-4">
                                <span class="px-2.5 py-1 bg-kia-100 text-kia-700 rounded-lg text-xs font-bold">{{ ucfirst($l->kategori) }}</span>
                            </td>
                            <td class="px-4 py-4 text-xs">
                                <div class="text-kia-600 font-bold mb-1">{{ $l->posyandu->nama_posyandu ?? '-' }}</div>
                                <div class="font-bold text-kia-900 text-sm">{{ $id['nama'] ?? '-' }}</div>
                                <div class="text-gray-500 mt-0.5">NIK: {{ $id['nik'] ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-4 text-xs text-gray-700">
                                @if($l->kategori === 'balita')
                                    <div>BB: <strong>{{ $data['berat_badan'] ?? '-' }}</strong> kg | TB: <strong>{{ $data['tinggi_badan'] ?? '-' }}</strong> cm</div>
                                    <div>LIKA: <strong>{{ $data['lingkar_kepala'] ?? '-' }}</strong> cm | LILA: <strong>{{ $data['lila'] ?? '-' }}</strong> cm</div>
                                    <div class="mt-1 text-gray-600">
                                        Ukur: {{ ($data['cara_ukur'] ?? '') == 'berdiri' ? 'Berdiri' : 'Telentang' }} | 
                                        ASI: {{ !empty($data['asi_eksklusif']) ? 'Ya' : 'Tidak' }}
                                    </div>
                                @else
                                    <div>BB: <strong>{{ $data['bb'] ?? '-' }}</strong> kg | TB: <strong>{{ $data['tinggi_badan'] ?? '-' }}</strong> cm | LP: <strong>{{ $data['lingkar_perut'] ?? '-' }}</strong> cm</div>
                                    <div>LiLA: <strong>{{ $data['lila'] ?? '-' }}</strong> cm | HB: <strong>{{ $data['hemoglobin'] ?? '-' }}</strong> | TD: <strong>{{ $data['tekanan_darah'] ?? '-' }}</strong></div>
                                    <div class="mt-1 text-gray-600">
                                        TFU: {{ $data['tfu'] ?? '-' }} cm | 
                                        TBJ: {{ $data['taksiran_bb_janin'] ?? '-' }} gr
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-xs">
                                @if($l->kategori === 'balita')
                                    <div class="mb-1 text-gray-700">BB/U: <strong>{{ $gizi['bb_u'] ?? '-' }}</strong></div>
                                    <div class="mb-1 text-gray-700">TB/U: <strong>{{ $gizi['tb_u'] ?? '-' }}</strong></div>
                                    <div class="text-gray-700">BB/TB: <strong>{{ $gizi['bb_tb'] ?? '-' }}</strong></div>
                                @else
                                    <div class="mb-1 text-gray-700">KEK: <strong>{{ $gizi['kek'] ?? '-' }}</strong></div>
                                    <div class="mb-1 text-gray-700">HB: <strong>{{ $gizi['anemia'] ?? '-' }}</strong></div>
                                    <div class="text-gray-700">TD: <strong>{{ $gizi['td'] ?? '-' }}</strong></div>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-xs">
                                @if(!empty($data['tindakan_bidan']) && is_array($data['tindakan_bidan']))
                                    <ul class="list-disc pl-4 text-kia-700 font-medium">
                                        @foreach($data['tindakan_bidan'] as $tindakan)
                                            <li>{{ $tindakan }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-gray-400 italic">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-xs">
                                <div class="text-emerald-600 font-bold flex items-center gap-1 mb-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Valid
                                </div>
                                <div class="text-gray-500 truncate max-w-[120px]">{{ $l->validator->name ?? '-' }}</div>
                                <div class="text-gray-400 mt-0.5">{{ $l->tanggal_validasi?->format('d/m/y H:i') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-12 text-gray-400">Belum ada data laporan sesuai filter.</td></tr>
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
            @php 
                $id = $l->identitas ?? []; 
                $data = $l->data_pemeriksaan ?? []; 
                $gizi = $l->status_gizi ?? []; 
                $imun = $l->imunisasi ?? [];
            @endphp
            <div class="bg-white border border-kia-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="bg-kia-50/50 px-4 py-3 border-b border-kia-100 flex justify-between items-start">
                    <div>
                        <span class="text-[10px] font-bold text-kia-500 uppercase tracking-wider">{{ ucfirst($l->kategori) }} • {{ $l->posyandu->nama_posyandu ?? '-' }}</span>
                        <h4 class="font-extrabold text-kia-900 text-lg">{{ $id['nama'] ?? '-' }}</h4>
                        <p class="text-xs text-gray-500 font-medium">NIK: {{ $id['nik'] ?? '-' }}</p>
                    </div>
                    <span class="text-[10px] font-bold bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-lg flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Valid
                    </span>
                </div>
                
                <div class="p-4 grid grid-cols-2 gap-4 text-sm">
                    @if($l->kategori === 'balita')
                        <div>
                            <span class="text-gray-400 text-[10px] font-bold block mb-0.5">BERAT / TINGGI</span> 
                            <span class="font-bold text-gray-800">{{ $data['berat_badan'] ?? '-' }}kg / {{ $data['tinggi_badan'] ?? '-' }}cm</span>
                        </div>
                        <div>
                            <span class="text-gray-400 text-[10px] font-bold block mb-0.5">LIKA / LILA</span> 
                            <span class="font-bold text-gray-800">{{ $data['lingkar_kepala'] ?? '-' }}cm / {{ $data['lila'] ?? '-' }}cm</span>
                        </div>
                        <div>
                            <span class="text-gray-400 text-[10px] font-bold block mb-0.5">CARA UKUR</span> 
                            <span class="font-medium text-gray-800 capitalize">{{ $data['cara_ukur'] ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 text-[10px] font-bold block mb-0.5">ASI EKSKLUSIF</span> 
                            <span class="font-medium text-gray-800">{{ !empty($data['asi_eksklusif']) ? 'Ya' : 'Tidak' }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-400 text-[10px] font-bold block mb-0.5">STATUS GIZI (BB/U | TB/U | BB/TB)</span> 
                            <span class="font-medium text-gray-800">
                                {{ $gizi['bb_u'] ?? '-' }} | {{ $gizi['tb_u'] ?? '-' }} | {{ $gizi['bb_tb'] ?? '-' }}
                            </span>
                        </div>
                    @else
                        <div>
                            <span class="text-gray-400 text-[10px] font-bold block mb-0.5">BB / TB</span> 
                            <span class="font-bold text-gray-800">{{ $data['bb'] ?? '-' }}kg / {{ $data['tinggi_badan'] ?? '-' }}cm</span>
                        </div>
                        <div>
                            <span class="text-gray-400 text-[10px] font-bold block mb-0.5">LILA / LP</span> 
                            <span class="font-bold text-gray-800">{{ $data['lila'] ?? '-' }}cm / {{ $data['lingkar_perut'] ?? '-' }}cm</span>
                        </div>
                        <div>
                            <span class="text-gray-400 text-[10px] font-bold block mb-0.5">HB / TD</span> 
                            <span class="font-bold text-gray-800">{{ $data['hemoglobin'] ?? '-' }} / {{ $data['tekanan_darah'] ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 text-[10px] font-bold block mb-0.5">TFU</span> 
                            <span class="font-bold text-gray-800">{{ $data['tfu'] ?? '-' }}cm ({{ $data['kategori_tfu'] ?? '-' }})</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-400 text-[10px] font-bold block mb-0.5">TBJ (Janin)</span> 
                            <span class="font-medium text-gray-800">{{ $data['taksiran_bb_janin'] ?? '-' }}gr ({{ $data['kategori_janin'] ?? '-' }})</span>
                        </div>
                        <div class="col-span-2 bg-kia-50 p-2 rounded-lg border border-kia-100">
                            <span class="text-gray-400 text-[10px] font-bold block mb-0.5">STATUS KESEHATAN</span> 
                            <span class="font-medium text-gray-800">
                                KEK: {{ $gizi['kek'] ?? '-' }} | HB: {{ $gizi['anemia'] ?? '-' }} | TD: {{ $gizi['td'] ?? '-' }}
                            </span>
                        </div>
                    @endif

                    <!-- Tindakan Bidan (Mobile) -->
                    <div class="col-span-2 border-t border-gray-100 pt-3 mt-1">
                        <span class="text-gray-400 text-[10px] font-bold block mb-1">TINDAKAN BIDAN</span>
                        @if(!empty($data['tindakan_bidan']) && is_array($data['tindakan_bidan']))
                            <ul class="list-disc pl-4 text-kia-700 font-medium text-xs">
                                @foreach($data['tindakan_bidan'] as $tindakan)
                                    <li>{{ $tindakan }}</li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-gray-400 italic text-xs">Belum ada tindakan.</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white border border-kia-100 rounded-2xl p-8 text-center text-gray-500 shadow-sm">Belum ada data laporan.</div>
        @endforelse
        
        <div class="mt-4">{{ $laporans->links() }}</div>
    </div>
@endsection