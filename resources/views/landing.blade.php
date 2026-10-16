@extends('layouts.landing')

@section('title', 'PETANGBULA - Pemantauan Tumbuh Kembang Ibu Hamil dan Balita')

@section('content')

    {{-- ==========================================
         HERO SECTION
         ========================================== --}}
    <section id="home" class="max-w-7xl mx-auto px-4 md:px-6 py-10 md:py-20">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-16 items-center">
            
            <div class="text-center md:text-left order-2 md:order-1">
                <div class="inline-flex items-center gap-2 bg-kia-100 text-kia-700 px-3 py-1.5 rounded-md text-xs font-semibold mb-5 border border-kia-300">
                    <span class="w-1.5 h-1.5 bg-kia-500 rounded-full"></span>
                    Posyandu Desa Sindangpanon
                </div>
                
                <h1 class="text-3xl md:text-5xl font-extrabold leading-tight text-kia-900 mb-4 md:mb-5 tracking-tight">
                    Pantau Tumbuh Kembang<br>
                    <span class="text-kia-500">Si Kecil</span> dengan Penuh Kasih
                </h1>
                
                <p class="text-sm md:text-base text-text-medium leading-relaxed mb-6 md:mb-8 max-w-xl mx-auto md:mx-0">
                    Sistem pemantauan kesehatan gizi terpadu Posyandu Desa Sindangpanon berbasis acuan standar resmi Permenkes RI No. 2 Tahun 2020.
                </p>
                
                <a href="#cek-gizi" class="inline-flex items-center justify-center gap-2 px-6 md:px-7 py-3 bg-kia-500 text-white rounded-md text-sm md:text-base font-semibold hover:bg-kia-600 transition-all">
                    Cek Gizi Sekarang
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/>
                    </svg>
                </a>
            </div>

            <div class="flex justify-center items-center order-1 md:order-2">
                <img src="{{ asset('images/Logo2.png') }}" alt="Ilustrasi Ibu dan Anak" class="w-48 md:w-full max-w-md">
            </div>
        </div>
    </section>

    {{-- ==========================================
         INFORMASI & EDUKASI
         ========================================== --}}
    <section id="edukasi" class="bg-kia-100 border-y border-kia-200">
        <div class="max-w-7xl mx-auto px-4 md:px-6 py-12 md:py-20">
            
            <div class="text-center mb-8 md:mb-12">
                <h2 class="text-2xl md:text-3xl font-extrabold text-kia-900 mb-2 tracking-tight">
                    Informasi & Edukasi Kesehatan
                </h2>
                <p class="text-sm md:text-base text-text-medium">
                    Tips penting untuk tumbuh kembang optimal ibu dan buah hati
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">
                
                <div class="bg-white p-5 md:p-6 rounded-lg border border-kia-200 hover:border-kia-500 hover:shadow-md transition-all">
                    <svg class="w-9 h-9 md:w-10 md:h-10 text-kia-500 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <h3 class="text-sm md:text-base font-bold text-kia-900 mb-2">ASI Eksklusif</h3>
                    <p class="text-xs md:text-sm text-text-medium leading-relaxed">
                        Berikan ASI eksklusif selama 6 bulan pertama kehidupan untuk tumbuh kembang optimal.
                    </p>
                </div>

                <div class="bg-white p-5 md:p-6 rounded-lg border border-kia-200 hover:border-kia-500 hover:shadow-md transition-all">
                    <svg class="w-9 h-9 md:w-10 md:h-10 text-kia-500 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h3 class="text-sm md:text-base font-bold text-kia-900 mb-2">MPASI Sehat</h3>
                    <p class="text-xs md:text-sm text-text-medium leading-relaxed">
                        Mulai MPASI pada usia 6 bulan dengan gizi seimbang untuk mendukung pertumbuhan.
                    </p>
                </div>

                <div class="bg-white p-5 md:p-6 rounded-lg border border-kia-200 hover:border-kia-500 hover:shadow-md transition-all">
                    <svg class="w-9 h-9 md:w-10 md:h-10 text-kia-500 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M12 3a9 9 0 100 18 9 9 0 000-18z"/>
                    </svg>
                    <h3 class="text-sm md:text-base font-bold text-kia-900 mb-2">Pencegahan Stunting</h3>
                    <p class="text-xs md:text-sm text-text-medium leading-relaxed">
                        Pastikan tumbuh kembang anak optimal sejak dini dengan pemantauan rutin.
                    </p>
                </div>

                <div class="bg-white p-5 md:p-6 rounded-lg border border-kia-200 hover:border-kia-500 hover:shadow-md transition-all">
                    <svg class="w-9 h-9 md:w-10 md:h-10 text-kia-500 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                    </svg>
                    <h3 class="text-sm md:text-base font-bold text-kia-900 mb-2">Gizi Ibu Hamil</h3>
                    <p class="text-xs md:text-sm text-text-medium leading-relaxed">
                        Konsumsi makanan bergizi untuk ibu dan janin yang sehat selama kehamilan.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ==========================================
         JADWAL POSYANDU
         ========================================== --}}
    <section id="jadwal" class="max-w-7xl mx-auto px-4 md:px-6 py-12 md:py-20">
        
        <div class="mb-6 md:mb-8">
            <h2 class="text-2xl md:text-3xl font-extrabold text-kia-900 mb-2 tracking-tight">
                Jadwal Posyandu
            </h2>
            <p class="text-sm md:text-base text-text-medium">
                Pilih RW untuk melihat jadwal kegiatan posyandu di wilayah Anda
            </p>
        </div>

        <form method="GET" action="{{ route('home') }}#jadwal" class="flex flex-col sm:flex-row gap-3 mb-5">
            <select name="rw" class="w-full sm:max-w-xs px-4 py-2.5 text-sm border border-kia-300 rounded-md bg-white text-text-dark focus:border-kia-500 focus:ring-2 focus:ring-kia-200 outline-none transition-all" onchange="this.form.submit()">
                <option value="">Semua RW</option>
                @foreach($posyandus as $p)
                    <option value="{{ $p->rw }}" {{ $selectedRw == $p->rw ? 'selected' : '' }}>
                        RW {{ $p->rw }} — {{ $p->nama_posyandu }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="px-5 py-2.5 bg-kia-500 text-white text-sm font-semibold rounded-md hover:bg-kia-600 transition-all">
                Filter
            </button>
            @if($selectedRw)
                <a href="{{ route('home') }}#jadwal" class="px-5 py-2.5 bg-white border-2 border-kia-300 text-kia-700 text-sm font-semibold rounded-md hover:border-kia-400 transition-all text-center">
                    Reset
                </a>
            @endif
        </form>

        @if($jadwals->count() > 0)
            <div class="border border-kia-200 rounded-lg overflow-hidden bg-white">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[700px]">
                        <thead class="bg-kia-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-kia-900 uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-kia-900 uppercase tracking-wider">Nama Kegiatan</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-kia-900 uppercase tracking-wider">Waktu</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-kia-900 uppercase tracking-wider">Lokasi</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-kia-900 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-kia-200">
                            @foreach($jadwals as $j)
                                <tr class="hover:bg-kia-100/50 transition-colors">
                                    <td class="px-4 py-4">
                                        <div class="inline-flex flex-col items-center justify-center min-w-[52px] px-2 py-1.5 bg-kia-500 text-white rounded-md leading-tight">
                                            <span class="text-base font-extrabold">{{ $j->tanggal->format('d') }}</span>
                                            <span class="text-[10px] font-semibold uppercase tracking-wide opacity-90">{{ $j->tanggal->translatedFormat('M') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm font-semibold text-text-dark">{{ $j->kegiatan }}</div>
                                        @if($j->keterangan)
                                            <div class="text-xs text-text-light mt-1">{{ Str::limit($j->keterangan, 60) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm text-text-medium">
                                        {{ \Carbon\Carbon::parse($j->waktu_mulai)->format('H:i') }}
                                        @if($j->waktu_selesai)
                                            — {{ \Carbon\Carbon::parse($j->waktu_selesai)->format('H:i') }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm text-text-medium">
                                        {{ $j->lokasi }}
                                        @if($j->posyandu)
                                            <div class="text-xs text-kia-500 mt-1 font-medium">RW {{ $j->posyandu->rw }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        @if($j->tanggal->isToday())
                                            <span class="inline-block px-2.5 py-1 bg-amber-50 text-amber-700 text-xs font-semibold rounded">Hari Ini</span>
                                        @elseif($j->tanggal->isFuture())
                                            <span class="inline-block px-2.5 py-1 bg-emerald-50 text-emerald-700 text-xs font-semibold rounded">Tersedia</span>
                                        @else
                                            <span class="inline-block px-2.5 py-1 bg-kia-100 text-kia-700 text-xs font-semibold rounded">Selesai</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="text-center py-16 bg-kia-100 rounded-lg border border-dashed border-kia-300">
                <svg class="w-12 h-12 text-kia-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm text-text-medium">Belum ada jadwal posyandu untuk periode ini.</p>
            </div>
        @endif
    </section>

    {{-- ==========================================
         KALKULATOR GIZI
         ========================================== --}}
    <section id="cek-gizi" class="max-w-3xl mx-auto px-4 md:px-6 pb-12 md:pb-20">
        <div class="bg-white border border-kia-200 rounded-lg p-5 md:p-8 shadow-sm">
            
            <div class="text-center mb-6 md:mb-8 pb-6 border-b border-kia-200">
                <h2 class="text-xl md:text-2xl font-extrabold text-kia-900 mb-1.5 tracking-tight">
                    Kalkulator Status Gizi Mandiri
                </h2>
                <p class="text-xs md:text-sm text-text-light">
                    Berdasarkan Standar Baku Permenkes RI No. 2 Tahun 2020
                </p>
            </div>

            <div class="flex border-b border-kia-200 mb-6 md:mb-8">
                <button id="btn-tab-balita" class="flex-1 px-4 py-3.5 text-sm font-semibold text-kia-700 border-b-2 border-kia-500 -mb-px transition-all bg-transparent" onclick="switchTab('balita')">
                    Kalkulator Balita
                </button>
                <button id="btn-tab-bumil" class="flex-1 px-4 py-3.5 text-sm font-semibold text-text-light border-b-2 border-transparent -mb-px hover:text-kia-500 transition-all bg-transparent" onclick="switchTab('bumil')">
                    Kalkulator Ibu Hamil
                </button>
            </div>

            {{-- TAB BALITA --}}
            <div id="tab-balita" class="tab-content active">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-3">
                        <label for="gender" class="block text-xs font-semibold text-kia-800 mb-1.5">Jenis Kelamin</label>
                        <select id="gender" class="w-full px-3.5 py-2.5 text-sm border border-kia-300 rounded-md bg-white text-text-dark focus:border-kia-500 focus:ring-2 focus:ring-kia-200 outline-none transition-all">
                            <option value="male">Laki-Laki</option>
                            <option value="female">Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="umur" class="block text-xs font-semibold text-kia-800 mb-1.5">Umur (Bulan)</label>
                        <input type="number" id="umur" class="w-full px-3.5 py-2.5 text-sm border border-kia-300 rounded-md bg-white text-text-dark focus:border-kia-500 focus:ring-2 focus:ring-kia-200 outline-none transition-all" placeholder="0 - 60" min="0" max="60">
                    </div>
                    <div class="mb-3">
                        <label for="bbRiil" class="block text-xs font-semibold text-kia-800 mb-1.5">Berat Badan (kg)</label>
                        <input type="number" id="bbRiil" step="0.1" class="w-full px-3.5 py-2.5 text-sm border border-kia-300 rounded-md bg-white text-text-dark focus:border-kia-500 focus:ring-2 focus:ring-kia-200 outline-none transition-all" placeholder="Contoh: 8.5">
                    </div>
                    <div class="mb-3">
                        <label for="tbRiil" class="block text-xs font-semibold text-kia-800 mb-1.5">Tinggi/Panjang Badan (cm)</label>
                        <input type="number" id="tbRiil" step="0.1" class="w-full px-3.5 py-2.5 text-sm border border-kia-300 rounded-md bg-white text-text-dark focus:border-kia-500 focus:ring-2 focus:ring-kia-200 outline-none transition-all" placeholder="Contoh: 75.0">
                    </div>
                </div>
                <button type="button" class="w-full mt-2 inline-flex items-center justify-center gap-2 px-6 py-3 bg-kia-500 text-white rounded-md text-sm font-semibold hover:bg-kia-600 transition-all" onclick="hitungGiziBalita()">
                    Hitung Status Gizi
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/>
                    </svg>
                </button>
                <div id="resultBalita" class="mt-5 flex flex-col gap-2"></div>
            </div>

            {{-- TAB BUMIL --}}
            <div id="tab-bumil" class="tab-content" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-3">
                        <label for="tbBumil" class="block text-xs font-semibold text-kia-800 mb-1.5">Tinggi Badan (cm)</label>
                        <input type="number" id="tbBumil" step="0.1" class="w-full px-3.5 py-2.5 text-sm border border-kia-300 rounded-md bg-white text-text-dark focus:border-kia-500 focus:ring-2 focus:ring-kia-200 outline-none transition-all" placeholder="Contoh: 155">
                    </div>
                    <div class="mb-3">
                        <label for="bbPra" class="block text-xs font-semibold text-kia-800 mb-1.5">BB Sebelum Hamil (kg)</label>
                        <input type="number" id="bbPra" step="0.1" class="w-full px-3.5 py-2.5 text-sm border border-kia-300 rounded-md bg-white text-text-dark focus:border-kia-500 focus:ring-2 focus:ring-kia-200 outline-none transition-all" placeholder="Contoh: 50">
                    </div>
                    <div class="mb-3">
                        <label for="bbSkrg" class="block text-xs font-semibold text-kia-800 mb-1.5">BB Saat Ini (kg)</label>
                        <input type="number" id="bbSkrg" step="0.1" class="w-full px-3.5 py-2.5 text-sm border border-kia-300 rounded-md bg-white text-text-dark focus:border-kia-500 focus:ring-2 focus:ring-kia-200 outline-none transition-all" placeholder="Contoh: 55">
                    </div>
                    <div class="mb-3">
                        <label for="usiaHamil" class="block text-xs font-semibold text-kia-800 mb-1.5">Usia Kehamilan (minggu)</label>
                        <input type="number" id="usiaHamil" min="1" max="42" class="w-full px-3.5 py-2.5 text-sm border border-kia-300 rounded-md bg-white text-text-dark focus:border-kia-500 focus:ring-2 focus:ring-kia-200 outline-none transition-all" placeholder="1 - 42">
                    </div>
                    <div class="mb-3">
                        <label for="lila" class="block text-xs font-semibold text-kia-800 mb-1.5">LiLA (cm) <span class="text-text-light font-normal">(Opsional)</span></label>
                        <input type="number" id="lila" step="0.1" class="w-full px-3.5 py-2.5 text-sm border border-kia-300 rounded-md bg-white text-text-dark focus:border-kia-500 focus:ring-2 focus:ring-kia-200 outline-none transition-all" placeholder="Contoh: 24">
                    </div>
                    <div class="mb-3">
                        <label for="hb" class="block text-xs font-semibold text-kia-800 mb-1.5">Hemoglobin (g/dL) <span class="text-text-light font-normal">(Opsional)</span></label>
                        <input type="number" id="hb" step="0.1" class="w-full px-3.5 py-2.5 text-sm border border-kia-300 rounded-md bg-white text-text-dark focus:border-kia-500 focus:ring-2 focus:ring-kia-200 outline-none transition-all" placeholder="Contoh: 11.5">
                    </div>
                </div>
                <button type="button" class="w-full mt-2 inline-flex items-center justify-center gap-2 px-6 py-3 bg-kia-500 text-white rounded-md text-sm font-semibold hover:bg-kia-600 transition-all" onclick="hitungGiziBumil()">
                    Hitung Status Gizi
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/>
                    </svg>
                </button>
                <div id="resultBumil" class="mt-5 flex flex-col gap-2"></div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
<script src="{{ asset('js/dataset.js') }}"></script>
<script src="{{ asset('js/kalkulator.js') }}"></script>
<script>
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href.startsWith('#')) {
                e.preventDefault();
                document.querySelector(href)?.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
    
    window.switchTab = function(tabName) {
        const tabBalita = document.getElementById('tab-balita');
        const tabBumil = document.getElementById('tab-bumil');
        const btnBalita = document.getElementById('btn-tab-balita');
        const btnBumil = document.getElementById('btn-tab-bumil');
        
        if (tabName === 'balita') {
            tabBalita.style.display = 'block';
            tabBumil.style.display = 'none';
            btnBalita.classList.add('text-kia-700', 'border-kia-500');
            btnBalita.classList.remove('text-text-light', 'border-transparent');
            btnBumil.classList.remove('text-kia-700', 'border-kia-500');
            btnBumil.classList.add('text-text-light', 'border-transparent');
        } else {
            tabBumil.style.display = 'block';
            tabBalita.style.display = 'none';
            btnBumil.classList.add('text-kia-700', 'border-kia-500');
            btnBumil.classList.remove('text-text-light', 'border-transparent');
            btnBalita.classList.remove('text-kia-700', 'border-kia-500');
            btnBalita.classList.add('text-text-light', 'border-transparent');
        }
    };
</script>
@endpush