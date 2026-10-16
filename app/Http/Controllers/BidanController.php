<?php

namespace App\Http\Controllers;

use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanBumil;
use App\Models\LaporanPosyandu;
use App\Models\PemeriksaanDarurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BidanController extends Controller
{
    public function dashboard()
    {
        $totalBalita     = Balita::count();
        $totalIbuHamil   = IbuHamil::count();
        $totalPending    = PemeriksaanBalita::where('status_review', 'pending')->count()
                         + PemeriksaanBumil::where('status_review', 'pending')->count();

        return view('bidan.dashboard', compact('totalBalita', 'totalIbuHamil', 'totalPending'));
    }

    // ==========================================
    // VALIDASI BALITA
    // ==========================================

    public function validasiBalita()
    {
        $pemeriksaans = PemeriksaanBalita::with(['balita.posyandu', 'kader'])
            ->where('status_review', 'pending')
            ->latest()
            ->get();

        return view('bidan.validasi-balita', compact('pemeriksaans'));
    }

    public function prosesValidasiBalita(Request $request, $id)
    {
        $request->validate([
            'aksi'    => 'required|in:disetujui,perbaikan',
            'catatan' => 'nullable|string|max:500',
        ]);

        $pemeriksaan = PemeriksaanBalita::with('balita.posyandu')->findOrFail($id);

        $status = $request->aksi === 'disetujui' ? 'disetujui' : 'butuh_perbaikan';

        $pemeriksaan->update([
            'status_review' => $status,
            'catatan_bidan' => $request->catatan,
        ]);

        if ($status === 'disetujui') {
            $this->simpanLaporanBalita($pemeriksaan);
        }

        return back()->with('success', 'Validasi berhasil diproses.');
    }

    public function prosesValidasiBalitaBulk(Request $request)
    {
        $request->validate([
            'ids'     => 'required|array|min:1',
            'ids.*'   => 'exists:pemeriksaan_balita,id',
            'aksi'    => 'required|in:disetujui,perbaikan',
            'catatan' => 'nullable|string|max:500',
        ]);

        $status = $request->aksi === 'disetujui' ? 'disetujui' : 'butuh_perbaikan';

        $pemeriksaans = PemeriksaanBalita::with('balita.posyandu')
            ->whereIn('id', $request->ids)
            ->get();

        foreach ($pemeriksaans as $p) {
            $p->update([
                'status_review' => $status,
                'catatan_bidan' => $request->catatan,
            ]);

            if ($status === 'disetujui') {
                $this->simpanLaporanBalita($p);
            }
        }

        $count = count($request->ids);
        $msg = $status === 'disetujui'
            ? "{$count} data berhasil disetujui dan masuk laporan."
            : "{$count} data dikembalikan untuk perbaikan.";

        return back()->with('success', $msg);
    }

    private function simpanLaporanBalita(PemeriksaanBalita $pemeriksaan): void
    {
        $balita = $pemeriksaan->balita;
        if (!$balita || !$balita->posyandu_id) return;

        $existing = LaporanPosyandu::where('pemeriksaan_balita_id', $pemeriksaan->id)->first();
        if ($existing) return;

        $tanggalPeriksa = Carbon::parse($pemeriksaan->tanggal_periksa);

        LaporanPosyandu::create([
            'posyandu_id'             => $balita->posyandu_id,
            'pemeriksaan_balita_id'   => $pemeriksaan->id,
            'validated_by'            => Auth::id(),
            'kategori'                => 'balita',
            'periode_bulan'           => $tanggalPeriksa->month,
            'periode_tahun'           => $tanggalPeriksa->year,
            'identitas' => [
                'nama'          => $balita->nama,
                'nik'           => $balita->nik,
                'tanggal_lahir' => $balita->tanggal_lahir->format('Y-m-d'),
                'umur_bulan'    => $pemeriksaan->umur_bulan,
                'jenis_kelamin' => $balita->jenis_kelamin,
                'nama_ibu'      => $balita->nama_ibu,
                'nama_ayah'     => $balita->nama_ayah,
                'alamat'        => $balita->alamat,
                'rw'            => $balita->rw,
                'rt'            => $balita->rt,
            ],
            'data_pemeriksaan' => [
                'tanggal_periksa' => $pemeriksaan->tanggal_periksa->format('Y-m-d'),
                'berat_badan'     => $pemeriksaan->berat_badan,
                'tinggi_badan'    => $pemeriksaan->tinggi_badan,
                'panjang_badan'   => $pemeriksaan->panjang_badan,
                'lingkar_kepala'  => $pemeriksaan->lingkar_kepala,
                'vitamin_a'       => $pemeriksaan->vitamin_a,
                'keterangan'      => $pemeriksaan->keterangan,
            ],
            'status_gizi' => [
                'bb_u'  => $pemeriksaan->status_bb_u,
                'tb_u'  => $pemeriksaan->status_tb_u,
                'bb_tb' => $pemeriksaan->status_bb_tb,
            ],
            'imunisasi'         => $pemeriksaan->imunisasi ?? [],
            'tanggal_validasi'  => now(),
        ]);
    }

    // ==========================================
    // VALIDASI BUMIL
    // ==========================================

    public function validasiBumil()
    {
        $pemeriksaans = PemeriksaanBumil::with(['ibuHamil.posyandu', 'kader'])
            ->where('status_review', 'pending')
            ->latest()
            ->get();

        return view('bidan.validasi-bumil', compact('pemeriksaans'));
    }

    public function prosesValidasiBumil(Request $request, $id)
    {
        $request->validate([
            'aksi'    => 'required|in:disetujui,perbaikan',
            'catatan' => 'nullable|string|max:500',
        ]);

        $pemeriksaan = PemeriksaanBumil::with('ibuHamil.posyandu')->findOrFail($id);

        $status = $request->aksi === 'disetujui' ? 'disetujui' : 'butuh_perbaikan';

        $pemeriksaan->update([
            'status_review' => $status,
            'catatan_bidan' => $request->catatan,
        ]);

        if ($status === 'disetujui') {
            $this->simpanLaporanBumil($pemeriksaan);
        }

        return back()->with('success', 'Validasi berhasil diproses.');
    }

    public function prosesValidasiBumilBulk(Request $request)
    {
        $request->validate([
            'ids'     => 'required|array|min:1',
            'ids.*'   => 'exists:pemeriksaan_bumil,id',
            'aksi'    => 'required|in:disetujui,perbaikan',
            'catatan' => 'nullable|string|max:500',
        ]);

        $status = $request->aksi === 'disetujui' ? 'disetujui' : 'butuh_perbaikan';

        $pemeriksaans = PemeriksaanBumil::with('ibuHamil.posyandu')
            ->whereIn('id', $request->ids)
            ->get();

        foreach ($pemeriksaans as $p) {
            $p->update([
                'status_review' => $status,
                'catatan_bidan' => $request->catatan,
            ]);

            if ($status === 'disetujui') {
                $this->simpanLaporanBumil($p);
            }
        }

        $count = count($request->ids);
        $msg = $status === 'disetujui'
            ? "{$count} data berhasil disetujui dan masuk laporan."
            : "{$count} data dikembalikan untuk perbaikan.";

        return back()->with('success', $msg);
    }

    private function simpanLaporanBumil(PemeriksaanBumil $pemeriksaan): void
    {
        $bumil = $pemeriksaan->ibuHamil;
        if (!$bumil || !$bumil->posyandu_id) return;

        $existing = LaporanPosyandu::where('pemeriksaan_bumil_id', $pemeriksaan->id)->first();
        if ($existing) return;

        $tanggalPeriksa = Carbon::parse($pemeriksaan->tanggal_periksa);

        LaporanPosyandu::create([
            'posyandu_id'           => $bumil->posyandu_id,
            'pemeriksaan_bumil_id'  => $pemeriksaan->id,
            'validated_by'          => Auth::id(),
            'kategori'              => 'bumil',
            'periode_bulan'         => $tanggalPeriksa->month,
            'periode_tahun'         => $tanggalPeriksa->year,
            'identitas' => [
                'nama'                => $bumil->nama,
                'nik'                 => $bumil->nik,
                'umur'                => $bumil->umur,
                'hamil_ke'            => $bumil->hamil_ke,
                'klmp_dasawisma'      => $bumil->klmp_dasawisma,
                'tanggal_pendaftaran' => $bumil->tanggal_pendaftaran ? $bumil->tanggal_pendaftaran->format('Y-m-d') : null,
                'nama_suami'          => $bumil->nama_suami,
                'alamat'              => $bumil->alamat,
                'rw'                  => $bumil->rw,
                'rt'                  => $bumil->rt,
            ],
            'data_pemeriksaan' => [
                'tanggal_periksa'      => $pemeriksaan->tanggal_periksa->format('Y-m-d'),
                'minggu_ke'            => $pemeriksaan->minggu_ke,
                'bb'                   => $pemeriksaan->bb,
                'lila'                 => $pemeriksaan->lila,
                'hemoglobin'           => $pemeriksaan->hemoglobin,
                'tablet_tambah_darah'  => $pemeriksaan->tablet_tambah_darah,
                'pmt_pemulihan'        => $pemeriksaan->pmt_pemulihan,
                'tekanan_darah'        => $pemeriksaan->tekanan_darah,
            ],
            'status_gizi' => [
                'kek'    => $pemeriksaan->status_kek,
                'anemia' => $pemeriksaan->status_anemia,
                'td'     => $pemeriksaan->status_td,
            ],
            'imunisasi'         => null,
            'tanggal_validasi'  => now(),
        ]);
    }

    // ==========================================
    // PEMERIKSAAN DARURAT
    // ==========================================

    public function pemeriksaanDarurat()
    {
        $riwayat = PemeriksaanDarurat::with('bidan')
            ->latest('tanggal')
            ->limit(20)
            ->get();

        return view('bidan.darurat', compact('riwayat'));
    }

    public function storePemeriksaanDarurat(Request $request)
    {
        $validated = $request->validate([
            'nama_pasien'    => 'required|string|max:255',
            'kategori'       => 'required|in:balita,bumil',
            'jenis_kelamin'  => 'nullable|in:L,P',
            'tanggal'        => 'required|date|before_or_equal:today',
            'keluhan'        => 'required|string|max:1000',
            'tindakan'       => 'nullable|string|max:1000',
        ]);

        $validated['bidan_id'] = Auth::id();

        PemeriksaanDarurat::create($validated);

        return redirect()->route('bidan.darurat')
            ->with('success', 'Data pemeriksaan darurat berhasil disimpan!');
    }

    public function laporanDarurat()
    {
        $laporans = PemeriksaanDarurat::with('bidan')
            ->latest('tanggal')
            ->get();

        return view('bidan.laporan-darurat', compact('laporans'));
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

        $laporans = $query->orderBy('kategori')->orderBy('id')->get();

        $posyandus = \App\Models\Posyandu::orderBy('rw')->get();

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
}