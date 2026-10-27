@extends('layouts.dashboard')

@section('title', 'Buku KIA - ' . $ibuHamil->nama)
@section('page-title', 'Buku KIA Ibu Hamil')
@section('page-subtitle', $ibuHamil->nama . ' | Kehamilan Ke-' . $ibuHamil->hamil_ke)

@section('content')
    <!-- Header Info Ibu Hamil -->
    <div class="bg-white border border-kia-100 rounded-2xl shadow-sm p-5 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h3 class="text-xl font-extrabold text-kia-900">{{ $ibuHamil->nama }}</h3>
            <p class="text-sm text-gray-600 mt-1">
                NIK: <strong>{{ $ibuHamil->nik }}</strong> | Umur: <strong>{{ $ibuHamil->umur }} tahun</strong> | Hamil Ke-<strong>{{ $ibuHamil->hamil_ke }}</strong>
            </p>
            @if($ibuHamil->posyandu)
                <p class="text-xs text-kia-600 font-bold mt-2 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ $ibuHamil->posyandu->nama_posyandu }}
                </p>
            @endif
        </div>
        <div class="bg-kia-50 px-5 py-3 rounded-xl border border-kia-100 text-center w-full md:w-auto">
            <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Usia Kehamilan</div>
            <div class="text-xl text-kia-600 font-extrabold">{{ $ibuHamil->usia_kehamilan_lengkap }}</div>
        </div>
    </div>

    <!-- Layout Grid: Panel Imunisasi vs Daftar 40 Minggu -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- KOLOM KIRI: Panel Imunisasi TT -->
        <div class="lg:col-span-4 card">
            <h3 class="card-title text-sm">💉 Imunisasi TT & Vitamin A</h3>
            <div class="flex flex-col gap-2">
                @foreach($master_tt as $tt)
                    @php $vaks = $riwayat_imunisasi->get($tt); @endphp
                    <div class="flex items-center justify-between p-3 border border-kia-100 rounded-xl bg-kia-50/30">
                        <div>
                            <div class="font-bold text-kia-900 text-sm">{{ $tt }}</div>
                            @if($vaks)
                                <div class="text-xs text-kia-600 font-semibold mt-0.5">✓ {{ $vaks->tanggal_vaksinasi->format('d M Y') }}</div>
                            @endif
                        </div>
                        @if(!$vaks)
                            <button onclick="bukaModalTT('{{ $tt }}')" class="px-2.5 py-1 bg-amber-500 text-white rounded-lg text-xs font-bold hover:bg-amber-600 shadow-sm transition-colors">
                                + Input
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- KOLOM KANAN: Daftar 40 Minggu -->
        <div class="lg:col-span-8 card">
            <h3 class="card-title">📋 Pengukuran Mingguan (1 - 40 Minggu)</h3>
            
            <div class="flex flex-col gap-3 max-h-[700px] overflow-y-auto pr-2">
                @for($m = 1; $m <= 40; $m++)
                    @php
                        $pemeriksaan = $riwayat_pemeriksaan->get($m);
                        $isFuture = $m > $usia_kehamilan_minggu;
                    @endphp
                    
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center p-3 md:p-4 border rounded-xl gap-3 {{ $isFuture ? 'bg-gray-50 border-gray-100 opacity-60' : 'bg-white border-kia-200 shadow-sm' }}">
                        <div class="flex-1 w-full">
                            <div class="font-extrabold text-kia-900">
                                Minggu Ke-{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}
                            </div>
                            
                            @if($pemeriksaan)
                                <div class="text-xs text-gray-600 mt-1 flex flex-wrap gap-x-3 gap-y-1">
                                    <span>BB: <strong>{{ $pemeriksaan->bb }} kg</strong></span>
                                    <span>LiLA: <strong>{{ $pemeriksaan->lila }} cm</strong></span>
                                    <span>HB: <strong>{{ $pemeriksaan->hemoglobin ?? '-' }}</strong></span>
                                    <span>TTD: <strong>{{ $pemeriksaan->tablet_tambah_darah }} Bks</strong></span>
                                    @if($pemeriksaan->tekanan_darah)
                                        <span>TD: <strong>{{ $pemeriksaan->tekanan_darah }}</strong></span>
                                    @endif
                                </div>
                                <div class="flex gap-1.5 flex-wrap mt-2">
                                    @if($pemeriksaan->status_kek)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold border" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($pemeriksaan->status_kek) }}; border-color: rgba(0,0,0,0.1);">
                                            KEK: {{ $pemeriksaan->status_kek }}
                                        </span>
                                    @endif
                                    @if($pemeriksaan->status_anemia)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold border" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($pemeriksaan->status_anemia) }}; border-color: rgba(0,0,0,0.1);">
                                            HB: {{ $pemeriksaan->status_anemia }}
                                        </span>
                                    @endif
                                    @if($pemeriksaan->status_td)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold border" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($pemeriksaan->status_td) }}; border-color: rgba(0,0,0,0.1);">
                                            TD: {{ $pemeriksaan->status_td }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                            @if($isFuture)
                                <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-lg text-xs font-bold w-full text-center">🔒 Terkunci</span>
                            @elseif($pemeriksaan)
                                @if($pemeriksaan->status_review === 'pending')
                                    <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-lg text-xs font-bold">⏳ Review</span>
                                    <a href="{{ route('ibu-hamil.checkup', ['id' => $ibuHamil->id, 'minggu_ke' => $m]) }}" class="p-1.5 bg-kia-100 text-kia-700 rounded-lg hover:bg-kia-200" title="Edit">✏️</a>
                                    <form action="{{ route('ibu-hamil.pemeriksaan.hapus', $pemeriksaan->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus data minggu {{ $m }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100" title="Hapus">🗑️</button>
                                    </form>
                                @elseif($pemeriksaan->status_review === 'disetujui')
                                    <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold">✅ Disetujui</span>
                                @else
                                    <a href="{{ route('ibu-hamil.checkup', ['id' => $ibuHamil->id, 'minggu_ke' => $m]) }}" class="px-2 py-1 bg-red-500 text-white rounded-lg text-xs font-bold hover:bg-red-600">🔧 Perbaiki</a>
                                @endif
                                
                                <button onclick="bukaModalDetail({
                                    minggu: '{{ $m }}',
                                    bb: '{{ $pemeriksaan->bb }}',
                                    lila: '{{ $pemeriksaan->lila }}',
                                    hb: '{{ $pemeriksaan->hemoglobin ?? "-" }}',
                                    ttd: '{{ $pemeriksaan->tablet_tambah_darah }}',
                                    pmt: '{{ $pemeriksaan->pmt_pemulihan ? "Ya" : "Tidak" }}',
                                    td: '{{ $pemeriksaan->tekanan_darah ?? "-" }}',
                                    kek: '{{ $pemeriksaan->status_kek ?? "-" }}',
                                    anemia: '{{ $pemeriksaan->status_anemia ?? "-" }}',
                                    statusTd: '{{ $pemeriksaan->status_td ?? "-" }}',
                                    catatan_kader: '{{ addslashes($pemeriksaan->keterangan ?? "Tidak ada catatan.") }}',
                                    catatan_bidan: '{{ addslashes($pemeriksaan->catatan_bidan ?? "Belum ada catatan dari Bidan.") }}'
                                })" class="px-2 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-bold hover:bg-blue-200">🔍 Detail</button>
                            @else
                                <a href="{{ route('ibu-hamil.checkup', ['id' => $ibuHamil->id, 'minggu_ke' => $m]) }}" class="px-3 py-1.5 bg-kia-500 text-white rounded-lg text-xs font-bold hover:bg-kia-600 w-full md:w-auto text-center transition-colors">+ Input Data</a>
                            @endif
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- MODAL DETAIL (Tailwind Murni) -->
    <div id="modal-detail" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md overflow-hidden shadow-2xl">
            <div class="bg-gradient-to-r from-kia-500 to-kia-600 p-4 flex justify-between items-center text-white">
                <h3 class="font-bold">Detail Minggu Ke-<span id="md-minggu"></span></h3>
                <button onclick="document.getElementById('modal-detail').classList.replace('flex', 'hidden')" class="text-2xl leading-none hover:text-kia-100">&times;</button>
            </div>
            <div class="p-5 text-sm">
                <div class="grid grid-cols-2 gap-4 mb-4 bg-kia-50 p-4 rounded-xl">
                    <div><span class="text-gray-400 text-xs block mb-0.5">BB</span> <strong id="md-bb" class="text-kia-900"></strong> kg</div>
                    <div><span class="text-gray-400 text-xs block mb-0.5">LiLA</span> <strong id="md-lila" class="text-kia-900"></strong> cm</div>
                    <div><span class="text-gray-400 text-xs block mb-0.5">HB</span> <strong id="md-hb" class="text-kia-900"></strong> g/dL</div>
                    <div><span class="text-gray-400 text-xs block mb-0.5">TD</span> <strong id="md-td" class="text-kia-900"></strong></div>
                    <div><span class="text-gray-400 text-xs block mb-0.5">TTD</span> <strong id="md-ttd" class="text-kia-900"></strong> Bks</div>
                    <div><span class="text-gray-400 text-xs block mb-0.5">PMT</span> <strong id="md-pmt" class="text-kia-900"></strong></div>
                </div>
                <div class="flex flex-col gap-2 mb-4">
                    <div class="flex justify-between border-b border-kia-100 pb-1"><span>KEK:</span> <strong id="md-kek"></strong></div>
                    <div class="flex justify-between border-b border-kia-100 pb-1"><span>Anemia:</span> <strong id="md-anemia"></strong></div>
                    <div class="flex justify-between border-b border-kia-100 pb-1"><span>Status TD:</span> <strong id="md-status-td"></strong></div>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 mb-2">
                    <span class="text-gray-700 font-bold block mb-1 text-xs">Catatan / Keluhan Kader:</span>
                    <p id="md-catatan-kader" class="text-gray-800 italic text-xs"></p>
                </div>
                <div class="bg-amber-50 p-3 rounded-lg border border-amber-200">
                    <span class="text-amber-800 font-bold block mb-1 text-xs">Catatan dari Bidan:</span>
                    <p id="md-catatan-bidan" class="text-amber-900 italic text-xs"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL TT (Tailwind Murni) -->
    <div id="modal-tt" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl">
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 p-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-sm">Catat Imunisasi: <span id="text-tt"></span></h3>
                <button onclick="document.getElementById('modal-tt').classList.replace('flex', 'hidden')" class="text-2xl leading-none hover:text-amber-100">&times;</button>
            </div>
            <form action="{{ route('ibu-hamil.imunisasi.store', $ibuHamil->id) }}" method="POST" class="p-5">
                @csrf
                <input type="hidden" name="jenis_tt" id="input-jenis-tt">
                <div class="form-group">
                    <label class="block text-sm font-bold text-kia-900 mb-1.5">Tanggal Vaksinasi</label>
                    <input type="date" name="tanggal_vaksinasi" class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none" min="{{ $ibuHamil->tanggal_pendaftaran ? $ibuHamil->tanggal_pendaftaran->format('Y-m-d') : '' }}" max="{{ date('Y-m-d') }}" required>
                </div>
                <div class="flex gap-2 mt-4">
                    <button type="submit" class="flex-1 py-2.5 bg-gradient-to-r from-kia-500 to-kia-600 text-white font-bold rounded-xl shadow-sm">Simpan</button>
                    <button type="button" onclick="document.getElementById('modal-tt').classList.replace('flex', 'hidden')" class="flex-1 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 border border-gray-200">Batal</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function bukaModalDetail(data) {
    document.getElementById('md-minggu').innerText = data.minggu.padStart(2, '0');
    document.getElementById('md-bb').innerText = data.bb;
    document.getElementById('md-lila').innerText = data.lila;
    document.getElementById('md-hb').innerText = data.hb;
    document.getElementById('md-td').innerText = data.td;
    document.getElementById('md-ttd').innerText = data.ttd;
    document.getElementById('md-pmt').innerText = data.pmt;
    document.getElementById('md-kek').innerText = data.kek;
    document.getElementById('md-anemia').innerText = data.anemia;
    document.getElementById('md-status-td').innerText = data.statusTd;
    document.getElementById('md-catatan-kader').innerText = '"' + data.catatan_kader + '"';
    document.getElementById('md-catatan-bidan').innerText = '"' + data.catatan_bidan + '"';
    document.getElementById('modal-detail').classList.replace('hidden', 'flex');
}

function bukaModalTT(jenis) {
    document.getElementById('input-jenis-tt').value = jenis;
    document.getElementById('text-tt').innerText = jenis;
    document.getElementById('modal-tt').classList.replace('hidden', 'flex');
}
</script>
@endpush