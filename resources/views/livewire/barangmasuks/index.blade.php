<?php

use App\Models\BarangMasuk;
use App\Models\Barang;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MasukExport;

new class extends Component {
    use Toast;
    use WithPagination;

    public string $search = '';

    public bool $drawer = false;

    public array $sortBy = ['column' => 'id', 'direction' => 'asc'];

    // Create a public property.
    public int $barang_id = 0;

    public int $filter = 0;

    public $page = [['id' => 10, 'name' => '10'], ['id' => 25, 'name' => '25'], ['id' => 50, 'name' => '50'], ['id' => 100, 'name' => '100']];

    public int $perPage = 10; // Default jumlah data per halaman

    public bool $showExportModal = false;
    public string $exportMode = 'all'; // 'all', 'range', 'month'
    public ?string $startDate = null;
    public ?string $endDate = null;

    // Clear filters
    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-top');
    }

    // Delete action
    public function delete($id): void
    {
        $masuk = BarangMasuk::findOrFail($id);
        $barang = Barang::findOrFail($masuk->barang_id);
        $barang->update([
            'stok' => $barang->stok - $masuk->jumlah,
        ]);
        $masuk->delete();
        $this->warning("Barang masuk $masuk->name akan dihapus", position: 'toast-top');

        logActivity('deleted', 'Menghapus transaksi masuk ' . $masuk->kode);
    }

    public function export(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $query = BarangMasuk::with('barang');

        if ($this->exportMode === 'range' && $this->startDate && $this->endDate) {
            $query->whereBetween('tanggal', [$this->startDate, $this->endDate]);
        } elseif ($this->exportMode === 'month') {
            $query->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year);
        }

        $data = $query->get();
        // dd($this->exportMode, $this->startDate, $this->endDate ,$data);
        $this->showExportModal = false;
        logActivity('export', 'Mencetak transaksi keluar');
        return Excel::download(new MasukExport($data), 'barang-masuks.xlsx');
    }

    // Table headers
    public function headers(): array
    {
        return [['key' => 'id', 'label' => '#', 'class' => 'w-1'], ['key' => 'kode', 'label' => 'Kode'], ['key' => 'barang_name', 'label' => 'Barang', 'class' => 'w-64'], ['key' => 'tanggal', 'label' => 'Tanggal'], ['key' => 'jumlah', 'label' => 'Jumlah']];
    }

    public function masuks(): LengthAwarePaginator
    {
        return BarangMasuk::query()->withAggregate('barang', 'name')->when($this->search, fn(Builder $q) => $q->where('kode', 'like', "%$this->search%"))->when($this->barang_id, fn(Builder $q) => $q->where('barang_id', $this->barang_id))->orderBy(...array_values($this->sortBy))->paginate($this->perPage);
    }

    public function with(): array
    {
        if ($this->filter >= 0 && $this->filter < 3) {
            if (!$this->search == null) {
                $this->filter = 1;
            } else {
                $this->filter = 0;
            }
            if (!$this->barang_id == 0) {
                $this->filter += 1;
            }
        }
        return [
            'masuks' => $this->masuks(),
            'headers' => $this->headers(),
            'barang' => Barang::all(),
            'perPage' => $this->perPage,
            'pages' => $this->page,
        ];
    }

    // Reset pagination when any component property changes
    public function updated($property): void
    {
        if (!is_array($property) && $property != '') {
            $this->resetPage();
        }
    }
};

?>

<div>
    <!-- HEADER -->
    <x-header title="Barang Masuks" separator progress-indicator>
        <x-slot:actions>
            <x-button spinner label="Create" link="/barangmasuks/create" responsive icon="o-plus" class="btn-primary" />
            <x-button spinner label="Export" wire:click="$set('showExportModal', true)" icon="o-arrow-down-tray"
                class="btn-secondary" responsive />
        </x-slot:actions>
    </x-header>

    <!-- FILTERS -->
    <div class="grid grid-cols-1 md:grid-cols-8 gap-4  items-end mb-4">
        <div class="md:col-span-1">
            <x-select label="Show entries" :options="$pages" wire:model.live="perPage" class="w-15" />
        </div>
        <div class="md:col-span-6">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass"
                class="" />
        </div>
        <div class="md:col-span-1 flex">
            <x-button spinner label="Filters" @click="$wire.drawer=true" icon="o-funnel" badge="{{ $filter }}"
                class="" responsive />
        </div>
        <!-- Dropdown untuk jumlah data per halaman -->
    </div>

    <!-- TABLE wire:poll.5s="users"  -->
    <x-card>
        <x-table :headers="$headers" :rows="$masuks" :sort-by="$sortBy" with-pagination
            link="barangmasuks/{id}/edit?tanggal={tanggal}&barang={barang.name}">
            @scope('actions', $masuks)
                <x-button spinner icon="o-trash" wire:click="delete({{ $masuks['id'] }})"
                    wire:confirm="Yakin ingin menghapus {{ $masuks['name'] }}?" spinner
                    class="btn-ghost btn-sm text-red-500" />
            @endscope
        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button spinner class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
            <x-select placeholder="Barang" wire:model.live="barang_id" :options="$barang" icon="o-flag"
                placeholder-value="0" />

        </div>

        <x-slot:actions>
            <x-button spinner label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button spinner label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer=false" />
        </x-slot:actions>
    </x-drawer>

    <x-modal wire:model="showExportModal" title="Export Barang Masuk">
        <div class="grid gap-4">
            <x-select label="Mode Export" wire:model.live="exportMode" :options="[
                ['id' => 'all', 'name' => 'Semua Data'],
                ['id' => 'range', 'name' => 'Berdasarkan Tanggal'],
                ['id' => 'month', 'name' => 'Bulan Ini'],
            ]" />

            @if ($exportMode === 'range')
                <x-input type="date" label="Dari Tanggal" wire:model.live="startDate" />
                <x-input type="date" label="Sampai Tanggal" wire:model.live="endDate" />
            @endif
        </div>

        <x-slot:actions>
            <x-button spinner label="Batal" @click="$set('showExportModal', false)" />
            <x-button spinner label="Export" wire:click="export" spinner class="btn-primary" />
        </x-slot:actions>
    </x-modal>
</div>
