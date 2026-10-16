<?php

namespace App\Http\Controllers;

use App\Models\JadwalPosyandu;
use App\Models\Posyandu;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua posyandu untuk filter RW
        $posyandus = Posyandu::orderBy('rw', 'asc')->get();

        // Filter jadwal berdasarkan RW
        $selectedRw = $request->query('rw');
        $jadwals = collect();

        if ($selectedRw) {
            $posyanduIds = $posyandus->where('rw', $selectedRw)->pluck('id');
            $jadwals = JadwalPosyandu::with(['posyandu', 'kader'])
                ->whereIn('posyandu_id', $posyanduIds)
                ->where('tanggal', '>=', now()->subDays(30)) // tampilkan jadwal 30 hari terakhir & yang akan datang
                ->orderBy('tanggal', 'asc')
                ->get();
        } else {
            // Kalau tidak ada filter, tampilkan semua jadwal mendatang (maks 10)
            $jadwals = JadwalPosyandu::with(['posyandu', 'kader'])
                ->where('tanggal', '>=', now())
                ->orderBy('tanggal', 'asc')
                ->limit(10)
                ->get();
        }

        return view('landing', compact('posyandus', 'jadwals', 'selectedRw'));
    }
}