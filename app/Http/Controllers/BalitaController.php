<?php

namespace App\Http\Controllers;

use App\Models\Balita;
use App\Models\PemeriksaanBalita;
use App\Models\ImunisasiBalita;
use App\Helpers\ZScoreHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BalitaController extends Controller
{
    public function index(Request $request)
    {
        $user       = Auth::user();
        $posyanduId = $user->posyandu_id;
    
        $query = Balita::with(['posyandu', 'pemeriksaan']);
    
        // Filter role
        if ($user->isKader()) {
            if (!$posyanduId) {
                return redirect()->route('kader.dashboard')
                    ->with('error', 'Akun Anda belum terhubung ke Posyandu.');
            }
            $query->where('posyandu_id', $posyanduId);
        }
    
        // ==========================================
        // SEARCH: nama / NIK / nama ibu
        // ==========================================
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('nama_ibu', 'like', "%{$search}%");
            });
        }
    
        // ==========================================
        // FILTER: Jenis Kelamin
        // ==========================================
        if ($request->filled('jk')) {
            $query->where('jenis_kelamin', $request->jk);
        }
    
        // ==========================================
        // FILTER: Posyandu (Bidan & Admin)
        // ==========================================
        if (($user->isBidan() || $user->isAdmin()) && $request->filled('posyandu_id')) {
            $query->where('posyandu_id', $request->posyandu_id);
        }
    
        // ==========================================
        // URUTKAN berdasarkan nama depan
        // ==========================================
        $balitas = $query->orderBy('nama', 'asc')
            ->paginate(15)
            ->withQueryString();
    
        // Data untuk filter dropdown
        $posyandus = ($user->isBidan() || $user->isAdmin())
            ? \App\Models\Posyandu::orderBy('rw')->get()
            : collect();
    
        return view('kader.balita.index', compact('balitas', 'posyandus'));
    }

    public function create()
    {
        return view('kader.balita.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->posyandu_id) {
            return back()->withErrors(['posyandu_id' => 'Akun Anda tidak terhubung ke Posyandu.'])->withInput();
        }

        $validated = $request->validate([
            'nama'           => 'required|string|max:255',
            'nik'            => 'nullable|string|unique:balita,nik|max:20',
            'tanggal_lahir'  => 'required|date|before_or_equal:today',
            'jenis_kelamin'  => 'required|in:L,P',
            'nama_ibu'       => 'required|string|max:255',
            'nama_ayah'      => 'nullable|string|max:255',
            'alamat'         => 'nullable|string',
            'rw'             => 'nullable|string|max:5',
            'rt'             => 'nullable|string|max:5',
        ]);

        $validated['posyandu_id'] = $user->posyandu_id;
        // Auto-set RW dari posyandu kader
        if ($user->posyandu && !isset($validated['rw'])) {
            $validated['rw'] = $user->posyandu->rw;
        }
        Balita::create($validated);

        return redirect()->route('balita.index') ->with('success', 'Data Balita berhasil diregistrasi.');
    }

    public function show($id)
    {
        $balita = Balita::findOrFail($id);

        $usia_sekarang_bulan = $balita->tanggal_lahir->diffInMonths(Carbon::now());
        $usia_sekarang_bulan = $usia_sekarang_bulan > 59 ? 59 : $usia_sekarang_bulan;

        $riwayat_pemeriksaan = PemeriksaanBalita::where('balita_id', $id)
            ->orderBy('umur_bulan', 'asc')
            ->get()
            ->keyBy('umur_bulan');

        $riwayat_imunisasi = ImunisasiBalita::where('balita_id', $id)
            ->get()
            ->keyBy('jenis_vaksin');

        $master_vaksin = [
            'HB-0', 'BCG', 'Polio 1', 'DPT-HB-Hib 1', 'Polio 2',
            'DPT-HB-Hib 2', 'Polio 3', 'DPT-HB-Hib 3', 'Polio 4',
            'IPV', 'Campak/MR'
        ];

        return view('kader.balita.show', compact(
            'balita', 'usia_sekarang_bulan',
            'riwayat_pemeriksaan', 'riwayat_imunisasi', 'master_vaksin'
        ));
    }

    public function checkup($id, Request $request)
    {
        $balita      = Balita::findOrFail($id);
        $target_bulan = $request->query('bulan_ke', 0);
        $usiaSaatIni  = $balita->tanggal_lahir->diffInMonths(Carbon::now());

        if ($target_bulan > $usiaSaatIni) {
            return redirect()->route('balita.show', $balita->id)
                ->with('error', "Tidak dapat mengisi data bulan ke-{$target_bulan}. Usia balita baru {$usiaSaatIni} bulan.");
        }

        $pemeriksaan = PemeriksaanBalita::where('balita_id', $balita->id)
            ->where('umur_bulan', $target_bulan)
            ->first();

        return view('kader.balita.checkup', compact('balita', 'target_bulan', 'pemeriksaan'));
    }

    public function storeCheckup(Request $request, $id)
    {
        $balita = Balita::findOrFail($id);

        $validated = $request->validate([
            'target_bulan'    => 'required|integer|between:0,59',
            'tanggal_periksa' => 'required|date',
            'berat_badan'     => 'required|numeric|min:0.5|max:50',
            'tinggi_badan'    => 'required|numeric|min:30|max:150',
            'lingkar_kepala'  => 'nullable|numeric|min:20|max:70',
            'imunisasi'       => 'nullable|array',
            'vitamin_a'       => 'nullable',
            'keterangan'      => 'nullable|string|max:500',
        ]);

        // Hitung semua status gizi dengan Helper
        $statusBBU  = ZScoreHelper::statusBBU($balita->jenis_kelamin, $validated['target_bulan'], $validated['berat_badan']);
        $statusTBU  = ZScoreHelper::statusTBU($balita->jenis_kelamin, $validated['target_bulan'], $validated['tinggi_badan']);
        $statusBBTB = ZScoreHelper::statusBBTB($balita->jenis_kelamin, $validated['tinggi_badan'], $validated['berat_badan']);

        $pemeriksaanExist = PemeriksaanBalita::where('balita_id', $balita->id)
            ->where('umur_bulan', $validated['target_bulan'])
            ->first();

        $dataPemeriksaan = [
            'balita_id'       => $balita->id,
            'umur_bulan'      => $validated['target_bulan'],
            'tanggal_periksa' => $validated['tanggal_periksa'],
            'berat_badan'     => $validated['berat_badan'],
            'tinggi_badan'    => $validated['tinggi_badan'],
            'panjang_badan'   => $validated['target_bulan'] < 24 ? $validated['tinggi_badan'] : null,
            'lingkar_kepala'  => $validated['lingkar_kepala'] ?? null,
            'status_gizi'     => $statusBBU,
            'status_bb_u'     => $statusBBU,
            'status_tb_u'     => $statusTBU,
            'status_bb_tb'    => $statusBBTB,
            'imunisasi'       => $validated['imunisasi'] ?? [],
            'vitamin_a'       => isset($validated['vitamin_a']) && $validated['vitamin_a'] == '1',
            'keterangan'      => $validated['keterangan'] ?? null,
            'kader_id'        => Auth::user()->id,
            'status_review'   => 'pending',
        ];

        if ($pemeriksaanExist) {
            $pemeriksaanExist->update($dataPemeriksaan);
            $pesan = "Data pemeriksaan bulan ke-{$validated['target_bulan']} berhasil diperbarui!";
        } else {
            PemeriksaanBalita::create($dataPemeriksaan);
            $pesan = "Data pemeriksaan bulan ke-{$validated['target_bulan']} berhasil disimpan!";
        }

        return redirect()->route('balita.show', $balita->id)->with('success', $pesan);
    }

    public function simpanImunisasi(Request $request, $id)
    {
        $balita = Balita::findOrFail($id);

        $request->validate([
            'jenis_vaksin'      => 'required|string',
            'tanggal_vaksinasi' => 'required|date|before_or_equal:today',
        ]);

        $sudahAda = ImunisasiBalita::where('balita_id', $balita->id)
            ->where('jenis_vaksin', $request->jenis_vaksin)
            ->exists();

        if ($sudahAda) {
            return back()->with('error', "Imunisasi {$request->jenis_vaksin} sudah tercatat.");
        }

        ImunisasiBalita::create([
            'balita_id'         => $balita->id,
            'jenis_vaksin'      => $request->jenis_vaksin,
            'tanggal_vaksinasi' => $request->tanggal_vaksinasi,
        ]);

        return redirect()->route('balita.show', $balita->id)
            ->with('success', "Imunisasi {$request->jenis_vaksin} berhasil dicatat!");
    }

    public function edit($id)
    {
        $balita = Balita::findOrFail($id);
        return view('kader.balita.edit', compact('balita'));
    }

    public function update(Request $request, $id)
    {
        $balita = Balita::findOrFail($id);

        $validated = $request->validate([
            'nama'          => 'required|string|max:255',
            'nik'           => 'nullable|string|unique:balita,nik,' . $balita->id . '|max:20',
            'tanggal_lahir' => 'required|date|before_or_equal:today',
            'jenis_kelamin' => 'required|in:L,P',
            'nama_ibu'      => 'required|string|max:255',
            'nama_ayah'     => 'nullable|string|max:255',
            'alamat'        => 'nullable|string',
            'rw'            => 'nullable|string|max:5',
            'rt'            => 'nullable|string|max:5',
        ]);

        $balita->update($validated);

        return redirect()->route('balita.index')->with('success', 'Data Balita berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $balita = Balita::findOrFail($id);
        $balita->pemeriksaan()->delete();
        $balita->imunisasis()->delete();
        $balita->delete();

        return redirect()->route('balita.index')->with('success', 'Data Balita berhasil dihapus!');
    }

    /**
     * Hapus data pemeriksaan balita (hanya jika belum divalidasi bidan)
     */
    public function hapusPemeriksaan($id)
    {
        $pemeriksaan = PemeriksaanBalita::findOrFail($id);

        // Cek authorization: hanya kader pembuat atau admin
        $user = Auth::user();
        if (!$user->isAdmin() && $pemeriksaan->kader_id !== $user->id) {
            return back()->with('error', 'Anda tidak berhak menghapus data ini.');
        }

        // Cek status review
        if ($pemeriksaan->status_review === 'disetujui') {
            return back()->with('error', 'Data yang sudah disetujui Bidan tidak dapat dihapus.');
        }

        $balitaId = $pemeriksaan->balita_id;
        $pemeriksaan->delete();

        return redirect()->route('balita.show', $balitaId)
            ->with('success', 'Data pemeriksaan berhasil dihapus.');
    }    
}