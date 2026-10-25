@extends('layouts.dashboard')

@section('title', 'Pemeriksaan Darurat')
@section('page-title', 'Pemeriksaan Darurat')
@section('page-subtitle', 'Catat dan kelola kasus pemeriksaan darurat')

@section('content')
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
        
        <!-- KOLOM KIRI: FORM INPUT (2/3 Lebar) -->
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white border border-kia-100 rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-kia-900 mb-4 border-b border-kia-100 pb-3">🚨 Form Pemeriksaan Darurat</h2>
                <p class="text-sm text-gray-500 mb-6">Catat kasus pemeriksaan darurat yang membutuhkan penanganan segera.</p>

                @if($errors->any())
                    <div class="bg-red-50 text-red-700 p-3 rounded-xl mb-4 text-sm font-medium border-l-4 border-red-500">
                        <ul class="list-disc pl-4">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('bidan.darurat.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-bold text-kia-900 mb-1.5">Nama Pasien <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_pasien" class="w-full px-3 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none" value="{{ old('nama_pasien') }}" required placeholder="Nama lengkap pasien">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-kia-900 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                            <select name="kategori" class="w-full px-3 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none" required>
                                <option value="balita" {{ old('kategori') == 'balita' ? 'selected' : '' }}>Balita</option>
                                <option value="bumil" {{ old('kategori') == 'bumil' ? 'selected' : '' }}>Ibu Hamil</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-kia-900 mb-1.5">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="w-full px-3 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none">
                                <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-kia-900 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal" class="w-full px-3 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none" value="{{ old('tanggal', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-kia-900 mb-1.5">Keluhan / Diagnosis <span class="text-red-500">*</span></label>
                        <textarea name="keluhan" class="w-full px-3 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none" rows="3" required placeholder="Deskripsi keluhan atau diagnosis">{{ old('keluhan') }}</textarea>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-kia-900 mb-1.5">Tindakan / Penanganan</label>
                        <textarea name="tindakan" class="w-full px-3 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none" rows="2" placeholder="Tindakan yang telah diberikan">{{ old('tindakan') }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-kia-500 to-kia-600 text-white font-bold rounded-xl shadow-md hover:shadow-lg transition-all">
                        💾 Simpan Data Darurat
                    </button>
                </form>
            </div>
        </div>

        <!-- KOLOM KANAN: RIWAYAT (1/3 Lebar) -->
        <div class="xl:col-span-1">
            <div class="bg-white border border-kia-100 rounded-2xl shadow-sm p-5">
                <h3 class="text-base font-bold text-kia-900 mb-4 border-b border-kia-100 pb-3">📋 Riwayat Terakhir</h3>
                
                <div class="space-y-4 max-h-[600px] overflow-y-auto pr-2">
                    @forelse($riwayat as $r)
                        <div class="bg-kia-50/50 border border-kia-100 rounded-xl p-3 text-sm">
                            <div class="flex justify-between items-start mb-1">
                                <strong class="text-kia-900">{{ $r->nama_pasien }}</strong>
                                <span class="text-xs font-bold px-2 py-0.5 rounded {{ $r->kategori == 'balita' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                    {{ ucfirst($r->kategori) }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 mb-2">{{ $r->tanggal->format('d M Y') }}</div>
                            <div class="text-xs text-gray-700 mb-2"><strong>Keluhan:</strong> {{ Str::limit($r->keluhan, 50) }}</div>
                            <div class="text-xs text-gray-700"><strong>Tindakan:</strong> {{ Str::limit($r->tindakan ?? '-', 50) }}</div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-400 text-sm">
                            Belum ada riwayat pemeriksaan darurat.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection