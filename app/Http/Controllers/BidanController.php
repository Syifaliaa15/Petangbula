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
use App\Services\C45\C45ModelRepository;
use Carbon\Carbon;

class BidanController extends Controller
{
    public function dashboard()
    {
        $totalBalita     = Balita::count();
        $totalIbuHamil   = IbuHamil::count();
        $totalPending    = PemeriksaanBalita::where('status_review', 'pending')->count()
                         + PemeriksaanBumil::where('status_review', 'pending')->count();

        // Statistik Balita (Semua Posyandu)
        $baseBalitaQuery = PemeriksaanBalita::where('status_review', 'disetujui');
        
        $giziBaik   = (clone $baseBalitaQuery)->where('status_bb_u', 'Berat Badan Normal')->count();
        $giziKurang = (clone $baseBalitaQuery)->where('status_bb_u', 'Berat Badan Kurang')->count();
        $giziLebih  = (clone $baseBalitaQuery)->where('status_bb_u', 'Risiko Berat Badan Lebih')->count();
        $giziBuruk  = (clone $baseBalitaQuery)->where('status_bb_u', 'Berat Badan Sangat Kurang')->count();

        $tbNormal      = (clone $baseBalitaQuery)->where('status_tb_u', 'Normal')->count();
        $tbPendek      = (clone $baseBalitaQuery)->where('status_tb_u', 'Pendek')->count();
        $tbSangatPendek= (clone $baseBalitaQuery)->where('status_tb_u', 'Sangat Pendek')->count();
        $tbTinggi      = (clone $baseBalitaQuery)->where('status_tb_u', 'Tinggi')->count();

        $bbtbNormal    = (clone $baseBalitaQuery)->where('status_bb_tb', 'Gizi Baik')->count();
        $bbtbKurang    = (clone $baseBalitaQuery)->where('status_bb_tb', 'Gizi Kurang')->count();
        $bbtbBuruk     = (clone $baseBalitaQuery)->where('status_bb_tb', 'Gizi Buruk')->count();
        $bbtbLebih     = (clone $baseBalitaQuery)->where('status_bb_tb', 'Gizi Lebih')->count();
        $bbtbObesitas  = (clone $baseBalitaQuery)->where('status_bb_tb', 'Obesitas')->count();

        // Statistik Bumil
        $baseBumilQuery = PemeriksaanBumil::where('status_review', 'disetujui');
        
        $bumilNormal = (clone $baseBumilQuery)->where('status_kek', 'Tidak Risiko')->where('status_anemia', 'Tidak Anemia')->count();
        $bumilKEK    = (clone $baseBumilQuery)->where('status_kek', 'Risiko KEK')->count();
        $bumilAnemia = (clone $baseBumilQuery)->where('status_anemia', 'Anemia')->count();
        
        $tdHipertensi = (clone $baseBumilQuery)->where('status_td', 'Hipertensi')->count();

        return view('bidan.dashboard', compact(
            'totalBalita', 'totalIbuHamil', 'totalPending',
            'giziBaik', 'giziKurang', 'giziLebih', 'giziBuruk',
            'tbNormal', 'tbPendek', 'tbSangatPendek', 'tbTinggi',
            'bbtbNormal', 'bbtbKurang', 'bbtbBuruk', 'bbtbLebih', 'bbtbObesitas',
            'bumilNormal', 'bumilKEK', 'bumilAnemia', 'tdHipertensi'
        ));
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

        $repo = app(C45ModelRepository::class);

        // Generate fitur C4.5 untuk setiap balita
        foreach ($pemeriksaans as $p) {
            // 1. Tren BB
            $prev = PemeriksaanBalita::where('balita_id', $p->balita_id)
                ->where('umur_bulan', '<', $p->umur_bulan)
                ->latest('umur_bulan')->first();
            $tren = ($prev && $p->berat_badan < $prev->berat_badan) ? 'Turun' : 'Naik';

            // 2. Fitur lainnya
            $fitur = [
                'Tren_BB'       => $tren,
                'Kelompok_Usia' => ($p->umur_bulan < 24) ? '< 24 bulan' : '>= 24 bulan',
                'Status_Gizi'   => $p->status_bb_tb ?? $p->status_bb_u, 
                'LILA'          => ($p->lila && $p->lila < 12.5) ? 'Kurang' : 'Cukup',
            ];

            // 3. Simpan hasil prediksi
            $p->rekomendasi_1 = $repo->prediksi('balita', $fitur, 'Prioritas_1')['kelas'] ?? '-';
            $p->rekomendasi_2 = $repo->prediksi('balita', $fitur, 'Prioritas_2')['kelas'] ?? '-';
            $p->rekomendasi_3 = $repo->prediksi('balita', $fitur, 'Prioritas_3')['kelas'] ?? '-';
        }

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

        // Tangkap data checkbox tindakan
        $tindakan = $request->input('tindakan_bidan', []);
        
        // Jika bidan memilih "Lainnya" dan mengisi teks
        if (in_array('Lainnya', $tindakan) && $request->filled('catatan_tindakan_lain')) {
            $key = array_search('Lainnya', $tindakan);
            $tindakan[$key] = 'Lainnya: ' . $request->catatan_tindakan_lain;
        }

        $pemeriksaan->update([
            'status_review'   => $status,
            'catatan_bidan'   => $request->catatan,
            'tindakan_bidan'  => $tindakan, 
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
                'tindakan_bidan' => [], // Bulk action tidak punya checkbox
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
                'lila'            => $pemeriksaan->lila,              
                'asi_eksklusif'   => $pemeriksaan->asi_eksklusif,     
                'cara_ukur'       => $pemeriksaan->cara_ukur,         
                'vitamin_a'       => $pemeriksaan->vitamin_a,
                'keterangan'      => $pemeriksaan->keterangan,
                'tindakan_bidan'  => $pemeriksaan->tindakan_bidan, // <-- PASTIKAN INI ADA
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

        $repo = app(C45ModelRepository::class);

        foreach ($pemeriksaans as $p) {
            $trimester = 'Trimester 1';
            if ($p->minggu_ke > 13 && $p->minggu_ke <= 27) $trimester = 'Trimester 2';
            elseif ($p->minggu_ke > 27) $trimester = 'Trimester 3';

            $tensi = 'Normal';
            if ($p->tekanan_darah) {
                $td = explode('/', $p->tekanan_darah);
                if (count($td) == 2 && ((int)$td[0] >= 140 || (int)$td[1] >= 90)) {
                    $tensi = 'Hipertensi';
                }
            }

            $fitur = [
                'Trimester' => $trimester,
                'LILA'      => ($p->lila < 23.5) ? 'Risiko KEK' : 'Normal',
                'Hb'        => ($p->hemoglobin && $p->hemoglobin < 11.0) ? 'Anemia' : 'Normal',
                'Tensi'     => $tensi,
            ];

            $p->rekomendasi_1 = $repo->prediksi('bumil', $fitur, 'Prioritas_1')['kelas'] ?? '-';
            $p->rekomendasi_2 = $repo->prediksi('bumil', $fitur, 'Prioritas_2')['kelas'] ?? '-';
            $p->rekomendasi_3 = $repo->prediksi('bumil', $fitur, 'Prioritas_3')['kelas'] ?? '-';
        }

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

        $tindakan = $request->input('tindakan_bidan', []);
        
        if (in_array('Lainnya', $tindakan) && $request->filled('catatan_tindakan_lain')) {
            $key = array_search('Lainnya', $tindakan);
            $tindakan[$key] = 'Lainnya: ' . $request->catatan_tindakan_lain;
        }

        $pemeriksaan->update([
            'status_review'   => $status,
            'catatan_bidan'   => $request->catatan,
            'tindakan_bidan'  => $tindakan, 
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
                'tindakan_bidan' => [],
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
                'tanggal_periksa'     => $pemeriksaan->tanggal_periksa->format('Y-m-d'),
                'minggu_ke'           => $pemeriksaan->minggu_ke,
                'bb_pra_hamil'        => $pemeriksaan->bb_pra_hamil,       
                'tinggi_badan'        => $pemeriksaan->tinggi_badan,       
                'bb'                  => $pemeriksaan->bb,
                'lila'                => $pemeriksaan->lila,
                'lingkar_perut'       => $pemeriksaan->lingkar_perut,      
                'hemoglobin'          => $pemeriksaan->hemoglobin,
                'tfu'                 => $pemeriksaan->tfu,                
                'kategori_tfu'        => $pemeriksaan->kategori_tfu,       
                'taksiran_bb_janin'   => $pemeriksaan->taksiran_bb_janin,   
                'kategori_janin'      => $pemeriksaan->kategori_janin,      
                'tablet_tambah_darah' => $pemeriksaan->tablet_tambah_darah,
                'pmt_pemulihan'       => $pemeriksaan->pmt_pemulihan,
                'tekanan_darah'       => $pemeriksaan->tekanan_darah,
                'tindakan_bidan'      => $pemeriksaan->tindakan_bidan, // <-- PASTIKAN INI ADA
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

    public function laporanPosyandu(Request $request)
    {
        $bulan = $request->query('bulan', now()->month);
        $tahun = $request->query('tahun', now()->year);
        $tipe_periode = $request->query('tipe_periode', 'bulanan');
        $kategori = $request->query('kategori');
        $status_gizi = $request->query('status_gizi');
        $posyanduId = $request->query('posyandu_id');

        $query = LaporanPosyandu::with(['posyandu', 'validator']);

        if ($tipe_periode === 'tahunan') {
            $query->where('periode_tahun', $tahun);
        } else {
            $query->where('periode_bulan', $bulan)->where('periode_tahun', $tahun);
        }

        if ($posyanduId) {
            $query->where('posyandu_id', $posyanduId);
        }

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

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

        $laporans = $query->orderBy('kategori')->orderBy('id')->paginate(50)->withQueryString();
        
        $posyandus = \App\Models\Posyandu::orderBy('rw')->get();
        $tahunList = LaporanPosyandu::select('periode_tahun')->distinct()->orderBy('periode_tahun', 'desc')->pluck('periode_tahun')->toArray();
        if (!in_array(now()->year, $tahunList)) $tahunList[] = now()->year;
        sort($tahunList);

        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return view('laporan-posyandu.index', compact('laporans', 'bulan', 'tahun', 'tahunList', 'bulanList', 'tipe_periode', 'kategori', 'status_gizi', 'posyandus', 'posyanduId'));
    }
}