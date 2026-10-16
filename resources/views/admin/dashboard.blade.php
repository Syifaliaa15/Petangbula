@extends('layouts.dashboard')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Admin')
@section('page-subtitle', 'Ringkasan data kesehatan seluruh posyandu di Desa Sindangpanon')

@section('sidebar-menu')
    <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
        Dashboard
    </a>
    <a href="{{ route('admin.kelola-akun') }}" class="menu-item {{ request()->routeIs('admin.kelola-akun*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
        Kelola Akun
    </a>
    <a href="{{ route('kader.jadwal.index') }}" class="menu-item {{ request()->routeIs('jadwal.*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
        Jadwal Posyandu
    </a>
    <a href="{{ route('admin.laporan-posyandu') }}" class="menu-item {{ request()->routeIs('admin.laporan-posyandu') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
        Laporan Posyandu
    </a>
    <a href="{{ route('admin.laporan-darurat') }}" class="menu-item {{ request()->routeIs('admin.laporan-darurat') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
        Laporan Darurat
    </a>
@endsection

@section('content')

    <!-- ==========================================
         RINGKASAN UTAMA
         ========================================== -->
    <h2 style="color: #831843; font-size: 1.1rem; font-weight: 800; margin-bottom: 12px;">📊 Ringkasan Umum</h2>
    <div class="stat-grid">
        <div class="stat-card">
            <div>
                <span class="stat-label">Total Balita</span>
                <div class="stat-value">{{ $totalBalita }}</div>
                <div class="stat-sub">{{ $totalPemeriksaanBalita }} total pemeriksaan</div>
            </div>
            <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg></div>
        </div>

        <div class="stat-card">
            <div>
                <span class="stat-label">Total Ibu Hamil</span>
                <div class="stat-value">{{ $totalIbuHamil }}</div>
                <div class="stat-sub">{{ $totalPemeriksaanBumil }} total pemeriksaan</div>
            </div>
            <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg></div>
        </div>

        <div class="stat-card">
            <div>
                <span class="stat-label">Total Posyandu</span>
                <div class="stat-value">{{ $totalPosyandu }}</div>
                <div class="stat-sub">posyandu aktif</div>
            </div>
            <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg></div>
        </div>

        <div class="stat-card">
            <div>
                <span class="stat-label">Akun Aktif</span>
                <div class="stat-value">{{ $totalUserAktif }}</div>
                <div class="stat-sub">{{ $totalBidan }} bidan · {{ $totalKader }} kader</div>
            </div>
            <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg></div>
        </div>
    </div>

    <!-- ==========================================
         STATISTIK BALITA
         ========================================== -->
    <h2 style="color: #831843; font-size: 1.1rem; font-weight: 800; margin: 28px 0 12px;">👶 Statistik Balita</h2>

    <!-- BB/U -->
    <div class="card" style="margin-bottom: 20px;">
        <h3 class="card-title" style="font-size: 1rem;">1. Berat Badan menurut Umur (BB/U)</h3>
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px;">
            <div style="background: #d1fae5; padding: 16px; border-radius: 12px; text-align: center;">
                <div style="font-size: 1.8rem; font-weight: 800; color: #065f46;">{{ $giziBaik }}</div>
                <div style="font-size: 0.75rem; font-weight: 700; color: #065f46; text-transform: uppercase;">Gizi Baik</div>
            </div>
            <div style="background: #fef3c7; padding: 16px; border-radius: 12px; text-align: center;">
                <div style="font-size: 1.8rem; font-weight: 800; color: #92400e;">{{ $giziKurang }}</div>
                <div style="font-size: 0.75rem; font-weight: 700; color: #92400e; text-transform: uppercase;">Gizi Kurang</div>
            </div>
            <div style="background: #dbeafe; padding: 16px; border-radius: 12px; text-align: center;">
                <div style="font-size: 1.8rem; font-weight: 800; color: #1d4ed8;">{{ $giziLebih }}</div>
                <div style="font-size: 0.75rem; font-weight: 700; color: #1d4ed8; text-transform: uppercase;">Gizi Lebih</div>
            </div>
            <div style="background: #fee2e2; padding: 16px; border-radius: 12px; text-align: center;">
                <div style="font-size: 1.8rem; font-weight: 800; color: #991b1b;">{{ $giziBuruk }}</div>
                <div style="font-size: 0.75rem; font-weight: 700; color: #991b1b; text-transform: uppercase;">Gizi Buruk</div>
            </div>
        </div>
        <div style="position: relative; height: 260px; margin-top: 20px;">
            <canvas id="chartBBU"></canvas>
        </div>
    </div>

    <!-- TB/U -->
    <div class="card" style="margin-bottom: 20px;">
        <h3 class="card-title" style="font-size: 1rem;">2. Tinggi Badan menurut Umur (TB/U)</h3>
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px;">
            <div style="background: #d1fae5; padding: 16px; border-radius: 12px; text-align: center;">
                <div style="font-size: 1.8rem; font-weight: 800; color: #065f46;">{{ $tbNormal }}</div>
                <div style="font-size: 0.75rem; font-weight: 700; color: #065f46; text-transform: uppercase;">Normal</div>
            </div>
            <div style="background: #fef3c7; padding: 16px; border-radius: 12px; text-align: center;">
                <div style="font-size: 1.8rem; font-weight: 800; color: #92400e;">{{ $tbPendek }}</div>
                <div style="font-size: 0.75rem; font-weight: 700; color: #92400e; text-transform: uppercase;">Pendek</div>
            </div>
            <div style="background: #fee2e2; padding: 16px; border-radius: 12px; text-align: center;">
                <div style="font-size: 1.8rem; font-weight: 800; color: #991b1b;">{{ $tbSangatPendek }}</div>
                <div style="font-size: 0.75rem; font-weight: 700; color: #991b1b; text-transform: uppercase;">Sangat Pendek</div>
            </div>
            <div style="background: #dbeafe; padding: 16px; border-radius: 12px; text-align: center;">
                <div style="font-size: 1.8rem; font-weight: 800; color: #1d4ed8;">{{ $tbTinggi }}</div>
                <div style="font-size: 0.75rem; font-weight: 700; color: #1d4ed8; text-transform: uppercase;">Tinggi</div>
            </div>
        </div>
        <div style="background: #fee2e2; padding: 12px 16px; border-radius: 12px; margin-top: 12px; border-left: 4px solid #ef4444;">
            <strong style="color: #991b1b;">⚠️ Total Stunting:</strong>
            <span style="color: #991b1b; font-weight: 700;">{{ $stunting }} balita</span>
            <span style="color: #991b1b; font-size: 0.85rem;">(Pendek + Sangat Pendek)</span>
        </div>
    </div>

    <!-- BB/TB -->
    <div class="card" style="margin-bottom: 20px;">
        <h3 class="card-title" style="font-size: 1rem;">3. Berat Badan menurut Tinggi Badan (BB/TB)</h3>
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px;">
            <div style="background: #d1fae5; padding: 16px; border-radius: 12px; text-align: center;">
                <div style="font-size: 1.8rem; font-weight: 800; color: #065f46;">{{ $bbtbNormal }}</div>
                <div style="font-size: 0.75rem; font-weight: 700; color: #065f46; text-transform: uppercase;">Normal</div>
            </div>
            <div style="background: #fef3c7; padding: 16px; border-radius: 12px; text-align: center;">
                <div style="font-size: 1.8rem; font-weight: 800; color: #92400e;">{{ $bbtbKurus }}</div>
                <div style="font-size: 0.75rem; font-weight: 700; color: #92400e; text-transform: uppercase;">Kurus</div>
            </div>
            <div style="background: #fee2e2; padding: 16px; border-radius: 12px; text-align: center;">
                <div style="font-size: 1.8rem; font-weight: 800; color: #991b1b;">{{ $bbtbSangatKurus }}</div>
                <div style="font-size: 0.75rem; font-weight: 700; color: #991b1b; text-transform: uppercase;">Sangat Kurus</div>
            </div>
            <div style="background: #dbeafe; padding: 16px; border-radius: 12px; text-align: center;">
                <div style="font-size: 1.8rem; font-weight: 800; color: #1d4ed8;">{{ $bbtbGemuk }}</div>
                <div style="font-size: 0.75rem; font-weight: 700; color: #1d4ed8; text-transform: uppercase;">Gemuk</div>
            </div>
        </div>
        <div style="background: #fee2e2; padding: 12px 16px; border-radius: 12px; margin-top: 12px; border-left: 4px solid #ef4444;">
            <strong style="color: #991b1b;">⚠️ Total Wasting:</strong>
            <span style="color: #991b1b; font-weight: 700;">{{ $wasting }} balita</span>
            <span style="color: #991b1b; font-size: 0.85rem;">(Kurus + Sangat Kurus)</span>
        </div>
    </div>

    <!-- ==========================================
         STATISTIK IBU HAMIL
         ========================================== -->
    <h2 style="color: #831843; font-size: 1.1rem; font-weight: 800; margin: 28px 0 12px;">🤰 Statistik Ibu Hamil</h2>

    <div class="card">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;">
            <!-- KEK -->
            <div style="background: #fef3c7; padding: 20px; border-radius: 12px; border-left: 4px solid #f59e0b;">
                <div style="font-size: 0.75rem; font-weight: 700; color: #92400e; text-transform: uppercase; margin-bottom: 6px;">
                    Kurang Energi Kronis (KEK)
                </div>
                <div style="font-size: 1.6rem; font-weight: 800; color: #92400e;">{{ $kekCount }}</div>
                <div style="font-size: 0.78rem; color: #78350f; margin-top: 4px;">
                    dari {{ $kekCount + $tidakKekCount }} bumil diperiksa
                </div>
            </div>

            <!-- Anemia -->
            <div style="background: #fee2e2; padding: 20px; border-radius: 12px; border-left: 4px solid #ef4444;">
                <div style="font-size: 0.75rem; font-weight: 700; color: #991b1b; text-transform: uppercase; margin-bottom: 6px;">
                    Anemia (HB &lt; 11)
                </div>
                <div style="font-size: 1.6rem; font-weight: 800; color: #991b1b;">{{ $anemiaCount }}</div>
                <div style="font-size: 0.78rem; color: #7f1d1d; margin-top: 4px;">
                    dari {{ $anemiaCount + $tidakAnemiaCount }} bumil diperiksa
                </div>
            </div>

            <!-- Komplikasi -->
            <div style="background: #fce7f3; padding: 20px; border-radius: 12px; border-left: 4px solid #ec4899;">
                <div style="font-size: 0.75rem; font-weight: 700; color: #be185d; text-transform: uppercase; margin-bottom: 6px;">
                    Bumil Berkomplikasi
                </div>
                <div style="font-size: 1.6rem; font-weight: 800; color: #be185d;">{{ $bumilBerkomplikasi }}</div>
                <div style="font-size: 0.78rem; color: #831843; margin-top: 4px;">
                    KEK atau Anemia
                </div>
            </div>
        </div>

        <h4 style="color: #831843; font-weight: 700; margin-top: 20px; margin-bottom: 12px; font-size: 0.9rem;">
            Tekanan Darah
        </h4>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;">
            <div style="background: #d1fae5; padding: 12px; border-radius: 10px; text-align: center;">
                <div style="font-size: 1.3rem; font-weight: 800; color: #065f46;">{{ $tdNormal }}</div>
                <div style="font-size: 0.75rem; font-weight: 700; color: #065f46;">Normal</div>
            </div>
            <div style="background: #fee2e2; padding: 12px; border-radius: 10px; text-align: center;">
                <div style="font-size: 1.3rem; font-weight: 800; color: #991b1b;">{{ $tdHipertensi }}</div>
                <div style="font-size: 0.75rem; font-weight: 700; color: #991b1b;">Hipertensi</div>
            </div>
            <div style="background: #fef3c7; padding: 12px; border-radius: 10px; text-align: center;">
                <div style="font-size: 1.3rem; font-weight: 800; color: #92400e;">{{ $tdHipotensi }}</div>
                <div style="font-size: 0.75rem; font-weight: 700; color: #92400e;">Hipotensi</div>
            </div>
        </div>
    </div>

    <!-- ==========================================
         GRAFIK BUMIL
         ========================================== -->
    <div class="card" style="margin-top: 20px;">
        <h3 class="card-title" style="font-size: 1rem;">📊 Grafik Status Kesehatan Ibu Hamil</h3>
        <div style="position: relative; height: 280px;">
            <canvas id="chartBumil"></canvas>
        </div>
    </div>

    <!-- ==========================================
         STATUS VALIDASI & LAPORAN
         ========================================== -->
    <h2 style="color: #831843; font-size: 1.1rem; font-weight: 800; margin: 28px 0 12px;">✅ Status Validasi & Laporan</h2>
    <div class="stat-grid">
        <div class="stat-card" style="background: linear-gradient(135deg, #fef3c7, #fde68a); border-color: #fbbf24;">
            <div>
                <span class="stat-label" style="color: #92400e;">⏳ Menunggu Validasi</span>
                <div class="stat-value" style="color: #78350f;">{{ $totalPending }}</div>
                <div class="stat-sub" style="color: #92400e;">{{ $totalPendingBalita }} balita · {{ $totalPendingBumil }} bumil</div>
            </div>
        </div>

        <div class="stat-card" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); border-color: #34d399;">
            <div>
                <span class="stat-label" style="color: #065f46;">✅ Total Data Valid</span>
                <div class="stat-value" style="color: #065f46;">{{ $totalLaporan }}</div>
                <div class="stat-sub" style="color: #065f46;">masuk laporan posyandu</div>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <span class="stat-label">Laporan Bulan Ini</span>
                <div class="stat-value">{{ $laporanBulanIni }}</div>
                <div class="stat-sub">{{ now()->translatedFormat('F Y') }}</div>
            </div>
        </div>

        <div class="stat-card" style="background: linear-gradient(135deg, #fee2e2, #fecaca); border-color: #f87171;">
            <div>
                <span class="stat-label" style="color: #991b1b;">🚨 Pemeriksaan Darurat</span>
                <div class="stat-value" style="color: #991b1b;">{{ $totalDarurat }}</div>
                <div class="stat-sub" style="color: #991b1b;">{{ $daruratBulanIni }} bulan ini</div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Chart BB/U
    const ctxBBU = document.getElementById('chartBBU').getContext('2d');
    new Chart(ctxBBU, {
        type: 'bar',
        data: {
            labels: ['Gizi Baik', 'Gizi Kurang', 'Gizi Lebih', 'Gizi Buruk'],
            datasets: [{
                label: 'Jumlah Balita',
                data: [{{ $giziBaik }}, {{ $giziKurang }}, {{ $giziLebih }}, {{ $giziBuruk }}],
                backgroundColor: ['#22c55e', '#f59e0b', '#3b82f6', '#ef4444'],
                borderRadius: 8,
                borderWidth: 2,
                borderColor: ['#16a34a', '#d97706', '#2563eb', '#dc2626'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // Chart Bumil
    const ctxBumil = document.getElementById('chartBumil').getContext('2d');
    new Chart(ctxBumil, {
        type: 'doughnut',
        data: {
            labels: ['KEK', 'Anemia', 'Hipertensi', 'Normal'],
            datasets: [{
                data: [{{ $kekCount }}, {{ $anemiaCount }}, {{ $tdHipertensi }}, {{ $tdNormal }}],
                backgroundColor: ['#fbbf24', '#ef4444', '#ec4899', '#22c55e'],
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 20 } }
            }
        }
    });
});
</script>
@endpush