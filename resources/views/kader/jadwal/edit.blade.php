@extends('layouts.dashboard')

@section('title', 'Edit Jadwal Posyandu')
@section('page-title', 'Edit Jadwal Posyandu')
@section('page-subtitle', 'Perbarui informasi jadwal kegiatan')

@section('content')
    <div class="max-w-2xl mx-auto">
        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mb-6 shadow-sm">
                <div class="font-bold mb-1">Terdapat kesalahan:</div>
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('kader.jadwal.update', $jadwal->id) }}" method="POST" class="bg-white border border-kia-100 rounded-2xl shadow-sm p-6 md:p-8">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-kia-900 mb-1.5">Kegiatan <span class="text-red-500">*</span></label>
                    <input type="text" name="kegiatan" value="{{ old('kegiatan', $jadwal->kegiatan) }}" required
                           class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-bold text-kia-900 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', $jadwal->tanggal->format('Y-m-d')) }}" required
                           class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-bold text-kia-900 mb-1.5">Lokasi <span class="text-red-500">*</span></label>
                    <input type="text" name="lokasi" value="{{ old('lokasi', $jadwal->lokasi) }}" required
                           class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-bold text-kia-900 mb-1.5">Waktu Mulai <span class="text-red-500">*</span></label>
                    <input type="time" name="waktu_mulai" value="{{ old('waktu_mulai', \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i')) }}" required
                           class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-bold text-kia-900 mb-1.5">Waktu Selesai</label>
                    <input type="time" name="waktu_selesai" value="{{ old('waktu_selesai', $jadwal->waktu_selesai ? \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') : '') }}"
                           class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-kia-900 mb-1.5">Keterangan (Opsional)</label>
                    <textarea name="keterangan" rows="3"
                              class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none transition-all">{{ old('keterangan', $jadwal->keterangan) }}</textarea>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-kia-100 flex flex-col md:flex-row gap-3">
                <button type="submit" class="w-full md:w-auto px-8 py-3 bg-gradient-to-r from-kia-500 to-kia-600 text-white rounded-xl font-bold shadow-md hover:shadow-lg transition-all">
                    Perbarui Jadwal
                </button>
                <a href="{{ route('kader.jadwal.index') }}" class="w-full md:w-auto px-8 py-3 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition-colors text-center border border-gray-200">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection