<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Posyandu;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::query()->delete();
        Posyandu::query()->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Buat 19 Posyandu (RW 1 - RW 19 Desa Sindangpanon)
        $posyandus = [];
        for ($i = 1; $i <= 19; $i++) {
            $posyandus[] = Posyandu::create([
                'nama_posyandu' => "Posyandu Mawar RW {$i}",
                'rw' => str_pad($i, 2, '0', STR_PAD_LEFT),
                'alamat' => "Desa Sindangpanon, RW {$i}",
                'nama_ketua' => "Ketua RW {$i}",
            ]);
        }

        // 2. Buat Admin (tidak terikat posyandu)
        User::create([
            'name' => 'Administrator Posyandu',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'posyandu_id' => null,
        ]);

        // 3. Buat Bidan (tidak terikat posyandu, bisa lihat semua)
        User::create([
            'name' => 'Bidan Siti Aminah',
            'username' => 'bidan',
            'password' => Hash::make('password'),
            'role' => 'bidan',
            'posyandu_id' => null,
        ]);

        // 4. Buat Kader untuk setiap Posyandu (19 Kader)
        $namaKader = [
            'Kader Rina Wati', 'Kader Dewi Lestari', 'Kader Siti Nurhaliza',
            'Kader Ani Suryani', 'Kader Yuni Astuti', 'Kader Rini Marlina',
            'Kader Diah Pitaloka', 'Kader Wulan Dari', 'Kader Fitri Handayani',
            'Kader Lina Kurnia', 'Kader Maya Sari', 'Kader Nengsih',
            'Kader Oki Pratama', 'Kader Putri Ayu', 'Kader Rahma Dewi',
            'Kader Sinta Melati', 'Kader Tuti Alawiyah', 'Kader Umi Kalsum',
            'Kader Vina Amelia'
        ];

        foreach ($posyandus as $index => $posyandu) {
            User::create([
                'name' => $namaKader[$index],
                'username' => 'kader' . str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                'password' => Hash::make('password'),
                'role' => 'kader',
                'posyandu_id' => $posyandu->id,
            ]);
        }
    }
}