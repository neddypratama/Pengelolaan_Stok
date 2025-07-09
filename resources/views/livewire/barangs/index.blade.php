<?php

use App\Models\Barang;
use App\Models\Satuan;
use App\Models\JenisBarang;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BarangsExport;

new class extends Component {
    use Toast;
    use WithPagination;

    public string $search = '';

    public bool $drawer = false;

    public array $sortBy = ['column' => 'id', 'direction' => 'asc'];

    // Create a public property.
    public int $jenis_id = 0;
    public int $satuan_id = 0;

    public int $filter = 0;

    public $page = [['id' => 10, 'name' => '10'], ['id' => 25, 'name' => '25'], ['id' => 50, 'name' => '50'], ['id' => 100, 'name' => '100']];

    public int $perPage = 10; // Default jumlah data per halaman

    public int $stokFilter = 0;

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
        $barang = Barang::findOrFail($id);

        // Cek apakah barang masih dipakai di tabel barang_keluars
        if ($barang->barangkeluars()->exists()) {
            $this->error("Barang \"$barang->name\" tidak dapat dihapus karena sudah digunakan dalam data keluar.", position: 'toast-top');
            return;
        }

        logActivity('deleted', 'Menghapus data barang ' . $barang->name);
        $barang->delete();

        $this->warning("Barang \"$barang->name\" telah dihapus", position: 'toast-top');
    }

    public function export(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        logActivity('export', 'Mencetak data barang');
        return Excel::download(new BarangsExport(), 'barangs.xlsx');
    }

    // Table headers
    public function headers(): array
    {
        return [['key' => 'id', 'label' => '#', 'class' => 'w-1'], ['key' => 'kode', 'label' => 'Kode'], ['key' => 'jenis_name', 'label' => 'Jenis Barang'], ['key' => 'name', 'label' => 'Name', 'class' => 'w-64'], ['key' => 'stok', 'label' => 'Stok'], ['key' => 'satuan_name', 'label' => 'Satuan']];
    }

    public function barangs(): LengthAwarePaginator
    {
        return Barang::query()
            ->withAggregate('jenis', 'name')
            ->withAggregate('satuan', 'name')
            ->when($this->search, fn(Builder $q) => $q->where('name', 'like', "%$this->search%"))
            ->when($this->jenis_id, fn(Builder $q) => $q->where('jenis_id', $this->jenis_id))
            ->when($this->satuan_id, fn(Builder $q) => $q->where('satuan_id', $this->satuan_id))
            ->when($this->stokFilter === 1, fn(Builder $q) => $q->where('stok', '<', 10))
            ->when($this->stokFilter === 2, fn(Builder $q) => $q->where('stok', '>=', 10))
            ->orderBy(...array_values($this->sortBy))
            ->paginate($this->perPage);
    }

    public function with(): array
    {
        if ($this->filter >= 0 && $this->filter < 3) {
            if (!$this->search == null) {
                $this->filter = 1;
            } else {
                $this->filter = 0;
            }
            if (!$this->jenis_id == 0) {
                $this->filter += 1;
            }
            if (!$this->satuan_id == 0) {
                $this->filter += 1;
            }
            if (!$this->stokFilter == 0) {
                $this->filter += 1;
            }
        }
        return [
            'barangs' => $this->barangs(),
            'headers' => $this->headers(),
            'jenis' => JenisBarang::all(),
            'satuan' => Satuan::all(),
            'perPage' => $this->perPage,
            'pages' => $this->page,
            'stokFilters' => [['id' => 0, 'name' => 'Semua'], ['id' => 1, 'name' => 'Stok dibawah minimum'], ['id' => 2, 'name' => 'Stok diatas minimum']],
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
    <x-header title="Barangs" separator progress-indicator>
        <x-slot:actions>
            <div class="flex gap-2">
                <x-button spinner label="Create" link="/barangs/create" responsive icon="o-plus" class="btn-primary" />
                <x-button spinner label="Export" wire:click="export" icon="o-arrow-down-tray" class="btn-secondary"
                    responsive />
            </div>
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
        <x-table :headers="$headers" :rows="$barangs" :sort-by="$sortBy" with-pagination
            link="barangs/{id}/edit?name={name}&jenisbarangs={jenis.name}">
            @scope('actions', $barangs)
                @if (auth()->user()->role_id != 3)
                    <x-button spinner icon="o-trash" wire:click="delete({{ $barangs['id'] }})"
                        wire:confirm="Yakin ingin menghapus {{ $barangs['name'] }}?" spinner
                        class="btn-ghost btn-sm text-red-500" />
                @endif
            @endscope
        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button spinner class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
            <x-select placeholder="Jenis Barang" wire:model.live="jenis_id" :options="$jenis" icon="o-flag"
                placeholder-value="0" />
            <x-select placeholder="Satuan" wire:model.live="satuan_id" :options="$satuan" icon="o-flag"
                placeholder-value="0" />
            <x-select label="" wire:model.live="stokFilter" :options="$stokFilters" icon="o-archive-box" />

        </div>

        <x-slot:actions>
            <x-button spinner label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button spinner label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer=false" />
        </x-slot:actions>
    </x-drawer>
</div>
