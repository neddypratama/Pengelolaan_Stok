<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Gram', 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Kilogram', 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Liter', 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Mililiter', 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Pcs', 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Pack', 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Botol', 'created_at' => now(), 'updated_at' => now(),],
            ['name' => 'Dus', 'created_at' => now(), 'updated_at' => now(),],
        ];

        DB::table('satuans')->insert($data);
    }
}
