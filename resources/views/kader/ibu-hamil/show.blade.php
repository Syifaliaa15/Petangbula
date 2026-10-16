@extends('layouts.dashboard')

@section('title', 'Buku KIA - ' . $ibuHamil->nama)
@section('page-title', 'Buku KIA Ibu Hamil')
@section('page-subtitle', $ibuHamil->nama . ' | Kehamilan Ke-' . $ibuHamil->hamil_ke)

@section('content')
    <!-- Header Info Ibu Hamil -->
    <div class="card" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h3 style="color: #831843; font-size: 1.3rem; font-weight: 800;">{{ $ibuHamil->nama }}</h3>
            <p style="color: #6b7280; font-size: 0.85rem; margin-top: 4px;">
                NIK: <strong>{{ $ibuHamil->nik }}</strong> |
                Umur: <strong>{{ $ibuHamil->umur }} tahun</strong> |
                Hamil Ke-<strong>{{ $ibuHamil->hamil_ke }}</strong>
            </p>
            @if($ibuHamil->posyandu)
                <p style="color: #ec4899; font-size: 0.8rem; margin-top: 4px; font-weight: 700;">
                    📍 {{ $ibuHamil->posyandu->nama_posyandu }}
                </p>
            @endif
        </div>
        <div style="padding: 12px 20px; background: linear-gradient(135deg, #fce7f3, #fbcfe8); border-radius: 12px; text-align: center;">
            <div style="font-size: 0.75rem; color: #9ca3af; font-weight: 700; text-transform: uppercase;">Usia Kehamilan</div>
            <div style="font-size: 1.3rem; color: #be185d; font-weight: 800;">{{ $ibuHamil->usia_kehamilan_lengkap }}</div>
        </div>
    </div>

    <!-- Layout Grid: Panel Imunisasi vs Daftar 40 Minggu -->
    <div style="display: grid; grid-template-columns: 300px 1fr; gap: 20px; align-items: start;">

        <!-- Panel Imunisasi TT -->
        <div class="card">
            <h3 class="card-title" style="font-size: 0.95rem;">💉 Imunisasi TT & Vitamin A</h3>
            @foreach($master_tt as $tt)
                @php $vaks = $riwayat_imunisasi->get($tt); @endphp
                <div style="padding: 10px; border: 1px solid #fbcfe8; border-radius: 10px; margin-bottom: 8px; font-size: 0.8rem;">
                    <div style="font-weight: 700; color: #831843;">{{ $tt }}</div>
                    @if($vaks)
                        <div style="color: #ec4899; font-size: 0.75rem;">
                            ✓ {{ $vaks->tanggal_vaksinasi->format('d M Y') }}
                        </div>
                    @else
                        <button onclick="bukaModalTT('{{ $tt }}')"
                            style="margin-top: 6px; padding: 4px 10px; background: #f97316; color: #fff; border: none; border-radius: 6px; font-size: 0.7rem; font-weight: 700; cursor: pointer;">
                            + Input Tanggal
                        </button>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Daftar 40 Minggu -->
        <div class="card">
            <h3 class="card-title">📋 Pengukuran Mingguan (1 - 40 Minggu)</h3>
            <div style="max-height: 700px; overflow-y: auto; padding-right: 8px;">
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    @for($m = 1; $m <= 40; $m++)
                        @php
                            $pemeriksaan = $riwayat_pemeriksaan->get($m);
                            $isFuture = $m > $usia_kehamilan_minggu;
                        @endphp
                        <div style="padding: 12px 16px; border: 1px solid #fbcfe8; border-radius: 12px; background: {{ $isFuture ? '#f9fafb' : '#fff' }}; opacity: {{ $isFuture ? '0.6' : '1' }};">
                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                                <div style="flex: 1;">
                                    <div style="font-weight: 700; color: #831843;">
                                        Minggu Ke-{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}
                                    </div>
                                    @if($pemeriksaan)
                                        <div style="font-size: 0.78rem; color: #6b7280; margin-top: 4px;">
                                            BB: <strong>{{ $pemeriksaan->bb }} kg</strong> |
                                            LiLA: <strong>{{ $pemeriksaan->lila }} cm</strong> |
                                            HB: <strong>{{ $pemeriksaan->hemoglobin ?? '-' }}</strong> |
                                            TTD: <strong>{{ $pemeriksaan->tablet_tambah_darah }} Bks</strong>
                                            @if($pemeriksaan->tekanan_darah)
                                                | TD: <strong>{{ $pemeriksaan->tekanan_darah }}</strong>
                                            @endif
                                        </div>
                                        <div style="display: flex; gap: 4px; flex-wrap: wrap; margin-top: 6px;">
                                            @if($pemeriksaan->status_kek)
                                                <span class="badge" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($pemeriksaan->status_kek) }}; color: #4a1d3a; font-size: 0.68rem;">
                                                    KEK: {{ $pemeriksaan->status_kek }}
                                                </span>
                                            @endif
                                            @if($pemeriksaan->status_anemia)
                                                <span class="badge" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($pemeriksaan->status_anemia) }}; color: #4a1d3a; font-size: 0.68rem;">
                                                    HB: {{ $pemeriksaan->status_anemia }}
                                                </span>
                                            @endif
                                            @if($pemeriksaan->status_td)
                                                <span class="badge" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($pemeriksaan->status_td) }}; color: #4a1d3a; font-size: 0.68rem;">
                                                    TD: {{ $pemeriksaan->status_td }}
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
                                        <a href="{{ route('ibu-hamil.checkup', ['id' => $ibuHamil->id, 'minggu_ke' => $m]) }}" 
                                        class="btn-action btn-edit" title="Edit">✏️</a>
                                        <form action="{{ route('ibu-hamil.pemeriksaan.hapus', $pemeriksaan->id) }}" 
                                            method="POST" style="display:inline;" 
                                            onsubmit="return confirm('Yakin hapus data minggu {{ $m }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-action btn-delete" title="Hapus">🗑️</button>
                                        </form>
                                    @elseif($pemeriksaan->status_review === 'disetujui')
                                        <span class="badge badge-success">✅ Disetujui</span>
                                        <span style="font-size: 0.7rem; color: #9ca3af;">🔒</span>
                                    @else
                                        <a href="{{ route('ibu-hamil.checkup', ['id' => $ibuHamil->id, 'minggu_ke' => $m]) }}" 
                                        class="btn-action" style="background: #ef4444; color: #fff;">🔧 Perbaiki</a>
                                        <form action="{{ route('ibu-hamil.pemeriksaan.hapus', $pemeriksaan->id) }}" 
                                            method="POST" style="display:inline;" 
                                            onsubmit="return confirm('Yakin hapus data minggu {{ $m }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-action btn-delete" title="Hapus">🗑️</button>
                                        </form>
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
                                            catatan: '{{ addslashes($pemeriksaan->catatan_bidan ?? "Tidak ada catatan.") }}'
                                        })" class="btn-action btn-edit">🔍 Detail</button>
                                    @else
                                        <a href="{{ route('ibu-hamil.checkup', ['id' => $ibuHamil->id, 'minggu_ke' => $m]) }}" class="btn-action btn-edit">+ Input</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DETAIL -->
    <div id="modal-detail" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: #fff; border-radius: 20px; max-width: 480px; width: 100%; overflow: hidden;">
            <div style="background: linear-gradient(135deg, #ec4899, #f43f5e); padding: 16px 20px; color: #fff; display: flex; justify-content: space-between;">
                <strong>Detail Minggu Ke-<span id="md-minggu"></span></strong>
                <button onclick="tutupModal('modal-detail')" style="background: none; border: none; color: #fff; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            <div style="padding: 20px; font-size: 0.9rem;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                    <div><strong>BB:</strong> <span id="md-bb"></span> kg</div>
                    <div><strong>LiLA:</strong> <span id="md-lila"></span> cm</div>
                    <div><strong>HB:</strong> <span id="md-hb"></span> g/dL</div>
                    <div><strong>TD:</strong> <span id="md-td"></span></div>
                    <div><strong>TTD:</strong> <span id="md-ttd"></span> Bks</div>
                    <div><strong>PMT:</strong> <span id="md-pmt"></span></div>
                </div>
                <div style="margin-bottom: 8px;"><strong>KEK:</strong> <span id="md-kek"></span></div>
                <div style="margin-bottom: 8px;"><strong>Anemia:</strong> <span id="md-anemia"></span></div>
                <div style="margin-bottom: 8px;"><strong>Status TD:</strong> <span id="md-status-td"></span></div>
                <hr style="margin: 12px 0; border: 0; border-top: 1px solid #fbcfe8;">
                <div><strong>Catatan Bidan:</strong></div>
                <div id="md-catatan" style="font-style: italic; color: #6b7280; margin-top: 4px;"></div>
            </div>
        </div>
    </div>

    <!-- MODAL TT -->
    <div id="modal-tt" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: #fff; border-radius: 20px; max-width: 420px; width: 100%;">
            <div style="background: linear-gradient(135deg, #ec4899, #f43f5e); padding: 16px 20px; color: #fff; display: flex; justify-content: space-between;">
                <strong>Catat Imunisasi: <span id="text-tt"></span></strong>
                <button onclick="tutupModal('modal-tt')" style="background: none; border: none; color: #fff; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('ibu-hamil.imunisasi.store', $ibuHamil->id) }}" method="POST" style="padding: 20px;">
                @csrf
                <input type="hidden" name="jenis_tt" id="input-jenis-tt">
                <div class="form-group">
                    <label>Tanggal Vaksinasi</label>
                    <input type="date" name="tanggal_vaksinasi" class="form-control"
                        min="{{ $ibuHamil->tanggal_pendaftaran ? $ibuHamil->tanggal_pendaftaran->format('Y-m-d') : '' }}"
                        max="{{ date('Y-m-d') }}" required>
                </div>
                <div style="display: flex; gap: 8px; margin-top: 16px;">
                    <button type="submit" class="btn-primary" style="flex: 1;">Simpan</button>
                    <button type="button" onclick="tutupModal('modal-tt')" class="btn-secondary" style="flex: 1;">Batal</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function tutupModal(id) {
    document.getElementById(id).style.display = 'none';
}

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
    document.getElementById('md-catatan').innerText = '"' + data.catatan + '"';
    document.getElementById('modal-detail').style.display = 'flex';
}

function bukaModalTT(jenis) {
    document.getElementById('input-jenis-tt').value = jenis;
    document.getElementById('text-tt').innerText = jenis;
    document.getElementById('modal-tt').style.display = 'flex';
}
</script>
@endpush