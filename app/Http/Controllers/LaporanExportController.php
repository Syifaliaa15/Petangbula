<?php

namespace App\Http\Controllers;

use App\Models\LaporanPosyandu;
use App\Exports\LaporanPosyanduExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanExportController extends Controller
{
    /**
     * Export PDF Laporan Posyandu (Landscape)
     */
    public function pdf(Request $request)
    {
        $data = $this->getData($request);

        $pdf = Pdf::loadView('laporan-posyandu.pdf', $data)
            ->setPaper('a4', 'landscape');

        $filename = 'laporan-posyandu-' . $data['bulanLabel'] . '-' . $data['tahun'] . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Export Excel Laporan Posyandu
     */
    public function excel(Request $request)
    {
        $data = $this->getData($request);

        $filename = 'laporan-posyandu-' . $data['bulanLabel'] . '-' . $data['tahun'] . '.xlsx';
        return Excel::download(new LaporanPosyanduExport($data['laporans']), $filename);
    }

    /**
     * Ambil data berdasarkan filter + role
     */
    private function getData(Request $request): array
    {
        $user  = Auth::user();
        $bulan = (int) $request->query('bulan', now()->month);
        $tahun = (int) $request->query('tahun', now()->year);
        $posyanduId = $request->query('posyandu_id');

        $query = LaporanPosyandu::with(['posyandu', 'validator'])
            ->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun);

        // Filter role
        if ($user->isKader()) {
            $query->where('posyandu_id', $user->posyandu_id);
        } elseif ($posyanduId) {
            $query->where('posyandu_id', $posyanduId);
        }

        $laporans = $query->orderBy('kategori')->orderBy('id')->get();

        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $posyanduLabel = 'Semua Posyandu';
        if ($user->isKader() && $user->posyandu) {
            $posyanduLabel = $user->posyandu->nama_posyandu;
        } elseif ($posyanduId) {
            $p = \App\Models\Posyandu::find($posyanduId);
            $posyanduLabel = $p ? $p->nama_posyandu : 'Semua Posyandu';
        }

        return [
            'laporans'        => $laporans,
            'bulan'           => $bulan,
            'tahun'           => $tahun,
            'bulanLabel'      => $bulanList[$bulan] ?? '-',
            'bulanList'       => $bulanList,
            'posyanduLabel'   => $posyanduLabel,
            'tanggalCetak'    => now()->translatedFormat('d F Y H:i'),
        ];
    }
}