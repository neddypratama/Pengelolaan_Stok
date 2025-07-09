<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisBarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Bahan Baku', 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Minuman', 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Makanan', 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Alat Masak', 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Peralatan Kafe', 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Kemasan', 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Kebersihan', 'created_at' => now(), 'updated_at' => now(),],
        ];

        DB::table('jenis_barangs')->insert($data);
    }
}
