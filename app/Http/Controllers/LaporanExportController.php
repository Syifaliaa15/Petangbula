<?php

namespace App\Http\Controllers;

use App\Models\LaporanPosyandu;
use App\Exports\LaporanPosyanduExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LaporanExportController extends Controller
{
    private const BULAN_LIST = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function pdf(Request $request)
    {
        $data = $this->getData($request);
        if ($data instanceof \Illuminate\Http\RedirectResponse) return $data;

        try {
            $pdf = Pdf::loadView('laporan-posyandu.pdf', $data)
                ->setPaper('a4', 'landscape')
                ->setOptions(['isRemoteEnabled' => false, 'isHtml5ParserEnabled' => true, 'defaultFont' => 'DejaVu Sans']);

            return $pdf->download($this->buildFilename($data, 'pdf'));
        } catch (\Throwable $e) {
            Log::error('Export PDF Gagal: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    public function excel(Request $request)
    {
        $data = $this->getData($request);
        if ($data instanceof \Illuminate\Http\RedirectResponse) return $data;

        try {
            return Excel::download(new LaporanPosyanduExport($data['laporans'], $data), $this->buildFilename($data, 'xlsx'));
        } catch (\Throwable $e) {
            Log::error('Export Excel Gagal: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat Excel: ' . $e->getMessage());
        }
    }

    private function getData(Request $request)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login')->with('error', 'Sesi berakhir.');

        $bulan = (int) $request->query('bulan', now()->month);
        $tahun = (int) $request->query('tahun', now()->year);
        $tipe_periode = $request->query('tipe_periode', 'bulanan');
        $kategori = $request->query('kategori');
        $status_gizi = $request->query('status_gizi');
        $posyanduId = $request->query('posyandu_id');

        $query = LaporanPosyandu::with(['posyandu', 'validator']);

        // Scope Role
        if ($user->isKader()) {
            if (!$user->posyandu_id) return back()->with('error', 'Akun belum terhubung ke Posyandu.');
            $query->where('posyandu_id', $user->posyandu_id);
        } elseif ($posyanduId) {
            $query->where('posyandu_id', $posyanduId);
        }

        // Filter Periode
        if ($tipe_periode === 'tahunan') {
            $query->where('periode_tahun', $tahun);
        } else {
            $query->where('periode_bulan', $bulan)->where('periode_tahun', $tahun);
        }

        // Filter Kategori
        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        // Filter Status Gizi Negatif
        if ($status_gizi === 'negatif') {
            $query->where(function($q) {
                $q->where('status_gizi->bb_u', 'like', '%Kurang%')
                  ->orWhere('status_gizi->tb_u', 'like', '%Pendek%')
                  ->orWhere('status_gizi->bb_tb', 'like', '%Kurang%')
                  ->orWhere('status_gizi->bb_tb', 'like', '%Buruk%')
                  ->orWhere('status_gizi->kek', 'Risiko KEK')
                  ->orWhere('status_gizi->anemia', 'Anemia')
                  ->orWhere('status_gizi->td', 'Hipertensi');
            });
        }

        $laporans = $query->orderBy('kategori')->orderBy('id')->get();

        $posyanduLabel = 'Semua Posyandu';
        if ($user->isKader() && $user->posyandu) {
            $posyanduLabel = $user->posyandu->nama_posyandu;
        } elseif ($posyanduId) {
            $posyanduLabel = \App\Models\Posyandu::find($posyanduId)?->nama_posyandu ?? 'Semua Posyandu';
        }

        // Label Periode
        $periodeLabel = $tipe_periode === 'tahunan' 
            ? 'Tahun ' . $tahun 
            : self::BULAN_LIST[$bulan] . ' ' . $tahun;

        // Label Filter Tambahan
        $filterLabel = [];
        if ($kategori) $filterLabel[] = 'Kategori: ' . ucfirst($kategori);
        if ($status_gizi === 'negatif') $filterLabel[] = 'Hanya Gizi Bermasalah';
        $filterInfo = count($filterLabel) > 0 ? implode(' | ', $filterLabel) : 'Semua Data';

        return [
            'laporans'      => $laporans,
            'bulan'         => $bulan,
            'tahun'         => $tahun,
            'tipe_periode'  => $tipe_periode,
            'periodeLabel'  => $periodeLabel,
            'filterInfo'    => $filterInfo,
            'posyanduLabel' => $posyanduLabel,
            'tanggalCetak'  => now()->translatedFormat('d F Y H:i'),
            'dicetakOleh'   => $user->name . ' (' . ucfirst($user->role) . ')',
        ];
    }

    private function buildFilename(array $data, string $ext): string
    {
        $periode = str_replace(' ', '-', $data['periodeLabel']);
        $pos   = preg_replace('/[^A-Za-z0-9]+/', '-', $data['posyanduLabel']);
        $filter = $data['filterInfo'] !== 'Semua Data' ? '-Filter' : '';
        return "laporan-{$periode}-{$pos}{$filter}.{$ext}";
    }
}