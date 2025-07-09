<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangKeluarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['kode' => 'TK-0000001', 'tanggal' => Carbon::now()->subDays(10), 'jumlah' => 5, 'barang_id' => 1, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'TK-0000002', 'tanggal' => Carbon::now()->subDays(9), 'jumlah' => 8, 'barang_id' => 2, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'TK-0000003', 'tanggal' => Carbon::now()->subDays(8), 'jumlah' => 6, 'barang_id' => 3, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'TK-0000004', 'tanggal' => Carbon::now()->subDays(7), 'jumlah' => 10, 'barang_id' => 4, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'TK-0000005', 'tanggal' => Carbon::now()->subDays(6), 'jumlah' => 12, 'barang_id' => 5, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'TK-0000006', 'tanggal' => Carbon::now()->subDays(5), 'jumlah' => 9, 'barang_id' => 6, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'TK-0000007', 'tanggal' => Carbon::now()->subDays(4), 'jumlah' => 7, 'barang_id' => 7, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'TK-0000008', 'tanggal' => Carbon::now()->subDays(3), 'jumlah' => 11, 'barang_id' => 8, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'TK-0000009', 'tanggal' => Carbon::now()->subDays(2), 'jumlah' => 13, 'barang_id' => 9, 'created_at' => now(), 'updated_at' => now(),],
            ['kode' => 'TK-0000010', 'tanggal' => Carbon::now()->subDays(1), 'jumlah' => 15, 'barang_id' => 10, 'created_at' => now(), 'updated_at' => now(),],
        ];

        DB::table('barang_keluars')->insert($data);
    }
}
