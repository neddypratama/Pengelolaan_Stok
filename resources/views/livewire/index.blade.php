<?php

use App\Models\Barang;
use App\Models\Satuan;
use App\Models\User;
use App\Models\JenisBarang;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

new class extends Component {
    use Toast;
    use WithPagination;

    public array $jenisChart = [];
    public array $satuanChart = [];
    public array $stokChart = [];
    public array $barangMasukChart = [];
    public array $barangKeluarChart = [];

    public $selectedMasuk = null; // Menyimpan ID barang yang dipilih
    public $selectedKeluar = null; // Menyimpan ID barang yang dipilih

    public function mount()
    {
        $this->chartJenisBarangs();
        $this->chartSatuans();
        $this->chartStok();
        $this->chartBarangMasuk();
        $this->chartBarangKeluar();
    }

    public function updatedSelectedMasuk()
    {
        $this->chartBarangMasuk(); // Memanggil ulang method untuk memperbarui chart
    }

    public function updatedSelectedKeluar()
    {
        $this->chartBarangKeluar(); // Memanggil ulang method untuk memperbarui chart
    }

    public function chartBarangMasuk()
    {
        // Debugging untuk memastikan filter bekerja
        logger('selectedMasuk: ' . json_encode($this->selectedMasuk));

        // Jika tidak ada barang yang dipilih, tampilkan semua barang masuk
        $query = BarangMasuk::selectRaw('DATE(tanggal) as tanggal, SUM(jumlah) as total_masuk')->groupBy('tanggal')->orderBy('tanggal');

        // Jika ada barang yang dipilih, filter berdasarkan barang_id
        if ($this->selectedMasuk) {
            $query->where('barang_id', $this->selectedMasuk); // Gunakan selectedMasuk untuk filter
        }

        $data = $query->get();

        // Format data untuk chart
        $labels = $data->pluck('tanggal')->toArray(); // Mengambil tanggal sebagai label
        $jumlahMasuk = $data->pluck('total_masuk')->toArray(); // Mengambil jumlah barang masuk sebagai data chart

        // Fungsi untuk menghasilkan warna acak
        $generateRandomColor = fn() => '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6);

        // Menghasilkan warna acak untuk chart
        $colors = array_map($generateRandomColor, range(1, count($data)));

        // Mengatur data chart untuk Barang Masuk
        $this->barangMasukChart = [
            'type' => 'line', // Jenis chart adalah line
            'data' => [
                'labels' => $labels, // Label adalah tanggal
                'datasets' => [
                    [
                        'label' => 'Barang Masuk',
                        'data' => $jumlahMasuk, // Data jumlah barang masuk
                        'backgroundColor' => 'rgba(75, 192, 192, 0.2)', // Warna background
                        'borderColor' => 'rgba(75, 192, 192, 1)', // Warna border
                        'borderWidth' => 2, // Ketebalan border
                        'fill' => true, // Mengisi area di bawah garis
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                    ],
                ],
            ],
        ];
    }

    public function chartBarangKeluar()
    {
        // Jika tidak ada barang yang dipilih, tampilkan semua barang keluar
        $query = BarangKeluar::selectRaw('DATE(tanggal) as tanggal, SUM(jumlah) as total_keluar')->groupBy('tanggal')->orderBy('tanggal');

        // Jika ada barang yang dipilih, filter berdasarkan barang_id
        if ($this->selectedKeluar) {
            $query->where('barang_id', $this->selectedKeluar); // Gunakan selectedKeluar untuk filter
        }

        $data = $query->get();

        // Format data untuk chart
        $labels = $data->pluck('tanggal')->toArray(); // Mengambil tanggal sebagai label
        $jumlahKeluar = $data->pluck('total_keluar')->toArray(); // Mengambil jumlah barang keluar sebagai data chart

        // Fungsi untuk menghasilkan warna acak
        $generateRandomColor = fn() => '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6);

        // Menghasilkan warna acak untuk chart
        $colors = array_map($generateRandomColor, range(1, count($data)));

        // Mengatur data chart untuk Barang Keluar
        $this->barangKeluarChart = [
            'type' => 'line', // Jenis chart adalah line
            'data' => [
                'labels' => $labels, // Label adalah tanggal
                'datasets' => [
                    [
                        'label' => 'Barang Keluar',
                        'data' => $jumlahKeluar, // Data jumlah barang keluar
                        'backgroundColor' => 'rgba(255, 99, 132, 0.2)', // Warna background
                        'borderColor' => 'rgba(255, 99, 132, 1)', // Warna border
                        'borderWidth' => 2, // Ketebalan border
                        'fill' => true, // Mengisi area di bawah garis
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                    ],
                ],
            ],
        ];
    }

    public function chartStok()
    {
        // Ambil data barang dan stoknya
        $data = Barang::select('name', 'stok')
            ->orderBy('name') // Menyusun berdasarkan nama barang
            ->get();

        // Menyusun data untuk chart
        $labels = $data->pluck('name')->toArray(); // Ambil nama barang untuk label
        $stokData = $data->pluck('stok')->toArray(); // Ambil stok untuk data chart

        // Fungsi untuk menghasilkan warna acak
        $generateRandomColor = fn() => '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6);

        // Menghasilkan array warna acak untuk setiap barang
        $colors = array_map($generateRandomColor, range(1, count($data)));

        // Mengatur data chart
        $this->stokChart = [
            'type' => 'bar', // Jenis chart bar
            'data' => [
                'labels' => $labels, // Menggunakan nama barang sebagai label
                'datasets' => [
                    [
                        'label' => 'Stock Barang',
                        'data' => $stokData, // Menggunakan stok sebagai data
                        'backgroundColor' => $colors, // Warna background batang
                        'borderColor' => $colors, // Warna border batang
                        'borderWidth' => 1, // Ketebalan border
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
            ],
        ];
    }

    public function chartJenisBarangs()
    {
        // Ambil data jenis barang dan jumlah barang per jenis
        $data = JenisBarang::withCount('barangs') // Menghitung jumlah barang per jenis
            ->get()
            ->pluck('barangs_count', 'name') // Mengambil nama jenis barang dan jumlah barang
            ->toArray();

        // Fungsi untuk menghasilkan warna acak
        $generateRandomColor = fn() => '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6);

        // Menghasilkan array warna acak untuk setiap jenis
        $colors = array_map($generateRandomColor, range(1, count($data)));

        // Menyiapkan data untuk chart
        $this->jenisChart = [
            'type' => 'pie', // Jenis chart adalah pie
            'data' => [
                'labels' => array_keys($data), // Label menggunakan nama jenis barang
                'datasets' => [
                    [
                        'label' => 'Total Barang per Jenis', // Label chart
                        'data' => array_values($data), // Jumlah barang per jenis
                        'backgroundColor' => $colors, // Warna untuk setiap bagian chart
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'position' => 'left', // Posisi label di bawah chart
                        'labels' => [
                            'usePointStyle' => true, // Gunakan titik warna di label
                            'pointStyle' => 'circle', // Bentuk ikon legenda
                        ],
                    ],
                ],
            ],
        ];
    }

    public function chartSatuans()
    {
        // Ambil data satuan dan jumlah barang per satuan
        $data = Satuan::withCount('barangs') // Menghitung jumlah barang per satuan
            ->get()
            ->pluck('barangs_count', 'name') // Mengambil nama satuan dan jumlah barang
            ->toArray();

        // Fungsi untuk menghasilkan warna acak
        $generateRandomColor = fn() => '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6);

        // Menghasilkan array warna acak untuk setiap satuan
        $colors = array_map($generateRandomColor, range(1, count($data)));

        // Menyiapkan data untuk chart
        $this->satuanChart = [
            'type' => 'doughnut', // Jenis chart adalah doughnut
            'data' => [
                'labels' => array_keys($data), // Label menggunakan nama satuan
                'datasets' => [
                    [
                        'label' => 'Total Barang per Satuan', // Label chart
                        'data' => array_values($data), // Jumlah barang per satuan
                        'backgroundColor' => $colors, // Warna untuk setiap bagian chart
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'cutout' => '60%', // Ukuran lubang tengah
                'plugins' => [
                    'legend' => [
                        'position' => 'right', // Posisi label di bawah chart
                        'labels' => [
                            'usePointStyle' => true, // Gunakan titik warna di label
                            'pointStyle' => 'circle', // Bentuk ikon legenda
                        ],
                    ],
                ],
            ],
        ];
    }

    public array $sortBy = ['column' => 'id', 'direction' => 'asc'];
    public $page = [['id' => 10, 'name' => '10'], ['id' => 25, 'name' => '25'], ['id' => 50, 'name' => '50'], ['id' => 100, 'name' => '100']];
    public int $perPage = 10; // Default jumlah data per halaman

    public function headers(): array
    {
        return [['key' => 'id', 'label' => '#', 'class' => 'w-1'], ['key' => 'kode', 'label' => 'Kode'], ['key' => 'jenis_name', 'label' => 'Jenis Barang'], ['key' => 'name', 'label' => 'Name', 'class' => 'w-64'], ['key' => 'stok', 'label' => 'Stok'], ['key' => 'satuan_name', 'label' => 'Satuan']];
    }

    public function stokMinimum(): LengthAwarePaginator
    {
        return Barang::query()->withAggregate('jenis', 'name')->withAggregate('satuan', 'name')->where('stok', '<', 10)->orderBy(...array_values($this->sortBy))->paginate($this->perPage);
    }

    public function with()
    {
        return [
            'barangTotal' => Barang::count(),
            'satuanTotal' => Satuan::count(),
            'jenisbarangTotal' => JenisBarang::count(),
            'barangmasukTotal' => BarangMasuk::count(),
            'barangkeluarTotal' => BarangKeluar::count(),
            'barangs' => Barang::all(),
            'stokMinimum' => $this->stokMinimum(),
            'perPage' => $this->perPage,
            'pages' => $this->page,
            'headers' => $this->headers(),
        ];
    }
};
?>

<div>
    <x-header title="Dashboard" separator progress-indicator />

    @if (auth()->user()->role_id == 4)
        
    @else
        <!-- Grid untuk Kartu Data -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mt-6">
        <x-card class="rounded-lg shadow p-6">
            <div class="flex items-center gap-4">
                <x-icon name="fas.box-open" class="text-purple-600 w-12 h-12" />
                <div>
                    <p class="text-gray-600">Total Barang</p>
                    <p class="text-2xl font-bold">{{ $barangTotal }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="rounded-lg shadow p-6">
            <div class="flex items-center gap-4">
                <x-icon name="fas.balance-scale" class="text-blue-600 w-12 h-12" />
                <div>
                    <p class="text-gray-600">Total Satuan</p>
                    <p class="text-2xl font-bold">{{ $satuanTotal }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="rounded-lg shadow p-6">
            <div class="flex items-center gap-4">
                <x-icon name="fas.tags" class="text-green-600 w-12 h-12" />
                <div>
                    <p class="text-gray-600">Jenis Barang</p>
                    <p class="text-2xl font-bold">{{ $jenisbarangTotal }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="rounded-lg shadow p-6">
            <div class="flex items-center gap-4">
                <x-icon name="fas.arrow-down" class="text-yellow-500 w-12 h-12" />
                <div>
                    <p class="text-gray-600">Barang Masuk</p>
                    <p class="text-2xl font-bold">{{ $barangmasukTotal }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="rounded-lg shadow p-6">
            <div class="flex items-center gap-4">
                <x-icon name="fas.arrow-up" class="text-red-500 w-12 h-12" />
                <div>
                    <p class="text-gray-600">Barang Keluar</p>
                    <p class="text-2xl font-bold">{{ $barangkeluarTotal }}</p>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Grid untuk Chart -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <x-card class="grid col-span-1">
            <x-slot:title>Jenis Barang</x-slot:title>
            <x-chart wire:model="jenisChart" />
        </x-card>

        <x-card class="grid col-span-1">
            <x-slot:title>Satuan</x-slot:title>
            <x-chart wire:model="satuanChart" />
        </x-card>

        <x-card class="grid col-span-2">
            <x-slot:title>Stok Barang</x-slot:title>
            <x-chart wire:model="stokChart" />
        </x-card>

        <!-- Barang Masuk Chart -->
        <x-card class="grid col-span-1">
            <x-slot:title>Barang Masuk</x-slot:title>
            <x-slot:menu>
                <x-select wire:model.live="selectedMasuk" :options="$barangs" prefix="Nama Barang"
                    hint="Pilih barang yang diinginkan!" placeholder="-- Semua Barang --" />
            </x-slot:menu>
            <x-chart wire:model.live="barangMasukChart" />
        </x-card>

        <!-- Barang Keluar Chart -->
        <x-card class="grid col-span-1">
            <x-slot:title>Barang Keluar</x-slot:title>
            <x-slot:menu>
                <x-select wire:model.live="selectedKeluar" :options="$barangs" prefix="Nama Barang"
                    hint="Pilih barang yang diinginkan!" placeholder="-- Semua Barang --" />
            </x-slot:menu>
            <x-chart wire:model="barangKeluarChart" />
        </x-card>
    </div>

    <!-- TABLE wire:poll.5s="users"  -->
    <x-card class="mt-4">
        <x-slot:title>Barang Stok Minimum</x-slot:title>
        <x-slot:menu>
            <x-button label="Barangs" link="/barangs" class="btn-ghost" icon="o-arrow-right" responsive />
        </x-slot:menu>
        <x-table :headers="$headers" :rows="$stokMinimum" :sort-by="$sortBy" with-pagination
            link="barangs/{id}/edit?name={name}&jenisbarangs={jenis.name}">
            @scope('cell_stok', $stokMinimum)
                <x-badge :value="$stokMinimum->stok" class="badge-warning badge-soft" />
            @endscope
        </x-table>
    </x-card>
    @endif
</div>
