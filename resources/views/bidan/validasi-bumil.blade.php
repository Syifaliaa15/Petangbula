@extends('layouts.dashboard')

@section('title', 'Validasi Data Ibu Hamil')
@section('page-title', 'Validasi Data Ibu Hamil')
@section('page-subtitle', 'Verifikasi data pemeriksaan bumil dari Kader')

@section('sidebar-menu')
    <a href="{{ route('bidan.dashboard') }}" class="menu-item">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
        Dashboard
    </a>
    <a href="{{ route('bidan.validasi.balita') }}" class="menu-item">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
        Validasi Data Balita
    </a>
    <a href="{{ route('bidan.validasi.bumil') }}" class="menu-item active">
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
                Total: <strong>{{ $pemeriksaans->count() }}</strong> pemeriksaan ibu hamil
            </p>
        </div>
    </div>

    @if($pemeriksaans->count() > 0)
    <form id="form-bulk-bumil" action="{{ route('bidan.validasi.bumil.bulk') }}" method="POST">
        @csrf
        <input type="hidden" name="aksi" id="bulk-aksi-bumil" value="disetujui">
        <input type="hidden" name="catatan" id="bulk-catatan-bumil" value="">

        <div class="card" style="margin-bottom: 16px; padding: 16px; background: #fce7f3; border-color: #f9a8d4;">
            <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 700; color: #831843;">
                    <input type="checkbox" id="check-all-bumil" onclick="toggleAllBumil(this)" style="width: 18px; height: 18px; accent-color: #ec4899;">
                    Pilih Semua
                </label>
                <span style="color: #9ca3af; font-size: 0.85rem;">|</span>
                <span style="color: #831843; font-size: 0.85rem;">
                    Terpilih: <strong id="count-selected-bumil">0</strong> data
                </span>
                <div style="margin-left: auto; display: flex; gap: 8px;">
                    <button type="button" onclick="bulkActionBumil('disetujui')" 
                            class="btn-primary" style="background: linear-gradient(135deg, #22c55e, #16a34a); padding: 10px 20px; font-size: 0.85rem;">
                        ✅ Setujui Terpilih
                    </button>
                    <button type="button" onclick="bulkActionBumil('perbaikan')" 
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
                            <input type="checkbox" onclick="toggleAllBumil(this)" style="width: 16px; height: 16px; accent-color: #ec4899;">
                        </th>
                        <th>No</th>
                        <th>Nama Ibu Hamil</th>
                        <th>Posyandu</th>
                        <th>Minggu Ke</th>
                        <th>BB / LiLA / HB</th>
                        <th>Status Kesehatan</th>
                        <th>Kader</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pemeriksaans as $index => $p)
                    <tr>
                        <td>
                            <input type="checkbox" name="ids[]" value="{{ $p->id }}" 
                                   class="check-item-bumil" onclick="updateCountBumil()"
                                   style="width: 16px; height: 16px; accent-color: #ec4899;">
                        </td>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div style="font-weight: 700; color: #831843;">{{ $p->ibuHamil->nama ?? '-' }}</div>
                            <div style="font-size: 0.75rem; color: #9ca3af;">Hamil Ke-{{ $p->ibuHamil->hamil_ke ?? '-' }}</div>
                        </td>
                        <td>
                            @if($p->ibuHamil && $p->ibuHamil->posyandu)
                                <span class="badge badge-info">{{ $p->ibuHamil->posyandu->nama_posyandu }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td><span class="badge badge-success">Minggu {{ $p->minggu_ke }}</span></td>
                        <td>
                            <div style="font-size: 0.78rem;">
                                <div>BB: <strong>{{ $p->bb }} kg</strong></div>
                                <div>LiLA: <strong>{{ $p->lila }} cm</strong></div>
                                <div>HB: <strong>{{ $p->hemoglobin ?? '-' }} g/dL</strong></div>
                            </div>
                        </td>
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <span class="badge" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($p->status_kek) }}; color: #4a1d3a; font-size: 0.68rem;">
                                    KEK: {{ $p->status_kek ?? '-' }}
                                </span>
                                <span class="badge" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($p->status_anemia) }}; color: #4a1d3a; font-size: 0.68rem;">
                                    HB: {{ $p->status_anemia ?? '-' }}
                                </span>
                                <span class="badge" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($p->status_td) }}; color: #4a1d3a; font-size: 0.68rem;">
                                    TD: {{ $p->status_td ?? '-' }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 0.78rem;">{{ $p->kader->name ?? '-' }}</div>
                        </td>
                        <td style="text-align: center;">
                            <button type="button" onclick="bukaModalValidasi({
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
            <div style="font-size: 0.85rem; margin-top: 4px;">Semua data pemeriksaan ibu hamil sudah divalidasi.</div>
        </div>
    @endif

    <!-- MODAL VALIDASI -->
    <div id="modal-validasi" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 20px; overflow-y: auto;">
        <div style="background: #fff; border-radius: 20px; max-width: 560px; width: 100%; margin: auto;">
            <div style="background: linear-gradient(135deg, #ec4899, #f43f5e); padding: 16px 20px; color: #fff; display: flex; justify-content: space-between;">
                <strong>Review: <span id="mv-nama"></span></strong>
                <button onclick="tutupModal('modal-validasi')" style="background: none; border: none; color: #fff; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>

            <form id="form-validasi" method="POST" style="padding: 20px;">
                @csrf
                <input type="hidden" name="aksi" id="input-aksi" value="disetujui">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; background: #fff5f8; padding: 16px; border-radius: 12px; margin-bottom: 16px; font-size: 0.9rem;">
                    <div><strong>Minggu Ke:</strong><br><span id="mv-minggu"></span></div>
                    <div><strong>Tanggal:</strong><br><span id="mv-tgl"></span></div>
                    <div><strong>BB:</strong><br><span id="mv-bb"></span> kg</div>
                    <div><strong>LiLA:</strong><br><span id="mv-lila"></span> cm</div>
                    <div><strong>HB:</strong><br><span id="mv-hb"></span> g/dL</div>
                    <div><strong>TD:</strong><br><span id="mv-td"></span></div>
                    <div><strong>TTD:</strong><br><span id="mv-ttd"></span> Bks</div>
                    <div><strong>PMT:</strong><br><span id="mv-pmt"></span></div>
                </div>

                <div style="background: #f9fafb; padding: 12px; border-radius: 12px; margin-bottom: 16px;">
                    <div style="font-weight: 700; color: #831843; margin-bottom: 8px;">Status Kesehatan</div>
                    <div style="display: flex; flex-direction: column; gap: 6px; font-size: 0.85rem;">
                        <div>KEK: <strong id="mv-kek"></strong></div>
                        <div>Anemia: <strong id="mv-anemia"></strong></div>
                        <div>Status TD: <strong id="mv-status-td"></strong></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Catatan untuk Kader (opsional)</label>
                    <textarea name="catatan" class="form-control" rows="3" placeholder="Isi jika ada koreksi atau catatan medis..."></textarea>
                </div>

                <div style="display: flex; gap: 8px; margin-top: 20px;">
                    <button type="button" onclick="setAksi('disetujui')" class="btn-primary" style="flex: 1; background: linear-gradient(135deg, #22c55e, #16a34a);">
                        ✅ Setujui
                    </button>
                    <button type="button" onclick="setAksi('perbaikan')" class="btn-primary" style="flex: 1; background: linear-gradient(135deg, #ef4444, #dc2626);">
                        🔧 Minta Perbaikan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function tutupModal(id) { document.getElementById(id).style.display = 'none'; }

function toggleAllBumil(el) {
    document.querySelectorAll('.check-item-bumil').forEach(cb => cb.checked = el.checked);
    updateCountBumil();
}

function updateCountBumil() {
    const count = document.querySelectorAll('.check-item-bumil:checked').length;
    document.getElementById('count-selected-bumil').innerText = count;
    const total = document.querySelectorAll('.check-item-bumil').length;
    document.getElementById('check-all-bumil').checked = (count === total && total > 0);
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

function bukaModalValidasi(data) {
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
    document.getElementById('form-validasi').action = '/bidan/validasi/bumil/' + data.id;
    document.getElementById('modal-validasi').style.display = 'flex';
}

function setAksi(aksi) {
    document.getElementById('input-aksi').value = aksi;
    document.getElementById('form-validasi').submit();
}
</script>
@endpush