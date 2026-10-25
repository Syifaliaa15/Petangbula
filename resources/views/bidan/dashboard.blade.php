@extends('layouts.dashboard')

@section('title', 'Dashboard Bidan')
@section('page-title', 'Dashboard Bidan Desa')
@section('page-subtitle', 'Pantau dan validasi data kesehatan dari seluruh posyandu')

@section('content')
    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
        <div class="stat-card">
            <div>
                <span class="stat-label">Total Balita</span>
                <div class="stat-value">{{ $totalBalita }}</div>
                <div class="stat-sub">di seluruh posyandu</div>
            </div>
            <div class="w-14 h-14 bg-kia-50 rounded-xl flex items-center justify-center text-kia-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <span class="stat-label">Total Ibu Hamil</span>
                <div class="stat-value">{{ $totalIbuHamil }}</div>
                <div class="stat-sub">ibu hamil aktif</div>
            </div>
            <div class="w-14 h-14 bg-kia-50 rounded-xl flex items-center justify-center text-kia-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </div>
        </div>

        <div class="stat-card" style="background: linear-gradient(135deg, #fef3c7, #fde68a); border-color: #fbbf24;">
            <div>
                <span class="stat-label" style="color: #92400e;">⏳ Menunggu Validasi</span>
                <div class="stat-value" style="color: #78350f;">{{ $totalPending }}</div>
                <div class="stat-sub" style="color: #92400e;">data perlu diverifikasi</div>
            </div>
            <div class="w-14 h-14 bg-amber-200 rounded-xl flex items-center justify-center text-amber-700">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Chart Balita -->
    <div class="card mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-3">
            <h2 class="text-lg font-bold text-kia-900">Grafik Status Gizi Balita</h2>
            <div class="flex gap-2 bg-white border border-kia-200 p-1 rounded-lg">
                <button onclick="switchChart('bbu')" id="tab-bbu" class="btn-tab btn-tab-active">BB/U</button>
                <button onclick="switchChart('tbu')" id="tab-tbu" class="btn-tab btn-tab-inactive">TB/U</button>
                <button onclick="switchChart('bbtb')" id="tab-bbtb" class="btn-tab btn-tab-inactive">BB/TB</button>
            </div>
        </div>
        <div class="relative h-[300px] w-full">
            <canvas id="chartGiziBalita"></canvas>
        </div>
    </div>

    <!-- Chart Bumil -->
    <div class="card">
        <h2 class="text-lg font-bold text-kia-900 mb-6 border-b border-kia-100 pb-3">Grafik Status Kesehatan Ibu Hamil</h2>
        <div class="relative h-[300px] w-full flex justify-center">
            <div class="w-full max-w-md">
                <canvas id="chartBumil"></canvas>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let myChart = null;

    const dataBBU = {
        labels: ['Berat Badan Normal', 'Berat Badan Kurang', 'Risiko Berat Badan Lebih', 'Berat Badan Sangat Kurang'],
        values: [{{ $giziBaik }}, {{ $giziKurang }}, {{ $giziLebih }}, {{ $giziBuruk }}],
        colors: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444']
    };

    const dataTBU = {
        labels: ['Normal', 'Pendek', 'Sangat Pendek', 'Tinggi'],
        values: [{{ $tbNormal }}, {{ $tbPendek }}, {{ $tbSangatPendek }}, {{ $tbTinggi }}],
        colors: ['#10b981', '#f59e0b', '#ef4444', '#3b82f6']
    };

    const dataBBTB = {
        labels: ['Gizi Baik', 'Gizi Kurang', 'Gizi Buruk', 'Gizi Lebih', 'Obesitas'],
        values: [{{ $bbtbNormal }}, {{ $bbtbKurang }}, {{ $bbtbBuruk }}, {{ $bbtbLebih }}, {{ $bbtbObesitas }}],
        colors: ['#10b981', '#f59e0b', '#ef4444', '#3b82f6', '#8b5cf6']
    };

    function renderChart(type) {
        const ctx = document.getElementById('chartGiziBalita').getContext('2d');
        let dataset = type === 'bbu' ? dataBBU : (type === 'tbu' ? dataTBU : dataBBTB);

        if (myChart) { myChart.destroy(); }

        myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: dataset.labels,
                datasets: [{ data: dataset.values, backgroundColor: dataset.colors, borderRadius: 6 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    }

    function switchChart(type) {
        ['bbu', 'tbu', 'bbtb'].forEach(t => {
            document.getElementById('tab-' + t).className = 'btn-tab btn-tab-inactive';
        });
        document.getElementById('tab-' + type).className = 'btn-tab btn-tab-active';
        renderChart(type);
    }

    document.addEventListener("DOMContentLoaded", function() {
        renderChart('bbu');

        const ctxBumil = document.getElementById('chartBumil').getContext('2d');
        new Chart(ctxBumil, {
            type: 'doughnut',
            data: {
                labels: ['Normal', 'Risiko KEK', 'Anemia', 'Hipertensi'],
                datasets: [{
                    data: [{{ $bumilNormal }}, {{ $bumilKEK }}, {{ $bumilAnemia }}, {{ $tdHipertensi }}],
                    backgroundColor: ['#10b981', '#fbbf24', '#ef4444', '#ec4899'],
                    borderWidth: 2, borderColor: '#fff'
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom' } } }
        });
    });
</script>
@endpush