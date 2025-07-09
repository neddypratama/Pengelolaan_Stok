<?php
    namespace App\Exports;

    use App\Models\Barang;
    use Maatwebsite\Excel\Concerns\FromCollection;
    use Maatwebsite\Excel\Concerns\WithHeadings;

    class BarangsExport implements FromCollection, WithHeadings
    {
        public function collection()
        {
            return Barang::with(['jenis', 'satuan'])
                ->get()
                ->map(function ($barang) {
                    return [
                        $barang->kode,
                        $barang->jenis->name ?? '',
                        $barang->name,
                        $barang->stok,
                        $barang->satuan->name ?? '',
                    ];
                });
        }

        public function headings(): array
        {
            return ['Kode', 'Jenis Barang', 'Nama Barang', 'Stok', 'Satuan'];
        }
    }