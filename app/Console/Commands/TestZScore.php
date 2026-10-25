<?php

namespace App\Console\Commands;

use App\Helpers\ZScoreHelper;
use App\Helpers\ZScoreDataset;
use Illuminate\Console\Command;

class TestZScore extends Command
{
    protected $signature   = 'petangbula:test-zscore';
    protected $description = 'Jalankan unit test ZScoreHelper sesuai Permenkes No. 2/2020';

    private int $pass = 0;
    private int $fail = 0;
    private array $failures = [];

    public function handle(): int
    {
        $this->info('🧪 Test ZScoreHelper & ZScoreDataset — Permenkes No. 2/2020');
        $this->line('');

        // ============================================================
        // CEK 1: Dataset bisa di-load
        // ============================================================
        $this->section('Dataset Loading');
        $this->assert('BB/U L, 0 bln',      ZScoreDataset::getBBU('L', 0),   [2.1, 2.5, 2.9, 3.3, 3.9, 4.4, 5.0]);
        $this->assert('BB/U P, 0 bln',      ZScoreDataset::getBBU('P', 0),   [2.0, 2.4, 2.8, 3.2, 3.7, 4.2, 4.8]);
        $this->assert('BB/U L, 24 bln',     ZScoreDataset::getBBU('L', 24),  [8.6, 9.7, 10.8, 12.2, 13.6, 15.3, 17.1]);
        $this->assert('TB/U L, 0 bln',      ZScoreDataset::getTBU('L', 0),   [44.2, 46.1, 48.0, 49.9, 51.8, 53.7, 55.6]);
        $this->assert('TB/U P, 0 bln',      ZScoreDataset::getTBU('P', 0),   [43.6, 45.4, 47.3, 49.1, 51.0, 52.9, 54.7]);
        $this->assert('BB/PB L, 60 cm',     ZScoreDataset::getBBTB('L', 60, 12), [4.7, 5.1, 5.5, 6.0, 6.5, 7.1, 7.8]);
        // FIX #1: correct Permenkes Tabel 5 row 100 = [12.1, 13.1, 14.2, 15.4, 16.7, 18.2, 19.9]
        $this->assert('BB/TB L, 100 cm',    ZScoreDataset::getBBTB('L', 100, 48), [12.1, 13.1, 14.2, 15.4, 16.7, 18.2, 19.9]);
        $this->assert('BB/TB P, 100 cm',    ZScoreDataset::getBBTB('P', 100, 48), [11.7, 12.8, 13.9, 15.2, 16.7, 18.3, 20.3]);

        // ============================================================
        // CEK 2: Formula Z-score (interpolasi piecewise)
        // ============================================================
        $this->section('Formula Z-Score (interpolasi)');

        $sd7 = ZScoreDataset::getBBU('L', 0);
        $this->assertApprox('BB/U L, 0 bln, BB=median (3.3) → z=0',
            ZScoreHelper::hitungZScore(3.3, $sd7), 0.0, 0.01);
        $this->assertApprox('BB/U L, 0 bln, BB=-3SD (2.1) → z=-3',
            ZScoreHelper::hitungZScore(2.1, $sd7), -3.0, 0.01);
        $this->assertApprox('BB/U L, 0 bln, BB=+3SD (5.0) → z=+3',
            ZScoreHelper::hitungZScore(5.0, $sd7), 3.0, 0.01);
        $this->assertApprox('BB/U L, 0 bln, BB=3.1 → z≈-0.5',
            ZScoreHelper::hitungZScore(3.1, $sd7), -0.5, 0.05);
        $this->assertTrue('BB/U L, 0 bln, BB=1.5 (di bawah -3SD) → z<-3',
            ZScoreHelper::hitungZScore(1.5, $sd7) < -3.0);

        // ============================================================
        // CEK 3: Kategori BB/U (4 kategori Permenkes)
        // ============================================================
        $this->section('Kategori BB/U');
        $this->assertEq('BB/U z=-3.5', ZScoreHelper::kategoriBBU(-3.5), 'Berat Badan Sangat Kurang');
        $this->assertEq('BB/U z=-3.0', ZScoreHelper::kategoriBBU(-3.0), 'Berat Badan Kurang');  // strict <
        $this->assertEq('BB/U z=-2.5', ZScoreHelper::kategoriBBU(-2.5), 'Berat Badan Kurang');
        $this->assertEq('BB/U z=0',    ZScoreHelper::kategoriBBU(0),    'Berat Badan Normal');
        $this->assertEq('BB/U z=1.0',  ZScoreHelper::kategoriBBU(1.0),  'Berat Badan Normal');
        $this->assertEq('BB/U z=1.5',  ZScoreHelper::kategoriBBU(1.5),  'Risiko Berat Badan Lebih');

        // ============================================================
        // CEK 4: Kategori TB/U (4 kategori)
        // ============================================================
        $this->section('Kategori TB/U');
        $this->assertEq('TB/U z=-3.5', ZScoreHelper::kategoriTBU(-3.5), 'Sangat Pendek');
        $this->assertEq('TB/U z=-3.0', ZScoreHelper::kategoriTBU(-3.0), 'Pendek');  // strict <
        $this->assertEq('TB/U z=-2.5', ZScoreHelper::kategoriTBU(-2.5), 'Pendek');
        $this->assertEq('TB/U z=0',    ZScoreHelper::kategoriTBU(0),    'Normal');
        $this->assertEq('TB/U z=3.0',  ZScoreHelper::kategoriTBU(3.0),  'Normal');
        $this->assertEq('TB/U z=3.5',  ZScoreHelper::kategoriTBU(3.5),  'Tinggi');

        // ============================================================
        // CEK 5: Kategori BB/TB (6 kategori)
        // ============================================================
        $this->section('Kategori BB/TB & IMT/U');
        $this->assertEq('BB/TB z=-3.5', ZScoreHelper::kategoriBBTB(-3.5), 'Gizi Buruk');
        $this->assertEq('BB/TB z=-3.0', ZScoreHelper::kategoriBBTB(-3.0), 'Gizi Kurang');  // strict <
        $this->assertEq('BB/TB z=-2.5', ZScoreHelper::kategoriBBTB(-2.5), 'Gizi Kurang');
        $this->assertEq('BB/TB z=0',    ZScoreHelper::kategoriBBTB(0),    'Gizi Baik');
        $this->assertEq('BB/TB z=1.5',  ZScoreHelper::kategoriBBTB(1.5),  'Berisiko Gizi Lebih');
        $this->assertEq('BB/TB z=2.5',  ZScoreHelper::kategoriBBTB(2.5),  'Gizi Lebih');
        $this->assertEq('BB/TB z=3.0',  ZScoreHelper::kategoriBBTB(3.0),  'Gizi Lebih');  // strict <
        $this->assertEq('BB/TB z=3.5',  ZScoreHelper::kategoriBBTB(3.5),  'Obesitas');

        // ============================================================
        // CEK 6: End-to-End Status
        // Menggunakan nilai mid-category (bukan boundary) supaya
        // tidak bergantung interpretasi < vs <=
        // ============================================================
        $this->section('End-to-End Status');

        // --- BB/U L, 0 bln (data: [2.1, 2.5, 2.9, 3.3, 3.9, 4.4, 5.0]) ---
        // BB=1.9 → z ≈ -3.5 → "Sangat Kurang"
        $this->assertEq('BB/U L, 0 bln, BB=1.9 (z≈-3.5)',
            ZScoreHelper::statusBBU('L', 0, 1.9), 'Berat Badan Sangat Kurang');
        // BB=2.3 → z ≈ -2.5 → "Kurang"
        $this->assertEq('BB/U L, 0 bln, BB=2.3 (z≈-2.5)',
            ZScoreHelper::statusBBU('L', 0, 2.3), 'Berat Badan Kurang');
        // BB=3.3 → z = 0 → "Normal"
        $this->assertEq('BB/U L, 0 bln, BB=3.3 (z=0)',
            ZScoreHelper::statusBBU('L', 0, 3.3), 'Berat Badan Normal');
        // BB=4.7 → z > +1 → "Risiko Lebih"
        $this->assertEq('BB/U L, 0 bln, BB=4.7 (z>1)',
            ZScoreHelper::statusBBU('L', 0, 4.7), 'Risiko Berat Badan Lebih');

        // --- TB/U L, 0 bln (data: [44.2, 46.1, 48.0, 49.9, 51.8, 53.7, 55.6]) ---
        // TB=43.0 → z < -3 → "Sangat Pendek"
        $this->assertEq('TB/U L, 0 bln, TB=43.0 (z<-3)',
            ZScoreHelper::statusTBU('L', 0, 43.0), 'Sangat Pendek');
        // TB=45.0 → -3 < z < -2 → "Pendek"
        $this->assertEq('TB/U L, 0 bln, TB=45.0 (z≈-2.6)',
            ZScoreHelper::statusTBU('L', 0, 45.0), 'Pendek');
        // TB=49.9 → z = 0 → "Normal"
        $this->assertEq('TB/U L, 0 bln, TB=49.9 (z=0)',
            ZScoreHelper::statusTBU('L', 0, 49.9), 'Normal');
        // TB=56.0 → z > +3 → "Tinggi"
        $this->assertEq('TB/U L, 0 bln, TB=56.0 (z>3)',
            ZScoreHelper::statusTBU('L', 0, 56.0), 'Tinggi');

        // --- BB/TB L, TB=60 (data: [4.7, 5.1, 5.5, 6.0, 6.5, 7.1, 7.8]) ---
        $this->assertEq('BB/TB L, TB=60, BB=4.3 (z<-3)',
            ZScoreHelper::statusBBTB('L', 60, 4.3, 12), 'Gizi Buruk');
        $this->assertEq('BB/TB L, TB=60, BB=4.9 (z≈-2.5)',
            ZScoreHelper::statusBBTB('L', 60, 4.9, 12), 'Gizi Kurang');
        $this->assertEq('BB/TB L, TB=60, BB=6.0 (z=0)',
            ZScoreHelper::statusBBTB('L', 60, 6.0, 12), 'Gizi Baik');
        $this->assertEq('BB/TB L, TB=60, BB=6.9 (z≈1.5)',
            ZScoreHelper::statusBBTB('L', 60, 6.9, 12), 'Berisiko Gizi Lebih');
        $this->assertEq('BB/TB L, TB=60, BB=7.4 (z≈2.5)',
            ZScoreHelper::statusBBTB('L', 60, 7.4, 12), 'Gizi Lebih');
        $this->assertEq('BB/TB L, TB=60, BB=8.1 (z>3)',
            ZScoreHelper::statusBBTB('L', 60, 8.1, 12), 'Obesitas');

        // --- BB/TB L, TB=100 (data: [12.1, 13.1, 14.2, 15.4, 16.7, 18.2, 19.9]) ---
        $this->assertEq('BB/TB L, TB=100, BB=11.5 (z<-3)',
            ZScoreHelper::statusBBTB('L', 100, 11.5, 48), 'Gizi Buruk');
        $this->assertEq('BB/TB L, TB=100, BB=15.4 (z=0)',
            ZScoreHelper::statusBBTB('L', 100, 15.4, 48), 'Gizi Baik');
        $this->assertEq('BB/TB L, TB=100, BB=20.5 (z>3)',
            ZScoreHelper::statusBBTB('L', 100, 20.5, 48), 'Obesitas');

        // --- BB/TB P, TB=100 (data: [11.7, 12.8, 13.9, 15.2, 16.7, 18.3, 20.3]) ---
        $this->assertEq('BB/TB P, TB=100, BB=15.2 (z=0)',
            ZScoreHelper::statusBBTB('P', 100, 15.2, 48), 'Gizi Baik');

        // ============================================================
        // RINGKASAN
        // ============================================================
        $this->line('');
        $this->line(str_repeat('─', 60));
        if ($this->fail === 0) {
            $this->info("✅ SEMUA TEST PASS ({$this->pass}/{$this->pass})");
            return self::SUCCESS;
        }
        $this->error("❌ {$this->fail} TEST GAGAL, {$this->pass} test lulus");
        $this->line('');
        $this->warn('Detail kegagalan:');
        foreach ($this->failures as $f) {
            $this->line("  • {$f}");
        }
        return self::FAILURE;
    }

    private function section(string $title): void
    {
        $this->line('');
        $this->line("<fg=cyan>▶ {$title}</>");
    }

    private function assert(string $label, $actual, array $expected): void
    {
        if ($actual === $expected) {
            $this->line("  <fg=green>✓</> {$label}");
            $this->pass++;
        } else {
            $this->line("  <fg=red>✗</> {$label}");
            $this->line("     Expected: " . json_encode($expected));
            $this->line("     Actual  : " . json_encode($actual));
            $this->fail++;
            $this->failures[] = $label;
        }
    }

    private function assertEq(string $label, $actual, $expected): void
    {
        if ($actual === $expected) {
            $this->line("  <fg=green>✓</> {$label} → {$actual}");
            $this->pass++;
        } else {
            $this->line("  <fg=red>✗</> {$label}");
            $this->line("     Expected: {$expected}");
            $this->line("     Actual  : {$actual}");
            $this->fail++;
            $this->failures[] = "{$label} (expected={$expected}, actual={$actual})";
        }
    }

    private function assertApprox(string $label, float $actual, float $expected, float $tol): void
    {
        if (abs($actual - $expected) <= $tol) {
            $this->line("  <fg=green>✓</> {$label} → {$actual}");
            $this->pass++;
        } else {
            $this->line("  <fg=red>✗</> {$label}");
            $this->line("     Expected: {$expected} ± {$tol}");
            $this->line("     Actual  : {$actual}");
            $this->fail++;
            $this->failures[] = "{$label} (expected={$expected}±{$tol}, actual={$actual})";
        }
    }

    private function assertTrue(string $label, bool $cond): void
    {
        if ($cond) {
            $this->line("  <fg=green>✓</> {$label}");
            $this->pass++;
        } else {
            $this->line("  <fg=red>✗</> {$label}");
            $this->fail++;
            $this->failures[] = $label;
        }
    }
}