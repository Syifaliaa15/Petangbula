<?php

namespace App\Http\Controllers;

use App\Models\LaporanPosyandu;
use App\Models\Posyandu;
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

    /**
     * Export PDF Laporan Posyandu (Landscape A4)
     */
    public function pdf(Request $request)
    {
        $data = $this->getData($request);

        if ($data instanceof \Illuminate\Http\RedirectResponse) {
            return $data; // error redirect
        }

        try {
            $pdf = Pdf::loadView('laporan-posyandu.pdf', $data)
                ->setPaper('a4', 'landscape')
                ->setOptions([
                    'isRemoteEnabled'      => false,
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled'         => false,
                    'defaultFont'          => 'DejaVu Sans',
                ]);

            $filename = $this->buildFilename($data, 'pdf');
            return $pdf->download($filename);
        } catch (\Throwable $e) {
            Log::error('LaporanExportController@pdf gagal', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'bulan'   => $data['bulan'],
                'tahun'   => $data['tahun'],
            ]);

            return back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export Excel Laporan Posyandu
     */
    public function excel(Request $request)
    {
        $data = $this->getData($request);

        if ($data instanceof \Illuminate\Http\RedirectResponse) {
            return $data;
        }

        try {
            $filename = $this->buildFilename($data, 'xlsx');

            return Excel::download(
                new LaporanPosyanduExport($data['laporans'], $data),
                $filename
            );
        } catch (\Throwable $e) {
            Log::error('LaporanExportController@excel gagal', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'bulan'   => $data['bulan'],
                'tahun'   => $data['tahun'],
            ]);

            return back()->with('error', 'Gagal membuat Excel: ' . $e->getMessage());
        }
    }

    /**
     * Kumpulkan data + validasi + scope role.
     * @return array|\Illuminate\Http\RedirectResponse
     */
    private function getData(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Sesi Anda berakhir.');
        }

        $bulan = (int) $request->query('bulan', now()->month);
        $tahun = (int) $request->query('tahun', now()->year);

        if ($bulan < 1 || $bulan > 12) {
            return back()->with('error', 'Bulan tidak valid (1–12).');
        }
        if ($tahun < 2000 || $tahun > 2100) {
            return back()->with('error', 'Tahun tidak valid (2000–2100).');
        }

        $posyanduId = $request->query('posyandu_id');
        if ($posyanduId !== null && $posyanduId !== '') {
            $posyanduId = (int) $posyanduId;
            if (!Posyandu::where('id', $posyanduId)->exists()) {
                return back()->with('error', 'Posyandu tidak ditemukan.');
            }
        } else {
            $posyanduId = null;
        }

        $query = LaporanPosyandu::with(['posyandu', 'validator'])
            ->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun);

        // SCOPE KEAMANAN: kader hanya boleh export posyandu sendiri
        if ($user->isKader()) {
            if (!$user->posyandu_id) {
                return back()->with('error', 'Akun Anda belum terhubung ke Posyandu.');
            }
            $query->where('posyandu_id', $user->posyandu_id);
        } elseif ($posyanduId) {
            $query->where('posyandu_id', $posyanduId);
        }

        $laporans = $query->orderBy('kategori')->orderBy('id')->get();

        // Label posyandu
        $posyanduLabel = 'Semua Posyandu';
        if ($user->isKader() && $user->posyandu) {
            $posyanduLabel = $user->posyandu->nama_posyandu;
        } elseif ($posyanduId) {
            $posyanduLabel = Posyandu::find($posyanduId)?->nama_posyandu ?? 'Semua Posyandu';
        }

        return [
            'laporans'      => $laporans,
            'bulan'         => $bulan,
            'tahun'         => $tahun,
            'bulanLabel'    => self::BULAN_LIST[$bulan] ?? '-',
            'bulanList'     => self::BULAN_LIST,
            'posyanduLabel' => $posyanduLabel,
            'tanggalCetak'  => now()->translatedFormat('d F Y H:i'),
            'dicetakOleh'   => $user->name . ' (' . ucfirst($user->role) . ')',
        ];
    }

    private function buildFilename(array $data, string $ext): string
    {
        $bulan = preg_replace('/[^A-Za-z]/', '', $data['bulanLabel']);
        $pos   = preg_replace('/[^A-Za-z0-9]+/', '-', $data['posyanduLabel']);
        $pos   = trim($pos, '-');

        return "laporan-posyandu-{$bulan}-{$data['tahun']}-{$pos}.{$ext}";
    }
}