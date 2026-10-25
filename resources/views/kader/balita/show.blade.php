@extends('layouts.dashboard')

@section('title', 'Buku KIA - ' . $balita->nama)
@section('page-title', 'Buku KIA Digital')
@section('page-subtitle', $balita->nama . ' | NIK: ' . ($balita->nik ?? '-'))

@section('content')
    <!-- Header Info Balita -->
    <div class="card flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h3 class="text-xl font-extrabold text-kia-900">{{ $balita->nama }}</h3>
            <p class="text-sm text-text-medium mt-1">
                NIK: <strong>{{ $balita->nik ?? '-' }}</strong> | Ibu: <strong>{{ $balita->nama_ibu }}</strong> | 
                Usia Berjalan: <span class="font-bold text-kia-600">{{ $balita->umur_lengkap }}</span>
            </p>
            @if($balita->posyandu)
                <p class="text-xs text-kia-500 font-bold mt-2 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ $balita->posyandu->nama_posyandu }}
                </p>
            @endif
        </div>
    </div>

    <!-- Layout Grid: Panel Kiri (Tahun & Imunisasi) vs Kanan (Daftar Bulan) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- KOLOM KIRI: Panel Tahun & Imunisasi -->
        <div class="lg:col-span-4 flex flex-col gap-6">
            <!-- Panel Tahun -->
            <div class="card">
                <h3 class="card-title text-sm">📅 Periode Tahun</h3>
                <div class="flex flex-col gap-2">
                    @for($tahun = 1; $tahun <= 5; $tahun++)
                        <button onclick="pilihTahun({{ $tahun }}, this)"
                            class="tab-tahun flex items-center justify-between w-full px-4 py-2.5 rounded-lg text-sm font-bold transition-all {{ $tahun === 1 ? 'bg-kia-500 text-white shadow-md' : 'bg-white text-kia-700 border border-kia-200 hover:bg-kia-50' }}">
                            <span>Tahun Ke-{{ $tahun }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    @endfor
                </div>
            </div>

            <!-- Panel Imunisasi -->
            <div class="card">
                <h3 class="card-title text-sm">💉 Status Imunisasi</h3>
                <div class="flex flex-col gap-2">
                    @foreach($master_vaksin as $vaksin)
                        @php $vaks = $riwayat_imunisasi->get($vaksin); @endphp
                        <div class="flex items-center justify-between p-3 border border-kia-100 rounded-lg bg-kia-50/30">
                            <div>
                                <div class="font-bold text-kia-900 text-sm">{{ $vaksin }}</div>
                                @if($vaks)
                                    <div class="text-xs text-kia-600 font-semibold mt-0.5">✓ {{ $vaks->tanggal_vaksinasi->format('d M Y') }}</div>
                                @endif
                            </div>
                            @if(!$vaks)
                                <button onclick="bukaModalImunisasi('{{ $vaksin }}')" class="px-2.5 py-1 bg-amber-500 text-white rounded text-xs font-bold hover:bg-amber-600">
                                    + Input
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: Daftar Bulan -->
        <div class="lg:col-span-8 card">
            @for($tahun = 1; $tahun <= 5; $tahun++)
                <div id="konten-tahun-{{ $tahun }}" class="tahun-content" style="{{ $tahun === 1 ? '' : 'display: none;' }}">
                    <h3 class="card-title">📋 Pengukuran Bulanan (Tahun Ke-{{ $tahun }})</h3>

                    <div class="flex flex-col gap-3">
                        @for($m = 1; $m <= 12; $m++)
                            @php
                                $bulan_ke = (($tahun - 1) * 12) + ($m - 1);
                                $pemeriksaan = $riwayat_pemeriksaan->get($bulan_ke);
                                $isFuture = $bulan_ke > $usia_sekarang_bulan;
                            @endphp
                            
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center p-3 md:p-4 border rounded-xl gap-3 {{ $isFuture ? 'bg-gray-50 border-gray-100 opacity-60' : 'bg-white border-kia-200 shadow-sm' }}">
                                <div class="flex-1">
                                    <div class="font-extrabold text-kia-900">
                                        Bulan {{ str_pad($m, 2, '0', STR_PAD_LEFT) }}
                                        <span class="text-xs text-text-light font-normal ml-1">(Usia {{ $bulan_ke }} bln)</span>
                                    </div>
                                    
                                    @if($pemeriksaan)
                                        <div class="text-xs text-text-medium mt-1 flex gap-3">
                                            <span>BB: <strong>{{ $pemeriksaan->berat_badan }} kg</strong></span>
                                            <span>TB: <strong>{{ $pemeriksaan->tinggi_badan }} cm</strong></span>
                                            <span>LK: <strong>{{ $pemeriksaan->lingkar_kepala ?? '-' }} cm</strong></span>
                                        </div>
                                        <div class="flex gap-1.5 flex-wrap mt-2">
                                            @if($pemeriksaan->status_bb_u)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($pemeriksaan->status_bb_u) }}; border-color: rgba(0,0,0,0.1);">
                                                    BB/U: {{ $pemeriksaan->status_bb_u }}
                                                </span>
                                            @endif
                                            @if($pemeriksaan->status_tb_u)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($pemeriksaan->status_tb_u) }}; border-color: rgba(0,0,0,0.1);">
                                                    TB/U: {{ $pemeriksaan->status_tb_u }}
                                                </span>
                                            @endif
                                            @if($pemeriksaan->status_bb_tb)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($pemeriksaan->status_bb_tb) }}; border-color: rgba(0,0,0,0.1);">
                                                    BB/TB: {{ $pemeriksaan->status_bb_tb }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                                    @if($isFuture)
                                        <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded text-xs font-bold w-full text-center">🔒 Terkunci</span>
                                    @elseif($pemeriksaan)
                                        @if($pemeriksaan->status_review === 'pending')
                                            <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded text-xs font-bold">⏳ Review Bidan</span>
                                            <a href="{{ route('balita.checkup', ['id' => $balita->id, 'bulan_ke' => $bulan_ke]) }}" class="p-1.5 bg-kia-100 text-kia-700 rounded hover:bg-kia-200" title="Edit">✏️</a>
                                            <form action="{{ route('balita.pemeriksaan.hapus', $pemeriksaan->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus data bulan {{ $m }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-1.5 bg-red-50 text-red-600 rounded hover:bg-red-100" title="Hapus">🗑️</button>
                                            </form>
                                        @elseif($pemeriksaan->status_review === 'disetujui')
                                            <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded text-xs font-bold">✅ Disetujui</span>
                                        @else
                                            <a href="{{ route('balita.checkup', ['id' => $balita->id, 'bulan_ke' => $bulan_ke]) }}" class="px-2 py-1 bg-red-500 text-white rounded text-xs font-bold hover:bg-red-600">🔧 Perbaiki</a>
                                        @endif
                                        <button onclick="bukaModalDetail({
                                            bulan: '{{ $m }}',
                                            bb: '{{ $pemeriksaan->berat_badan }}',
                                            tb: '{{ $pemeriksaan->tinggi_badan }}',
                                            lk: '{{ $pemeriksaan->lingkar_kepala ?? "-" }}',
                                            bbu: '{{ $pemeriksaan->status_bb_u ?? "-" }}',
                                            tbu: '{{ $pemeriksaan->status_tb_u ?? "-" }}',
                                            bbtb: '{{ $pemeriksaan->status_bb_tb ?? "-" }}',
                                            catatan: '{{ addslashes($pemeriksaan->catatan_bidan ?? "Tidak ada catatan.") }}'
                                        })" class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-bold hover:bg-blue-200">🔍 Detail</button>
                                    @else
                                        <a href="{{ route('balita.checkup', ['id' => $balita->id, 'bulan_ke' => $bulan_ke]) }}" class="px-3 py-1.5 bg-kia-500 text-white rounded text-xs font-bold hover:bg-kia-600 w-full md:w-auto text-center transition-colors">+ Input Data</a>
                                    @endif
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <!-- MODAL DETAIL (Tailwind Murni) -->
    <div id="modal-detail" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md overflow-hidden shadow-2xl">
            <div class="bg-gradient-to-r from-kia-500 to-kia-600 p-4 flex justify-between items-center text-white">
                <h3 class="font-bold">Detail Bulan <span id="md-bulan"></span></h3>
                <button onclick="document.getElementById('modal-detail').classList.replace('flex', 'hidden')" class="text-2xl leading-none hover:text-kia-100">&times;</button>
            </div>
            <div class="p-5 text-sm">
                <div class="grid grid-cols-2 gap-4 mb-4 bg-kia-50 p-4 rounded-xl">
                    <div><span class="text-text-light block text-xs">Berat Badan</span> <strong id="md-bb" class="text-kia-900"></strong> kg</div>
                    <div><span class="text-text-light block text-xs">Tinggi Badan</span> <strong id="md-tb" class="text-kia-900"></strong> cm</div>
                    <div><span class="text-text-light block text-xs">Lingkar Kepala</span> <strong id="md-lk" class="text-kia-900"></strong> cm</div>
                </div>
                <div class="flex flex-col gap-2 mb-4">
                    <div class="flex justify-between border-b border-kia-100 pb-1"><span>BB/U:</span> <strong id="md-bbu"></strong></div>
                    <div class="flex justify-between border-b border-kia-100 pb-1"><span>TB/U:</span> <strong id="md-tbu"></strong></div>
                    <div class="flex justify-between border-b border-kia-100 pb-1"><span>BB/TB:</span> <strong id="md-bbtb"></strong></div>
                </div>
                <div class="bg-amber-50 p-3 rounded-lg border border-amber-200">
                    <span class="text-amber-800 font-bold block mb-1 text-xs">Catatan Bidan:</span>
                    <p id="md-catatan" class="text-amber-900 italic text-xs"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL IMUNISASI (Tailwind Murni) -->
    <div id="modal-imunisasi" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl">
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 p-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-sm">Catat Imunisasi: <span id="text-nama-vaksin"></span></h3>
                <button onclick="document.getElementById('modal-imunisasi').classList.replace('flex', 'hidden')" class="text-2xl leading-none hover:text-amber-100">&times;</button>
            </div>
            <form action="{{ route('balita.imunisasi.store', $balita->id) }}" method="POST" class="p-5">
                @csrf
                <input type="hidden" name="jenis_vaksin" id="input-jenis-vaksin">
                <div class="form-group">
                    <label>Tanggal Vaksinasi</label>
                    <input type="date" name="tanggal_vaksinasi" class="form-control" min="{{ $balita->tanggal_lahir->format('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                </div>
                <div class="flex gap-2 mt-4">
                    <button type="submit" class="btn-primary flex-1">Simpan</button>
                    <button type="button" onclick="document.getElementById('modal-imunisasi').classList.replace('flex', 'hidden')" class="btn-secondary flex-1">Batal</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function pilihTahun(tahun, btn) {
    // Sembunyikan semua konten
    document.querySelectorAll('.tahun-content').forEach(el => el.style.display = 'none');
    document.getElementById('konten-tahun-' + tahun).style.display = 'block';
    
    // Reset gaya semua tombol tab
    document.querySelectorAll('.tab-tahun').forEach(b => {
        b.className = 'tab-tahun flex items-center justify-between w-full px-4 py-2.5 rounded-lg text-sm font-bold transition-all bg-white text-kia-700 border border-kia-200 hover:bg-kia-50';
    });
    
    // Set tombol yang aktif
    btn.className = 'tab-tahun flex items-center justify-between w-full px-4 py-2.5 rounded-lg text-sm font-bold transition-all bg-kia-500 text-white shadow-md';
}

function bukaModalDetail(data) {
    document.getElementById('md-bulan').innerText = data.bulan.padStart(2, '0');
    document.getElementById('md-bb').innerText = data.bb;
    document.getElementById('md-tb').innerText = data.tb;
    document.getElementById('md-lk').innerText = data.lk;
    document.getElementById('md-bbu').innerText = data.bbu;
    document.getElementById('md-tbu').innerText = data.tbu;
    document.getElementById('md-bbtb').innerText = data.bbtb;
    document.getElementById('md-catatan').innerText = '"' + data.catatan + '"';
    document.getElementById('modal-detail').classList.replace('hidden', 'flex');
}

function bukaModalImunisasi(vaksin) {
    document.getElementById('input-jenis-vaksin').value = vaksin;
    document.getElementById('text-nama-vaksin').innerText = vaksin;
    document.getElementById('modal-imunisasi').classList.replace('hidden', 'flex');
}
</script>
@endpush