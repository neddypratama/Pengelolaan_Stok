<?php

namespace App\Http\Livewire\BarangMasuks;

use Livewire\Volt\Component;
use App\Models\Barang;
use App\Models\BarangMasuk;
use Mary\Traits\Toast;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;

new class extends Component {
    // We will use it later
    use Toast, WithFileUploads;

    // Component parameter
    public BarangMasuk $masuk;

    #[Rule('required|unique:barang_masuks')]
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
            'kode' => $this->generateKodemasuk(),
            'stoklama' => $this->generateStok(),
            'stokbaru' => $this->generateNewStok(),
            'satuan' => $this->generateSatuan(),
        ];
    }

    public function generateKodemasuk()
    {
        // Ambil data terakhir berdasarkan id_masuk (paling baru)
        $latestmasuk = BarangMasuk::latest('id')->first();

        // dd(((int) substr($latestmasuk->kode_masuk, 2)));
        // Jika ada data terakhir, ambil nomor urutnya dan tambahkan 1
        // substr($latestmasuk->kode_masuk, 2) digunakan untuk menghapus "TM" di depan
        $nextNumber = $latestmasuk ? ((int) substr($latestmasuk->kode, 3)) + 1 : 1;

        // Format kode dengan "TM" + nomor urut yang dipad dengan nol agar tetap 7 digit
        $this->kode = 'TM-' . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
        return $this->kode;
    }

    public function generateStok()
    {
        $barang = Barang::find($this->barang_id);
        if (!$barang) {
            $this->stoklama = 0;
        } else {
            $this->stoklama = $barang->stok;
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
        $this->stokbaru = $this->jumlah + $this->stoklama;
        return $this->stokbaru;
    }

    public function save(): void
    {
        // Validate
        $data = $this->validate();

        // Create
        $masuk = BarangMasuk::create($data);
        $barang = Barang::find($this->barang_id);
        $barang->update([
            'stok' => $barang->stok + $this->jumlah,
        ]);

        logActivity('created', $barang->name . ' masuk sejumlah ' . $this->jumlah);
        logActivity('updated', 'Merubah stok barang ' . $barang->name);

        // You can toast and redirect to any route
        $this->success('Barang masuk berhasil dibuat!', redirectTo: '/barangmasuks');
    }
};

?>

<div>
    <x-header title="Create" separator />

    <x-form wire:submit="save">
        {{--  Basic section  --}}
        <div class="lg:grid grid-cols-5">
            <div class="col-span-2">
                <x-header title="Basic" subtitle="Basic info from barang masuk" size="text-2xl" />
            </div>

            <div class="col-span-3 grid gap-3">
                <x-input label="Kode Masuk" wire:model="kode" readonly />
                <x-select label="Nama Barang" wire:model.live="barang_id" :options="$barang" placeholder="---" />
                <x-input label="Tanggal Masuk" type="date" wire:model="tanggal" />
                <x-input label="Jumlah Masuk" wire:model.live="jumlah" type="number" min="1" />
            </div>
        </div>

        {{--  Details section --}}
        <hr class="my-5" />

        <div class="lg:grid grid-cols-5">
            <div class="col-span-2">
                <x-header title="Details" subtitle="More about the barang masuk" size="text-2xl" />
            </div>
            <div class="col-span-3 grid gap-3">
                <x-input label="Stok Sebelumnya" wire:model.live="stoklama" type="number" min="1" readonly />
                <x-input label="Satuan" wire:model.live="satuan" readonly />
                <x-input label="Stok Sekarang" wire:model.live="stokbaru" type="number" min="1" readonly />
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" link="/barangmasuks" />
            {{-- The important thing here is `type="submit"` --}}
            {{-- The spinner property is nice! --}}
            <x-button label="Create" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>

    </x-form>
</div>
