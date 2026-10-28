<?php

namespace App\Http\Controllers;

use App\Models\C45Model;
use App\Services\C45\C45ModelRepository;
use App\Services\C45\C45Predictor;
use App\Services\C45\C45TrainingService;
use Illuminate\Http\Request;

/**
 * Endpoint C4.5 untuk web:
 *  - latih(): tombol "Latih Model" (khusus Admin/Bidan)
 *  - prediksiBalita()/prediksiBumil(): rekomendasi tindak lanjut (milidetik)
 *  - pohon(): halaman visualisasi pohon + aturan IF-THEN (materi Bab 4)
 */
class C45Controller extends Controller
{
    /** POST /c45/latih  {model: balita|bumil, target: Prioritas_1|2|3, evaluasi?: on} */
    public function latih(Request $request)
    {
        $request->validate(array(
            'model'  => 'required|in:balita,bumil',
            'target' => 'required|in:Prioritas_1,Prioritas_2,Prioritas_3',
        ));

        $m = app(C45TrainingService::class)->latih(
            $request->input('model'),
            $request->input('target'),
            $request->boolean('evaluasi')
        );

        return back()->with(
            'sukses',
            "Model {$request->input('model')}/{$request->input('target')} versi {$m->versi} berhasil dilatih. " .
            "Data latih: {$m->jumlah_data} baris, akurasi latih: {$m->akurasi_latih}%."
        );
    }

    /**
     * POST /c45/prediksi/balita
     * Dipanggil SETELAH gerbang Z-Score menandai kasus sebagai "indikator
     * tidak baik" (lihat README, bagian Integrasi). Body JSON:
     * { "Tren_BB": "Turun", "Kelompok_Usia": "< 24 bulan",
     *   "Status_Gizi": "Gizi Buruk", "LILA": "Kurang" }
     */
    public function prediksiBalita(Request $request)
    {
        $data = $request->validate(array(
            'Tren_BB'       => 'required|in:Turun,Naik',
            'Kelompok_Usia' => 'required|in:< 24 bulan,>= 24 bulan',
            'Status_Gizi'   => 'required|in:BB Sangat Kurang,BB Kurang,Sangat Pendek,Pendek,Gizi Buruk,Gizi Kurang,Obesitas',
            'LILA'          => 'required|in:Kurang,Cukup',
        ));
        return $this->jawab('balita', $data);
    }

    /**
     * POST /c45/prediksi/bumil
     * Body JSON: { "Trimester": "Trimester 3", "LILA": "Normal",
     *              "Hb": "Anemia", "Tensi": "Hipertensi" }
     */
    public function prediksiBumil(Request $request)
    {
        $data = $request->validate(array(
            'Trimester' => 'required|in:Trimester 1,Trimester 2,Trimester 3',
            'LILA'      => 'required|in:Risiko KEK,Normal',
            'Hb'        => 'required|in:Anemia,Normal',
            'Tensi'     => 'required|in:Hipertensi,Normal',
        ));
        return $this->jawab('bumil', $data);
    }

    /** GET /c45/pohon/{nama}  — visualisasi pohon + aturan (materi Bab 4). */
    public function pohon(string $nama)
    {
        $model = C45Model::where('nama', $nama)
            ->where('aktif', true)
            ->orderByDesc('versi')
            ->firstOrFail();

        $prediktor = new C45Predictor($model->tree);

        return view('c45.pohon', array(
            'nama'   => $nama,
            'model'  => $model,
            'pohon'  => $model->tree,
            'aturan' => $prediktor->aturanIFTHEN(),
        ));
    }

    // ------------------------------------------------------------------

    private function jawab(string $nama, array $fitur)
    {
        $t0   = microtime(true);
        $repo = app(C45ModelRepository::class);

        $rekomendasi = array();
        $detail      = null;
        foreach (array('Prioritas_1', 'Prioritas_2', 'Prioritas_3') as $target) {
            $h = $repo->prediksi($nama, $fitur, $target);
            if ($h === null) {
                $rekomendasi[$target] = null; // model target tsb belum dilatih
                continue;
            }
            $rekomendasi[$target] = $h['kelas'];
            if ($target === 'Prioritas_1') {
                $detail = $h;
            }
        }

        return response()->json(array(
            'rekomendasi' => $rekomendasi,                                  // P1/P2/P3
            'alasan'      => $detail === null ? null : array(
                'jalur'      => $detail['jalur'],                           // jejak pohon yang dilalui
                'confidence' => $detail['confidence'],
            ),
            'waktu_ms'    => round((microtime(true) - $t0) * 1000, 2),      // bukti cepat utk Bab 4
        ));
    }
}
