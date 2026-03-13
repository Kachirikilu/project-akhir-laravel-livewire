<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Models\Fakultas;
use App\Models\Jurusan;
use App\Models\Prodi;

trait WithProdiDelete
{
    public $showProdiDelete = false;

    public $prodiIdToDelete;

    public $typeNamaToDelete;

    public $typeForDelete;

    public $notFoundText;

    public function deleteProdi($id, $type)
    {
        if ($type == 'prodi') {
            $prodiType = Prodi::find($id);
        } elseif ($type == 'jurusan') {
            $prodiType = Jurusan::find($id);
        } elseif ($type == 'fakultas') {
            $prodiType = Fakultas::find($id);
        }

        if ($type == 'prodi') {
            $this->notFoundText = 'Program Studi';
        } else {
            $this->notFoundText = ucfirst($type);
        }

        if (! $prodiType) {
            session()->flash('error', $this->notFoundText .' tidak ditemukan!');

            return;
        }

        $this->prodiIdToDelete = $id;
        $this->typeNamaToDelete = $prodiType->nama_prodi ?? $prodiType->nama_jurusan ?? $prodiType->nama_fakultas;
        $this->typeForDelete = $type;

        $this->showProdiDelete = true;
        // $this->js("Flux.modal('prodi-delete').show()");
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
