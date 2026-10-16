<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalPosyandu;
use Carbon\Carbon;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat contoh jadwal tanpa relasi posyandu_id karena kolom tidak tersedia di tabel
        $data = [
            [
                'tanggal' => Carbon::now()->addDays(5)->format('Y-m-d'),
                'waktu_mulai' => '08:00:00',
                'waktu_selesai' => '12:00:00',
                'lokasi' => 'Balai Warga RW 01',
                'rw' => '01',
                'keterangan' => 'Pemeriksaan rutin bulanan balita dan ibu hamil.',
            ],
            [
                'tanggal' => Carbon::now()->addDays(12)->format('Y-m-d'),
                'waktu_mulai' => '09:00:00',
                'waktu_selesai' => '11:00:00',
                'lokasi' => 'Posyandu Melati RW 03',
                'rw' => '03',
                'keterangan' => 'Pemberian vitamin A dan imunisasi.',
            ],
        ];

        foreach ($data as $jadwal) {
            JadwalPosyandu::create($jadwal);
        }
    }
}