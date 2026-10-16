<?php

namespace App\Support;

use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanBumil;

/**
 * Satu sumber angka untuk dashboard Kader, Bidan, dan Admin supaya konsisten.
 *
 * Status gizi dihitung dari pemeriksaan TERAKHIR tiap balita / ibu hamil,
 * bukan seluruh riwayat bulanan (kode lama menghitung semua baris sehingga
 * satu anak bisa terhitung puluhan kali di grafik).
 */
class DashboardStats
{
    /**
     * @param int|null $posyanduId null = seluruh desa (Admin/Bidan), angka = satu posyandu (Kader)
     */
    public static function ringkasan(?int $posyanduId = null): array
    {
        $balita = self::balitaTerakhir($posyanduId);
        $bumil  = self::bumilTerakhir($posyanduId);

        $pendingBalita = PemeriksaanBalita::where('status_review', 'pending')
            ->when($posyanduId, fn ($q) => $q->whereHas('balita', fn ($b) => $b->where('posyandu_id', $posyanduId)))
            ->count();

        $pendingBumil = PemeriksaanBumil::where('status_review', 'pending')
            ->when($posyanduId, fn ($q) => $q->whereHas('ibuHamil', fn ($b) => $b->where('posyandu_id', $posyanduId)))
            ->count();

        // Definisi "perlu pendampingan" (sementara, bisa disesuaikan dengan Bidan):
        // balita: BB/U kurang-buruk ATAU TB/U pendek ATAU BB/TB kurus; ibu hamil: risiko KEK ATAU anemia.
        $perluBalita = (clone $balita)->where(function ($q) {
            $q->whereIn('status_bb_u', ['Gizi Buruk', 'Gizi Kurang'])
              ->orWhereIn('status_tb_u', ['Sangat Pendek', 'Pendek'])
              ->orWhereIn('status_bb_tb', ['Sangat Kurus', 'Kurus']);
        })->count();

        $perluBumil = (clone $bumil)->where(function ($q) {
            $q->where('status_kek', 'Risiko KEK')->orWhere('status_anemia', 'Anemia');
        })->count();

        return [
            'totalBalita'   => Balita::when($posyanduId, fn ($q) => $q->where('posyandu_id', $posyanduId))->count(),
            'totalIbuHamil' => IbuHamil::when($posyanduId, fn ($q) => $q->where('posyandu_id', $posyanduId))->count(),

            // Grafik balita (BB/U)
            'giziBaik'   => (clone $balita)->where('status_bb_u', 'Gizi Baik')->count(),
            'giziKurang' => (clone $balita)->where('status_bb_u', 'Gizi Kurang')->count(),
            'giziLebih'  => (clone $balita)->where('status_bb_u', 'Gizi Lebih')->count(),
            'giziBuruk'  => (clone $balita)->where('status_bb_u', 'Gizi Buruk')->count(),

            // Grafik ibu hamil
            'bumilNormal' => (clone $bumil)->where('status_kek', 'Tidak Risiko')->where('status_anemia', 'Tidak Anemia')->count(),
            'bumilKEK'    => (clone $bumil)->where('status_kek', 'Risiko KEK')->count(),
            'bumilAnemia' => (clone $bumil)->where('status_anemia', 'Anemia')->count(),

            // Kartu khusus Bidan & Admin
            'pendingBalita'        => $pendingBalita,
            'pendingBumil'         => $pendingBumil,
            'dataPending'          => $pendingBalita + $pendingBumil,
            'totalPerluPendampingan' => $perluBalita + $perluBumil,
        ];
    }

    private static function balitaTerakhir(?int $posyanduId)
    {
        return PemeriksaanBalita::query()
            ->whereRaw('pemeriksaan_balita.umur_bulan = (select max(pb.umur_bulan) from pemeriksaan_balita pb where pb.balita_id = pemeriksaan_balita.balita_id)')
            ->when($posyanduId, fn ($q) => $q->whereHas('balita', fn ($b) => $b->where('posyandu_id', $posyanduId)));
    }

    private static function bumilTerakhir(?int $posyanduId)
    {
        return PemeriksaanBumil::query()
            ->whereRaw('pemeriksaan_bumil.minggu_ke = (select max(pm.minggu_ke) from pemeriksaan_bumil pm where pm.ibu_hamil_id = pemeriksaan_bumil.ibu_hamil_id)')
            ->when($posyanduId, fn ($q) => $q->whereHas('ibuHamil', fn ($b) => $b->where('posyandu_id', $posyanduId)));
    }
}
