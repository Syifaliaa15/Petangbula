@extends('layouts.dashboard')

@section('title', 'Validasi Data Ibu Hamil')
@section('page-title', 'Validasi Data Ibu Hamil')
@section('page-subtitle', 'Verifikasi data pemeriksaan bumil dari Kader')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-extrabold text-kia-900 tracking-tight">Menunggu Validasi</h2>
        <p class="text-sm text-gray-500 mt-1 font-medium">Total: <span class="font-bold text-kia-600">{{ $pemeriksaans->count() }}</span> pemeriksaan ibu hamil</p>
    </div>

    @if($pemeriksaans->count() > 0)
    <form id="form-bulk-bumil" action="{{ route('bidan.validasi.bumil.bulk') }}" method="POST">
        @csrf
        <input type="hidden" name="aksi" id="bulk-aksi-bumil" value="disetujui">
        <input type="hidden" name="catatan" id="bulk-catatan-bumil" value="">

        <!-- BAR AKSI MASSAL -->
        <div class="bg-kia-50 border border-kia-200 rounded-2xl p-4 mb-6 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <label class="flex items-center gap-2 cursor-pointer font-bold text-kia-900 text-sm">
                    <input type="checkbox" id="check-all-bumil" onclick="toggleAllBumil(this)" class="w-5 h-5 accent-kia-500 rounded">
                    Pilih Semua Data
                </label>
                <div class="text-sm text-gray-600 font-medium">Terpilih: <strong id="count-selected-bumil" class="text-kia-600">0</strong> data</div>
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="button" onclick="bulkActionBumil('disetujui')" class="flex-1 sm:flex-none px-4 py-2 bg-emerald-500 text-white text-sm font-bold rounded-xl hover:bg-emerald-600 shadow-sm transition-colors">
                    ✅ Setujui Terpilih
                </button>
                <button type="button" onclick="bulkActionBumil('perbaikan')" class="flex-1 sm:flex-none px-4 py-2 bg-red-500 text-white text-sm font-bold rounded-xl hover:bg-red-600 shadow-sm transition-colors">
                    🔧 Minta Perbaikan
                </button>
            </div>
        </div>

        <!-- TAMPILAN DESKTOP -->
        <div class="hidden md:block bg-white border border-kia-100 rounded-2xl shadow-sm w-full overflow-hidden">
            <div class="overflow-x-auto w-full">
                <table class="w-full min-w-[900px] text-left border-collapse">
                    <thead class="bg-kia-50/80 border-b border-kia-100">
                        <tr>
                            <th class="px-6 py-4 w-12 text-center"></th>
                            <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase whitespace-nowrap">Nama Ibu Hamil</th>
                            <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase whitespace-nowrap">Posyandu</th>
                            <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase whitespace-nowrap">Data Periksa & Catatan</th>
                            <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase whitespace-nowrap">Status Kesehatan</th>
                            <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase text-center w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-kia-50">
                        @foreach($pemeriksaans as $p)
                        @php
                            $is_negatif = !(
                                $p->status_kek === 'Tidak Risiko' && 
                                $p->status_anemia === 'Tidak Anemia' && 
                                $p->status_td === 'Normal'
                            );

                            $data_review = [
                                'id' => $p->id,
                                'nama' => $p->ibuHamil->nama ?? '-',
                                'minggu' => $p->minggu_ke,
                                'tgl' => $p->tanggal_periksa->format('d M Y'),
                                'bb_pra' => $p->bb_pra_hamil ?? '-',
                                'tb' => $p->tinggi_badan ?? '-',
                                'bb' => $p->bb,
                                'lp' => $p->lingkar_perut ?? '-',
                                'lila' => $p->lila,
                                'hb' => $p->hemoglobin ?? '-',
                                'td' => $p->tekanan_darah ?? '-',
                                'tfu' => $p->tfu ?? '-',
                                'kat_tfu' => $p->kategori_tfu ?? '-',
                                'tbj' => $p->taksiran_bb_janin ?? '-',
                                'kat_janin' => $p->kategori_janin ?? '-',
                                'ttd' => $p->tablet_tambah_darah,
                                'pmt' => $p->pmt_pemulihan ? 'Ya' : 'Tidak',
                                'catatan_kader' => $p->keterangan ?? 'Tidak ada catatan.',
                                'kek' => $p->status_kek ?? '-',
                                'anemia' => $p->status_anemia ?? '-',
                                'statusTd' => $p->status_td ?? '-',
                                'p1' => $p->rekomendasi_1 ?? '-',
                                'p2' => $p->rekomendasi_2 ?? '-',
                                'p3' => $p->rekomendasi_3 ?? '-',
                                'is_negatif' => $is_negatif
                            ];
                        @endphp
                        <tr class="hover:bg-kia-50/50 transition-colors">
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" name="ids[]" value="{{ $p->id }}" class="check-item-bumil w-4 h-4 accent-kia-500 rounded" onclick="updateCountBumil()">
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-kia-900">{{ $p->ibuHamil->nama ?? '-' }}</div>
                                <div class="text-xs text-gray-500">Hamil Ke-{{ $p->ibuHamil->hamil_ke ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-2 py-1 bg-kia-50 border border-kia-100 text-kia-700 rounded text-xs font-bold whitespace-nowrap">{{ $p->ibuHamil->posyandu->nama_posyandu ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div class="whitespace-nowrap">BB: <strong>{{ $p->bb }} kg</strong> | LiLA: <strong>{{ $p->lila }} cm</strong></div>
                                <div class="whitespace-nowrap">HB: <strong>{{ $p->hemoglobin ?? '-' }} g/dL</strong> | TD: <strong>{{ $p->tekanan_darah ?? '-' }}</strong></div>
                                <div class="text-xs text-gray-500 mt-1 italic max-w-[280px]">Catatan: "{{ $p->keterangan ?? 'Tidak ada catatan' }}"</div>
                                <div class="text-xs text-kia-500 mt-1 whitespace-nowrap">Minggu Ke: {{ $p->minggu_ke }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5 max-w-[220px]">
                                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold border whitespace-nowrap" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($p->status_kek) }}; border-color: rgba(0,0,0,0.1);">
                                        KEK: {{ $p->status_kek ?? '-' }}
                                    </span>
                                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold border whitespace-nowrap" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($p->status_anemia) }}; border-color: rgba(0,0,0,0.1);">
                                        HB: {{ $p->status_anemia ?? '-' }}
                                    </span>
                                    @if($p->status_td)
                                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold border whitespace-nowrap" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($p->status_td) }}; border-color: rgba(0,0,0,0.1);">
                                        TD: {{ $p->status_td }}
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button type="button" onclick='bukaModalValidasiBumil({!! json_encode($data_review) !!})' class="px-3 py-1.5 bg-kia-500 text-white rounded-lg text-xs font-bold hover:bg-kia-600 transition-colors shadow-sm whitespace-nowrap">
                                    🔍 Review
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAMPILAN MOBILE -->
        <div class="block md:hidden space-y-4">
            @foreach($pemeriksaans as $p)
            @php
                $is_negatif = !(
                    $p->status_kek === 'Tidak Risiko' && 
                    $p->status_anemia === 'Tidak Anemia' && 
                    $p->status_td === 'Normal'
                );

                $data_review = [
                    'id' => $p->id,
                    'nama' => $p->ibuHamil->nama ?? '-',
                    'minggu' => $p->minggu_ke,
                    'tgl' => $p->tanggal_periksa->format('d M Y'),
                    'bb_pra' => $p->bb_pra_hamil ?? '-',
                    'tb' => $p->tinggi_badan ?? '-',
                    'bb' => $p->bb,
                    'lp' => $p->lingkar_perut ?? '-',
                    'lila' => $p->lila,
                    'hb' => $p->hemoglobin ?? '-',
                    'td' => $p->tekanan_darah ?? '-',
                    'tfu' => $p->tfu ?? '-',
                    'kat_tfu' => $p->kategori_tfu ?? '-',
                    'tbj' => $p->taksiran_bb_janin ?? '-',
                    'kat_janin' => $p->kategori_janin ?? '-',
                    'ttd' => $p->tablet_tambah_darah,
                    'pmt' => $p->pmt_pemulihan ? 'Ya' : 'Tidak',
                    'catatan_kader' => $p->keterangan ?? 'Tidak ada catatan.',
                    'kek' => $p->status_kek ?? '-',
                    'anemia' => $p->status_anemia ?? '-',
                    'statusTd' => $p->status_td ?? '-',
                    'p1' => $p->rekomendasi_1 ?? '-',
                    'p2' => $p->rekomendasi_2 ?? '-',
                    'p3' => $p->rekomendasi_3 ?? '-',
                    'is_negatif' => $is_negatif
                ];
            @endphp
            <div class="bg-white border border-kia-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="bg-kia-50/50 px-4 py-3 border-b border-kia-100 flex justify-between items-center gap-3">
                    <div class="min-w-0">
                        <h3 class="font-extrabold text-kia-900 text-lg truncate">{{ $p->ibuHamil->nama ?? '-' }}</h3>
                        <p class="text-xs text-gray-500 font-medium truncate">Hamil Ke-{{ $p->ibuHamil->hamil_ke ?? '-' }}</p>
                    </div>
                    <input type="checkbox" name="ids[]" value="{{ $p->id }}" class="check-item-bumil w-5 h-5 accent-kia-500 rounded flex-shrink-0" onclick="updateCountBumil()">
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
                        <div class="col-span-2">
                            <span class="text-gray-400 text-[10px] font-bold block">BB / LiLA</span>
                            <span class="font-medium text-gray-800">{{ $p->bb }} kg / {{ $p->lila }} cm</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-400 text-[10px] font-bold block">HB / TD</span>
                            <span class="font-medium text-gray-800">{{ $p->hemoglobin ?? '-' }} / {{ $p->tekanan_darah ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="bg-gray-50 border border-gray-100 p-2 rounded-lg mb-3">
                        <span class="text-[10px] text-gray-500 font-bold block mb-1">CATATAN KADER</span>
                        <p class="text-xs text-gray-700 italic">"{{ $p->keterangan ?? 'Tidak ada catatan' }}"</p>
                    </div>

                    <div class="bg-kia-50/50 border border-kia-100 rounded-xl p-3 mb-4">
                        <span class="text-[10px] text-gray-500 font-bold block mb-1">STATUS KESEHATAN</span>
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-block px-2 py-1 rounded-md text-xs font-bold border" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($p->status_kek) }}; border-color: rgba(0,0,0,0.1);">
                                KEK: {{ $p->status_kek ?? '-' }}
                            </span>
                            <span class="inline-block px-2 py-1 rounded-md text-xs font-bold border" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($p->status_anemia) }}; border-color: rgba(0,0,0,0.1);">
                                HB: {{ $p->status_anemia ?? '-' }}
                            </span>
                            @if($p->status_td)
                            <span class="inline-block px-2 py-1 rounded-md text-xs font-bold border" style="background: {{ \App\Helpers\ZScoreHelper::getColorForStatus($p->status_td) }}; border-color: rgba(0,0,0,0.1);">
                                TD: {{ $p->status_td }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <button type="button" onclick='bukaModalValidasiBumil({!! json_encode($data_review) !!})' class="w-full py-2 bg-gradient-to-r from-kia-500 to-kia-600 text-white rounded-xl text-sm font-bold shadow-sm">
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

    <!-- ============================================== -->
    <!-- MODAL VALIDASI BUMIL (Bergaya Halaman Registrasi) -->
    <!-- ============================================== -->
    <div id="modal-validasi-bumil" style="display: none; z-index: 99999;" class="fixed inset-0 sm:p-4 items-center justify-center">
        
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="tutupModalValidasiBumil()"></div>
        
        <div class="relative w-full h-full sm:h-auto sm:max-h-[90vh] sm:max-w-2xl bg-kia-50 sm:rounded-2xl shadow-2xl flex flex-col z-10">
            
            <div class="flex-shrink-0 px-5 py-4 border-b border-kia-200 flex justify-between items-start bg-kia-50 sm:rounded-t-2xl">
                <div>
                    <p class="text-[10px] font-bold text-kia-500 uppercase tracking-wider mb-1">Validasi Data Ibu Hamil</p>
                    <h1 class="text-2xl font-extrabold text-kia-900 leading-tight" id="mv-nama">-</h1>
                </div>
                <button type="button" onclick="tutupModalValidasiBumil()" class="p-2 bg-white text-gray-500 hover:bg-red-50 hover:text-red-500 rounded-full transition-colors flex-shrink-0 mt-1 shadow-sm border border-kia-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="form-validasi-bumil" method="POST" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <input type="hidden" name="aksi" id="input-aksi-bumil" value="disetujui">

                <div class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-4">
                    
                    <!-- Data Pengukuran -->
                    <div class="bg-white p-4 rounded-xl border border-kia-200 shadow-sm">
                        <h3 class="font-bold text-kia-900 mb-3 border-b border-kia-100 pb-2">Data Penimbangan & Pemeriksaan</h3>
                        <div class="mb-3">
                            <span class="text-gray-500 text-xs block mb-0.5">Nama Ibu Hamil</span>
                            <strong class="text-base text-kia-900" id="mv-nama-2"></strong>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div><span class="text-gray-500 text-xs block">Tanggal</span> <strong class="text-kia-800" id="mv-tgl"></strong></div>
                            <div><span class="text-gray-500 text-xs block">Minggu Ke</span> <strong class="text-kia-800" id="mv-minggu"></strong></div>
                            <div><span class="text-gray-500 text-xs block">BB Pra Hamil</span> <strong class="text-kia-800" id="mv-bb-pra"></strong> kg</div>
                            <div><span class="text-gray-500 text-xs block">Tinggi Badan</span> <strong class="text-kia-800" id="mv-tb"></strong> cm</div>
                            <div><span class="text-gray-500 text-xs block">BB Saat Ini</span> <strong class="text-kia-800" id="mv-bb"></strong> kg</div>
                            <div><span class="text-gray-500 text-xs block">Lingkar Perut</span> <strong class="text-kia-800" id="mv-lp"></strong> cm</div>
                            <div><span class="text-gray-500 text-xs block">LiLA</span> <strong class="text-kia-800" id="mv-lila"></strong> cm</div>
                            <div><span class="text-gray-500 text-xs block">HB</span> <strong class="text-kia-800" id="mv-hb"></strong> g/dL</div>
                            <div><span class="text-gray-500 text-xs block">Tekanan Darah</span> <strong class="text-kia-800" id="mv-td"></strong></div>
                            <div><span class="text-gray-500 text-xs block">TFU</span> <strong class="text-kia-800" id="mv-tfu"></strong> cm</div>
                            <div><span class="text-gray-500 text-xs block">Kategori TFU</span> <strong class="text-kia-800" id="mv-kat-tfu"></strong></div>
                            <div><span class="text-gray-500 text-xs block">Taksiran BB Janin</span> <strong class="text-kia-800" id="mv-tbj"></strong> gr</div>
                            <div><span class="text-gray-500 text-xs block">Kategori Janin</span> <strong class="text-kia-800" id="mv-kat-janin"></strong></div>
                        </div>
                    </div>

                    <!-- Program & Suplementasi -->
                    <div class="bg-white p-4 rounded-xl border border-kia-200 shadow-sm">
                        <h3 class="font-bold text-kia-900 mb-3 border-b border-kia-100 pb-2">Program & Suplementasi</h3>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div><span class="text-gray-500 text-xs block">Tablet Tambah Darah</span> <strong class="text-kia-800" id="mv-ttd"></strong> Bks</div>
                            <div><span class="text-gray-500 text-xs block">PMT Pemulihan</span> <strong class="text-kia-800" id="mv-pmt"></strong></div>
                        </div>
                    </div>

                    <!-- Catatan Kader -->
                    <div class="bg-white p-4 rounded-xl border border-kia-200 shadow-sm">
                        <span class="text-xs text-kia-600 font-bold block mb-1">Catatan / Keluhan dari Kader:</span>
                        <p id="mv-catatan-kader" class="text-sm text-gray-800 italic"></p>
                    </div>

                    <!-- Status Kesehatan -->
                    <div class="bg-white p-4 rounded-xl border border-kia-200 shadow-sm">
                        <h4 class="font-bold text-kia-900 mb-2 border-b border-kia-100 pb-2">Status Kesehatan (Sistem)</h4>
                        <div class="flex flex-col gap-2 text-sm">
                            <div class="flex justify-between border-b border-gray-100 pb-1"><span>Status KEK</span> <strong id="mv-kek" class="text-kia-700"></strong></div>
                            <div class="flex justify-between border-b border-gray-100 pb-1"><span>Status Anemia</span> <strong id="mv-anemia" class="text-kia-700"></strong></div>
                            <div class="flex justify-between"><span>Status TD</span> <strong id="mv-status-td" class="text-kia-700"></strong></div>
                        </div>
                    </div>

                    <!-- Rekomendasi C4.5 (Akan disembunyikan jika normal) -->
                    <div id="section-c45-bumil" class="bg-white border border-kia-200 p-4 rounded-xl shadow-sm">
                        <h4 class="font-bold text-kia-900 mb-3 text-sm border-b border-kia-100 pb-2">💡 Rekomendasi Tindakan (Sistem C4.5)</h4>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 cursor-pointer p-3 bg-kia-50 border border-kia-100 rounded-lg hover:bg-kia-100 transition-colors">
                                <input type="checkbox" name="tindakan_bidan[]" id="cb-p1" value="Prioritas 1" class="w-4 h-4 accent-kia-500 rounded flex-shrink-0">
                                <span class="text-sm font-medium text-gray-800">Prioritas 1: <span id="text-p1" class="font-bold text-kia-700"></span></span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer p-3 bg-kia-50 border border-kia-100 rounded-lg hover:bg-kia-100 transition-colors">
                                <input type="checkbox" name="tindakan_bidan[]" id="cb-p2" value="Prioritas 2" class="w-4 h-4 accent-kia-500 rounded flex-shrink-0">
                                <span class="text-sm font-medium text-gray-800">Prioritas 2: <span id="text-p2" class="font-bold text-kia-700"></span></span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer p-3 bg-kia-50 border border-kia-100 rounded-lg hover:bg-kia-100 transition-colors">
                                <input type="checkbox" name="tindakan_bidan[]" id="cb-p3" value="Prioritas 3" class="w-4 h-4 accent-kia-500 rounded flex-shrink-0">
                                <span class="text-sm font-medium text-gray-800">Prioritas 3: <span id="text-p3" class="font-bold text-kia-700"></span></span>
                            </label>
                            <label class="flex flex-col gap-2 p-3 bg-kia-50 border border-kia-100 rounded-lg hover:bg-kia-100 transition-colors cursor-pointer">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" name="tindakan_bidan[]" id="cb-lain" value="Lainnya" class="w-4 h-4 accent-kia-500 rounded flex-shrink-0" onchange="toggleInputLain()">
                                    <span class="text-sm font-medium text-gray-800">Tindakan Lainnya (Bidan)</span>
                                </div>
                                <input type="text" name="catatan_tindakan_lain" id="input-lain" class="hidden w-full px-3 py-2 text-sm border border-kia-200 rounded-md bg-white focus:ring-1 focus:ring-kia-500 outline-none" placeholder="Ketik tindakan medis...">
                            </label>
                        </div>
                    </div>

                    <!-- Catatan Bidan -->
                    <div class="bg-white p-4 rounded-xl border border-kia-200 shadow-sm">
                        <label class="block text-sm font-bold text-kia-900 mb-2">Catatan untuk Kader (opsional)</label>
                        <textarea name="catatan" class="w-full px-3 py-2 border border-kia-200 rounded-xl bg-kia-50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none text-sm" rows="3" placeholder="Isi jika ada koreksi..."></textarea>
                    </div>
                </div>

                <div class="flex-shrink-0 p-4 border-t border-kia-200 bg-white sm:rounded-b-2xl flex gap-3">
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
        document.getElementById('mv-nama-2').innerText = data.nama;
        document.getElementById('mv-minggu').innerText = data.minggu;
        document.getElementById('mv-tgl').innerText = data.tgl;
        
        document.getElementById('mv-bb-pra').innerText = data.bb_pra;
        document.getElementById('mv-tb').innerText = data.tb;
        document.getElementById('mv-bb').innerText = data.bb;
        document.getElementById('mv-lp').innerText = data.lp;
        document.getElementById('mv-lila').innerText = data.lila;
        document.getElementById('mv-hb').innerText = data.hb;
        document.getElementById('mv-td').innerText = data.td;
        document.getElementById('mv-tfu').innerText = data.tfu;
        document.getElementById('mv-kat-tfu').innerText = data.kat_tfu;
        document.getElementById('mv-tbj').innerText = data.tbj;
        document.getElementById('mv-kat-janin').innerText = data.kat_janin;

        document.getElementById('mv-ttd').innerText = data.ttd;
        document.getElementById('mv-pmt').innerText = data.pmt;

        document.getElementById('mv-catatan-kader').innerText = '"' + data.catatan_kader + '"';
        document.getElementById('mv-kek').innerText = data.kek;
        document.getElementById('mv-anemia').innerText = data.anemia;
        document.getElementById('mv-status-td').innerText = data.statusTd;

        // C4.5
        document.getElementById('text-p1').innerText = data.p1 || '-';
        document.getElementById('text-p2').innerText = data.p2 || '-';
        document.getElementById('text-p3').innerText = data.p3 || '-';
        document.getElementById('cb-p1').value = data.p1 || '-';
        document.getElementById('cb-p2').value = data.p2 || '-';
        document.getElementById('cb-p3').value = data.p3 || '-';

        document.getElementById('cb-p1').checked = false;
        document.getElementById('cb-p2').checked = false;
        document.getElementById('cb-p3').checked = false;
        document.getElementById('cb-lain').checked = false;
        document.getElementById('input-lain').classList.add('hidden');
        document.getElementById('input-lain').value = '';
        document.getElementById('input-lain').required = false;

        // Tampilkan / Sembunyikan Section C4.5
        if (data.is_negatif) {
            document.getElementById('section-c45-bumil').style.display = 'block';
        } else {
            document.getElementById('section-c45-bumil').style.display = 'none';
        }

        document.getElementById('form-validasi-bumil').action = '/bidan/validasi/bumil/' + data.id;
        document.getElementById('modal-validasi-bumil').style.display = 'flex';
    }

    function tutupModalValidasiBumil() {
        document.getElementById('modal-validasi-bumil').style.display = 'none';
    }

    function setAksiBumil(aksi) {
        document.getElementById('input-aksi-bumil').value = aksi;
        document.getElementById('form-validasi-bumil').submit();
    }

    function toggleInputLain() {
        const cbLain = document.getElementById('cb-lain');
        const inputLain = document.getElementById('input-lain');
        if (cbLain.checked) {
            inputLain.classList.remove('hidden');
            inputLain.required = true;
        } else {
            inputLain.classList.add('hidden');
            inputLain.required = false;
        }
    }
</script>
@endpush