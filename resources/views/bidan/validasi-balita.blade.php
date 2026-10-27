@extends('layouts.dashboard')

@section('title', 'Validasi Data Balita')
@section('page-title', 'Validasi Data Balita')
@section('page-subtitle', 'Verifikasi data pemeriksaan yang dikirim oleh Kader')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-extrabold text-kia-900 tracking-tight">Menunggu Validasi</h2>
            <p class="text-sm text-gray-500 mt-1 font-medium">Total: <span class="font-bold text-kia-600">{{ $pemeriksaans->count() }}</span> data</p>
        </div>
    </div>

    @if($pemeriksaans->count() > 0)
    <form id="form-bulk-balita" action="{{ route('bidan.validasi.balita.bulk') }}" method="POST">
        @csrf
        <input type="hidden" name="aksi" id="bulk-aksi-balita" value="disetujui">
        <input type="hidden" name="catatan" id="bulk-catatan-balita" value="">

        <div class="bg-kia-50 border border-kia-200 rounded-2xl p-4 mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-sm">
            <label class="flex items-center gap-2 cursor-pointer font-bold text-kia-900 text-sm">
                <input type="checkbox" id="check-all-balita" onclick="toggleAllBalita(this)" class="w-5 h-5 accent-kia-500 rounded">
                Pilih Semua Data
            </label>
            <div class="text-sm text-gray-600 font-medium">Terpilih: <strong id="count-selected-balita" class="text-kia-600">0</strong> data</div>
            <div class="flex gap-2 w-full md:w-auto">
                <button type="button" onclick="bulkActionBalita('disetujui')" class="flex-1 md:flex-none px-4 py-2 bg-emerald-500 text-white text-sm font-bold rounded-xl hover:bg-emerald-600 shadow-sm transition-colors">
                    ✅ Setujui Terpilih
                </button>
                <button type="button" onclick="bulkActionBalita('perbaikan')" class="flex-1 md:flex-none px-4 py-2 bg-red-500 text-white text-sm font-bold rounded-xl hover:bg-red-600 shadow-sm transition-colors">
                    🔧 Minta Perbaikan
                </button>
            </div>
        </div>

        <!-- DESKTOP -->
        <div class="hidden md:block bg-white border border-kia-100 rounded-2xl shadow-sm w-full overflow-hidden">
            <div class="overflow-x-auto w-full">
                <table class="w-full min-w-[800px] text-left border-collapse">
                    <thead class="bg-kia-50/80 border-b border-kia-100">
                        <tr>
                            <th class="px-6 py-4 w-12 text-center"></th>
                            <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Nama Balita / Ibu</th>
                            <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Posyandu</th>
                            <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Data Periksa & Catatan</th>
                            <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Status Gizi (BB/U)</th>
                            <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase text-center w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-kia-50">
                        @foreach($pemeriksaans as $p)
                        <tr class="hover:bg-kia-50/50 transition-colors">
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" name="ids[]" value="{{ $p->id }}" class="check-item-balita w-4 h-4 accent-kia-500 rounded" onclick="updateCountBalita()">
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-kia-900">{{ $p->balita->nama ?? '-' }}</div>
                                <div class="text-xs text-gray-500">Ibu: {{ $p->balita->nama_ibu ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-2 py-1 bg-kia-50 border border-kia-100 text-kia-700 rounded text-xs font-bold">{{ $p->balita->posyandu->nama_posyandu ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div>BB: <strong>{{ $p->berat_badan }} kg</strong> | TB: <strong>{{ $p->tinggi_badan }} cm</strong></div>
                                <div class="text-xs text-gray-500 mt-1 italic">Catatan: "{{ $p->keterangan ?? 'Tidak ada catatan' }}"</div>
                                <div class="text-xs text-kia-500 mt-1">Umur: {{ $p->umur_bulan }} bln</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold border" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($p->status_bb_u) }}; border-color: rgba(0,0,0,0.1);">
                                    {{ $p->status_bb_u ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button type="button" onclick="bukaModalValidasi({
                                    id: '{{ $p->id }}',
                                    nama: '{{ addslashes($p->balita->nama ?? '-') }}',
                                    tgl: '{{ $p->tanggal_periksa->format('d M Y') }}',
                                    umur: '{{ $p->umur_bulan }}',
                                    bb: '{{ $p->berat_badan }}',
                                    tb: '{{ $p->tinggi_badan }}',
                                    lk: '{{ $p->lingkar_kepala ?? "-" }}',
                                    bbu: '{{ $p->status_bb_u ?? "-" }}',
                                    tbu: '{{ $p->status_tb_u ?? "-" }}',
                                    bbtb: '{{ $p->status_bb_tb ?? "-" }}',
                                    catatan_kader: '{{ addslashes($p->keterangan ?? "Tidak ada catatan.") }}'
                                })" class="px-3 py-1.5 bg-kia-500 text-white rounded-lg text-xs font-bold hover:bg-kia-600 transition-colors shadow-sm">
                                    🔍 Review
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MOBILE -->
        <div class="block md:hidden space-y-4">
            @foreach($pemeriksaans as $p)
            <div class="bg-white border border-kia-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="bg-kia-50/50 px-4 py-3 border-b border-kia-100 flex justify-between items-center">
                    <div>
                        <h3 class="font-extrabold text-kia-900 text-lg">{{ $p->balita->nama ?? '-' }}</h3>
                        <p class="text-xs text-gray-500 font-medium">Ibu: {{ $p->balita->nama_ibu ?? '-' }}</p>
                    </div>
                    <input type="checkbox" name="ids[]" value="{{ $p->id }}" class="check-item-balita w-5 h-5 accent-kia-500 rounded" onclick="updateCountBalita()">
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                        <div>
                            <span class="text-gray-400 text-[10px] font-bold block">POSYANDU</span>
                            <span class="font-medium text-gray-800">{{ $p->balita->posyandu->nama_posyandu ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 text-[10px] font-bold block">UMUR</span>
                            <span class="font-medium text-gray-800">{{ $p->umur_bulan }} bln</span>
                        </div>
                        <div>
                            <span class="text-gray-400 text-[10px] font-bold block">BB / TB</span>
                            <span class="font-medium text-gray-800">{{ $p->berat_badan }} kg / {{ $p->tinggi_badan }} cm</span>
                        </div>
                    </div>

                    <div class="bg-gray-50 border border-gray-100 p-2 rounded-lg mb-3">
                        <span class="text-[10px] text-gray-500 font-bold block mb-1">CATATAN KADER</span>
                        <p class="text-xs text-gray-700 italic">"{{ $p->keterangan ?? 'Tidak ada catatan' }}"</p>
                    </div>

                    <div class="bg-kia-50/50 border border-kia-100 rounded-xl p-3 mb-4">
                        <span class="text-[10px] text-gray-500 font-bold block mb-1">STATUS BB/U</span>
                        <span class="inline-block px-2 py-1 rounded-md text-xs font-bold border" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($p->status_bb_u) }}; border-color: rgba(0,0,0,0.1);">
                            {{ $p->status_bb_u ?? '-' }}
                        </span>
                    </div>
                    
                    <button type="button" onclick="bukaModalValidasi({
                        id: '{{ $p->id }}',
                        nama: '{{ addslashes($p->balita->nama ?? '-') }}',
                        tgl: '{{ $p->tanggal_periksa->format('d M Y') }}',
                        umur: '{{ $p->umur_bulan }}',
                        bb: '{{ $p->berat_badan }}',
                        tb: '{{ $p->tinggi_badan }}',
                        lk: '{{ $p->lingkar_kepala ?? "-" }}',
                        bbu: '{{ $p->status_bb_u ?? "-" }}',
                        tbu: '{{ $p->status_tb_u ?? "-" }}',
                        bbtb: '{{ $p->status_bb_tb ?? "-" }}',
                        catatan_kader: '{{ addslashes($p->keterangan ?? "Tidak ada catatan.") }}'
                    })" class="w-full py-2 bg-gradient-to-r from-kia-500 to-kia-600 text-white rounded-xl text-sm font-bold shadow-sm">
                        🔍 Review Data
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </form>
    @else
        <div class="bg-white border border-kia-100 rounded-2xl p-12 text-center text-gray-500 shadow-sm">
            <div class="text-5xl mb-4">✅</div>
            <h3 class="text-lg font-bold text-kia-900 mb-1">Tidak Ada Data Pending</h3>
            <p class="text-sm">Semua data pemeriksaan balita sudah divalidasi.</p>
        </div>
    @endif

    <!-- MODAL VALIDASI -->
    <div id="modal-validasi" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl">
            <div class="bg-gradient-to-r from-kia-500 to-kia-600 p-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg">Review Data: <span id="mv-nama"></span></h3>
                <button onclick="document.getElementById('modal-validasi').classList.replace('flex', 'hidden')" class="text-2xl leading-none hover:text-kia-100">&times;</button>
            </div>

            <form id="form-validasi" method="POST">
                @csrf
                <input type="hidden" name="aksi" id="input-aksi" value="disetujui">

                <div class="p-5">
                    <div class="grid grid-cols-2 gap-4 bg-kia-50/50 p-4 rounded-xl border border-kia-100 mb-4 text-sm">
                        <div><span class="text-gray-400 text-xs block mb-0.5">Tanggal</span> <strong id="mv-tgl"></strong></div>
                        <div><span class="text-gray-400 text-xs block mb-0.5">Umur</span> <strong id="mv-umur"></strong> bulan</div>
                        <div><span class="text-gray-400 text-xs block mb-0.5">Berat Badan</span> <strong id="mv-bb"></strong> kg</div>
                        <div><span class="text-gray-400 text-xs block mb-0.5">Tinggi Badan</span> <strong id="mv-tb"></strong> cm</div>
                        <div><span class="text-gray-400 text-xs block mb-0.5">Lingkar Kepala</span> <strong id="mv-lk"></strong> cm</div>
                    </div>

                    <!-- CATATAN KADER DI MODAL -->
                    <div class="bg-gray-50 border border-gray-100 p-3 rounded-xl mb-4">
                        <span class="text-xs text-gray-500 font-bold block mb-1">Catatan / Keluhan dari Kader:</span>
                        <p id="mv-catatan-kader" class="text-sm text-gray-800 italic"></p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 mb-5">
                        <h4 class="font-bold text-kia-900 mb-2 border-b border-gray-200 pb-2">Status Gizi (Z-Score)</h4>
                        <div class="flex flex-col gap-2 text-sm">
                            <div class="flex justify-between"><span>BB/U</span> <strong id="mv-bbu" class="text-kia-700"></strong></div>
                            <div class="flex justify-between"><span>TB/U</span> <strong id="mv-tbu" class="text-kia-700"></strong></div>
                            <div class="flex justify-between"><span>BB/TB</span> <strong id="mv-bbtb" class="text-kia-700"></strong></div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-kia-900 mb-2">Catatan untuk Kader (opsional)</label>
                        <textarea name="catatan" class="w-full px-3 py-2 border border-kia-200 rounded-xl bg-white focus:ring-2 focus:ring-kia-500 outline-none text-sm" rows="3" placeholder="Isi jika ada koreksi..."></textarea>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 border-t border-kia-100 flex gap-3">
                    <button type="button" onclick="setAksi('disetujui')" class="flex-1 py-2.5 bg-emerald-500 text-white font-bold rounded-xl hover:bg-emerald-600 shadow-sm transition-colors">✅ Setujui</button>
                    <button type="button" onclick="setAksi('perbaikan')" class="flex-1 py-2.5 bg-red-500 text-white font-bold rounded-xl hover:bg-red-600 shadow-sm transition-colors">🔧 Minta Perbaikan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function toggleAllBalita(el) {
    document.querySelectorAll('.check-item-balita').forEach(cb => cb.checked = el.checked);
    updateCountBalita();
}

function updateCountBalita() {
    const count = document.querySelectorAll('.check-item-balita:checked').length;
    document.getElementById('count-selected-balita').innerText = count;
    const total = document.querySelectorAll('.check-item-balita').length;
    const checkAll = document.getElementById('check-all-balita');
    if(checkAll) checkAll.checked = (count === total && total > 0);
}

function bulkActionBalita(aksi) {
    const selected = document.querySelectorAll('.check-item-balita:checked');
    if (selected.length === 0) {
        alert('Pilih minimal satu data terlebih dahulu.');
        return;
    }

    let catatan = '';
    if (aksi === 'perbaikan') {
        catatan = prompt('Masukkan catatan untuk kader (wajib):');
        if (!catatan || catatan.trim() === '') {
            alert('Catatan wajib diisi untuk minta perbaikan.');
            return;
        }
    }

    const label = aksi === 'disetujui' ? 'menyetujui' : 'meminta perbaikan';
    if (!confirm(`Yakin ${label} ${selected.length} data sekaligus?`)) return;

    document.getElementById('bulk-aksi-balita').value = aksi;
    document.getElementById('bulk-catatan-balita').value = catatan;
    document.getElementById('form-bulk-balita').submit();
}

function bukaModalValidasi(data) {
    document.getElementById('mv-nama').innerText = data.nama;
    document.getElementById('mv-tgl').innerText = data.tgl;
    document.getElementById('mv-umur').innerText = data.umur;
    document.getElementById('mv-bb').innerText = data.bb;
    document.getElementById('mv-tb').innerText = data.tb;
    document.getElementById('mv-lk').innerText = data.lk;
    document.getElementById('mv-catatan-kader').innerText = '"' + data.catatan_kader + '"';
    document.getElementById('mv-bbu').innerText = data.bbu;
    document.getElementById('mv-tbu').innerText = data.tbu;
    document.getElementById('mv-bbtb').innerText = data.bbtb;
    document.getElementById('form-validasi').action = '/bidan/validasi/balita/' + data.id;
    document.getElementById('modal-validasi').classList.replace('hidden', 'flex');
}

function setAksi(aksi) {
    document.getElementById('input-aksi').value = aksi;
    document.getElementById('form-validasi').submit();
}
</script>
@endpush