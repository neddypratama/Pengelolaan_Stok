<?php
    namespace App\Exports;

    use Maatwebsite\Excel\Concerns\FromCollection;
    use Maatwebsite\Excel\Concerns\WithHeadings;
    use Illuminate\Support\Collection;
    
    class MasukExport implements FromCollection, WithHeadings
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
            return ['Kode Barang', 'Nama Barang', 'Tanggal Masuk', 'Jumlah Masuk'];
        }
    }
    