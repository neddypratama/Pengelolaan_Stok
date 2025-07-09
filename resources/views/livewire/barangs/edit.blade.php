<?php

use Livewire\Volt\Component;
use App\Models\Barang;
use App\Models\JenisBarang;
use App\Models\Satuan;
use Mary\Traits\Toast;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;

new class extends Component {
    // We will use it later
    use Toast, WithFileUploads;

    // Component parameter
    public Barang $barang;

    #[Rule('required|max:225')]
    public string $name = '';

    #[Rule('required')]
    public string $kode;

    #[Rule('required|integer|min:1')]
    public int $stok;

    #[Rule('required|sometimes')]
    public ?int $jenis_id = null;

    #[Rule('required|sometimes')]
    public ?int $satuan_id = null;

    public function with(): array
    {
        return [
            'jenisbarang' => JenisBarang::all(),
            'satuan' => Satuan::all(),
        ];
    }

    public function mount(): void
    {
        $this->fill($this->barang);
    }

    public function save(): void
    {
        // Validate
        $data = $this->validate();

        // Update
        $this->barang->update($data);
        logActivity('updated', 'Merubah data barang ' . $this->barang->name);

        // You can toast and redirect to any route
        $this->success('Barang updated with success.', redirectTo: '/barangs');
    }
};

?>

<div>
    <x-header title="Update {{ $barang->name }}" separator />

    <x-form wire:submit="save">
        {{--  Basic section  --}}
        <div class="lg:grid grid-cols-5">
            <div class="col-span-2">
                <x-header title="Basic" subtitle="Basic info from barang" size="text-2xl" />
            </div>

            <div class="col-span-3 grid gap-3">
                <x-input label="Kode" wire:model="kode" readonly/>
                <x-select label="Jenis Barang" wire:model="jenis_id" :options="$jenisbarang" placeholder="---" />
                <x-input label="Name" wire:model="name" />
            </div>
        </div>

        {{--  Details section --}}
        <hr class="my-5" />

        <div class="lg:grid grid-cols-5">
            <div class="col-span-2">
                <x-header title="Details" subtitle="More about the barang" size="text-2xl" />
            </div>
            <div class="col-span-3 grid gap-3">
                <x-input label="Stok" wire:model="stok" type="number" min="1" />
                <x-select label="Satuan" wire:model="satuan_id" :options="$satuan" placeholder="---" />
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" link="/barangs" />
            {{-- The important thing here is `type="submit"` --}}
            {{-- The spinner property is nice! --}}
            <x-button label="Save" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>

    </x-form>
</div>
