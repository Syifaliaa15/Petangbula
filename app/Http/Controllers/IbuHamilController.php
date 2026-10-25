<?php

namespace App\Http\Controllers;

use App\Models\IbuHamil;
use App\Models\PemeriksaanBumil;
use App\Models\ImunisasiBumil;
use App\Helpers\ZScoreHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class IbuHamilController extends Controller
{
    public function index(Request $request)
    {
        $user       = Auth::user();
        $posyanduId = $user->posyandu_id;

        $query = IbuHamil::with(['posyandu', 'pemeriksaan']);

        if ($user->isKader()) {
            if (!$posyanduId) {
                return redirect()->route('kader.dashboard')
                    ->with('error', 'Akun Anda belum terhubung ke Posyandu.');
            }
            $query->where('posyandu_id', $posyanduId);
        }

        // SEARCH: nama / NIK / nama suami
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                ->orWhere('nik', 'like', "%{$search}%")
                ->orWhere('nama_suami', 'like', "%{$search}%");
            });
        }

        // FILTER: Hamil Ke
        if ($request->filled('hamil_ke')) {
            $query->where('hamil_ke', $request->hamil_ke);
        }

        // FILTER: Posyandu (Bidan & Admin)
        if (($user->isBidan() || $user->isAdmin()) && $request->filled('posyandu_id')) {
            $query->where('posyandu_id', $request->posyandu_id);
        }

        $ibuHamils = $query->orderBy('nama', 'asc')
            ->paginate(15)
            ->withQueryString();

        $posyandus = ($user->isBidan() || $user->isAdmin())
            ? \App\Models\Posyandu::orderBy('rw')->get()
            : collect();

        return view('kader.ibu-hamil.index', compact('ibuHamils', 'posyandus'));
    }

    public function create()
    {
        return view('kader.ibu-hamil.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->posyandu_id) {
            return back()->withErrors(['posyandu_id' => 'Akun Anda tidak terhubung ke Posyandu.'])->withInput();
        }

        // VALIDASI KETAT
        $validated = $request->validate([
            'nik'                 => 'required|string|digits:16|unique:ibu_hamil,nik',
            'nama'                => 'required|string|max:255|regex:/^[a-zA-Z\s\.]+$/',
            'umur'                => 'required|integer|min:15|max:50', // Umur realistis untuk hamil
            'klmp_dasawisma'      => 'nullable|string|max:255',
            'hamil_ke'            => 'required|integer|min:1|max:10', // Hamil ke- realistis
            'tanggal_pendaftaran' => 'required|date|before_or_equal:today',
            'nama_suami'          => 'nullable|string|max:255|regex:/^[a-zA-Z\s\.]+$/',
            'alamat'              => 'nullable|string',
            'rt'                  => 'nullable|string|max:5',
        ], [
            'nik.digits' => 'NIK harus tepat 16 digit angka.',
            'nama.regex' => 'Nama hanya boleh berisi huruf dan spasi.',
            'umur.min' => 'Umur terlalu muda untuk hamil.',
            'umur.max' => 'Umur terlalu tua untuk hamil.',
            'hamil_ke.max' => 'Jumlah kehamilan tidak masuk akal.',
            'nama_suami.regex' => 'Nama suami hanya boleh berisi huruf dan spasi.',
            'tanggal_pendaftaran.before_or_equal' => 'Tanggal pendaftaran tidak boleh di masa depan.',
        ]);

        $validated['posyandu_id'] = $user->posyandu_id;
        
        if ($user->posyandu) {
            $validated['rw'] = $user->posyandu->rw;
        }

        IbuHamil::create($validated);

        return redirect()->route('ibu-hamil.index')
            ->with('success', 'Profil Ibu Hamil berhasil didaftarkan.');
    }

    public function show($id)
    {
        $ibuHamil = IbuHamil::findOrFail($id);
        $usia_kehamilan_minggu = $ibuHamil->usia_kehamilan_minggu;

        $riwayat_pemeriksaan = PemeriksaanBumil::where('ibu_hamil_id', $ibuHamil->id)
            ->orderBy('minggu_ke', 'asc')
            ->get()
            ->keyBy('minggu_ke');

        $riwayat_imunisasi = ImunisasiBumil::where('ibu_hamil_id', $ibuHamil->id)
            ->get()
            ->keyBy('jenis_tt');

        $master_tt = ['TT I', 'TT II', 'TT III', 'TT IV', 'TT V', 'Vitamin A'];

        return view('kader.ibu-hamil.show', compact(
            'ibuHamil', 'usia_kehamilan_minggu',
            'riwayat_pemeriksaan', 'riwayat_imunisasi', 'master_tt'
        ));
    }

    public function checkup($id, Request $request)
    {
        $ibuHamil    = IbuHamil::findOrFail($id);
        $target_minggu = $request->query('minggu_ke', 1);

        $pemeriksaan = PemeriksaanBumil::where('ibu_hamil_id', $ibuHamil->id)
            ->where('minggu_ke', $target_minggu)
            ->first();

        return view('kader.ibu-hamil.checkup', compact('ibuHamil', 'target_minggu', 'pemeriksaan'));
    }

    public function storeCheckup(Request $request, $id)
    {
        $ibuHamil = IbuHamil::findOrFail($id);

        $validated = $request->validate([
            'target_minggu'       => 'required|integer|between:1,40',
            'bb'                  => 'required|numeric|min:30|max:150',
            'lila'                => 'required|numeric|min:15|max:50',
            'hemoglobin'          => 'nullable|numeric|min:5|max:20',
            'tablet_tambah_darah' => 'required|integer|min:0|max:1000',
            'pmt_pemulihan'       => 'required|boolean',
            'tekanan_darah'       => 'nullable|string|max:20',
            'keterangan'          => 'nullable|string|max:500',
        ], [
            'bb.min' => 'Berat badan tidak realistis.',
            'bb.max' => 'Berat badan tidak realistis.',
            'lila.min' => 'LILA tidak realistis.',
            'lila.max' => 'LILA tidak realistis.',
            'hemoglobin.min' => 'Nilai Hemoglobin tidak valid.',
            'hemoglobin.max' => 'Nilai Hemoglobin tidak valid.',
        ]);

        $statusKEK    = ZScoreHelper::statusKEK($validated['lila']);
        $statusAnemia = ZScoreHelper::statusAnemia($validated['hemoglobin']);
        $statusTD     = ZScoreHelper::statusTD($validated['tekanan_darah']);

        $statusGiziUtama = 'Normal';
        if ($statusKEK === 'Risiko KEK') $statusGiziUtama = 'Risiko KEK';
        if ($statusAnemia === 'Anemia') {
            $statusGiziUtama .= ($statusGiziUtama !== 'Normal' ? ' & ' : '') . 'Anemia';
        }

        $pemeriksaanExist = PemeriksaanBumil::where('ibu_hamil_id', $ibuHamil->id)
            ->where('minggu_ke', $validated['target_minggu'])
            ->first();

        $data = [
            'ibu_hamil_id'        => $ibuHamil->id,
            'minggu_ke'           => $validated['target_minggu'],
            'tanggal_periksa'     => now(),
            'bb'                  => $validated['bb'],
            'lila'                => $validated['lila'],
            'hemoglobin'          => $validated['hemoglobin'] ?? null,
            'tablet_tambah_darah' => $validated['tablet_tambah_darah'],
            'pmt_pemulihan'       => $validated['pmt_pemulihan'],
            'tekanan_darah'       => $validated['tekanan_darah'] ?? null,
            'status_gizi'         => $statusGiziUtama,
            'status_kek'          => $statusKEK,
            'status_anemia'       => $statusAnemia,
            'status_td'           => $statusTD,
            'status_review'       => 'pending',
            'kader_id'            => Auth::user()->id,
        ];

        if ($pemeriksaanExist) {
            if ($pemeriksaanExist->status_review === 'disetujui') {
                return back()->with('error', 'Data yang sudah disetujui tidak dapat diubah.');
            }
            $pemeriksaanExist->update($data);
            $pesan = "Pemeriksaan minggu ke-{$validated['target_minggu']} berhasil diperbarui!";
        } else {
            PemeriksaanBumil::create($data);
            $pesan = "Pemeriksaan minggu ke-{$validated['target_minggu']} berhasil dilaporkan!";
        }

        return redirect()->route('ibu-hamil.show', $ibuHamil->id)->with('success', $pesan);
    }

    public function simpanImunisasi(Request $request, $id)
    {
        $ibuHamil = IbuHamil::findOrFail($id);

        // Validasi dasar
        $rules = [
            'jenis_tt'          => 'required|string',
            'tanggal_vaksinasi' => 'required|date|before_or_equal:today',
        ];

        // Tambahkan validasi after_or_equal HANYA jika tanggal pendaftaran ada
        if ($ibuHamil->tanggal_pendaftaran) {
            $rules['tanggal_vaksinasi'] .= '|after_or_equal:' . $ibuHamil->tanggal_pendaftaran->format('Y-m-d');
        }

        $request->validate($rules, [
            'tanggal_vaksinasi.after_or_equal' => 'Tanggal vaksinasi tidak boleh sebelum tanggal pendaftaran/HPHT.',
            'tanggal_vaksinasi.before_or_equal' => 'Tanggal vaksinasi tidak boleh di masa depan.'
        ]);

        $sudahAda = ImunisasiBumil::where('ibu_hamil_id', $ibuHamil->id)
            ->where('jenis_tt', $request->jenis_tt)
            ->exists();

        if ($sudahAda) {
            return back()->with('error', "Imunisasi {$request->jenis_tt} sudah tercatat.");
        }

        ImunisasiBumil::create([
            'ibu_hamil_id'      => $ibuHamil->id,
            'jenis_tt'          => $request->jenis_tt,
            'tanggal_vaksinasi' => $request->tanggal_vaksinasi,
        ]);

        return redirect()->route('ibu-hamil.show', $ibuHamil->id)
            ->with('success', "Imunisasi {$request->jenis_tt} berhasil dicatat!");
    }

    public function edit($id)
    {
        $ibuHamil = IbuHamil::findOrFail($id);
        return view('kader.ibu-hamil.edit', compact('ibuHamil'));
    }

    public function update(Request $request, $id)
    {
        $ibuHamil = IbuHamil::findOrFail($id);

        $validated = $request->validate([
            'nik'                 => 'required|string|digits:16|unique:ibu_hamil,nik,' . $ibuHamil->id,
            'nama'                => 'required|string|max:255|regex:/^[a-zA-Z\s\.]+$/',
            'umur'                => 'required|integer|min:15|max:50',
            'klmp_dasawisma'      => 'nullable|string|max:255',
            'hamil_ke'            => 'required|integer|min:1|max:10',
            'tanggal_pendaftaran' => 'required|date|before_or_equal:today',
            'nama_suami'          => 'nullable|string|max:255|regex:/^[a-zA-Z\s\.]+$/',
            'alamat'              => 'nullable|string',
            'rt'                  => 'nullable|string|max:5',
        ], [
            'nik.digits' => 'NIK harus tepat 16 digit angka.',
            'nama.regex' => 'Nama hanya boleh berisi huruf dan spasi.',
            'nama_suami.regex' => 'Nama suami hanya boleh berisi huruf dan spasi.',
        ]);

        // Auto-set RW dari posyandu kader
        if (Auth::user()->posyandu) {
            $validated['rw'] = Auth::user()->posyandu->rw;
        }

        $ibuHamil->update($validated);

        return redirect()->route('ibu-hamil.index')->with('success', 'Data Ibu Hamil berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $ibuHamil = IbuHamil::findOrFail($id);
        $ibuHamil->imunisasis()->delete();
        $ibuHamil->pemeriksaan()->delete();
        $ibuHamil->delete();

        return redirect()->route('ibu-hamil.index')->with('success', 'Data Ibu Hamil berhasil dihapus!');
    }

    public function hapusPemeriksaan($id)
    {
        $pemeriksaan = PemeriksaanBumil::with('ibuHamil')->findOrFail($id);
        $user = Auth::user();

        // Validasi Keamanan
        if ($user->isKader() && $pemeriksaan->ibuHamil->posyandu_id !== $user->posyandu_id) {
            abort(403, 'Anda tidak berhak menghapus data posyandu lain.');
        }

        if ($pemeriksaan->status_review === 'disetujui') {
            return back()->with('error', 'Data yang sudah disetujui Bidan tidak dapat dihapus.');
        }

        $ibuHamilId = $pemeriksaan->ibu_hamil_id;
        $pemeriksaan->delete();

        return redirect()->route('ibu-hamil.show', $ibuHamilId)
            ->with('success', 'Data pemeriksaan berhasil dihapus.');
    }    
}