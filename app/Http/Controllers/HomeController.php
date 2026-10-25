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
        $jadwals = collect(); // Default kosong

        // HANYA ambil data jika user sudah memilih RW
        if ($selectedRw) {
            $posyanduIds = $posyandus->where('rw', $selectedRw)->pluck('id');
            
            $jadwals = JadwalPosyandu::with(['posyandu', 'kader'])
                ->whereIn('posyandu_id', $posyanduIds)
                // HANYA TAMPILKAN JADWAL HARI INI DAN MENDATANG
                ->where('tanggal', '>=', now()->startOfDay()) 
                ->orderBy('tanggal', 'asc')
                ->orderBy('waktu_mulai', 'asc')
                ->get();
        }

        return view('landing', compact('posyandus', 'jadwals', 'selectedRw'));
    }
}