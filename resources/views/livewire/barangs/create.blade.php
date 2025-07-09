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

    #[Rule('required|unique:barangs')]
    public string $name = '';

    #[Rule('required|unique:barangs')]
    public string $kode;

    #[Rule('required|sometimes')]
    public ?int $jenis_id = null;

    #[Rule('required|integer|min:1')]
    public int $stok;

    #[Rule('required|sometimes')]
    public ?int $satuan_id = null;

    public function with(): array
    {
        return [
            'jenisbarang' => JenisBarang::all(),
            'satuan' => Satuan::all(),
            'kode' => $this->generateKodeBarang(),
        ];
    }

    public function generateKodeBarang()
    {
        $latestBarang = Barang::latest('id')->first(); // Ambil data terakhir
        $nextNumber = $latestBarang ? ((int) substr($latestBarang->kode, 1)) + 1 : 1;
        $this->kode = 'B' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        return $this->kode;
    }

    public function save(): void
    {
        // Validate
        $data = $this->validate();

        // Create
        $barang = Barang::create($data);

        logActivity('created', $barang->name . ' ditambahkan');
        
        // You can toast and redirect to any route
        $this->success('Barang berhasil dibuat!', redirectTo: '/barangs');
    }
};

?>

<div>
    <x-header title="Create" separator />

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
            <x-button label="Create" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>

    </x-form>
</div>
