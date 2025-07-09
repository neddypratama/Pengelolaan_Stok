<?php

use Livewire\Volt\Component;
use App\Models\BarangKeluar;
use App\Models\Barang;
use Mary\Traits\Toast;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;

new class extends Component {
    // We will use it later
    use Toast, WithFileUploads;

    // Component parameter
    public BarangKeluar $keluar;

    #[Rule('required')]
    public string $kode = '';

    #[Rule('required|integer|min:1')]
    public ?int $jumlah = 0;

    #[Rule('required|sometimes')]
    public ?int $barang_id = null;

    #[Rule('required|date')]
    public ?string $tanggal = null;

    public int $stoklama = 0;
    public int $stokbaru = 0;
    public string $satuan = '';

    public function with(): array
    {
        return [
            'barang' => Barang::all(),
            'stoklama' => $this->generateStok(),
            'stokbaru' => $this->generateNewStok(),
            'satuan' => $this->generateSatuan(),
        ];
    }

    public function generateStok()
    {
        $barang = Barang::find($this->barang_id);
        if (!$barang) {
            $this->stoklama = 0;
        } else {
            $this->stoklama = $barang->stok + $this->keluar->jumlah;
        }
        return $this->stoklama;
    }

    public function generateSatuan()
    {
        $barang = Barang::find($this->barang_id);
        if (!$barang) {
            $this->satuan = '';
        } else {
            $this->satuan = $barang->satuan->name;
        }
        return $this->satuan;
    }

    public function generateNewStok()
    {
        $this->stokbaru = $this->stoklama - $this->jumlah;
        return $this->stokbaru;
    }

    public function mount(): void
    {
        $this->fill($this->keluar);
    }

    public function save(): void
    {
        // Validate
        $data = $this->validate();

        $selisih = $this->keluar->jumlah - $this->jumlah;
        $barang = Barang::find($this->barang_id);
        $barang->update([
            'stok' => $barang->stok + $selisih,
        ]);

        // Update
        $this->keluar->update($data);
        logActivity('updated', 'Merubah stok barang ' . $barang->name);
        logActivity('updated', 'Merubah stok keluar ' . $barang->name);

        // You can toast and redirect to any route
        $this->success('Menu updated with success.', redirectTo: '/barangkeluars');
    }
};

?>

<div>
    <x-header title="Update {{ $keluar->kode }}" separator />

    <x-form wire:submit="save">
        {{--  Basic section  --}}
        <div class="lg:grid grid-cols-5">
            <div class="col-span-2">
                <x-header title="Basic" subtitle="Basic info from barang keluar" size="text-2xl" />
            </div>

            <div class="col-span-3 grid gap-3">
                <x-input label="Kode Keluar" wire:model="kode" readonly />
                <x-select label="Nama Barang" wire:model.live="barang_id" :options="$barang" placeholder="---"
                    disabled />
                <x-input label="Tanggal Keluar" type="date" wire:model="tanggal" />
                <x-input label="Jumlah Keluar" wire:model.live="jumlah" type="number" min="1" />
            </div>
        </div>

        {{--  Details section --}}
        <hr class="my-5" />

        <div class="lg:grid grid-cols-5">
            <div class="col-span-2">
                <x-header title="Details" subtitle="More about the barang keluar" size="text-2xl" />
            </div>
            <div class="col-span-3 grid gap-3">
                <x-input label="Stok Sebelumnya" wire:model.live="stoklama" type="number" min="1" readonly />
                <x-input label="Satuan" wire:model.live="satuan" readonly />
                <x-input label="Stok Sekarang" wire:model.live="stokbaru" type="number" min="1" readonly />
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" link="/barangkeluars" />
            {{-- The important thing here is `type="submit"` --}}
            {{-- The spinner property is nice! --}}
            <x-button label="Save" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>

    </x-form>
</div>
