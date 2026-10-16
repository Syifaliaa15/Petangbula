@extends('layouts.dashboard')

@section('title', 'Buku KIA - ' . $balita->nama)
@section('page-title', 'Buku KIA Digital')
@section('page-subtitle', $balita->nama . ' | NIK: ' . ($balita->nik ?? '-'))

@section('content')
    <!-- Header Info Balita -->
    <div class="card" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h3 style="color: #831843; font-size: 1.3rem; font-weight: 800;">{{ $balita->nama }}</h3>
            <p style="color: #6b7280; font-size: 0.85rem; margin-top: 4px;">
                NIK: <strong>{{ $balita->nik ?? '-' }}</strong> | Ibu: <strong>{{ $balita->nama_ibu }}</strong> |
                Usia Berjalan: {{ $balita->umur_lengkap }}
            </p>
            @if($balita->posyandu)
                <p style="color: #ec4899; font-size: 0.8rem; margin-top: 4px; font-weight: 700;">
                    📍 {{ $balita->posyandu->nama_posyandu }}
                </p>
            @endif
        </div>
    </div>

    <!-- Layout Grid: Panel Tahun & Imunisasi vs Daftar Bulan -->
    <div style="display: grid; grid-template-columns: 300px 1fr; gap: 20px; align-items: start;">

        <!-- KOLOM KIRI: Panel Tahun & Imunisasi -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Panel Tahun -->
            <div class="card">
                <h3 class="card-title" style="font-size: 0.95rem;">📅 Periode Tahun</h3>
                @for($tahun = 1; $tahun <= 5; $tahun++)
                    <button onclick="pilihTahun({{ $tahun }}, this)"
                        class="menu-item tab-tahun {{ $tahun === 1 ? 'active' : '' }}"
                        style="width: 100%; margin-bottom: 4px;">
                        <span>📅 Tahun Ke-{{ $tahun }}</span>
                    </button>
                @endfor
            </div>

            <!-- Panel Imunisasi -->
            <div class="card">
                <h3 class="card-title" style="font-size: 0.95rem;">💉 Status Imunisasi</h3>
                @foreach($master_vaksin as $vaksin)
                    @php $vaks = $riwayat_imunisasi->get($vaksin); @endphp
                    <div style="padding: 10px; border: 1px solid #fbcfe8; border-radius: 10px; margin-bottom: 8px; font-size: 0.8rem;">
                        <div style="font-weight: 700; color: #831843;">{{ $vaksin }}</div>
                        @if($vaks)
                            <div style="color: #ec4899; font-size: 0.75rem;">
                                ✓ {{ $vaks->tanggal_vaksinasi->format('d M Y') }}
                            </div>
                        @else
                            <button onclick="bukaModalImunisasi('{{ $vaksin }}')"
                                style="margin-top: 6px; padding: 4px 10px; background: #f97316; color: #fff; border: none; border-radius: 6px; font-size: 0.7rem; font-weight: 700; cursor: pointer;">
                                + Input Tanggal
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- KOLOM KANAN: Daftar Bulan -->
        <div class="card">
            @for($tahun = 1; $tahun <= 5; $tahun++)
                <div id="konten-tahun-{{ $tahun }}" class="tahun-content" style="{{ $tahun === 1 ? '' : 'display: none;' }}">
                    <h3 class="card-title">📋 Pengukuran Bulanan (Tahun Ke-{{ $tahun }})</h3>

                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        @for($m = 1; $m <= 12; $m++)
                            @php
                                $bulan_ke = (($tahun - 1) * 12) + ($m - 1);
                                $pemeriksaan = $riwayat_pemeriksaan->get($bulan_ke);
                                $isFuture = $bulan_ke > $usia_sekarang_bulan;
                            @endphp
                            <div style="padding: 12px 16px; border: 1px solid #fbcfe8; border-radius: 12px; background: {{ $isFuture ? '#f9fafb' : '#fff' }}; opacity: {{ $isFuture ? '0.6' : '1' }};">
                                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                                    <div style="flex: 1;">
                                        <div style="font-weight: 700; color: #831843;">
                                            Bulan {{ str_pad($m, 2, '0', STR_PAD_LEFT) }}
                                            <span style="font-size: 0.75rem; color: #9ca3af; font-weight: normal;">
                                                (Usia {{ $bulan_ke }} bulan)
                                            </span>
                                        </div>
                                        @if($pemeriksaan)
                                            <div style="font-size: 0.78rem; color: #6b7280; margin-top: 4px;">
                                                BB: <strong>{{ $pemeriksaan->berat_badan }} kg</strong> |
                                                TB: <strong>{{ $pemeriksaan->tinggi_badan }} cm</strong> |
                                                LK: <strong>{{ $pemeriksaan->lingkar_kepala ?? '-' }} cm</strong>
                                            </div>
                                            <div style="display: flex; gap: 4px; flex-wrap: wrap; margin-top: 6px;">
                                                @if($pemeriksaan->status_bb_u)
                                                    <span class="badge" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($pemeriksaan->status_bb_u) }}; color: #4a1d3a; font-size: 0.68rem;">
                                                        BB/U: {{ $pemeriksaan->status_bb_u }}
                                                    </span>
                                                @endif
                                                @if($pemeriksaan->status_tb_u)
                                                    <span class="badge" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($pemeriksaan->status_tb_u) }}; color: #4a1d3a; font-size: 0.68rem;">
                                                        TB/U: {{ $pemeriksaan->status_tb_u }}
                                                    </span>
                                                @endif
                                                @if($pemeriksaan->status_bb_tb)
                                                    <span class="badge" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($pemeriksaan->status_bb_tb) }}; color: #4a1d3a; font-size: 0.68rem;">
                                                        BB/TB: {{ $pemeriksaan->status_bb_tb }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        @if($isFuture)
                                            <span class="badge badge-secondary">🔒 Terkunci</span>
                                        @elseif($pemeriksaan)
                                        @if($pemeriksaan->status_review === 'pending')
                                            <span class="badge badge-warning">⏳ Review Bidan</span>
                                            <a href="{{ route('balita.checkup', ['id' => $balita->id, 'bulan_ke' => $bulan_ke]) }}" 
                                            class="btn-action btn-edit" title="Edit">✏️</a>
                                            <form action="{{ route('balita.pemeriksaan.hapus', $pemeriksaan->id) }}" 
                                                method="POST" style="display:inline;" 
                                                onsubmit="return confirm('Yakin hapus data bulan {{ $m }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-action btn-delete" title="Hapus">🗑️</button>
                                            </form>
                                        @elseif($pemeriksaan->status_review === 'disetujui')
                                            <span class="badge badge-success">✅ Disetujui</span>
                                            <span style="font-size: 0.7rem; color: #9ca3af;" title="Data terkunci">🔒</span>
                                        @else
                                            <a href="{{ route('balita.checkup', ['id' => $balita->id, 'bulan_ke' => $bulan_ke]) }}" 
                                            class="btn-action" style="background: #ef4444; color: #fff;">🔧 Perbaiki</a>
                                            <form action="{{ route('balita.pemeriksaan.hapus', $pemeriksaan->id) }}" 
                                                method="POST" style="display:inline;" 
                                                onsubmit="return confirm('Yakin hapus data bulan {{ $m }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-action btn-delete" title="Hapus">🗑️</button>
                                            </form>
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
                                            })" class="btn-action btn-edit">🔍 Detail</button>
                                        @else
                                            <a href="{{ route('balita.checkup', ['id' => $balita->id, 'bulan_ke' => $bulan_ke]) }}" class="btn-action btn-edit">+ Input</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <!-- MODAL DETAIL -->
    <div id="modal-detail" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: #fff; border-radius: 20px; max-width: 480px; width: 100%; overflow: hidden;">
            <div style="background: linear-gradient(135deg, #ec4899, #f43f5e); padding: 16px 20px; color: #fff; display: flex; justify-content: space-between;">
                <strong>Detail Pengukuran Bulan <span id="md-bulan"></span></strong>
                <button onclick="tutupModal('modal-detail')" style="background: none; border: none; color: #fff; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            <div style="padding: 20px; font-size: 0.9rem;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                    <div><strong>BB:</strong> <span id="md-bb"></span> kg</div>
                    <div><strong>TB:</strong> <span id="md-tb"></span> cm</div>
                    <div><strong>LK:</strong> <span id="md-lk"></span> cm</div>
                </div>
                <div style="margin-bottom: 8px;"><strong>BB/U:</strong> <span id="md-bbu"></span></div>
                <div style="margin-bottom: 8px;"><strong>TB/U:</strong> <span id="md-tbu"></span></div>
                <div style="margin-bottom: 8px;"><strong>BB/TB:</strong> <span id="md-bbtb"></span></div>
                <hr style="margin: 12px 0; border: 0; border-top: 1px solid #fbcfe8;">
                <div><strong>Catatan Bidan:</strong></div>
                <div id="md-catatan" style="font-style: italic; color: #6b7280; margin-top: 4px;"></div>
            </div>
        </div>
    </div>

    <!-- MODAL IMUNISASI -->
    <div id="modal-imunisasi" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: #fff; border-radius: 20px; max-width: 420px; width: 100%;">
            <div style="background: linear-gradient(135deg, #f97316, #fb923c); padding: 16px 20px; color: #fff; display: flex; justify-content: space-between;">
                <strong>Catat Imunisasi: <span id="text-nama-vaksin"></span></strong>
                <button onclick="tutupModal('modal-imunisasi')" style="background: none; border: none; color: #fff; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('balita.imunisasi.store', $balita->id) }}" method="POST" style="padding: 20px;">
                @csrf
                <input type="hidden" name="jenis_vaksin" id="input-jenis-vaksin">
                <div class="form-group">
                    <label>Tanggal Vaksinasi</label>
                    <input type="date" name="tanggal_vaksinasi" class="form-control"
                        min="{{ $balita->tanggal_lahir->format('Y-m-d') }}"
                        max="{{ date('Y-m-d') }}" required>
                </div>
                <div style="display: flex; gap: 8px; margin-top: 16px;">
                    <button type="submit" class="btn-primary" style="flex: 1;">Simpan</button>
                    <button type="button" onclick="tutupModal('modal-imunisasi')" class="btn-secondary" style="flex: 1;">Batal</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function pilihTahun(tahun, btn) {
    document.querySelectorAll('.tahun-content').forEach(el => el.style.display = 'none');
    document.getElementById('konten-tahun-' + tahun).style.display = 'block';
    document.querySelectorAll('.tab-tahun').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

function tutupModal(id) {
    document.getElementById(id).style.display = 'none';
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
    document.getElementById('modal-detail').style.display = 'flex';
}

function bukaModalImunisasi(vaksin) {
    document.getElementById('input-jenis-vaksin').value = vaksin;
    document.getElementById('text-nama-vaksin').innerText = vaksin;
    document.getElementById('modal-imunisasi').style.display = 'flex';
}
</script>
@endpush