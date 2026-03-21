<?php

namespace App\Livewire\Admin\MatkulManagement;

use App\Models\Prodi;
use App\Models\Jurusan;
use App\Models\Fakultas;

trait WithMatkulDelete
{
    public $showProdiDelete = false;

    public $prodiIdToDelete;

    public $typeNamaToDelete;

    public $typeForDelete;

    public $notFoundText;

    public function deleteProdi($id, $type)
    {
        $models = [
            'prodi' => Prodi::class,
            'jurusan' => Jurusan::class,
            'fakultas' => Fakultas::class,
        ];

        $modelClass = $models[$type] ?? null;
        $prodiType = $modelClass ? $modelClass::find($id) : null;

        $this->notFoundText = ($type === 'prodi') ? 'Program Studi' : ucfirst($type);

        if (! $prodiType) {
            $this->js("Flux.toast({ variant: 'danger', text: '{$this->notFoundText} tidak ditemukan!' })");
            return;
        }

        $this->prodiIdToDelete = $id;
        $this->typeNamaToDelete = $prodiType->nama_prodi ?? $prodiType->nama_jurusan ?? $prodiType->nama_fakultas;
        $this->typeForDelete = $type;
        $this->showProdiDelete = true;
    }

    public function destroyProdi()
    {
        if (!$this->prodiIdToDelete) {
            return;
        }

        try {
            if ($this->typeForDelete == 'prodi') {
                $prodiType = Prodi::findOrFail($this->prodiIdToDelete);
            } elseif ($this->typeForDelete == 'jurusan') {
                $prodiType = Jurusan::findOrFail($this->prodiIdToDelete);
            } elseif ($this->typeForDelete == 'fakultas') {
                $prodiType = Fakultas::findOrFail($this->prodiIdToDelete);
            }

            $prodiType->delete();

            $this->js("Flux.toast('{$this->notFoundText} berhasil dihapus!')");

            $this->prodiIdToDelete = null;
            $this->typeNamaToDelete = null;

            $this->resetPage();
            $this->showProdiDelete = false;

        } catch (\Exception $e) {
            $this->js("Flux.toast({ variant: 'danger', text: 'Gagal menghapus!' })");
            $this->showProdiDelete = false;
        }
    }
}
