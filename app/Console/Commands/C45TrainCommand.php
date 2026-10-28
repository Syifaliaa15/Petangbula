<?php

namespace App\Console\Commands;

use App\Services\C45\C45DataProvider;
use App\Services\C45\C45ModelRepository;
use App\Services\C45\C45TrainingService;
use Illuminate\Console\Command;

/**
 * Melatih model C4.5 dari data latih dan menyimpan pohon keputusan sebagai
 * JSON (tabel c45_models). Contoh:
 *
 *   php artisan c45:train balita
 *   php artisan c45:train balita --target=Prioritas_2
 *   php artisan c45:train bumil  --evaluate      (plus 10-fold CV)
 */
class C45TrainCommand extends Command
{
    protected $signature = 'c45:train
        {model : balita | bumil}
        {--target=Prioritas_1}
        {--evaluate : jalankan juga validasi silang 10-fold}';

    protected $description = 'Melatih model C4.5 (entropy/gain ratio -> pohon) dan menyimpannya sebagai JSON';

    public function handle(C45TrainingService $svc): int
    {
        $nama   = strtolower(trim($this->argument('model')));
        $target = (string) $this->option('target');
        C45DataProvider::definisi($nama); // validasi nama model

        $this->info("Melatih model [{$nama}] target {$target} ...");

        $t0    = microtime(true);
        $m     = $svc->latih($nama, $target, (bool) $this->option('evaluate'));
        $detik = round(microtime(true) - $t0, 3);

        $akar = isset($m->tree['pohon']['atribut']) ? $m->tree['pohon']['atribut'] : '-';
        $this->info("Selesai dalam {$detik} dtk.");
        $this->line("  Versi            : {$m->versi}");
        $this->line("  Data latih       : {$m->jumlah_data} baris");
        $this->line("  Akurasi latih    : {$m->akurasi_latih}%");
        $this->line("  Akar pohon       : {$akar}");
        if (isset($m->tree['pohon']['gain_ratio'])) {
            $this->line("  GainRatio akar   : {$m->tree['pohon']['gain_ratio']}");
        }
        if (isset($m->metrik['jumlah_aturan'])) {
            $this->line("  Aturan IF-THEN   : {$m->metrik['jumlah_aturan']}");
        }
        if (isset($m->metrik['cv10']['akurasi_rata2'])) {
            $this->line("  Akurasi 10-fold  : {$m->metrik['cv10']['akurasi_rata2']}%");
        }

        // Contoh prediksi cepat untuk verifikasi
        $contoh = $nama === 'balita'
            ? array(
                'Tren_BB' => 'Turun', 'Kelompok_Usia' => '< 24 bulan',
                'Status_Gizi' => 'Gizi Buruk', 'LILA' => 'Kurang',
            )
            : array(
                'Trimester' => 'Trimester 3', 'LILA' => 'Normal',
                'Hb' => 'Anemia', 'Tensi' => 'Hipertensi',
            );
        $hasil = app(C45ModelRepository::class)->prediksi($nama, $contoh, $target);
        if ($hasil !== null) {
            $this->line('  Contoh prediksi  : ' . $hasil['kelas']
                . '   [' . implode(' -> ', $hasil['jalur']) . ']');
        }

        return self::SUCCESS;
    }
}
