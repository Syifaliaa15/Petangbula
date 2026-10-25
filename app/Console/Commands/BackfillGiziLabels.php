<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class BackfillGiziLabels extends Command
{
    /**
     * Nama command untuk dipanggil di CLI.
     */
    protected $signature = 'petangbula:backfill-labels
                            {--dry-run : Preview perubahan tanpa benar-benar mengubah DB}
                            {--force   : Lewati konfirmasi interaktif (untuk CI/CD)}';

    protected $description = 'Backfill label status gizi lama ke label baru sesuai Permenkes No. 2/2020';

    /**
     * Pemetaan label lama → label baru.
     * Tambahkan entri baru jika ada label lain yang perlu dimigrasikan.
     */
    private const MAP_BBU = [
        // [kolom, label_lama, label_baru]
        ['status_bb_u',  'Gizi Baik',   'Berat Badan Normal'],
        ['status_bb_u',  'Gizi Lebih',  'Risiko Berat Badan Lebih'],
        ['status_bb_u',  'Gizi Kurang', 'Berat Badan Kurang'],
        ['status_bb_u',  'Gizi Buruk',  'Berat Badan Sangat Kurang'],
        // legacy column
        ['status_gizi',  'Gizi Baik',   'Berat Badan Normal'],
        ['status_gizi',  'Gizi Lebih',  'Risiko Berat Badan Lebih'],
        ['status_gizi',  'Gizi Kurang', 'Berat Badan Kurang'],
        ['status_gizi',  'Gizi Buruk',  'Berat Badan Sangat Kurang'],
    ];

    private const MAP_BBTB = [
        ['status_bb_tb', 'Sangat Kurus', 'Gizi Buruk'],
        ['status_bb_tb', 'Kurus',        'Gizi Kurang'],
        ['status_bb_tb', 'Normal',       'Gizi Baik'],
        ['status_bb_tb', 'Gemuk',        'Gizi Lebih'],
    ];

    public function handle(): int
    {
        $this->info('🔄 Backfill Label Status Gizi — Permenkes No. 2 Tahun 2020');
        $this->line('');

        if (!Schema::hasTable('pemeriksaan_balita')) {
            $this->error('❌ Tabel pemeriksaan_balita tidak ditemukan.');
            return self::FAILURE;
        }

        $totalRows = DB::table('pemeriksaan_balita')->count();
        $this->line("📊 Total baris: <comment>{$totalRows}</comment>");

        // ---- PREVIEW: hitung dampak perubahan ----
        $preview = [];
        foreach (array_merge(self::MAP_BBU, self::MAP_BBTB) as $map) {
            [$col, $old, $new] = $map;
            $count = DB::table('pemeriksaan_balita')->where($col, $old)->count();
            if ($count > 0) {
                $preview[] = [$col, $old, $new, $count];
            }
        }

        if (empty($preview)) {
            $this->info('✅ Tidak ada label lama yang perlu diubah. Data sudah sesuai.');
            return self::SUCCESS;
        }

        $this->line('');
        $this->info('📋 Rencana perubahan:');
        $this->table(
            ['Kolom', 'Label Lama', 'Label Baru', 'Jumlah Baris'],
            $preview
        );

        // ---- DRY RUN ----
        if ($this->option('dry-run')) {
            $this->line('');
            $this->warn('⚠️  Mode --dry-run aktif. Tidak ada perubahan yang disimpan.');
            return self::SUCCESS;
        }

        // ---- KONFIRMASI (kecuali --force) ----
        if (!$this->option('force') && !$this->confirm('Lanjutkan perubahan ini?', true)) {
            $this->warn('❌ Dibatalkan oleh pengguna.');
            return self::FAILURE;
        }

        // ---- EKSEKUSI DALAM TRANSACTION ----
        $totalUpdated = 0;
        $startTime = microtime(true);

        try {
            DB::beginTransaction();

            foreach (array_merge(self::MAP_BBU, self::MAP_BBTB) as $map) {
                [$col, $old, $new] = $map;
                $updated = DB::table('pemeriksaan_balita')
                    ->where($col, $old)
                    ->update([$col => $new]);
                $totalUpdated += $updated;
            }

            DB::commit();

            $duration = round(microtime(true) - $startTime, 2);
            $this->line('');
            $this->info("✅ Selesai! {$totalUpdated} baris berhasil diperbarui dalam {$duration} detik.");

            Log::info('BackfillGiziLabels: sukses', [
                'total_rows_updated' => $totalUpdated,
                'duration_seconds'   => $duration,
                'user'               => function_exists('posix_getpwuid')
                    ? (posix_getpwuid(posix_geteuid())['name'] ?? 'cli')
                    : 'cli',
            ]);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('❌ Gagal: ' . $e->getMessage());
            Log::error('BackfillGiziLabels: gagal', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return self::FAILURE;
        }
    }
}