@extends('layouts.dashboard')

@section('title', 'Dashboard Kader')
@section('page-title', 'Selamat Bekerja, Kader! 👋')
@section('page-subtitle', 'Berikut ringkasan perkembangan kesehatan data berkala anak dan ibu hamil di wilayah Anda.')

@section('content')
    <!-- Stat Cards -->
    <div class="stat-grid">
        <div class="stat-card">
            <div>
                <span class="stat-label">Total Balita</span>
                <div class="stat-value">{{ $totalBalita }}</div>
                <div class="stat-sub">anak terdaftar di posyandu Anda</div>
            </div>
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                </svg>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <span class="stat-label">Total Ibu Hamil</span>
                <div class="stat-value">{{ $totalIbuHamil }}</div>
                <div class="stat-sub">ibu hamil aktif dipantau</div>
            </div>
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Chart Status Gizi Balita -->
    <div class="card">
        <h2 class="card-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="#ec4899">
                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
            </svg>
            Grafik Status Gizi Balita
        </h2>
        <div style="position: relative; height: 320px;">
            <canvas id="chartGiziBalita"></canvas>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('chartGiziBalita').getContext('2d');

    const gradientBaik = ctx.createLinearGradient(0, 0, 0, 300);
    gradientBaik.addColorStop(0, '#f472b6');
    gradientBaik.addColorStop(1, '#fbcfe8');

    const gradientKurang = ctx.createLinearGradient(0, 0, 0, 300);
    gradientKurang.addColorStop(0, '#fbbf24');
    gradientKurang.addColorStop(1, '#fde68a');

    const gradientLebih = ctx.createLinearGradient(0, 0, 0, 300);
    gradientLebih.addColorStop(0, '#60a5fa');
    gradientLebih.addColorStop(1, '#bfdbfe');

    const gradientBuruk = ctx.createLinearGradient(0, 0, 0, 300);
    gradientBuruk.addColorStop(0, '#f87171');
    gradientBuruk.addColorStop(1, '#fecaca');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Gizi Baik', 'Gizi Kurang', 'Gizi Lebih', 'Gizi Buruk'],
            datasets: [{
                label: 'Jumlah Balita',
                data: [{{ $giziBaik }}, {{ $giziKurang }}, {{ $giziLebih }}, {{ $giziBuruk }}],
                backgroundColor: [gradientBaik, gradientKurang, gradientLebih, gradientBuruk],
                borderColor: ['#ec4899', '#f59e0b', '#3b82f6', '#ef4444'],
                borderWidth: 2,
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
});
</script>
@endpush