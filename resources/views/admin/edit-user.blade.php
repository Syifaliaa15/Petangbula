@extends('layouts.dashboard')

@section('title', 'Edit Pengguna')
@section('page-title', 'Edit Pengguna: ' . $user->name)
@section('page-subtitle', 'Perbarui data pengguna sistem')

@section('sidebar-menu')
    <a href="{{ route('admin.dashboard') }}" class="menu-item">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
        Dashboard
    </a>
    <a href="{{ route('admin.kelola-akun') }}" class="menu-item active">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
        Kelola Akun
    </a>
    <a href="{{ route('admin.laporan-posyandu') }}" class="menu-item">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
        Laporan Posyandu
    </a>
    <a href="{{ route('admin.laporan-darurat') }}" class="menu-item">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
        Laporan Darurat
    </a>
@endsection

@section('content')
    <div class="card" style="max-width: 800px; margin: 0 auto;">

        @if($errors->any())
            <div class="alert alert-error">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.update-user', $user->id) }}" method="POST">
            @csrf @method('PUT')

            <div class="form-grid">
                <div class="form-group">
                    <label>Nama Lengkap <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="form-group">
                    <label>Username <span class="required">*</span></label>
                    <input type="text" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
                </div>

                <div class="form-group">
                    <label>Password Baru <small style="color:#9ca3af;">(kosongkan jika tidak ganti)</small></label>
                    <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter">
                    <small style="color: #9ca3af; font-size: 0.75rem; display: block; margin-top: 4px;">
                        📌 Harus minimal 8 karakter, mengandung huruf besar, huruf kecil, dan angka.
                    </small>
                    @error('password') <small style="color: #ef4444; display: block;">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Role Jabatan <span class="required">*</span></label>
                    <select name="role" id="role-select" class="form-control" required onchange="togglePosyanduSelect()">
                        <option value="bidan" {{ old('role', $user->role) == 'bidan' ? 'selected' : '' }}>Bidan</option>
                        <option value="kader" {{ old('role', $user->role) == 'kader' ? 'selected' : '' }}>Kader</option>
                    </select>
                </div>

                <div id="posyandu-container" class="form-group" style="display: {{ old('role', $user->role) == 'kader' ? 'block' : 'none' }};">
                    <label>Penempatan Posyandu <span class="required">*</span></label>
                    <select name="posyandu_id" class="form-control">
                        <option value="">-- Pilih Posyandu --</option>
                        @foreach($posyandus as $p)
                            <option value="{{ $p->id }}" {{ old('posyandu_id', $user->posyandu_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_posyandu }} (RW {{ $p->rw }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Status Akun <span class="required">*</span></label>
                    <select name="is_active" class="form-control" required>
                        <option value="1" {{ old('is_active', $user->is_active) ? 'selected' : '' }}>✅ Aktif</option>
                        <option value="0" {{ !old('is_active', $user->is_active) ? 'selected' : '' }}>❌ Nonaktif</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #fbcfe8;">
                <button type="submit" class="btn-primary" style="flex: 1;">💾 Perbarui Data</button>
                <a href="{{ route('admin.kelola-akun') }}" class="btn-secondary" style="flex: 1;">Batal</a>
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