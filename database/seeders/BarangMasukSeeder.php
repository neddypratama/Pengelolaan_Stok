<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangMasukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['kode' => 'TM-0000001', 'tanggal' => Carbon::now()->subDays(10), 'jumlah' => 10, 'barang_id' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'TM-0000002', 'tanggal' => Carbon::now()->subDays(9), 'jumlah' => 15, 'barang_id' => 2, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'TM-0000003', 'tanggal' => Carbon::now()->subDays(8), 'jumlah' => 20, 'barang_id' => 3, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'TM-0000004', 'tanggal' => Carbon::now()->subDays(7), 'jumlah' => 12,  'barang_id' => 4, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'TM-0000005', 'tanggal' => Carbon::now()->subDays(6), 'jumlah' => 30,  'barang_id' => 5, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'TM-0000006', 'tanggal' => Carbon::now()->subDays(5), 'jumlah' => 25, 'barang_id' => 6, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'TM-0000007', 'tanggal' => Carbon::now()->subDays(4), 'jumlah' => 18, 'barang_id' => 7, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'TM-0000008', 'tanggal' => Carbon::now()->subDays(3), 'jumlah' => 22,  'barang_id' => 8, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'TM-0000009', 'tanggal' => Carbon::now()->subDays(2), 'jumlah' => 16,  'barang_id' => 9, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'TM-0000010', 'tanggal' => Carbon::now()->subDays(1), 'jumlah' => 28,  'barang_id' => 10, 'created_at' => now(), 'updated_at' => now(),],
        ];

        DB::table('barang_masuks')->insert($data);
    }
}
