@extends('layouts.dashboard')

@section('title', 'Kelola Akun Pengguna')
@section('page-title', 'Kelola Akun Pengguna')
@section('page-subtitle', 'Manajemen user sistem Petangbula')

@section('content')
    <!-- FORM TAMBAH USER -->
    <div class="bg-white border border-kia-100 rounded-2xl shadow-sm mb-6 overflow-hidden" x-data="{ open: false }">
        <button @click="open = !open" class="w-full flex items-center justify-between p-5 bg-kia-50/50 hover:bg-kia-50 transition-colors">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-kia-500 text-white flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <h2 class="text-lg font-bold text-kia-900">Tambah Pengguna Baru</h2>
            </div>
            <svg :class="open ? 'rotate-180' : ''" class="w-5 h-5 text-kia-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>

        <div x-show="open" x-collapse class="p-6 border-t border-kia-100">
            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded-md mb-4 text-sm">
                    <ul class="list-disc pl-4">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.tambah-user') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-bold text-kia-900 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none" placeholder="Contoh: Kader Rina Wati">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-kia-900 mb-1.5">Username <span class="text-red-500">*</span></label>
                        <input type="text" name="username" value="{{ old('username') }}" required class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none" placeholder="Contoh: kader20">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-kia-900 mb-1.5">Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none" placeholder="Minimal 8 karakter, kombinasi huruf & angka">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-kia-900 mb-1.5">Role Jabatan <span class="text-red-500">*</span></label>
                        <select name="role" id="role-select" required class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none" onchange="togglePosyanduSelect()">
                            <option value="bidan" {{ old('role') == 'bidan' ? 'selected' : '' }}>Bidan</option>
                            <option value="kader" {{ old('role') == 'kader' ? 'selected' : '' }}>Kader</option>
                        </select>
                    </div>
                    <div id="posyandu-container" class="md:col-span-2" style="display: {{ old('role') == 'kader' ? 'block' : 'none' }};">
                        <label class="block text-sm font-bold text-kia-900 mb-1.5">Penempatan Posyandu <span class="text-red-500">*</span></label>
                        <select name="posyandu_id" class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none">
                            <option value="">-- Pilih Posyandu --</option>
                            @foreach($posyandus as $p)
                                <option value="{{ $p->id }}" {{ old('posyandu_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_posyandu }} (RW {{ $p->rw }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-kia-500 to-kia-600 text-white font-bold rounded-xl shadow-sm hover:shadow-md transition-all">
                        💾 Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- FILTER & SEARCH -->
    <div class="bg-white border border-kia-100 rounded-2xl p-5 shadow-sm mb-6 w-full">
        <form method="GET" action="{{ route('admin.kelola-akun') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-kia-800 mb-1.5 uppercase tracking-wide">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none" placeholder="Nama / Username...">
            </div>
            <div>
                <label class="block text-xs font-bold text-kia-800 mb-1.5 uppercase tracking-wide">Role</label>
                <select name="role" class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none" onchange="this.form.submit()">
                    <option value="">Semua Role</option>
                    <option value="bidan" {{ request('role') == 'bidan' ? 'selected' : '' }}>Bidan</option>
                    <option value="kader" {{ request('role') == 'kader' ? 'selected' : '' }}>Kader</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-kia-800 mb-1.5 uppercase tracking-wide">Status</label>
                <select name="status" class="w-full px-4 py-2.5 text-sm border border-kia-200 rounded-xl bg-kia-50/50 focus:bg-white focus:ring-2 focus:ring-kia-500 outline-none" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>✅ Aktif</option>
                    <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>❌ Nonaktif</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="w-full px-4 py-2.5 bg-kia-500 text-white font-bold rounded-xl hover:bg-kia-600 shadow-sm transition-colors">Cari</button>
                @if(request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('admin.kelola-akun') }}" class="px-4 py-2.5 bg-white border border-kia-200 text-kia-700 font-bold rounded-xl hover:bg-kia-50 text-center transition-colors">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- ========================================== -->
    <!-- TAMPILAN DESKTOP (TABEL) -->
    <!-- ========================================== -->
    <div class="hidden md:block bg-white border border-kia-100 rounded-2xl shadow-sm w-full overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="w-full min-w-[800px] text-left border-collapse">
                <thead class="bg-kia-50/80 border-b border-kia-100">
                    <tr>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase text-center w-16">No</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Nama & Username</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase text-center">Role</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase">Posyandu</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase text-center">Status</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-kia-800 uppercase text-center w-48">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-kia-50">
                    @forelse($users as $index => $user)
                    <tr class="hover:bg-kia-50/50 transition-colors group">
                        <td class="px-6 py-4 text-center text-sm font-medium text-gray-500">{{ $users->firstItem() + $index }}</td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-kia-900">{{ $user->name }}</div>
                            <div class="text-xs text-gray-500">@ {{ $user->username }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold {{ $user->role === 'bidan' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                {{ strtoupper($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $user->posyandu->nama_posyandu ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($user->is_active)
                                <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700">✅ Aktif</span>
                            @else
                                <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold bg-red-100 text-red-700">❌ Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-1.5 opacity-80 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('admin.edit-user', $user->id) }}" class="p-2 text-amber-600 bg-amber-50 rounded-lg hover:bg-amber-500 hover:text-white transition-all" title="Edit">✏️</a>
                                
                                <form action="{{ route('admin.toggle-status', $user->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 rounded-lg transition-all {{ $user->is_active ? 'bg-gray-100 text-gray-600 hover:bg-gray-500 hover:text-white' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white' }}" title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        {{ $user->is_active ? '🔒' : '🔓' }}
                                    </button>
                                </form>

                                <form action="{{ route('admin.hapus-user', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus user {{ $user->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-500 hover:text-white transition-all" title="Hapus">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-12 text-gray-400">Belum ada user terdaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-kia-100 bg-white">{{ $users->links() }}</div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- TAMPILAN MOBILE (CARD) -->
    <!-- ========================================== -->
    <div class="block md:hidden space-y-4 w-full">
        @forelse($users as $user)
            <div class="bg-white border border-kia-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="bg-kia-50/50 px-4 py-3 border-b border-kia-100 flex justify-between items-center">
                    <div>
                        <h3 class="font-extrabold text-kia-900 text-lg">{{ $user->name }}</h3>
                        <p class="text-xs text-gray-500 font-medium">@ {{ $user->username }}</p>
                    </div>
                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold {{ $user->role === 'bidan' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                        {{ strtoupper($user->role) }}
                    </span>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-3 text-sm mb-4">
                        <div>
                            <span class="text-gray-400 text-[10px] font-bold block mb-0.5">POSYANDU</span>
                            <span class="font-medium text-gray-800">{{ $user->posyandu->nama_posyandu ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 text-[10px] font-bold block mb-0.5">STATUS</span>
                            @if($user->is_active)
                                <span class="text-emerald-600 font-bold">✅ Aktif</span>
                            @else
                                <span class="text-red-600 font-bold">❌ Nonaktif</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex gap-2 border-t border-gray-100 pt-3">
                        <a href="{{ route('admin.edit-user', $user->id) }}" class="flex-1 py-2 text-center text-xs font-bold text-amber-700 bg-amber-100 rounded-lg hover:bg-amber-200">Edit</a>
                        
                        <form action="{{ route('admin.toggle-status', $user->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full py-2 text-xs font-bold rounded-lg {{ $user->is_active ? 'bg-gray-200 text-gray-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>

                        <form action="{{ route('admin.hapus-user', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus user {{ $user->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="py-2 px-4 text-xs font-bold text-red-700 bg-red-100 rounded-lg hover:bg-red-200">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-gray-500 bg-white rounded-2xl border border-kia-100 shadow-sm">Belum ada user terdaftar.</div>
        @endforelse
        
        <div class="mt-4">{{ $users->links() }}</div>
    </div>
@endsection

@push('scripts')
<script src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js" defer></script>
<script>
    function togglePosyanduSelect() {
        const role = document.getElementById('role-select').value;
        document.getElementById('posyandu-container').style.display = (role === 'kader') ? 'block' : 'none';
    }
</script>
@endpush