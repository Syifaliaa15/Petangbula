<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Posyandu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Nonaktifkan foreign key check sementara agar bisa truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::query()->delete();
        Posyandu::query()->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ============================================
        // 1. BUAT 19 POSYANDU (RW 01 - RW 19)
        // ============================================
        $posyandus = [];
        for ($i = 1; $i <= 19; $i++) {
            $posyandus[] = Posyandu::create([
                'nama_posyandu' => "Posyandu Mawar RW " . str_pad($i, 2, '0', STR_PAD_LEFT),
                'rw'            => str_pad($i, 2, '0', STR_PAD_LEFT),
                'alamat'        => "Desa Sindangpanon, RW " . str_pad($i, 2, '0', STR_PAD_LEFT),
                'nama_ketua'    => "Ketua RW " . str_pad($i, 2, '0', STR_PAD_LEFT),
            ]);
        }

        // ============================================
        // 2. BUAT AKUN ADMIN PUSAT
        // ============================================
        User::create([
            'name'        => 'Administrator Petangbula',
            'username'    => 'admin',
            'password'    => Hash::make('password'),
            'role'        => 'admin',
            'posyandu_id' => null,
            'is_active'   => true,
        ]);

        // ============================================
        // 3. BUAT AKUN BIDAN DESA
        // ============================================
        User::create([
            'name'        => 'Bidan Siti Aminah',
            'username'    => 'bidan',
            'password'    => Hash::make('password'),
            'role'        => 'bidan',
            'posyandu_id' => null,
            'is_active'   => true,
        ]);

        // ============================================
        // 4. BUAT 19 AKUN KADER (1 per Posyandu)
        // ============================================
        $namaKader = [
            'Kader Rina Wati', 'Kader Dewi Lestari', 'Kader Siti Nurhaliza',
            'Kader Ani Suryani', 'Kader Yuni Astuti', 'Kader Rini Marlina',
            'Kader Diah Pitaloka', 'Kader Wulan Dari', 'Kader Fitri Handayani',
            'Kader Lina Kurnia', 'Kader Maya Sari', 'Kader Nengsih',
            'Kader Oki Pratama', 'Kader Putri Ayu', 'Kader Rahma Dewi',
            'Kader Sinta Melati', 'Kader Tuti Alawiyah', 'Kader Umi Kalsum',
            'Kader Vina Amelia',
        ];

        foreach ($posyandus as $index => $posyandu) {
            User::create([
                'name'        => $namaKader[$index],
                'username'    => 'kader' . str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                'password'    => Hash::make('password'),
                'role'        => 'kader',
                'posyandu_id' => $posyandu->id,
                'is_active'   => true,
            ]);
        }

        $this->command->info('✅ Seeder berhasil!');
        $this->command->info('   Admin  : admin / password');
        $this->command->info('   Bidan  : bidan / password');
        $this->command->info('   Kader  : kader01 s/d kader19 / password');
    }
}