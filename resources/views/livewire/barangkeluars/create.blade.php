<?php

use Livewire\Volt\Component;
use App\Models\Barang;
use App\Models\BarangKeluar;
use Mary\Traits\Toast;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;

new class extends Component {
    // We will use it later
    use Toast, WithFileUploads;

    // Component parameter
    public BarangKeluar $keluar;

    #[Rule('required|unique:barang_keluars')]
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
            'kode' => $this->generateKodeKeluar(),
            'stoklama' => $this->generateStok(),
            'stokbaru' => $this->generateNewStok(),
            'satuan' => $this->generateSatuan(),
        ];
    }

    public function generateKodeKeluar()
    {
        // Ambil data terakhir berdasarkan id_Keluar (paling baru)
        $latestKeluar = BarangKeluar::latest('id')->first();

        // dd(((int) substr($latestKeluar->kode_Keluar, 2)));
        // Jika ada data terakhir, ambil nomor urutnya dan tambahkan 1
        // substr($latestKeluar->kode_Keluar, 2) digunakan untuk menghapus "TM" di depan
        $nextNumber = $latestKeluar ? ((int) substr($latestKeluar->kode, 3)) + 1 : 1;

        // Format kode dengan "TM" + nomor urut yang dipad dengan nol agar tetap 7 digit
        $this->kode = 'TK-' . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
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
        $this->stokbaru = $this->stoklama - $this->jumlah;
        return $this->stokbaru;
    }

    public function save(): void
    {
        // Validate
        $data = $this->validate();

        // Create
        $Keluar = BarangKeluar::create($data);
        $barang = Barang::find($this->barang_id);
        $barang->update([
            'stok' => $barang->stok - $this->jumlah,
        ]);
        logActivity('created', $barang->name . ' keluar sejumlah ' . $this->jumlah);
        logActivity('updated', 'Merubah stok barang ' . $barang->name);

        // You can toast and redirect to any route
        $this->success('Barang Keluar berhasil dibuat!', redirectTo: '/barangkeluars');
    }
};

?>

<div>
    <x-header title="Create" separator />

    <x-form wire:submit="save">
        {{--  Basic section  --}}
        <div class="lg:grid grid-cols-5">
            <div class="col-span-2">
                <x-header title="Basic" subtitle="Basic info from barang keluar" size="text-2xl" />
            </div>

            <div class="col-span-3 grid gap-3">
                <x-input label="Kode Keluar" wire:model="kode" readonly />
                <x-select label="Nama Barang" wire:model.live="barang_id" :options="$barang" placeholder="---" />
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
            <x-button label="Create" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>

    </x-form>
</div>
