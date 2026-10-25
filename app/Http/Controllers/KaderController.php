<?php

namespace App\Http\Controllers;

use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanBumil; // Tambahan import untuk Bumil
use App\Models\LaporanPosyandu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KaderController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $posyanduId = $user->posyandu_id;

        // Jika kader tidak terhubung ke posyandu
        if (!$posyanduId) {
            return view('kader.dashboard', [
                'totalBalita'   => 0,
                'totalIbuHamil' => 0,
                'giziBaik'      => 0,
                'giziKurang'    => 0,
                'giziLebih'     => 0,
                'giziBuruk'     => 0,
                'bumilNormal'   => 0,
                'bumilKEK'      => 0,
                'bumilAnemia'   => 0,
            ]);
        }

        // 1. Total Data (Menghitung SEMUA yang terdaftar, baik sudah divalidasi atau belum)
        $totalBalita   = Balita::where('posyandu_id', $posyanduId)->count();
        $totalIbuHamil = IbuHamil::where('posyandu_id', $posyanduId)->count();

                // ==========================================
        // 2. Statistik Balita (HANYA YANG SUDAH DISETUJUI BIDAN)
        // ==========================================
        $baseBalitaQuery = PemeriksaanBalita::where('status_review', 'disetujui')
            ->whereHas('balita', function ($q) use ($posyanduId) {
                $q->where('posyandu_id', $posyanduId);
            });

        // ------ BB/U ------
        $giziBaik   = (clone $baseBalitaQuery)->where('status_bb_u', 'Berat Badan Normal')->count();
        $giziKurang = (clone $baseBalitaQuery)->where('status_bb_u', 'Berat Badan Kurang')->count();
        $giziLebih  = (clone $baseBalitaQuery)->where('status_bb_u', 'Risiko Berat Badan Lebih')->count();
        $giziBuruk  = (clone $baseBalitaQuery)->where('status_bb_u', 'Berat Badan Sangat Kurang')->count();

        // ------ TB/U ------
        $tbNormal      = (clone $baseBalitaQuery)->where('status_tb_u', 'Normal')->count();
        $tbPendek      = (clone $baseBalitaQuery)->where('status_tb_u', 'Pendek')->count();
        $tbSangatPendek= (clone $baseBalitaQuery)->where('status_tb_u', 'Sangat Pendek')->count();
        $tbTinggi      = (clone $baseBalitaQuery)->where('status_tb_u', 'Tinggi')->count();

        // ------ BB/TB ------
        $bbtbNormal    = (clone $baseBalitaQuery)->where('status_bb_tb', 'Gizi Baik')->count();
        $bbtbKurang    = (clone $baseBalitaQuery)->where('status_bb_tb', 'Gizi Kurang')->count();
        $bbtbBuruk     = (clone $baseBalitaQuery)->where('status_bb_tb', 'Gizi Buruk')->count();
        $bbtbLebih     = (clone $baseBalitaQuery)->where('status_bb_tb', 'Gizi Lebih')->count();
        $bbtbObesitas  = (clone $baseBalitaQuery)->where('status_bb_tb', 'Obesitas')->count();

        // ==========================================
        // 3. Statistik Ibu Hamil
        // ==========================================
        $baseBumilQuery = PemeriksaanBumil::where('status_review', 'disetujui')
            ->whereHas('ibuHamil', function ($q) use ($posyanduId) {
                $q->where('posyandu_id', $posyanduId);
            });

        $bumilNormal = (clone $baseBumilQuery)
            ->where('status_kek', 'Tidak Risiko')
            ->where('status_anemia', 'Tidak Anemia')
            ->count();
            
        $bumilKEK    = (clone $baseBumilQuery)->where('status_kek', 'Risiko KEK')->count();
        $bumilAnemia = (clone $baseBumilQuery)->where('status_anemia', 'Anemia')->count();

        return view('kader.dashboard', compact(
            'totalBalita', 'totalIbuHamil',
            'giziBaik', 'giziKurang', 'giziLebih', 'giziBuruk',
            'tbNormal', 'tbPendek', 'tbSangatPendek', 'tbTinggi',
            'bbtbNormal', 'bbtbKurang', 'bbtbBuruk', 'bbtbLebih', 'bbtbObesitas',
            'bumilNormal', 'bumilKEK', 'bumilAnemia'
        ));
    }

    /**
     * Laporan Posyandu (Kader hanya lihat posyandu sendiri)
     */
    public function laporanPosyandu(Request $request)
    {
        $user = Auth::user();
        if (!$user->posyandu_id) {
            return redirect()->route('kader.dashboard')
                ->with('error', 'Akun Anda belum terhubung ke Posyandu.');
        }

        $bulan = $request->query('bulan', now()->month);
        $tahun = $request->query('tahun', now()->year);

        $query = LaporanPosyandu::with(['posyandu', 'validator'])
            ->where('posyandu_id', $user->posyandu_id)
            ->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('identitas->nama', 'like', "%{$search}%")
                ->orWhere('identitas->nik', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $laporans = $query->orderBy('kategori')->orderBy('id')->paginate(20)->withQueryString();

        $tahunList = LaporanPosyandu::where('posyandu_id', $user->posyandu_id)
            ->select('periode_tahun')->distinct()->orderBy('periode_tahun', 'desc')
            ->pluck('periode_tahun')->toArray();

        if (!in_array(now()->year, $tahunList)) {
            $tahunList[] = now()->year;
        }
        sort($tahunList);

        // TAMBAHKAN INI:
        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        // PASTIKAN $bulanList DIMASUKKAN KE COMPACT:
        return view('laporan-posyandu.index', compact('laporans', 'bulan', 'tahun', 'tahunList', 'bulanList'));
    }
}