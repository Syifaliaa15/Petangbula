<?php

namespace App\Http\Controllers;

use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\PemeriksaanBalita;
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
            ]);
        }

        $totalBalita   = Balita::where('posyandu_id', $posyanduId)->count();
        $totalIbuHamil = IbuHamil::where('posyandu_id', $posyanduId)->count();

        $giziBaik = PemeriksaanBalita::whereHas('balita', function ($q) use ($posyanduId) {
            $q->where('posyandu_id', $posyanduId);
        })->where('status_bb_u', 'Gizi Baik')->count();

        $giziKurang = PemeriksaanBalita::whereHas('balita', function ($q) use ($posyanduId) {
            $q->where('posyandu_id', $posyanduId);
        })->where('status_bb_u', 'Gizi Kurang')->count();

        $giziLebih = PemeriksaanBalita::whereHas('balita', function ($q) use ($posyanduId) {
            $q->where('posyandu_id', $posyanduId);
        })->where('status_bb_u', 'Gizi Lebih')->count();

        $giziBuruk = PemeriksaanBalita::whereHas('balita', function ($q) use ($posyanduId) {
            $q->where('posyandu_id', $posyanduId);
        })->where('status_bb_u', 'Gizi Buruk')->count();

        return view('kader.dashboard', compact(
            'totalBalita', 'totalIbuHamil',
            'giziBaik', 'giziKurang', 'giziLebih', 'giziBuruk'
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

        // SEARCH
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('identitas->nama', 'like', "%{$search}%")
                ->orWhere('identitas->nik', 'like', "%{$search}%");
            });
        }

        // FILTER kategori
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

        return view('laporan-posyandu.index', compact('laporans', 'bulan', 'tahun', 'tahunList'));
    }
}