<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['kode' => 'B0001', 'name' => 'Kopi Arabika', 'stok' => 50, 'satuan_id' => 2, 'jenis_id' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'B0002', 'name' => 'Kopi Robusta', 'stok' => 40, 'satuan_id' => 2, 'jenis_id' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'B0003', 'name' => 'Susu UHT', 'stok' => 30, 'satuan_id' => 3, 'jenis_id' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'B0004', 'name' => 'Susu Kental Manis', 'stok' => 25, 'satuan_id' => 7, 'jenis_id' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'B0005', 'name' => 'Gula Pasir', 'stok' => 40, 'satuan_id' => 2, 'jenis_id' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'B0006', 'name' => 'Gula Aren', 'stok' => 35, 'satuan_id' => 2, 'jenis_id' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'B0007', 'name' => 'Sirup Vanila', 'stok' => 20, 'satuan_id' => 7, 'jenis_id' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'B0008', 'name' => 'Sirup Hazelnut', 'stok' => 18, 'satuan_id' => 7, 'jenis_id' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'B0009', 'name' => 'Coklat Bubuk', 'stok' => 22, 'satuan_id' => 2, 'jenis_id' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'B0010', 'name' => 'Matcha Powder', 'stok' => 15, 'satuan_id' => 2, 'jenis_id' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'B0011', 'name' => 'Cup Plastik 16oz', 'stok' => 100, 'satuan_id' => 5, 'jenis_id' => 2, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'B0012', 'name' => 'Cup Plastik 22oz', 'stok' => 80, 'satuan_id' => 5, 'jenis_id' => 2, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'B0013', 'name' => 'Sedotan Jumbo', 'stok' => 200, 'satuan_id' => 6, 'jenis_id' => 2, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'B0014', 'name' => 'Tutup Cup Plastik', 'stok' => 150, 'satuan_id' => 5, 'jenis_id' => 2, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'B0015', 'name' => 'Saringan Kopi', 'stok' => 10, 'satuan_id' => 5, 'jenis_id' => 2, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'B0016', 'name' => 'French Press', 'stok' => 8, 'satuan_id' => 5, 'jenis_id' => 2, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'B0017', 'name' => 'Teko Susu', 'stok' => 12, 'satuan_id' => 5, 'jenis_id' => 2, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'B0018', 'name' => 'Gelas Takar', 'stok' => 15, 'satuan_id' => 5, 'jenis_id' => 2, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'B0019', 'name' => 'Plastik Pembungkus', 'stok' => 50, 'satuan_id' => 6, 'jenis_id' => 2, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'B0020', 'name' => 'Kantong Kertas', 'stok' => 60, 'satuan_id' => 6, 'jenis_id' => 2, 'created_at' => now(), 'updated_at' => now(),],
        ];

        DB::table('barangs')->insert($data);
    }
}
