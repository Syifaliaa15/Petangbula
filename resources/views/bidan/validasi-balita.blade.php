@extends('layouts.dashboard')

@section('title', 'Validasi Data Balita')
@section('page-title', 'Validasi Data Balita')
@section('page-subtitle', 'Verifikasi data pemeriksaan yang dikirim oleh Kader')

@section('sidebar-menu')
    <a href="{{ route('bidan.dashboard') }}" class="menu-item">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
        Dashboard
    </a>
    <a href="{{ route('bidan.validasi.balita') }}" class="menu-item active">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
        Validasi Data Balita
    </a>
    <a href="{{ route('bidan.validasi.bumil') }}" class="menu-item">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
        Validasi Data Bumil
    </a>
    <a href="{{ route('bidan.darurat') }}" class="menu-item">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
        Pemeriksaan Darurat
    </a>
    <a href="{{ route('bidan.laporan.posyandu') }}" class="menu-item">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
        Laporan Posyandu
    </a>
    <a href="{{ route('bidan.laporan.darurat') }}" class="menu-item">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
        Laporan Darurat
    </a>
@endsection

@section('content')
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="color: #831843; font-size: 1.2rem; font-weight: 800;">Daftar Data Menunggu Validasi</h2>
            <p style="color: #6b7280; font-size: 0.85rem; margin-top: 4px;">
                Total: <strong>{{ $pemeriksaans->count() }}</strong> pemeriksaan
            </p>
        </div>
    </div>

    @if($pemeriksaans->count() > 0)
    <form id="form-bulk-balita" action="{{ route('bidan.validasi.balita.bulk') }}" method="POST">
        @csrf
        <input type="hidden" name="aksi" id="bulk-aksi-balita" value="disetujui">
        <input type="hidden" name="catatan" id="bulk-catatan-balita" value="">

        <div class="card" style="margin-bottom: 16px; padding: 16px; background: #fce7f3; border-color: #f9a8d4;">
            <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 700; color: #831843;">
                    <input type="checkbox" id="check-all-balita" onclick="toggleAllBalita(this)" style="width: 18px; height: 18px; accent-color: #ec4899;">
                    Pilih Semua
                </label>
                <span style="color: #9ca3af; font-size: 0.85rem;">|</span>
                <span style="color: #831843; font-size: 0.85rem;">
                    Terpilih: <strong id="count-selected-balita">0</strong> data
                </span>
                <div style="margin-left: auto; display: flex; gap: 8px;">
                    <button type="button" onclick="bulkActionBalita('disetujui')" 
                            class="btn-primary" style="background: linear-gradient(135deg, #22c55e, #16a34a); padding: 10px 20px; font-size: 0.85rem;">
                        ✅ Setujui Terpilih
                    </button>
                    <button type="button" onclick="bulkActionBalita('perbaikan')" 
                            class="btn-primary" style="background: linear-gradient(135deg, #ef4444, #dc2626); padding: 10px 20px; font-size: 0.85rem;">
                        🔧 Minta Perbaikan
                    </button>
                </div>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" onclick="toggleAllBalita(this)" style="width: 16px; height: 16px; accent-color: #ec4899;">
                        </th>
                        <th>No</th>
                        <th>Nama Balita</th>
                        <th>Posyandu</th>
                        <th>Umur</th>
                        <th>BB / TB</th>
                        <th>Status Gizi</th>
                        <th>Kader</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pemeriksaans as $index => $p)
                    <tr>
                        <td>
                            <input type="checkbox" name="ids[]" value="{{ $p->id }}" 
                                   class="check-item-balita" onclick="updateCountBalita()"
                                   style="width: 16px; height: 16px; accent-color: #ec4899;">
                        </td>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div style="font-weight: 700; color: #831843;">{{ $p->balita->nama ?? '-' }}</div>
                            <div style="font-size: 0.75rem; color: #9ca3af;">Ibu: {{ $p->balita->nama_ibu ?? '-' }}</div>
                        </td>
                        <td>
                            @if($p->balita && $p->balita->posyandu)
                                <span class="badge badge-info">{{ $p->balita->posyandu->nama_posyandu }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $p->balita->umur_lengkap ?? ($p->umur_bulan . ' bulan') }}</td>
                        <td>
                            <div style="font-weight: 700;">{{ $p->berat_badan }} kg / {{ $p->tinggi_badan }} cm</div>
                        </td>
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <span class="badge" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($p->status_bb_u) }}; color: #4a1d3a; font-size: 0.68rem;">
                                    BB/U: {{ $p->status_bb_u ?? '-' }}
                                </span>
                                <span class="badge" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($p->status_tb_u) }}; color: #4a1d3a; font-size: 0.68rem;">
                                    TB/U: {{ $p->status_tb_u ?? '-' }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 0.78rem;">{{ $p->kader->name ?? '-' }}</div>
                        </td>
                        <td style="text-align: center;">
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
                                bbtb: '{{ $p->status_bb_tb ?? "-" }}'
                            })" class="btn-action" style="background: linear-gradient(135deg, #ec4899, #f43f5e); color: #fff;">
                                🔍 Review
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>
    @else
        <div class="card" style="text-align: center; padding: 60px; color: #9ca3af;">
            <div style="font-size: 3rem; margin-bottom: 12px;">✅</div>
            <div style="font-weight: 700; color: #831843;">Tidak Ada Data Pending</div>
            <div style="font-size: 0.85rem; margin-top: 4px;">Semua data pemeriksaan sudah divalidasi.</div>
        </div>
    @endif

    <!-- MODAL VALIDASI -->
    <div id="modal-validasi" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 20px; overflow-y: auto;">
        <div style="background: #fff; border-radius: 20px; max-width: 560px; width: 100%; margin: auto;">
            <div style="background: linear-gradient(135deg, #ec4899, #f43f5e); padding: 16px 20px; color: #fff; display: flex; justify-content: space-between;">
                <strong>Review Data: <span id="mv-nama"></span></strong>
                <button onclick="tutupModal('modal-validasi')" style="background: none; border: none; color: #fff; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>

            <form id="form-validasi" method="POST" style="padding: 20px;">
                @csrf
                <input type="hidden" name="aksi" id="input-aksi" value="disetujui">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; background: #fff5f8; padding: 16px; border-radius: 12px; margin-bottom: 16px; font-size: 0.9rem;">
                    <div><strong>Tanggal:</strong><br><span id="mv-tgl"></span></div>
                    <div><strong>Umur:</strong><br><span id="mv-umur"></span> bulan</div>
                    <div><strong>BB:</strong><br><span id="mv-bb"></span> kg</div>
                    <div><strong>TB:</strong><br><span id="mv-tb"></span> cm</div>
                    <div><strong>LK:</strong><br><span id="mv-lk"></span> cm</div>
                </div>

                <div style="background: #f9fafb; padding: 12px; border-radius: 12px; margin-bottom: 16px;">
                    <div style="font-weight: 700; color: #831843; margin-bottom: 8px;">Status Gizi</div>
                    <div style="display: flex; flex-direction: column; gap: 6px; font-size: 0.85rem;">
                        <div>BB/U: <strong id="mv-bbu"></strong></div>
                        <div>TB/U: <strong id="mv-tbu"></strong></div>
                        <div>BB/TB: <strong id="mv-bbtb"></strong></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Catatan untuk Kader (opsional)</label>
                    <textarea name="catatan" class="form-control" rows="3" placeholder="Isi jika ada koreksi atau catatan medis..."></textarea>
                </div>

                <div style="display: flex; gap: 8px; margin-top: 20px;">
                    <button type="button" onclick="setAksi('disetujui')" class="btn-primary" style="flex: 1; background: linear-gradient(135deg, #22c55e, #16a34a);">✅ Setujui</button>
                    <button type="button" onclick="setAksi('perbaikan')" class="btn-primary" style="flex: 1; background: linear-gradient(135deg, #ef4444, #dc2626);">🔧 Minta Perbaikan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function tutupModal(id) { document.getElementById(id).style.display = 'none'; }

function toggleAllBalita(el) {
    document.querySelectorAll('.check-item-balita').forEach(cb => cb.checked = el.checked);
    updateCountBalita();
}

function updateCountBalita() {
    const count = document.querySelectorAll('.check-item-balita:checked').length;
    document.getElementById('count-selected-balita').innerText = count;
    const total = document.querySelectorAll('.check-item-balita').length;
    document.getElementById('check-all-balita').checked = (count === total && total > 0);
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
    document.getElementById('mv-bbu').innerText = data.bbu;
    document.getElementById('mv-tbu').innerText = data.tbu;
    document.getElementById('mv-bbtb').innerText = data.bbtb;
    document.getElementById('form-validasi').action = '/bidan/validasi/balita/' + data.id;
    document.getElementById('modal-validasi').style.display = 'flex';
}

function setAksi(aksi) {
    document.getElementById('input-aksi').value = aksi;
    document.getElementById('form-validasi').submit();
}
</script>
@endpush