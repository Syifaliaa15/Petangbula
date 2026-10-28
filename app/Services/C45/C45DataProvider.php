<?php

namespace App\Services\C45;

/**
 * Definisi & sumber data latih untuk tiap model.
 *
 * Dua mode (pilih salah satu di muat()):
 *  (A) CSV di storage/app/c45/  -> bawaan, langsung jalan dgn dataset Excel
 *      (salin training_balita.csv & training_bumil.csv ke folder tsb)
 *  (B) Tabel pengukuran aplikasi -> lihat barisDariDatabase() di bawah,
 *      aktifkan setelah modul Z-Score Anda menyimpan hasil diskretisasi.
 */
class C45DataProvider
{
    /** @return array{nama:string, atribut:string[], target:string[], csv:string} */
    public static function definisi(string $nama): array
    {
        $nama = strtolower(trim($nama));

        if ($nama === 'balita') {
            return array(
                'nama'    => 'balita',
                'atribut' => array('Tren_BB', 'Kelompok_Usia', 'Status_Gizi', 'LILA'),
                'target'  => array('Prioritas_1', 'Prioritas_2', 'Prioritas_3'),
                'csv'     => 'training_balita.csv',
            );
        }

        if ($nama === 'bumil') {
            return array(
                'nama'    => 'bumil',
                'atribut' => array('Trimester', 'LILA', 'Hb', 'Tensi'),
                'target'  => array('Prioritas_1', 'Prioritas_2', 'Prioritas_3'),
                'csv'     => 'training_bumil.csv',
            );
        }

        throw new \InvalidArgumentException("Model tidak dikenal: '{$nama}' (gunakan 'balita' atau 'bumil').");
    }

    /**
     * Muat baris data latih sebagai array of assoc (nilai string/kategorik).
     * Kolom numerik (ZScore, LILA_cm, Hb_gdl, Sistolik, Diastolik, Usia_Bulan)
     * sengaja TIDAK dipakai C4.5 — mereka dipakai di hulu (gerbang Z-Score,
     * diskretisasi Status_Gizi/LILA/Hb/Tensi) sebelum masuk sini.
     */
    public static function muat(string $nama): array
    {
        $def  = self::definisi($nama);
        $path = storage_path('app/c45/' . $def['csv']);

        if (!is_file($path)) {
            throw new \RuntimeException(
                "File data latih tidak ditemukan: {$path}. " .
                "Salin CSV dari dataset Excel (sheet Training_*) ke storage/app/c45/."
            );
        }

        return self::bacaCsv($path);
    }

    public static function bacaCsv(string $path): array
    {
        $rows = array();
        $fh   = fopen($path, 'r');
        if ($fh === false) {
            throw new \RuntimeException("Gagal membuka file: {$path}");
        }

        $header = fgetcsv($fh);
        if ($header === false) {
            fclose($fh);
            throw new \RuntimeException("CSV kosong: {$path}");
        }
        $header = array_map('trim', $header);

        while (($baris = fgetcsv($fh)) !== false) {
            if (count($baris) === 1 && ($baris[0] === null || $baris[0] === '')) {
                continue; // lewati baris kosong
            }
            $row = array();
            foreach ($header as $i => $kol) {
                $row[$kol] = isset($baris[$i]) ? trim((string) $baris[$i]) : '';
            }
            $rows[] = $row;
        }
        fclose($fh);

        return $rows;
    }

    /* ------------------------------------------------------------------
     |  MODE (B): dari tabel pengukuran aplikasi Anda.
     |  Aktifkan dengan mengganti isi muat() menjadi:
     |      return $nama === 'balita' ? self::barisDariDatabase('balita') : self::barisDariDatabase('bumil');
     |
     |  Contoh (sesuaikan nama model/tabel & pemetaan kolom dengan proyek Anda):
     |
     |  private static function barisDariDatabase(string $nama): array
     |  {
     |      if ($nama === 'balita') {
     |          return \App\Models\PengukuranBalita::query()      // <-- SESUAIKAN
     |              ->join('balita', 'balita.id', '=', 'pengukuran_balitas.balita_id')
     |              ->get()
     |              ->map(function ($u) {
     |                  // Gunakan logika modul Z-Score Anda utk memetakan
     |                  // hasil pengukuran -> fitur kategorik:
     |                  return array(
     |                      'Tren_BB'      => $u->tren_bb,             // 'Naik' | 'Turun'
     |                      'Kelompok_Usia'=> $u->usia_bulan < 24 ? '< 24 bulan' : '>= 24 bulan',
     |                      'Status_Gizi'  => $u->status_gizi,         // hasil Z-Score (7 kelas)
     |                      'LILA'         => $u->lila_cm < 12.5 ? 'Kurang' : 'Cukup',
     |                      'Prioritas_1'  => $u->tindak_lanjut_1,     // keputusan bidan
     |                      'Prioritas_2'  => $u->tindak_lanjut_2,
     |                      'Prioritas_3'  => $u->tindak_lanjut_3,
     |                  );
     |              })->all();
     |      }
     |      // ... pemetaan serupa untuk bumil (Trimester, LILA, Hb, Tensi)
     |  }
     * ------------------------------------------------------------------ */
}
