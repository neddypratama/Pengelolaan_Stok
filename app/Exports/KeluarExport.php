<?php
    namespace App\Exports;

    use Illuminate\Support\Collection;
    use Maatwebsite\Excel\Concerns\FromCollection;
    use Maatwebsite\Excel\Concerns\WithHeadings;

    class KeluarExport implements FromCollection, WithHeadings
    {
        protected Collection $data;
    
        public function __construct(Collection $data)
        {
            $this->data = $data;
        }
    
        public function collection(): Collection
        {
            return $this->data->map(function ($item) {
                return [
                    $item->kode,
                    $item->barang->name ?? '',
                    $item->tanggal,
                    $item->jumlah,
                ];
            });
        }

    public function headings(): array
    {
        return ['Kode Barang', 'Nama Barang', 'Tanggal Keluar', 'Jumlah Keluar' ];
    }
    }