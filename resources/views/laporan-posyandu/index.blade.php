@extends('layouts.dashboard')

@section('title', 'Laporan Posyandu')
@section('page-title', 'Laporan Posyandu')
@section('page-subtitle', 'Rekapitulasi data yang sudah divalidasi Bidan')

@section('sidebar-menu')
    @if(auth()->user()->isKader())
        <a href="{{ route('kader.dashboard') }}" class="menu-item">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
            Dashboard
        </a>
        <a href="{{ route('balita.index') }}" class="menu-item">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
            Kelola Data Balita
        </a>
        <a href="{{ route('ibu-hamil.index') }}" class="menu-item">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
            Kelola Data Bumil
        </a>
        <a href="{{ route('kader.jadwal.index') }}" class="menu-item">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
            Jadwal Posyandu
        </a>
        <a href="{{ route('kader.laporan.posyandu') }}" class="menu-item active">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
            Laporan Posyandu
        </a>
    @elseif(auth()->user()->isBidan())
        <a href="{{ route('bidan.dashboard') }}" class="menu-item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg> Dashboard</a>
        <a href="{{ route('bidan.validasi.balita') }}" class="menu-item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> Validasi Data Balita</a>
        <a href="{{ route('bidan.validasi.bumil') }}" class="menu-item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> Validasi Data Bumil</a>
        <a href="{{ route('kader.jadwal.index') }}" class="menu-item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg> Jadwal Posyandu</a>
        <a href="{{ route('bidan.darurat') }}" class="menu-item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg> Pemeriksaan Darurat</a>
        <a href="{{ route('bidan.laporan.posyandu') }}" class="menu-item active"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg> Laporan Posyandu</a>
        <a href="{{ route('bidan.laporan.darurat') }}" class="menu-item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg> Laporan Darurat</a>
    @elseif(auth()->user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}" class="menu-item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg> Dashboard</a>
        <a href="{{ route('admin.kelola-akun') }}" class="menu-item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg> Kelola Akun</a>
        <a href="{{ route('kader.jadwal.index') }}" class="menu-item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg> Jadwal Posyandu</a>
        <a href="{{ route('admin.laporan-posyandu') }}" class="menu-item active"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg> Laporan Posyandu</a>
        <a href="{{ route('admin.laporan-darurat') }}" class="menu-item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg> Laporan Darurat</a>
    @endif
@endsection

@section('content')
    <!-- FILTER -->
    <div class="card" style="margin-bottom: 20px;">
        <form method="GET" action="" style="display: flex; gap: 12px; align-items: end; flex-wrap: wrap;">
            <div class="form-group" style="margin-bottom: 0; min-width: 160px;">
                <label style="font-size: 0.8rem;">Bulan</label>
                <select name="bulan" class="form-control" onchange="this.form.submit()">
                    @php
                        $bulanList = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                        ];
                    @endphp
                    @foreach($bulanList as $num => $nama)
                        <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0; min-width: 120px;">
                <label style="font-size: 0.8rem;">Tahun</label>
                <select name="tahun" class="form-control" onchange="this.form.submit()">
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            @if(isset($posyandus) && auth()->user()->isBidan() || auth()->user()->isAdmin())
                <div class="form-group" style="margin-bottom: 0; min-width: 200px;">
                    <label style="font-size: 0.8rem;">Posyandu</label>
                    <select name="posyandu_id" class="form-control" onchange="this.form.submit()">
                        <option value="">Semua Posyandu</option>
                        @foreach($posyandus as $p)
                            <option value="{{ $p->id }}" {{ ($posyanduId ?? null) == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_posyandu }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <a href="{{ url()->current() }}" class="btn-secondary" style="padding: 12px 20px;">Reset</a>
        </form>
    </div>

    <!-- TOMBOL EXPORT -->
<div class="card" style="margin-bottom: 20px; padding: 16px; background: linear-gradient(135deg, #fce7f3, #fbcfe8); border-color: #f9a8d4;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <strong style="color: #831843; font-size: 0.95rem;">📥 Export Laporan</strong>
            <p style="font-size: 0.8rem; color: #6b7280; margin-top: 4px;">
                Download laporan periode {{ $bulanList[$bulan] ?? '-' }} {{ $tahun }}
            </p>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('laporan-posyandu.pdf', ['bulan' => $bulan, 'tahun' => $tahun, 'posyandu_id' => $posyanduId ?? null]) }}" 
               class="btn-primary" 
               style="background: linear-gradient(135deg, #dc2626, #b91c1c); display: inline-flex; align-items: center; gap: 6px; padding: 12px 20px;"
               target="_blank">
                📄 Download PDF
            </a>
            <a href="{{ route('laporan-posyandu.excel', ['bulan' => $bulan, 'tahun' => $tahun, 'posyandu_id' => $posyanduId ?? null]) }}" 
               class="btn-primary" 
               style="background: linear-gradient(135deg, #059669, #047857); display: inline-flex; align-items: center; gap: 6px; padding: 12px 20px;">
                📊 Download Excel
            </a>
        </div>
    </div>
</div>

    <!-- SUMMARY -->
    <div class="stat-grid" style="margin-bottom: 20px;">
        <div class="stat-card">
            <div>
                <span class="stat-label">Total Laporan</span>
                <div class="stat-value">{{ $laporans->count() }}</div>
                <div class="stat-sub">data tervalidasi</div>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <span class="stat-label">Balita</span>
                <div class="stat-value">{{ $laporans->where('kategori', 'balita')->count() }}</div>
                <div class="stat-sub">data</div>
            </div>
        </div>
        <div class="stat-card">
            <div>
                <span class="stat-label">Ibu Hamil</span>
                <div class="stat-value">{{ $laporans->where('kategori', 'bumil')->count() }}</div>
                <div class="stat-sub">data</div>
            </div>
        </div>
    </div>

    <!-- TABEL -->
    <div class="card">
        <h2 class="card-title">📊 Detail Laporan</h2>

        @if($laporans->count() > 0)
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kategori</th>
                            <th>Posyandu</th>
                            <th>Identitas</th>
                            <th>Data Pemeriksaan</th>
                            <th>Status Gizi/Kesehatan</th>
                            <th>Imunisasi</th>
                            <th>Validasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($laporans as $index => $l)
                            @php
                                $id  = $l->identitas ?? [];
                                $data = $l->data_pemeriksaan ?? [];
                                $gizi = $l->status_gizi ?? [];
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="badge {{ $l->kategori === 'balita' ? 'badge-info' : 'badge-warning' }}">
                                        {{ ucfirst($l->kategori) }}
                                    </span>
                                </td>
                                <td style="font-size: 0.8rem;">{{ $l->posyandu->nama_posyandu ?? '-' }}</td>
                                <td>
                                    <div style="font-weight: 700; color: #831843;">{{ $id['nama'] ?? '-' }}</div>
                                    @if($l->kategori === 'balita')
                                        <div style="font-size: 0.72rem; color: #9ca3af;">
                                            NIK: {{ $id['nik'] ?? '-' }}<br>
                                            Umur: {{ $id['umur_bulan'] ?? '-' }} bulan<br>
                                            JK: {{ ($id['jenis_kelamin'] ?? '-') === 'L' ? 'Laki-laki' : 'Perempuan' }}<br>
                                            Ibu: {{ $id['nama_ibu'] ?? '-' }}
                                        </div>
                                    @else
                                        <div style="font-size: 0.72rem; color: #9ca3af;">
                                            NIK: {{ $id['nik'] ?? '-' }}<br>
                                            Umur: {{ $id['umur'] ?? '-' }} tahun<br>
                                            Hamil Ke: {{ $id['hamil_ke'] ?? '-' }}<br>
                                            Suami: {{ $id['nama_suami'] ?? '-' }}
                                        </div>
                                    @endif
                                </td>
                                <td style="font-size: 0.78rem;">
                                    @if($l->kategori === 'balita')
                                        <div>BB: <strong>{{ $data['berat_badan'] ?? '-' }} kg</strong></div>
                                        <div>TB: <strong>{{ $data['tinggi_badan'] ?? '-' }} cm</strong></div>
                                        <div>LK: <strong>{{ $data['lingkar_kepala'] ?? '-' }} cm</strong></div>
                                        @if(!empty($data['vitamin_a']))
                                            <div style="color: #f59e0b; font-weight: 700;">💊 Vitamin A ✓</div>
                                        @endif
                                    @else
                                        <div>BB: <strong>{{ $data['bb'] ?? '-' }} kg</strong></div>
                                        <div>LiLA: <strong>{{ $data['lila'] ?? '-' }} cm</strong></div>
                                        <div>HB: <strong>{{ $data['hemoglobin'] ?? '-' }} g/dL</strong></div>
                                        <div>TTD: <strong>{{ $data['tablet_tambah_darah'] ?? '-' }} Bks</strong></div>
                                        <div>TD: <strong>{{ $data['tekanan_darah'] ?? '-' }}</strong></div>
                                        <div>PMT: <strong>{{ !empty($data['pmt_pemulihan']) ? 'Ya' : 'Tidak' }}</strong></div>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 4px;">
                                        @if($l->kategori === 'balita')
                                            @if(!empty($gizi['bb_u']))
                                                <span class="badge" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($gizi['bb_u']) }}; color: #4a1d3a; font-size: 0.68rem;">
                                                    BB/U: {{ $gizi['bb_u'] }}
                                                </span>
                                            @endif
                                            @if(!empty($gizi['tb_u']))
                                                <span class="badge" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($gizi['tb_u']) }}; color: #4a1d3a; font-size: 0.68rem;">
                                                    TB/U: {{ $gizi['tb_u'] }}
                                                </span>
                                            @endif
                                            @if(!empty($gizi['bb_tb']))
                                                <span class="badge" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($gizi['bb_tb']) }}; color: #4a1d3a; font-size: 0.68rem;">
                                                    BB/TB: {{ $gizi['bb_tb'] }}
                                                </span>
                                            @endif
                                        @else
                                            @if(!empty($gizi['kek']))
                                                <span class="badge" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($gizi['kek']) }}; color: #4a1d3a; font-size: 0.68rem;">
                                                    KEK: {{ $gizi['kek'] }}
                                                </span>
                                            @endif
                                            @if(!empty($gizi['anemia']))
                                                <span class="badge" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($gizi['anemia']) }}; color: #4a1d3a; font-size: 0.68rem;">
                                                    HB: {{ $gizi['anemia'] }}
                                                </span>
                                            @endif
                                            @if(!empty($gizi['td']))
                                                <span class="badge" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($gizi['td']) }}; color: #4a1d3a; font-size: 0.68rem;">
                                                    TD: {{ $gizi['td'] }}
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td style="font-size: 0.75rem;">
                                    @if($l->kategori === 'balita' && !empty($l->imunisasi))
                                        @foreach($l->imunisasi as $vaksin)
                                            <div>💉 {{ $vaksin }}</div>
                                        @endforeach
                                    @else
                                        <span style="color: #9ca3af;">-</span>
                                    @endif
                                </td>
                                <td style="font-size: 0.72rem;">
                                    <div style="font-weight: 700; color: #065f46;">✅ Disetujui</div>
                                    <div style="color: #9ca3af;">Oleh: {{ $l->validator->name ?? '-' }}</div>
                                    <div style="color: #9ca3af;">{{ $l->tanggal_validasi?->format('d M Y H:i') }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 60px; color: #9ca3af;">
                <div style="font-size: 3rem; margin-bottom: 12px;">📋</div>
                <div style="font-weight: 700; color: #831843; margin-bottom: 4px;">
                    Belum Ada Laporan
                </div>
                <div style="font-size: 0.85rem;">
                    Belum ada data validasi untuk periode {{ $bulanList[$bulan] ?? '-' }} {{ $tahun }}.
                </div>
            </div>
        @endif
    </div>
@endsection