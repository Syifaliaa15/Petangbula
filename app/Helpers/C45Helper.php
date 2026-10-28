<?php

namespace App\Helpers;

class C45Helper
{
    /**
     * Menentukan 3 Rekomendasi Tindakan untuk BALITA
     * Mengacu pada Matriks Prioritas 28 Kombinasi (Dataset Excel)
     * 
     * @param string $statusUtama (Gizi Buruk, BB Sangat Kurang, Gizi Kurang, BB Kurang, Sangat Pendek, Pendek, Obesitas)
     * @param float $lila
     * @param int $umurBulan
     * @return array ['prioritas_1', 'prioritas_2', 'prioritas_3']
     */
    public static function getRekomendasiBalita($statusUtama, $lila, $umurBulan)
    {
        // Tentukan Kategori LiLA (Berdasarkan dataset: < 12.5 cm = Kurang, >= 12.5 cm = Cukup)
        $kategoriLila = ($lila < 12.5) ? 'Kurang' : 'Cukup';
        $kelUmur = ($umurBulan < 24) ? '<24' : '>=24';

        // 1. GIZI BURUK
        if ($statusUtama == 'Gizi Buruk') {
            return [
                'prioritas_1' => 'Rujuk Puskesmas/RS',
                'prioritas_2' => 'Program PMT',
                'prioritas_3' => ($kategoriLila == 'Kurang' || $kelUmur == '<24') ? 'Pendampingan Bidan' : 'Konseling Gizi',
            ];
        }

        // 2. BB SANGAT KURANG
        if ($statusUtama == 'Berat Badan Sangat Kurang' || $statusUtama == 'BB Sangat Kurang') {
            return [
                'prioritas_1' => 'Rujuk Puskesmas/RS',
                'prioritas_2' => 'Program PMT',
                'prioritas_3' => ($kategoriLila == 'Kurang' || $kelUmur == '<24') ? 'Pendampingan Bidan' : 'Konseling Gizi',
            ];
        }

        // 3. GIZI KURANG (Wasting)
        if ($statusUtama == 'Gizi Kurang') {
            return [
                'prioritas_1' => 'Program PMT',
                'prioritas_2' => ($kategoriLila == 'Kurang') ? 'Pendampingan Bidan' : 'Konseling Gizi',
                'prioritas_3' => ($kelUmur == '<24') ? (($kategoriLila == 'Kurang') ? 'Konseling Gizi' : 'Pendampingan Bidan') : 'Pemantauan Rutin (Home Visit)',
            ];
        }

        // 4. BB KURANG (Underweight)
        if ($statusUtama == 'Berat Badan Kurang' || $statusUtama == 'BB Kurang') {
            if ($kategoriLila == 'Kurang') {
                return [
                    'prioritas_1' => 'Program PMT',
                    'prioritas_2' => 'Pendampingan Bidan',
                    'prioritas_3' => ($kelUmur == '<24') ? 'Konseling Gizi' : 'Pemantauan Rutin (Home Visit)',
                ];
            } else {
                return [
                    'prioritas_1' => ($kelUmur == '<24') ? 'Konseling Gizi' : 'Pemantauan Rutin (Home Visit)',
                    'prioritas_2' => ($kelUmur == '<24') ? 'Pendampingan Bidan' : 'Konseling Gizi',
                    'prioritas_3' => 'Pemantauan Rutin (Home Visit)',
                ];
            }
        }

        // 5. SANGAT PENDEK (Stunting Berat)
        if ($statusUtama == 'Sangat Pendek') {
            return [
                'prioritas_1' => ($kategoriLila == 'Kurang') ? 'Rujuk Puskesmas/RS' : 'Program PMT',
                'prioritas_2' => ($kategoriLila == 'Kurang') ? 'Program PMT' : 'Rujuk Puskesmas/RS',
                'prioritas_3' => ($kategoriLila == 'Kurang') ? 'Pendampingan Bidan' : 'Konseling Gizi',
            ];
        }

        // 6. PENDEK (Stunting Ringan)
        if ($statusUtama == 'Pendek') {
            if ($kategoriLila == 'Kurang') {
                return [
                    'prioritas_1' => 'Program PMT',
                    'prioritas_2' => 'Pendampingan Bidan',
                    'prioritas_3' => ($kelUmur == '<24') ? 'Konseling Gizi' : 'Pemantauan Rutin (Home Visit)',
                ];
            } else {
                return [
                    'prioritas_1' => ($kelUmur == '<24') ? 'Konseling Gizi' : 'Pemantauan Rutin (Home Visit)',
                    'prioritas_2' => 'Pendampingan Bidan',
                    'prioritas_3' => ($kelUmur == '<24') ? 'Pemantauan Rutin (Home Visit)' : 'Konseling Gizi',
                ];
            }
        }

        // 7. OBESITAS
        if ($statusUtama == 'Obesitas') {
            return [
                'prioritas_1' => ($kategoriLila == 'Kurang') ? 'Rujuk Puskesmas/RS' : 'Konseling Gizi',
                'prioritas_2' => ($kategoriLila == 'Kurang') ? 'Konseling Gizi' : 'Pendampingan Bidan',
                'prioritas_3' => ($kategoriLila == 'Kurang') ? 'Pendampingan Bidan' : 'Pemantauan Rutin (Home Visit)',
            ];
        }

        // Default jika status tidak terdeteksi
        return [
            'prioritas_1' => 'Pemantauan Rutin (Home Visit)',
            'prioritas_2' => 'Konseling Gizi',
            'prioritas_3' => 'Pendampingan Bidan',
        ];
    }

    /**
     * Menentukan 3 Rekomendasi Tindakan untuk IBU HAMIL
     * Mengacu pada Matriks Prioritas 8 Kombinasi (Dataset Excel)
     * 
     * @param string $kek (Ya / Tidak)
     * @param string $anemia (Ya / Tidak)
     * @param string $hipertensi (Ya / Tidak)
     * @return array ['prioritas_1', 'prioritas_2', 'prioritas_3']
     */
    public static function getRekomendasiBumil($kek, $anemia, $hipertensi)
    {
        // Konversi ke boolean untuk mempermudah logika
        $isKek = ($kek == 'Ya' || $kek == 'Risiko KEK');
        $isAnemia = ($anemia == 'Ya' || $anemia == 'Anemia');
        $isHipertensi = ($hipertensi == 'Ya' || $hipertensi == 'Hipertensi');

        // 1. KEK: Y, Anemia: Y, Hipertensi: Y (Risiko Tinggi)
        if ($isKek && $isAnemia && $isHipertensi) {
            return [
                'prioritas_1' => 'Rujuk Puskesmas/RS',
                'prioritas_2' => 'Pendampingan Bidan',
                'prioritas_3' => 'Program PMT',
            ];
        }

        // 2. Hipertensi + (KEK atau Anemia)
        if ($isHipertensi && ($isKek || $isAnemia)) {
            return [
                'prioritas_1' => 'Rujuk Puskesmas/RS',
                'prioritas_2' => ($isKek) ? 'Program PMT' : 'Pendampingan Bidan',
                'prioritas_3' => ($isKek) ? 'Pendampingan Bidan' : 'Konseling Gizi',
            ];
        }

        // 3. Hipertensi Saja
        if ($isHipertensi) {
            return [
                'prioritas_1' => 'Rujuk Puskesmas/RS',
                'prioritas_2' => 'Pendampingan Bidan',
                'prioritas_3' => 'Konseling Gizi',
            ];
        }

        // 4. KEK + Anemia (Tanpa Hipertensi)
        if ($isKek && $isAnemia) {
            return [
                'prioritas_1' => 'Program PMT',
                'prioritas_2' => 'Pendampingan Bidan',
                'prioritas_3' => 'Konseling Gizi',
            ];
        }

        // 5. KEK Saja
        if ($isKek) {
            return [
                'prioritas_1' => 'Program PMT',
                'prioritas_2' => 'Konseling Gizi',
                'prioritas_3' => 'Pendampingan Bidan',
            ];
        }

        // 6. Anemia Saja
        if ($isAnemia) {
            return [
                'prioritas_1' => 'Konseling Gizi',
                'prioritas_2' => 'Pendampingan Bidan',
                'prioritas_3' => 'Pemantauan Rutin (Home Visit)',
            ];
        }

        // 7. Normal (Tidak ada indikator negatif)
        return [
            'prioritas_1' => 'Pemantauan Rutin (Home Visit)',
            'prioritas_2' => 'Konseling Gizi',
            'prioritas_3' => 'Pendampingan Bidan',
        ];
    }
}