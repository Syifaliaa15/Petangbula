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
    
        // SEARCH: nama / NIK / nama ibu
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('nama_ibu', 'like', "%{$search}%");
            });
        }
    
        // FILTER: Jenis Kelamin
        if ($request->filled('jk')) {
            $query->where('jenis_kelamin', $request->jk);
        }
    
        // FILTER: Posyandu (Bidan & Admin)
        if (($user->isBidan() || $user->isAdmin()) && $request->filled('posyandu_id')) {
            $query->where('posyandu_id', $request->posyandu_id);
        }
    
        // URUTKAN berdasarkan nama depan
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

        // VALIDASI KETAT
        $validated = $request->validate([
            'nama'           => 'required|string|max:255|regex:/^[a-zA-Z\s\.]+$/',
            'nik'            => 'nullable|string|digits:16|unique:balita,nik',
            'tanggal_lahir'  => 'required|date|before_or_equal:today|after_or_equal:' . now()->subYears(5)->format('Y-m-d'),
            'jenis_kelamin'  => 'required|in:L,P',
            'nama_ibu'       => 'required|string|max:255|regex:/^[a-zA-Z\s\.]+$/',
            'nama_ayah'      => 'nullable|string|max:255|regex:/^[a-zA-Z\s\.]+$/',
            'alamat'         => 'nullable|string',
            'rt'             => 'nullable|string|max:5',
        ], [
            'nama.regex' => 'Nama hanya boleh berisi huruf dan spasi.',
            'nik.digits' => 'NIK harus tepat 16 digit angka.',
            'tanggal_lahir.after_or_equal' => 'Tanggal lahir tidak boleh lebih dari 5 tahun yang lalu (batas usia balita).',
            'nama_ibu.regex' => 'Nama Ibu hanya boleh berisi huruf dan spasi.',
            'nama_ayah.regex' => 'Nama Ayah hanya boleh berisi huruf dan spasi.',
        ]);

        $validated['posyandu_id'] = $user->posyandu_id;
        // Auto-set RW dari posyandu kader
        if ($user->posyandu) {
            $validated['rw'] = $user->posyandu->rw;
        }
        
        Balita::create($validated);

        return redirect()->route('balita.index')->with('success', 'Data Balita berhasil diregistrasi.');
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
            'tanggal_periksa' => 'required|date|before_or_equal:today',
            'berat_badan'     => 'required|numeric|min:0.5|max:50',
            'tinggi_badan'    => 'required|numeric|min:30|max:150',
            'lingkar_kepala'  => 'required|numeric|min:20|max:70', 
            'lila'            => 'required|numeric|min:10|max:30',  
            'asi_eksklusif'   => 'nullable|boolean',                
            'cara_ukur'       => 'required|in:telentang,berdiri',      
            'imunisasi'       => 'nullable|array',
            'vitamin_a'       => 'nullable',
            'keterangan'      => 'nullable|string|max:500',
        ], [
            'berat_badan.min' => 'Berat badan tidak realistis (terlalu ringan).',
            'berat_badan.max' => 'Berat badan tidak realistis (terlalu berat untuk balita).',
            'tinggi_badan.min' => 'Tinggi badan tidak realistis.',
            'tinggi_badan.max' => 'Tinggi badan tidak realistis untuk balita.',
            'tanggal_periksa.before_or_equal' => 'Tanggal periksa tidak boleh di masa depan.',
            'lingkar_kepala.required' => 'Lingkar Kepala (LIKA) wajib diisi.',
            'lila.required' => 'Lingkar Lengan Atas (LILA) wajib diisi.',
        ]);

        // Hitung status gizi dengan Helper (revisi Permenkes)
        $umur = (int) $validated['target_bulan'];
        $gender = $balita->jenis_kelamin;

        $statusBBU  = ZScoreHelper::statusBBU($gender, $umur, $validated['berat_badan']);
        $statusTBU  = ZScoreHelper::statusTBU($gender, $umur, $validated['tinggi_badan']);
        $statusBBTB = ZScoreHelper::statusBBTB(
            $gender,
            (float) $validated['tinggi_badan'],
            (float) $validated['berat_badan'],
            $umur
        );

        $pemeriksaanExist = PemeriksaanBalita::where('balita_id', $balita->id)
            ->where('umur_bulan', $umur)
            ->first();

        $dataPemeriksaan = [
            'balita_id'       => $balita->id,
            'umur_bulan'      => $umur,
            'tanggal_periksa' => $validated['tanggal_periksa'],
            'berat_badan'     => $validated['berat_badan'],
            'tinggi_badan'    => $validated['tinggi_badan'],
            'panjang_badan'   => $umur < 24 ? $validated['tinggi_badan'] : null,
            'lingkar_kepala'  => $validated['lingkar_kepala'], 
            'lila'            => $validated['lila'],           
            'asi_eksklusif'   => isset($validated['asi_eksklusif']) && $validated['asi_eksklusif'] == '1',
            'cara_ukur'       => $validated['cara_ukur'],
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
            if ($pemeriksaanExist->status_review === 'disetujui') {
                return back()->with('error', 'Data yang sudah disetujui tidak dapat diubah.');
            }
            $pemeriksaanExist->update($dataPemeriksaan);
            $pesan = "Data pemeriksaan bulan ke-{$umur} berhasil diperbarui!";
        } else {
            PemeriksaanBalita::create($dataPemeriksaan);
            $pesan = "Data pemeriksaan bulan ke-{$umur} berhasil disimpan!";
        }

        return redirect()->route('balita.show', $balita->id)->with('success', $pesan);
    }

    public function simpanImunisasi(Request $request, $id)
    {
        $balita = Balita::findOrFail($id);

        $request->validate([
            'jenis_vaksin'      => 'required|string',
            'tanggal_vaksinasi' => 'required|date|before_or_equal:today|after_or_equal:' . $balita->tanggal_lahir->format('Y-m-d'),
        ], [
            'tanggal_vaksinasi.after_or_equal' => 'Tanggal vaksinasi tidak boleh sebelum tanggal lahir balita.',
            'tanggal_vaksinasi.before_or_equal' => 'Tanggal vaksinasi tidak boleh di masa depan.'
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
            'nama'          => 'required|string|max:255|regex:/^[a-zA-Z\s\.]+$/',
            'nik'           => 'nullable|string|digits:16|unique:balita,nik,' . $balita->id,
            'tanggal_lahir' => 'required|date|before_or_equal:today|after_or_equal:' . now()->subYears(5)->format('Y-m-d'),
            'jenis_kelamin' => 'required|in:L,P',
            'nama_ibu'      => 'required|string|max:255|regex:/^[a-zA-Z\s\.]+$/',
            'nama_ayah'     => 'nullable|string|max:255|regex:/^[a-zA-Z\s\.]+$/',
            'alamat'        => 'nullable|string',
            'rt'            => 'nullable|string|max:5',
        ], [
            'nama.regex' => 'Nama hanya boleh berisi huruf dan spasi.',
            'nik.digits' => 'NIK harus tepat 16 digit angka.',
            'tanggal_lahir.after_or_equal' => 'Tanggal lahir tidak boleh lebih dari 5 tahun yang lalu.',
            'nama_ibu.regex' => 'Nama Ibu hanya boleh berisi huruf dan spasi.',
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

    public function hapusPemeriksaan($id)
    {
        // Tambahkan with('balita') untuk memvalidasi posyandu
        $pemeriksaan = PemeriksaanBalita::with('balita')->findOrFail($id);
        $user = Auth::user();

        // Validasi Keamanan: Kader hanya boleh hapus data posyandu-nya sendiri
        if ($user->isKader() && $pemeriksaan->balita->posyandu_id !== $user->posyandu_id) {
            abort(403, 'Anda tidak berhak menghapus data posyandu lain.');
        }

        if ($pemeriksaan->status_review === 'disetujui') {
            return back()->with('error', 'Data yang sudah disetujui Bidan tidak dapat dihapus.');
        }

        $balitaId = $pemeriksaan->balita_id;
        $pemeriksaan->delete();

        return redirect()->route('balita.show', $balitaId)
            ->with('success', 'Data pemeriksaan berhasil dihapus.');
    }    
}