@extends('layouts.dashboard')

@section('title', 'Edit Pengguna')
@section('page-title', 'Edit Pengguna: ' . $user->name)
@section('page-subtitle', 'Perbarui data pengguna sistem')

@section('content')
    <div class="max-w-2xl mx-auto">
        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mb-6 shadow-sm">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.update-user', $user->id) }}" method="POST" class="bg-white border border-kia-100 rounded-2xl shadow-sm p-6 md:p-8">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-kia-900 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-kia-900 mb-1.5">Username <span class="text-red-500">*</span></label>
                    <input type="text" name="username" class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none" value="{{ old('username', $user->username) }}" required>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-kia-900 mb-1.5">Password Baru <small class="text-gray-400 font-normal">(Kosongkan jika tidak ingin ganti)</small></label>
                    <input type="password" name="password" class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none" placeholder="Minimal 8 karakter, kombinasi huruf & angka">
                    <p class="text-[11px] text-kia-500 mt-1 italic">* Harus mengandung huruf besar, huruf kecil, dan angka. Tidak boleh sama dengan user lain.</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-kia-900 mb-1.5">Role Jabatan <span class="text-red-500">*</span></label>
                    <select name="role" id="role-select" class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none" required onchange="togglePosyanduSelect()">
                        <option value="bidan" {{ old('role', $user->role) == 'bidan' ? 'selected' : '' }}>Bidan</option>
                        <option value="kader" {{ old('role', $user->role) == 'kader' ? 'selected' : '' }}>Kader</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-kia-900 mb-1.5">Status Akun <span class="text-red-500">*</span></label>
                    <select name="is_active" class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none" required>
                        <option value="1" {{ old('is_active', $user->is_active) ? 'selected' : '' }}>✅ Aktif</option>
                        <option value="0" {{ !old('is_active', $user->is_active) ? 'selected' : '' }}>❌ Nonaktif</option>
                    </select>
                </div>

                <div id="posyandu-container" class="md:col-span-2" style="display: {{ old('role', $user->role) == 'kader' ? 'block' : 'none' }};">
                    <label class="block text-sm font-bold text-kia-900 mb-1.5">Penempatan Posyandu <span class="text-red-500">*</span></label>
                    <select name="posyandu_id" class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none">
                        <option value="">-- Pilih Posyandu --</option>
                        @foreach($posyandus as $p)
                            <option value="{{ $p->id }}" {{ old('posyandu_id', $user->posyandu_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_posyandu }} (RW {{ $p->rw }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-kia-100 flex flex-col md:flex-row gap-3">
                <button type="submit" class="w-full md:w-auto px-8 py-3 bg-gradient-to-r from-kia-500 to-kia-600 text-white rounded-xl font-bold shadow-md hover:shadow-lg transition-all">
                    💾 Perbarui Data
                </button>
                <a href="{{ route('admin.kelola-akun') }}" class="w-full md:w-auto px-8 py-3 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition-colors text-center border border-gray-200">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    function togglePosyanduSelect() {
        const role = document.getElementById('role-select').value;
        document.getElementById('posyandu-container').style.display = (role === 'kader') ? 'block' : 'none';
    }
</script>
@endpush