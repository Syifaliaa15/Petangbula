<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Posyandu;
use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanBumil;
use App\Models\PemeriksaanDarurat;
use App\Models\LaporanPosyandu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // ==========================================
    // DASHBOARD ADMIN (Statistik Detail)
    // ==========================================
    public function dashboard()
    {
        // ----- TOTAL UMUM -----
        $totalBalita   = Balita::count();
        $totalIbuHamil = IbuHamil::count();
        $totalPosyandu = Posyandu::count();

        // ==========================================
        // STATISTIK BALITA — Berdasarkan Pemeriksaan Terakhir
        // ==========================================
        // Ambil ID balita yang punya pemeriksaan
        $balitaIds = Balita::pluck('id');

        // Total pemeriksaan balita (semua record, bukan per-balita)
        $totalPemeriksaanBalita = PemeriksaanBalita::count();

        // ------ BB/U ------
        $bbuStats = PemeriksaanBalita::selectRaw('status_bb_u, COUNT(*) as total')
            ->whereNotNull('status_bb_u')
            ->groupBy('status_bb_u')
            ->pluck('total', 'status_bb_u')
            ->toArray();

        $giziBaik   = $bbuStats['Gizi Baik']   ?? 0;
        $giziKurang = $bbuStats['Gizi Kurang'] ?? 0;
        $giziLebih  = $bbuStats['Gizi Lebih']  ?? 0;
        $giziBuruk  = $bbuStats['Gizi Buruk']  ?? 0;

        // ------ TB/U ------
        $tbuStats = PemeriksaanBalita::selectRaw('status_tb_u, COUNT(*) as total')
            ->whereNotNull('status_tb_u')
            ->groupBy('status_tb_u')
            ->pluck('total', 'status_tb_u')
            ->toArray();

        $tbNormal      = $tbuStats['Normal']        ?? 0;
        $tbPendek      = $tbuStats['Pendek']        ?? 0;
        $tbSangatPendek= $tbuStats['Sangat Pendek'] ?? 0;
        $tbTinggi      = $tbuStats['Tinggi']        ?? 0;

        // ------ BB/TB ------
        $bbtbStats = PemeriksaanBalita::selectRaw('status_bb_tb, COUNT(*) as total')
            ->whereNotNull('status_bb_tb')
            ->groupBy('status_bb_tb')
            ->pluck('total', 'status_bb_tb')
            ->toArray();

        $bbtbNormal       = $bbtbStats['Normal']       ?? 0;
        $bbtbKurus        = $bbtbStats['Kurus']        ?? 0;
        $bbtbSangatKurus  = $bbtbStats['Sangat Kurus'] ?? 0;
        $bbtbGemuk        = $bbtbStats['Gemuk']        ?? 0;

        // ------ Stunting / Wasting ------
        $stunting  = $tbPendek + $tbSangatPendek;
        $wasting   = $bbtbKurus + $bbtbSangatKurus;

        // ==========================================
        // STATISTIK IBU HAMIL
        // ==========================================
        $totalPemeriksaanBumil = PemeriksaanBumil::count();

        // ------ KEK ------
        $kekStats = PemeriksaanBumil::selectRaw('status_kek, COUNT(*) as total')
            ->whereNotNull('status_kek')
            ->groupBy('status_kek')
            ->pluck('total', 'status_kek')
            ->toArray();

        $kekCount         = $kekStats['Risiko KEK']   ?? 0;
        $tidakKekCount    = $kekStats['Tidak Risiko'] ?? 0;

        // ------ Anemia ------
        $anemiaStats = PemeriksaanBumil::selectRaw('status_anemia, COUNT(*) as total')
            ->whereNotNull('status_anemia')
            ->groupBy('status_anemia')
            ->pluck('total', 'status_anemia')
            ->toArray();

        $anemiaCount      = $anemiaStats['Anemia']       ?? 0;
        $tidakAnemiaCount = $anemiaStats['Tidak Anemia'] ?? 0;

        // ------ Tekanan Darah ------
        $tdStats = PemeriksaanBumil::selectRaw('status_td, COUNT(*) as total')
            ->whereNotNull('status_td')
            ->groupBy('status_td')
            ->pluck('total', 'status_td')
            ->toArray();

        $tdNormal      = $tdStats['Normal']     ?? 0;
        $tdHipertensi  = $tdStats['Hipertensi'] ?? 0;
        $tdHipotensi   = $tdStats['Hipotensi']  ?? 0;

        // Bumil dengan komplikasi (KEK atau Anemia)
        $bumilBerkomplikasi = PemeriksaanBumil::where(function ($q) {
            $q->where('status_kek', 'Risiko KEK')
              ->orWhere('status_anemia', 'Anemia');
        })->distinct('ibu_hamil_id')->count('ibu_hamil_id');

        // ----- Statistik untuk ringkasan umum (dipakai chart lama) -----
        $bumilNormal = $tidakKekCount;
        $bumilKEK    = $kekCount;
        $bumilAnemia = $anemiaCount;

        // ==========================================
        // STATISTIK VALIDASI & LAPORAN
        // ==========================================
        $totalPendingBalita = PemeriksaanBalita::where('status_review', 'pending')->count();
        $totalPendingBumil  = PemeriksaanBumil::where('status_review', 'pending')->count();
        $totalPending       = $totalPendingBalita + $totalPendingBumil;

        $totalLaporan       = LaporanPosyandu::count();
        $laporanBulanIni    = LaporanPosyandu::where('periode_bulan', now()->month)
                                ->where('periode_tahun', now()->year)->count();

        // ==========================================
        // STATISTIK PEMENTAUAN DARURAT
        // ==========================================
        $totalDarurat = PemeriksaanDarurat::count();
        $daruratBulanIni = PemeriksaanDarurat::whereMonth('tanggal', now()->month)
                                ->whereYear('tanggal', now()->year)->count();

        // ==========================================
        // STATISTIK USER & POSYANDU
        // ==========================================
        $totalKader = User::where('role', 'kader')->count();
        $totalBidan = User::where('role', 'bidan')->count();
        $totalUserAktif = User::where('is_active', true)->count();

        return view('admin.dashboard', compact(
            // Umum
            'totalBalita', 'totalIbuHamil', 'totalPosyandu',
            'totalPemeriksaanBalita', 'totalPemeriksaanBumil',
            // BB/U
            'giziBaik', 'giziKurang', 'giziLebih', 'giziBuruk',
            // TB/U
            'tbNormal', 'tbPendek', 'tbSangatPendek', 'tbTinggi',
            // BB/TB
            'bbtbNormal', 'bbtbKurus', 'bbtbSangatKurus', 'bbtbGemuk',
            // Komplikasi
            'stunting', 'wasting',
            // Bumil
            'kekCount', 'tidakKekCount', 'anemiaCount', 'tidakAnemiaCount',
            'tdNormal', 'tdHipertensi', 'tdHipotensi', 'bumilBerkomplikasi',
            'bumilNormal', 'bumilKEK', 'bumilAnemia',
            // Validasi & laporan
            'totalPending', 'totalPendingBalita', 'totalPendingBumil',
            'totalLaporan', 'laporanBulanIni',
            // Darurat
            'totalDarurat', 'daruratBulanIni',
            // User
            'totalKader', 'totalBidan', 'totalUserAktif'
        ));
    }

    // ==========================================
    // KELOLA AKUN
    // ==========================================
    public function kelolaAkun(Request $request)
    {
        $query = User::with('posyandu')->where('role', '!=', 'admin');

        // SEARCH: nama / username
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // FILTER: Role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // FILTER: Status Aktif
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        // FILTER: Posyandu
        if ($request->filled('posyandu_id')) {
            $query->where('posyandu_id', $request->posyandu_id);
        }

        $users     = $query->orderBy('name', 'asc')->paginate(15)->withQueryString();
        $posyandus = Posyandu::orderBy('rw')->get();

        return view('admin.kelola-akun', compact('users', 'posyandus'));
    }

    public function tambahUser(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'username'    => 'required|string|unique:users,username|max:50',
            'password'    => ['required', 'string', new \App\Rules\PasswordRules(), new \App\Rules\UniquePassword()],
            'role'        => 'required|in:bidan,kader',
            'posyandu_id' => 'required_if:role,kader|nullable|exists:posyandu,id',
        ]);

        User::create([
            'name'        => $request->name,
            'username'    => $request->username,
            'password'    => Hash::make($request->password),
            'role'        => $request->role,
            'posyandu_id' => $request->role === 'kader' ? $request->posyandu_id : null,
            'is_active'   => true,
        ]);

        return redirect()->route('admin.kelola-akun')->with('success', 'User berhasil ditambahkan.');
    }

    public function editUser($id)
    {
        $user      = User::findOrFail($id);
        $posyandus = Posyandu::all();
        return view('admin.edit-user', compact('user', 'posyandus'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'username'    => 'required|string|unique:users,username,' . $user->id . '|max:50',
            'password'    => ['nullable', 'string', new \App\Rules\PasswordRules(), new \App\Rules\UniquePassword($user->id)],
            'role'        => 'required|in:bidan,kader',
            'posyandu_id' => 'required_if:role,kader|nullable|exists:posyandu,id',
            'is_active'   => 'required|boolean',
        ]);

        $data = [
            'name'        => $request->name,
            'username'    => $request->username,
            'role'        => $request->role,
            'posyandu_id' => $request->role === 'kader' ? $request->posyandu_id : null,
            'is_active'   => $request->is_active,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.kelola-akun')->with('success', 'User berhasil diperbarui.');
    }

    public function hapusUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.kelola-akun')->with('success', 'User berhasil dihapus.');
    }

    public function toggleStatusUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri!');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "User berhasil {$status}!");
    }

    // ==========================================
    // LAPORAN POSYANDU
    // ==========================================
    public function laporanPosyandu(Request $request)
    {
        $bulan      = $request->query('bulan', now()->month);
        $tahun      = $request->query('tahun', now()->year);
        $posyanduId = $request->query('posyandu_id');

        $query = LaporanPosyandu::with(['posyandu', 'validator'])
            ->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun);

        if ($posyanduId) {
            $query->where('posyandu_id', $posyanduId);
        }

        $laporans  = $query->orderBy('kategori')->orderBy('id')->get();
        $posyandus = Posyandu::orderBy('rw')->get();

        $tahunList = LaporanPosyandu::select('periode_tahun')
            ->distinct()
            ->orderBy('periode_tahun', 'desc')
            ->pluck('periode_tahun')
            ->toArray();

        if (!in_array(now()->year, $tahunList)) {
            $tahunList[] = now()->year;
        }
        sort($tahunList);

        return view('laporan-posyandu.index', compact(
            'laporans', 'posyandus', 'bulan', 'tahun', 'tahunList', 'posyanduId'
        ));
    }

    // ==========================================
    // LAPORAN DARURAT
    // ==========================================
    public function laporanDarurat()
    {
        $laporans = PemeriksaanDarurat::with('bidan')
            ->latest('tanggal')
            ->get();

        return view('admin.laporan-darurat', compact('laporans'));
    }
}