@extends('layouts.dashboard')

@section('title', 'Kelola Akun Pengguna')
@section('page-title', 'Kelola Akun Pengguna')
@section('page-subtitle', 'Manajemen user sistem Petangbula')

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
    <!-- FORM TAMBAH USER -->
    <div class="card">
        <h2 class="card-title">➕ Tambah Pengguna Baru</h2>

        <form action="{{ route('admin.tambah-user') }}" method="POST">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label>Nama Lengkap <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Contoh: Kader Rina Wati">
                    @error('name') <small style="color: #ef4444;">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Username <span class="required">*</span></label>
                    <input type="text" name="username" class="form-control" value="{{ old('username') }}" required placeholder="Contoh: kader20">
                    @error('username') <small style="color: #ef4444;">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Password <span class="required">*</span></label>
                    <input type="password" name="password" class="form-control" required placeholder="Minimal 8 karakter">
                    <small style="color: #9ca3af; font-size: 0.75rem; display: block; margin-top: 4px;">
                        📌 Harus minimal 8 karakter, mengandung huruf besar, huruf kecil, dan angka.
                        Password tidak boleh sama dengan user lain.
                    </small>
                    @error('password') <small style="color: #ef4444; display: block;">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Role Jabatan <span class="required">*</span></label>
                    <select name="role" id="role-select" class="form-control" required onchange="togglePosyanduSelect()">
                        <option value="bidan" {{ old('role') == 'bidan' ? 'selected' : '' }}>Bidan</option>
                        <option value="kader" {{ old('role') == 'kader' ? 'selected' : '' }}>Kader</option>
                    </select>
                </div>

                <div id="posyandu-container" class="form-group" style="display: {{ old('role') == 'kader' ? 'block' : 'none' }};">
                    <label>Penempatan Posyandu <span class="required">*</span></label>
                    <select name="posyandu_id" class="form-control">
                        <option value="">-- Pilih Posyandu --</option>
                        @foreach($posyandus as $p)
                            <option value="{{ $p->id }}" {{ old('posyandu_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_posyandu }} (RW {{ $p->rw }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" class="btn-primary">💾 Simpan Pengguna</button>
            </div>
        </form>
    </div>

    <!-- FILTER + SEARCH -->
    <div class="card" style="padding: 16px; margin-bottom: 20px;">
        <form method="GET" action="{{ route('admin.kelola-akun') }}" style="display: flex; gap: 12px; align-items: end; flex-wrap: wrap;">
            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
                <label style="font-size: 0.8rem;">🔍 Cari (Nama / Username)</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Ketik untuk mencari...">
            </div>

            <div class="form-group" style="margin-bottom: 0; min-width: 140px;">
                <label style="font-size: 0.8rem;">Role</label>
                <select name="role" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    <option value="bidan" {{ request('role') == 'bidan' ? 'selected' : '' }}>Bidan</option>
                    <option value="kader" {{ request('role') == 'kader' ? 'selected' : '' }}>Kader</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0; min-width: 140px;">
                <label style="font-size: 0.8rem;">Status</label>
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>✅ Aktif</option>
                    <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>❌ Nonaktif</option>
                </select>
            </div>

            <button type="submit" class="btn-primary" style="padding: 12px 20px;">Cari</button>
            @if(request()->hasAny(['search', 'role', 'status']))
                <a href="{{ route('admin.kelola-akun') }}" class="btn-secondary" style="padding: 12px 20px;">Reset</a>
            @endif
        </form>
    </div>

    <!-- DAFTAR USER -->
    <div class="card">
        <h2 class="card-title">👥 Daftar Pengguna Terdaftar ({{ $users->count() }})</h2>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama & Username</th>
                        <th>Role</th>
                        <th>Posyandu</th>
                        <th>Status</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div style="font-weight: 700; color: #831843;">{{ $user->name }}</div>
                            <div style="font-size: 0.78rem; color: #9ca3af;">@ {{ $user->username }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $user->role === 'bidan' ? 'badge-info' : 'badge-warning' }}">
                                {{ strtoupper($user->role) }}
                            </span>
                        </td>
                        <td>
                            @if($user->posyandu)
                                <span class="badge badge-success">{{ $user->posyandu->nama_posyandu }}</span>
                            @else
                                <span style="color: #9ca3af;">-</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge badge-success">✅ Aktif</span>
                            @else
                                <span class="badge badge-danger">❌ Nonaktif</span>
                            @endif
                        </td>
                        <td style="text-align: center; white-space: nowrap;">
                            <a href="{{ route('admin.edit-user', $user->id) }}" class="btn-action btn-edit" style="margin-right: 4px;">✏️ Edit</a>

                            <form action="{{ route('admin.toggle-status', $user->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn-action" style="background: {{ $user->is_active ? '#fef3c7' : '#d1fae5' }}; color: {{ $user->is_active ? '#92400e' : '#065f46' }}; margin-right: 4px;">
                                    {{ $user->is_active ? '🔒' : '🔓' }}
                                </button>
                            </form>

                            <form action="{{ route('admin.hapus-user', $user->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin hapus user {{ $user->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action btn-delete">🗑️</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #9ca3af;">Belum ada user terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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