@extends('layouts.dashboard')

@section('title', 'Validasi Data Ibu Hamil')
@section('page-title', 'Validasi Data Ibu Hamil')
@section('page-subtitle', 'Verifikasi data pemeriksaan bumil dari Kader')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-extrabold text-kia-900 tracking-tight">Menunggu Validasi</h2>
            <p class="text-sm text-gray-500 mt-1 font-medium">Total: <span class="font-bold text-kia-600">{{ $pemeriksaans->count() }}</span> pemeriksaan ibu hamil</p>
        </div>
    </div>

    @if($pemeriksaans->count() > 0)
    <form id="form-bulk-bumil" action="{{ route('bidan.validasi.bumil.bulk') }}" method="POST">
        @csrf
        <input type="hidden" name="aksi" id="bulk-aksi-bumil" value="disetujui">
        <input type="hidden" name="catatan" id="bulk-catatan-bumil" value="">

        <!-- Panel Aksi Bulk -->
        <div class="bg-kia-50 border border-kia-200 rounded-2xl p-4 mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-sm">
            <label class="flex items-center gap-2 cursor-pointer font-bold text-kia-900 text-sm">
                <input type="checkbox" id="check-all-bumil" onclick="toggleAllBumil(this)" class="w-5 h-5 accent-kia-500 rounded">
                Pilih Semua Data
            </label>
            <div class="text-sm text-gray-600 font-medium">Terpilih: <strong id="count-selected-bumil" class="text-kia-600">0</strong> data</div>
            <div class="flex gap-2 w-full md:w-auto">
                <button type="button" onclick="bulkActionBumil('disetujui')" class="flex-1 md:flex-none px-4 py-2 bg-emerald-500 text-white text-sm font-bold rounded-xl hover:bg-emerald-600 shadow-sm transition-colors">
                    ✅ Setujui Terpilih
                </button>
                <button type="button" onclick="bulkActionBumil('perbaikan')" class="flex-1 md:flex-none px-4 py-2 bg-red-500 text-white text-sm font-bold rounded-xl hover:bg-red-600 shadow-sm transition-colors">
                    🔧 Minta Perbaikan
                </button>
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
                            <th class="px-6 py-4 w-12 text-center"></th>
                            <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Nama Ibu Hamil</th>
                            <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Posyandu</th>
                            <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Data Periksa</th>
                            <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Status Kesehatan</th>
                            <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase text-center w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-kia-50">
                        @foreach($pemeriksaans as $p)
                        <tr class="hover:bg-kia-50/50 transition-colors">
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" name="ids[]" value="{{ $p->id }}" class="check-item-bumil w-4 h-4 accent-kia-500 rounded" onclick="updateCountBumil()">
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-kia-900">{{ $p->ibuHamil->nama ?? '-' }}</div>
                                <div class="text-xs text-gray-500">Hamil Ke-{{ $p->ibuHamil->hamil_ke ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-2 py-1 bg-kia-50 border border-kia-100 text-kia-700 rounded text-xs font-bold">{{ $p->ibuHamil->posyandu->nama_posyandu ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div>BB: <strong>{{ $p->bb }} kg</strong> | LiLA: <strong>{{ $p->lila }} cm</strong></div>
                                <div>HB: <strong>{{ $p->hemoglobin ?? '-' }} g/dL</strong></div>
                                <div class="text-xs text-gray-400 mt-1">Minggu: {{ $p->minggu_ke }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1.5 items-start">
                                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold border" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($p->status_kek) }}; border-color: rgba(0,0,0,0.1);">
                                        KEK: {{ $p->status_kek ?? '-' }}
                                    </span>
                                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold border" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($p->status_anemia) }}; border-color: rgba(0,0,0,0.1);">
                                        HB: {{ $p->status_anemia ?? '-' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button type="button" onclick="bukaModalValidasiBumil({
                                    id: '{{ $p->id }}',
                                    nama: '{{ addslashes($p->ibuHamil->nama ?? '-') }}',
                                    minggu: '{{ $p->minggu_ke }}',
                                    tgl: '{{ $p->tanggal_periksa->format('d M Y') }}',
                                    bb: '{{ $p->bb }}',
                                    lila: '{{ $p->lila }}',
                                    hb: '{{ $p->hemoglobin ?? "-" }}',
                                    td: '{{ $p->tekanan_darah ?? "-" }}',
                                    ttd: '{{ $p->tablet_tambah_darah }}',
                                    pmt: '{{ $p->pmt_pemulihan ? "Ya" : "Tidak" }}',
                                    kek: '{{ $p->status_kek ?? "-" }}',
                                    anemia: '{{ $p->status_anemia ?? "-" }}',
                                    statusTd: '{{ $p->status_td ?? "-" }}'
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

        <!-- ========================================== -->
        <!-- TAMPILAN MOBILE (CARD) -->
        <!-- ========================================== -->
        <div class="block md:hidden space-y-4">
            @foreach($pemeriksaans as $p)
            <div class="bg-white border border-kia-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="bg-kia-50/50 px-4 py-3 border-b border-kia-100 flex justify-between items-center">
                    <div>
                        <h3 class="font-extrabold text-kia-900 text-lg">{{ $p->ibuHamil->nama ?? '-' }}</h3>
                        <p class="text-xs text-gray-500 font-medium">Hamil Ke-{{ $p->ibuHamil->hamil_ke ?? '-' }}</p>
                    </div>
                    <input type="checkbox" name="ids[]" value="{{ $p->id }}" class="check-item-bumil w-5 h-5 accent-kia-500 rounded" onclick="updateCountBumil()">
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                        <div>
                            <span class="text-gray-400 text-[10px] font-bold block">POSYANDU</span>
                            <span class="font-medium text-gray-800">{{ $p->ibuHamil->posyandu->nama_posyandu ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 text-[10px] font-bold block">MINGGU KE</span>
                            <span class="font-medium text-gray-800">{{ $p->minggu_ke }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 text-[10px] font-bold block">BB / LiLA</span>
                            <span class="font-medium text-gray-800">{{ $p->bb }} kg / {{ $p->lila }} cm</span>
                        </div>
                        <div>
                            <span class="text-gray-400 text-[10px] font-bold block">HB</span>
                            <span class="font-medium text-gray-800">{{ $p->hemoglobin ?? '-' }} g/dL</span>
                        </div>
                    </div>
                    <div class="bg-kia-50/50 border border-kia-100 rounded-xl p-3 mb-4 flex flex-col gap-2">
                        <span class="text-[10px] text-gray-500 font-bold block">STATUS KESEHATAN</span>
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-block px-2 py-1 rounded-md text-xs font-bold border" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($p->status_kek) }}; border-color: rgba(0,0,0,0.1);">
                                KEK: {{ $p->status_kek ?? '-' }}
                            </span>
                            <span class="inline-block px-2 py-1 rounded-md text-xs font-bold border" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($p->status_anemia) }}; border-color: rgba(0,0,0,0.1);">
                                HB: {{ $p->status_anemia ?? '-' }}
                            </span>
                        </div>
                    </div>
                    <button type="button" onclick="bukaModalValidasiBumil({
                        id: '{{ $p->id }}',
                        nama: '{{ addslashes($p->ibuHamil->nama ?? '-') }}',
                        minggu: '{{ $p->minggu_ke }}',
                        tgl: '{{ $p->tanggal_periksa->format('d M Y') }}',
                        bb: '{{ $p->bb }}',
                        lila: '{{ $p->lila }}',
                        hb: '{{ $p->hemoglobin ?? "-" }}',
                        td: '{{ $p->tekanan_darah ?? "-" }}',
                        ttd: '{{ $p->tablet_tambah_darah }}',
                        pmt: '{{ $p->pmt_pemulihan ? "Ya" : "Tidak" }}',
                        kek: '{{ $p->status_kek ?? "-" }}',
                        anemia: '{{ $p->status_anemia ?? "-" }}',
                        statusTd: '{{ $p->status_td ?? "-" }}'
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
            <p class="text-sm">Semua data pemeriksaan ibu hamil sudah divalidasi.</p>
        </div>
    @endif

    <!-- MODAL VALIDASI BUMIL -->
    <div id="modal-validasi-bumil" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl">
            <div class="bg-gradient-to-r from-kia-500 to-kia-600 p-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg">Review: <span id="mv-nama"></span></h3>
                <button onclick="document.getElementById('modal-validasi-bumil').classList.replace('flex', 'hidden')" class="text-2xl leading-none hover:text-kia-100">&times;</button>
            </div>

            <form id="form-validasi-bumil" method="POST">
                @csrf
                <input type="hidden" name="aksi" id="input-aksi-bumil" value="disetujui">

                <div class="p-5">
                    <div class="grid grid-cols-2 gap-4 bg-kia-50/50 p-4 rounded-xl border border-kia-100 mb-5 text-sm">
                        <div><span class="text-gray-400 text-xs block mb-0.5">Minggu Ke</span> <strong id="mv-minggu"></strong></div>
                        <div><span class="text-gray-400 text-xs block mb-0.5">Tanggal</span> <strong id="mv-tgl"></strong></div>
                        <div><span class="text-gray-400 text-xs block mb-0.5">Berat Badan</span> <strong id="mv-bb"></strong> kg</div>
                        <div><span class="text-gray-400 text-xs block mb-0.5">LiLA</span> <strong id="mv-lila"></strong> cm</div>
                        <div><span class="text-gray-400 text-xs block mb-0.5">HB</span> <strong id="mv-hb"></strong> g/dL</div>
                        <div><span class="text-gray-400 text-xs block mb-0.5">Tekanan Darah</span> <strong id="mv-td"></strong></div>
                        <div><span class="text-gray-400 text-xs block mb-0.5">Tablet Darah</span> <strong id="mv-ttd"></strong> Bks</div>
                        <div><span class="text-gray-400 text-xs block mb-0.5">PMT</span> <strong id="mv-pmt"></strong></div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 mb-5">
                        <h4 class="font-bold text-kia-900 mb-2 border-b border-gray-200 pb-2">Status Kesehatan</h4>
                        <div class="flex flex-col gap-2 text-sm">
                            <div class="flex justify-between"><span>Status KEK</span> <strong id="mv-kek" class="text-kia-700"></strong></div>
                            <div class="flex justify-between"><span>Status Anemia</span> <strong id="mv-anemia" class="text-kia-700"></strong></div>
                            <div class="flex justify-between"><span>Status TD</span> <strong id="mv-status-td" class="text-kia-700"></strong></div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-kia-900 mb-2">Catatan untuk Kader (opsional)</label>
                        <textarea name="catatan" class="w-full px-3 py-2 border border-kia-200 rounded-xl bg-white focus:ring-2 focus:ring-kia-500 outline-none text-sm" rows="3" placeholder="Isi jika ada koreksi..."></textarea>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 border-t border-kia-100 flex gap-3">
                    <button type="button" onclick="setAksiBumil('disetujui')" class="flex-1 py-2.5 bg-emerald-500 text-white font-bold rounded-xl hover:bg-emerald-600 shadow-sm transition-colors">✅ Setujui</button>
                    <button type="button" onclick="setAksiBumil('perbaikan')" class="flex-1 py-2.5 bg-red-500 text-white font-bold rounded-xl hover:bg-red-600 shadow-sm transition-colors">🔧 Minta Perbaikan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function toggleAllBumil(el) {
    document.querySelectorAll('.check-item-bumil').forEach(cb => cb.checked = el.checked);
    updateCountBumil();
}

function updateCountBumil() {
    const count = document.querySelectorAll('.check-item-bumil:checked').length;
    document.getElementById('count-selected-bumil').innerText = count;
    const total = document.querySelectorAll('.check-item-bumil').length;
    const checkAll = document.getElementById('check-all-bumil');
    if(checkAll) checkAll.checked = (count === total && total > 0);
}

function bulkActionBumil(aksi) {
    const selected = document.querySelectorAll('.check-item-bumil:checked');
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

    document.getElementById('bulk-aksi-bumil').value = aksi;
    document.getElementById('bulk-catatan-bumil').value = catatan;
    document.getElementById('form-bulk-bumil').submit();
}

function bukaModalValidasiBumil(data) {
    document.getElementById('mv-nama').innerText = data.nama;
    document.getElementById('mv-minggu').innerText = data.minggu;
    document.getElementById('mv-tgl').innerText = data.tgl;
    document.getElementById('mv-bb').innerText = data.bb;
    document.getElementById('mv-lila').innerText = data.lila;
    document.getElementById('mv-hb').innerText = data.hb;
    document.getElementById('mv-td').innerText = data.td;
    document.getElementById('mv-ttd').innerText = data.ttd;
    document.getElementById('mv-pmt').innerText = data.pmt;
    document.getElementById('mv-kek').innerText = data.kek;
    document.getElementById('mv-anemia').innerText = data.anemia;
    document.getElementById('mv-status-td').innerText = data.statusTd;
    document.getElementById('form-validasi-bumil').action = '/bidan/validasi/bumil/' + data.id;
    document.getElementById('modal-validasi-bumil').classList.replace('hidden', 'flex');
}

function setAksiBumil(aksi) {
    document.getElementById('input-aksi-bumil').value = aksi;
    document.getElementById('form-validasi-bumil').submit();
}
</script>
@endpush