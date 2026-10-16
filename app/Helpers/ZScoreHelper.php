<?php

namespace App\Helpers;

class ZScoreHelper
{
    // ============================================
    // PERHITUNGAN STATUS GIZI BALITA
    // ============================================

    public static function statusBBU($gender, $umurBulan, $bb)
    {
        $standar = self::getDatasetBBU($gender, $umurBulan);
        if (!$standar) return 'Tidak Diketahui';

        if ($bb < $standar['minus3sd']) return 'Gizi Buruk';
        if ($bb < $standar['minus2sd']) return 'Gizi Kurang';
        if ($bb <= $standar['plus2sd']) return 'Gizi Baik';
        return 'Gizi Lebih';
    }

    public static function statusTBU($gender, $umurBulan, $tb)
    {
        $standar = self::getDatasetTBU($gender, $umurBulan);
        if (!$standar) return 'Tidak Diketahui';

        if ($tb < $standar['minus3sd']) return 'Sangat Pendek';
        if ($tb < $standar['minus2sd']) return 'Pendek';
        if ($tb <= $standar['plus2sd']) return 'Normal';
        return 'Tinggi';
    }

    public static function statusBBTB($gender, $tb, $bb)
    {
        $standar = self::getDatasetBBTB($gender, $tb);
        if (!$standar) return 'Tidak Diketahui';

        if ($bb < $standar['minus3sd']) return 'Sangat Kurus';
        if ($bb < $standar['minus2sd']) return 'Kurus';
        if ($bb <= $standar['plus2sd']) return 'Normal';
        return 'Gemuk';
    }

    // ============================================
    // PERHITUNGAN STATUS IBU HAMIL
    // ============================================

    public static function statusKEK($lila)
    {
        if (!$lila) return 'Tidak Diketahui';
        return $lila < 23.5 ? 'Risiko KEK' : 'Tidak Risiko';
    }

    public static function statusAnemia($hemoglobin)
    {
        if (!$hemoglobin) return 'Tidak Diketahui';
        return $hemoglobin < 11.0 ? 'Anemia' : 'Tidak Anemia';
    }

    public static function statusTD($tekananDarah)
    {
        if (!$tekananDarah) return 'Tidak Diketahui';

        $parts = explode('/', $tekananDarah);
        if (count($parts) !== 2) return 'Tidak Diketahui';

        $sistolik  = (int) $parts[0];
        $diastolik = (int) $parts[1];

        if ($sistolik >= 140 || $diastolik >= 90) return 'Hipertensi';
        if ($sistolik < 90 || $diastolik < 60)    return 'Hipotensi';
        return 'Normal';
    }

    // ============================================
    // WARNA BADGE STATUS
    // ============================================

    public static function getColorForStatus($status)
    {
        if (!$status) return '#f3f4f6';
        $s = strtolower($status);

        // HIJAU — Baik / Normal
        if (str_contains($s, 'baik') || str_contains($s, 'normal')
            || str_contains($s, 'tidak risiko') || str_contains($s, 'tidak anemia')) {
            return '#d1fae5';
        }
        // KUNING — Kurang / Waspada
        if (str_contains($s, 'kurang') || str_contains($s, 'pendek')
            || str_contains($s, 'kurus') || str_contains($s, 'risiko')) {
            return '#fef3c7';
        }
        // MERAH — Buruk / Bahaya
        if (str_contains($s, 'buruk') || str_contains($s, 'sangat')
            || str_contains($s, 'anemia') || str_contains($s, 'hipertensi')) {
            return '#fee2e2';
        }
        // BIRU — Lebih / Tinggi
        if (str_contains($s, 'lebih') || str_contains($s, 'gemuk')
            || str_contains($s, 'tinggi')) {
            return '#dbeafe';
        }
        return '#f3f4f6';
    }

    // ============================================
    // DATASET BB/U (Berat Badan menurut Umur)
    // Format: [minus3sd, minus2sd, median, plus2sd]
    // ============================================

    private static function getDatasetBBU($gender, $umurBulan)
    {
        $data = [
            0  => ['L' => [2.1, 2.5, 3.3, 4.4],   'P' => [2.0, 2.4, 3.2, 4.2]],
            1  => ['L' => [2.9, 3.4, 4.5, 5.8],   'P' => [2.8, 3.2, 4.2, 5.5]],
            2  => ['L' => [3.9, 4.4, 5.6, 7.1],   'P' => [3.6, 4.1, 5.1, 6.6]],
            3  => ['L' => [4.7, 5.2, 6.4, 8.0],   'P' => [4.3, 4.8, 5.8, 7.5]],
            4  => ['L' => [5.4, 5.9, 7.0, 8.7],   'P' => [5.0, 5.4, 6.4, 8.2]],
            5  => ['L' => [5.9, 6.4, 7.5, 9.3],   'P' => [5.4, 5.8, 6.9, 8.8]],
            6  => ['L' => [6.4, 6.9, 8.0, 9.8],   'P' => [5.8, 6.3, 7.3, 9.3]],
            7  => ['L' => [6.8, 7.3, 8.4, 10.3],  'P' => [6.1, 6.6, 7.7, 9.8]],
            8  => ['L' => [7.1, 7.6, 8.8, 10.8],  'P' => [6.4, 6.9, 8.0, 10.2]],
            9  => ['L' => [7.4, 7.9, 9.1, 11.2],  'P' => [6.7, 7.2, 8.3, 10.6]],
            10 => ['L' => [7.6, 8.2, 9.4, 11.5],  'P' => [6.9, 7.4, 8.6, 10.9]],
            11 => ['L' => [7.8, 8.4, 9.6, 11.8],  'P' => [7.1, 7.6, 8.8, 11.2]],
            12 => ['L' => [8.0, 8.6, 9.9, 12.1],  'P' => [7.3, 7.8, 9.0, 11.5]],
            13 => ['L' => [8.2, 8.8, 10.1, 12.4], 'P' => [7.4, 8.0, 9.2, 11.7]],
            14 => ['L' => [8.4, 9.0, 10.3, 12.6], 'P' => [7.6, 8.2, 9.4, 12.0]],
            15 => ['L' => [8.5, 9.2, 10.5, 12.9], 'P' => [7.8, 8.3, 9.6, 12.2]],
            16 => ['L' => [8.7, 9.3, 10.7, 13.1], 'P' => [7.9, 8.5, 9.8, 12.5]],
            17 => ['L' => [8.9, 9.5, 10.9, 13.4], 'P' => [8.1, 8.7, 10.0, 12.7]],
            18 => ['L' => [9.0, 9.7, 11.1, 13.6], 'P' => [8.2, 8.8, 10.2, 13.0]],
            19 => ['L' => [9.2, 9.8, 11.3, 13.9], 'P' => [8.4, 9.0, 10.4, 13.2]],
            20 => ['L' => [9.3, 10.0, 11.5, 14.1],'P' => [8.5, 9.2, 10.6, 13.5]],
            21 => ['L' => [9.5, 10.2, 11.7, 14.4],'P' => [8.7, 9.3, 10.8, 13.7]],
            22 => ['L' => [9.6, 10.3, 11.9, 14.6],'P' => [8.8, 9.5, 11.0, 14.0]],
            23 => ['L' => [9.8, 10.5, 12.1, 14.9],'P' => [9.0, 9.6, 11.2, 14.2]],
            24 => ['L' => [9.9, 10.7, 12.3, 15.1],'P' => [9.1, 9.8, 11.4, 14.5]],
            30 => ['L' => [10.5, 11.3, 13.1, 16.2],'P' => [9.7, 10.5, 12.2, 15.4]],
            36 => ['L' => [11.1, 12.0, 13.9, 17.3],'P' => [10.3, 11.2, 13.1, 16.4]],
            42 => ['L' => [11.7, 12.7, 14.7, 18.3],'P' => [10.9, 11.9, 13.9, 17.4]],
            48 => ['L' => [12.3, 13.4, 15.5, 19.4],'P' => [11.5, 12.6, 14.8, 18.5]],
            54 => ['L' => [12.9, 14.1, 16.3, 20.4],'P' => [12.1, 13.3, 15.6, 19.5]],
            59 => ['L' => [13.5, 14.7, 17.1, 21.4],'P' => [12.7, 14.0, 16.4, 20.5]],
        ];
        $key = self::getClosestKey($data, $umurBulan);
        if (!isset($data[$key][$gender])) return null;
        $v = $data[$key][$gender];
        return ['minus3sd' => $v[0], 'minus2sd' => $v[1], 'median' => $v[2], 'plus2sd' => $v[3]];
    }

    // ============================================
    // DATASET TB/U (Tinggi Badan menurut Umur)
    // ============================================

    private static function getDatasetTBU($gender, $umurBulan)
    {
        $data = [
            0  => ['L' => [44.2, 46.1, 49.9, 53.7],  'P' => [43.6, 45.4, 49.1, 52.9]],
            1  => ['L' => [48.9, 50.8, 54.7, 58.6],  'P' => [47.8, 49.8, 53.7, 57.6]],
            2  => ['L' => [52.4, 54.4, 58.4, 62.4],  'P' => [51.0, 53.0, 57.1, 61.1]],
            3  => ['L' => [55.3, 57.3, 61.4, 65.5],  'P' => [53.5, 55.6, 59.8, 64.0]],
            4  => ['L' => [57.6, 59.7, 63.9, 68.0],  'P' => [55.6, 57.8, 62.1, 66.4]],
            5  => ['L' => [59.6, 61.7, 65.9, 70.1],  'P' => [57.4, 59.6, 64.0, 68.2]],
            6  => ['L' => [61.2, 63.3, 67.6, 71.9],  'P' => [59.0, 61.2, 65.7, 70.0]],
            7  => ['L' => [62.7, 64.8, 69.2, 73.5],  'P' => [60.3, 62.6, 67.1, 71.5]],
            8  => ['L' => [64.0, 66.2, 70.6, 75.0],  'P' => [61.7, 64.0, 68.5, 73.0]],
            9  => ['L' => [65.3, 67.5, 72.0, 76.5],  'P' => [62.9, 65.3, 69.9, 74.4]],
            10 => ['L' => [66.5, 68.7, 73.3, 77.9],  'P' => [64.1, 66.5, 71.2, 75.8]],
            11 => ['L' => [67.6, 69.9, 74.5, 79.2],  'P' => [65.2, 67.7, 72.4, 77.1]],
            12 => ['L' => [68.7, 71.0, 75.7, 80.5],  'P' => [66.3, 68.8, 73.6, 78.4]],
            13 => ['L' => [69.8, 72.1, 76.9, 81.8],  'P' => [67.3, 69.9, 74.8, 79.7]],
            14 => ['L' => [70.8, 73.2, 78.1, 83.0],  'P' => [68.3, 70.9, 76.0, 81.0]],
            15 => ['L' => [71.8, 74.2, 79.2, 84.2],  'P' => [69.3, 71.9, 77.1, 82.3]],
            16 => ['L' => [72.8, 75.2, 80.3, 85.4],  'P' => [70.2, 72.9, 78.2, 83.5]],
            17 => ['L' => [73.7, 76.2, 81.4, 86.5],  'P' => [71.1, 73.8, 79.3, 84.7]],
            18 => ['L' => [74.7, 77.2, 82.5, 87.7],  'P' => [72.0, 74.7, 80.4, 85.9]],
            19 => ['L' => [75.6, 78.1, 83.5, 88.9],  'P' => [72.8, 75.6, 81.4, 87.1]],
            20 => ['L' => [76.5, 79.0, 84.6, 90.1],  'P' => [73.7, 76.4, 82.4, 88.3]],
            21 => ['L' => [77.4, 79.9, 85.6, 91.3],  'P' => [74.5, 77.3, 83.4, 89.5]],
            22 => ['L' => [78.3, 80.8, 86.7, 92.5],  'P' => [75.3, 78.1, 84.4, 90.7]],
            23 => ['L' => [79.1, 81.7, 87.7, 93.7],  'P' => [76.1, 79.0, 85.4, 91.9]],
            24 => ['L' => [80.0, 82.6, 88.7, 94.9],  'P' => [76.9, 79.8, 86.4, 93.0]],
            30 => ['L' => [85.0, 87.7, 93.9, 100.1], 'P' => [81.8, 84.7, 91.2, 97.7]],
            36 => ['L' => [89.5, 92.4, 98.7, 105.0], 'P' => [86.2, 89.2, 95.6, 102.0]],
            42 => ['L' => [93.7, 96.7, 103.3, 109.8],'P' => [90.2, 93.3, 99.9, 106.5]],
            48 => ['L' => [97.5, 100.7, 107.5, 114.2],'P'=> [93.8, 97.1, 103.9, 110.7]],
            54 => ['L' => [101.0, 104.4, 111.4, 118.4],'P'=>[97.2, 100.6, 107.7, 114.8]],
            59 => ['L' => [104.2, 107.7, 114.9, 122.0],'P'=>[100.3, 103.8, 111.1, 118.4]],
        ];
        $key = self::getClosestKey($data, $umurBulan);
        if (!isset($data[$key][$gender])) return null;
        $v = $data[$key][$gender];
        return ['minus3sd' => $v[0], 'minus2sd' => $v[1], 'median' => $v[2], 'plus2sd' => $v[3]];
    }

    // ============================================
    // DATASET BB/TB (Berat Badan menurut Tinggi)
    // ============================================

    private static function getDatasetBBTB($gender, $tb)
    {
        $data = [
            45  => ['L' => [1.8, 2.1, 2.8, 3.7],   'P' => [1.7, 2.0, 2.7, 3.6]],
            50  => ['L' => [2.3, 2.7, 3.5, 4.6],   'P' => [2.2, 2.6, 3.4, 4.4]],
            55  => ['L' => [2.9, 3.4, 4.3, 5.6],   'P' => [2.8, 3.2, 4.1, 5.3]],
            60  => ['L' => [3.6, 4.1, 5.2, 6.7],   'P' => [3.4, 3.9, 4.9, 6.3]],
            65  => ['L' => [4.3, 4.9, 6.1, 7.9],   'P' => [4.1, 4.6, 5.8, 7.4]],
            70  => ['L' => [5.0, 5.7, 7.1, 9.1],   'P' => [4.8, 5.4, 6.7, 8.6]],
            75  => ['L' => [5.8, 6.5, 8.1, 10.4],  'P' => [5.5, 6.2, 7.7, 9.9]],
            80  => ['L' => [6.6, 7.4, 9.2, 11.8],  'P' => [6.3, 7.0, 8.7, 11.2]],
            85  => ['L' => [7.4, 8.3, 10.3, 13.2], 'P' => [7.1, 7.9, 9.8, 12.6]],
            90  => ['L' => [8.3, 9.2, 11.5, 14.7], 'P' => [7.9, 8.8, 10.9, 14.0]],
            95  => ['L' => [9.2, 10.2, 12.7, 16.3],'P' => [8.8, 9.7, 12.1, 15.5]],
            100 => ['L' => [10.1, 11.2, 14.0, 18.0],'P'=> [9.7, 10.7, 13.3, 17.0]],
            105 => ['L' => [11.1, 12.3, 15.3, 19.7],'P'=> [10.6, 11.7, 14.5, 18.6]],
            110 => ['L' => [12.1, 13.4, 16.7, 21.5],'P'=> [11.5, 12.8, 15.9, 20.3]],
            115 => ['L' => [13.1, 14.6, 18.2, 23.4],'P'=> [12.5, 13.9, 17.3, 22.1]],
            120 => ['L' => [14.2, 15.8, 19.7, 25.4],'P'=> [13.5, 15.0, 18.8, 24.0]],
        ];
        $key = self::getClosestKey($data, $tb);
        if (!isset($data[$key][$gender])) return null;
        $v = $data[$key][$gender];
        return ['minus3sd' => $v[0], 'minus2sd' => $v[1], 'median' => $v[2], 'plus2sd' => $v[3]];
    }

    // ============================================
    // UTILITY: Cari kunci terdekat
    // ============================================

    private static function getClosestKey($data, $value)
    {
        $keys    = array_keys($data);
        $closest = $keys[0];
        $minDiff = abs($value - $closest);

        foreach ($keys as $key) {
            $diff = abs($value - $key);
            if ($diff < $minDiff) {
                $minDiff = $diff;
                $closest = $key;
            }
        }
        return $closest;
    }
}