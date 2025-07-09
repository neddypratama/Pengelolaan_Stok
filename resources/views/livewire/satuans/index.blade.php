<?php

use App\Models\Satuan;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;

new class extends Component {
    use Toast, WithPagination, WithFileUploads;
    public string $search = '';

    public bool $drawer = false;

    public array $sortBy = ['column' => 'id', 'direction' => 'asc'];

    // Create a public property.
    // public int $country_id = 0;

    public int $filter = 0;

    public $page = [['id' => 10, 'name' => '10'], ['id' => 25, 'name' => '25'], ['id' => 50, 'name' => '50'], ['id' => 100, 'name' => '100']];

    public int $perPage = 10; // Default jumlah data per halaman

    public bool $editModal = false; // Untuk menampilkan modal

    public ?Satuan $editingSatuan = null; // Menyimpan data Satuan yang sedang diedit

    public string $editingName = ''; // Menyimpan nilai input untuk nama Satuan

    public bool $createModal = false; // Untuk menampilkan modal create

    public string $newSatuanName = ''; // Untuk menyimpan input nama Satuan baru

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
        $Satuan = Satuan::findOrFail($id);

        // Cek apakah barang masih dipakai di tabel barang_keluars
        if ($Satuan->barangs()->exists()) {
            $this->error("Satuan \"$Satuan->name\" tidak dapat dihapus karena sudah digunakan dalam data barang.", position: 'toast-top');
            return;
        }
        
        logActivity('deleted', 'Menghapus data satuan ' . $Satuan->name);
        $Satuan->delete();
        $this->warning("Satuan $Satuan->name akan dihapus", position: 'toast-top');
    }

    public function create(): void
    {
        $this->newSatuanName = ''; // Reset input sebelum membuka modal
        $this->createModal = true;
    }

    public function saveCreate(): void
    {
        $this->validate([
            'newSatuanName' => 'required|string|max:255',
        ]);

        Satuan::create(['name' => $this->newSatuanName,]);

        logActivity('created', $this->newSatuanName . ' ditambahkan');
        $this->createModal = false;
        $this->success('Satuan created successfully.', position: 'toast-top');
    }

    public function edit($id): void
    {
        $this->editingSatuan = Satuan::find($id);

        if ($this->editingSatuan) {
            $this->editingName = $this->editingSatuan->name;
            $this->editModal = true; // Tampilkan modal
        }
    }
    
    public function saveEdit(): void
    {
        $this->validate([
            'editingName' => 'required|string|max:255',
        ]);
        
        if ($this->editingSatuan) {
            
            // Update Satuan
            $this->editingSatuan->update([
                'name' => $this->editingName,
            ]);
            
            logActivity('updated', 'Merubah data satuan ' . $this->editingName);
            $this->editModal = false;
            $this->success('Satuan updated successfully.', position: 'toast-top');
        }
    }

    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'name', 'label' => 'Name', 'class' => 'w-100'],
            ['key' => 'barangs_count', 'label' => 'Barang', 'class' => 'w-100'], // Gunakan `users_count`
            ['key' => 'created_at', 'label' => 'Tanggal dibuat', 'class' => 'w-30'],
        ];
    }

    public function Satuan(): LengthAwarePaginator
    {
        return Satuan::query()
            ->withCount('barangs') // Menghitung jumlah users di setiap role
            ->when($this->search, fn(Builder $q) => $q->where('name', 'like', "%$this->search%"))
            ->orderBy(...array_values($this->sortBy))
            ->paginate($this->perPage);
    }

    public function with(): array
    {
        if ($this->filter >= 0 && $this->filter < 2) {
            if (!$this->search == null) {
                $this->filter = 1;
            } else {
                $this->filter = 0;
            }
        }
        return [
            'Satuan' => $this->Satuan(),
            'headers' => $this->headers(),
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
    <x-header title="Satuans" separator progress-indicator>
        <x-slot:actions>
            <x-button spinner label="Create" @click="$wire.create()" responsive icon="o-plus" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <!-- FILTERS -->
    <div class="grid grid-cols-1 md:grid-cols-8 gap-4 items-end mb-4">
        <div class="md:col-span-1">
            <x-select label="Show entries" :options="$pages" wire:model.live="perPage" />
        </div>
        <div class="md:col-span-6">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass"
                class="" />
        </div>
        <div class="md:col-span-1 flex">
            <x-button spinner label="Filters" @click="$wire.drawer=true" icon="o-funnel" badge="{{ $filter }}"
                class="" responsive/>
        </div>
        <!-- Dropdown untuk jumlah data per halaman -->
    </div>

    <!-- TABLE wire:poll.5s="users"  -->
    <x-card>
        <x-table :headers="$headers" :rows="$Satuan" :sort-by="$sortBy" with-pagination
            @row-click="$wire.edit($event.detail.id)">
            @scope('cell_barangs_count', $Satuan)
                <span>{{ $Satuan->barangs_count }}</span>
            @endscope
            @scope('actions', $Satuan)
                <x-button spinner icon="o-trash" wire:click="delete({{ $Satuan['id'] }})"
                    wire:confirm="Yakin ingin menghapus {{ $Satuan['name'] }}?" spinner
                    class="btn-ghost btn-sm text-red-500" />
            @endscope
        </x-table>
    </x-card>

    <x-modal wire:model="createModal" title="Create Satuan">
        <div class="grid gap-4">
            <x-input label="Satuan Name" wire:model.live="newSatuanName" />
        </div>

        <x-slot:actions>
            <x-button spinner label="Cancel" icon="o-x-mark" @click="$wire.createModal=false" />
            <x-button spinner label="Save" icon="o-check" class="btn-primary" wire:click="saveCreate" />
        </x-slot:actions>
    </x-modal>

    <x-modal wire:model="editModal" title="Edit Satuan">
        <div class="grid gap-4">
            <x-input label="Satuan Name" wire:model.live="editingName" />
        </div>

        <x-slot:actions>
            <x-button spinner label="Cancel" icon="o-x-mark" @click="$wire.editModal=false" />
            <x-button spinner label="Save" icon="o-check" class="btn-primary" wire:click="saveEdit" />
        </x-slot:actions>
    </x-modal>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button spinner class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
            {{-- <x-select placeholder="Country" wire:model.live="country_id" :options="$countries" icon="o-flag"
                placeholder-value="0" /> --}}
        </div>

        <x-slot:actions>
            <x-button spinner label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button spinner label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer=false" />
        </x-slot:actions>
    </x-drawer>
</div>
