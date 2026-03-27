<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Models\ProgramStudi\Prodi;
use App\Models\ProgramStudi\Jurusan;
use App\Models\ProgramStudi\Fakultas;

trait WithProdiDelete
{
    public $showProdiDelete = false;

    public $prodiIdToDelete;

    public $prodiNamaToDelete;

    public $prodiForDelete;

    public $notFoundText;

    public $isPermanentDelete = false;

    private function getModels()
    {
        return [
            'prodi' => Prodi::class,
            'jurusan' => Jurusan::class,
            'fakultas' => Fakultas::class,
        ];
    }

    public function deleteProdi($id, $type, $isTrashed = false)
    {
        $modelClass = $this->getModels()[$type] ?? null;
        
        $data = $isTrashed 
            ? $modelClass::withTrashed()->find($id) 
            : $modelClass::find($id);

        if (!$data) {
            $this->js("Flux.toast({ variant: 'danger', text: 'Data tidak ditemukan!' })");
            return;
        }

        $this->prodiIdToDelete = $id;
        $this->prodiNamaToDelete = $data->nama_prodi ?? $data->nama_jurusan ?? $data->nama_fakultas;
        $this->prodiForDelete = $type;
        $this->isPermanentDelete = $isTrashed;
        $this->showProdiDelete = true;
    }

    public function destroyProdi()
    {
        if (!$this->prodiIdToDelete) return;

        try {
            $modelClass = $this->getModels()[$this->prodiForDelete] ?? null;
            $data = $modelClass::withTrashed()->findOrFail($this->prodiIdToDelete);

            if ($this->isPermanentDelete) {
                $data->forceDelete();
                $message = "Data {$this->prodiNamaToDelete} BERHASIL DIHAPUS PERMANEN!";
            } else {
                $data->delete();
                $message = "Data {$this->prodiNamaToDelete} berhasil dipindahkan ke sampah!";
            }

            $this->js("Flux.toast('{$message}')");
            $this->cleanupDeleteState();
            $this->dispatch('refresh-data');

        } catch (\Exception $e) {
            $this->js("Flux.toast({ variant: 'danger', text: 'Gagal memproses permintaan!' })");
        }
    }

    public function restoreProdi($id, $type)
    {
        try {
            $modelClass = $this->getModels()[$type] ?? null;
            $data = $modelClass::withTrashed()->findOrFail($id);
            $data->restore();
            
            $nama = $data->nama_prodi ?? $data->nama_jurusan ?? $data->nama_fakultas;
            $this->js("Flux.toast('Data {$nama} berhasil dipulihkan!')");
            $this->dispatch('refresh-data');
        } catch (\Exception $e) {
            $this->js("Flux.toast({ variant: 'danger', text: 'Gagal memulihkan data!' })");
        }
    }

    private function cleanupDeleteState()
    {
        $this->prodiIdToDelete = null;
        $this->prodiNamaToDelete = null;
        $this->showProdiDelete = false;
    }
}
