<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Models\Prodi;
use App\Models\Jurusan;
use App\Models\Fakultas;

use Illuminate\Support\Facades\DB;

trait WithProdiDelete
{
    public $showProdiDelete = false;

    public $prodiIdToDelete;

    public $prodiNamaToDelete;

    public $prodiForDelete;

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
        $this->prodiNamaToDelete = $prodiType->nama_prodi ?? $prodiType->nama_jurusan ?? $prodiType->nama_fakultas;
        $this->prodiForDelete = $type;
        $this->showProdiDelete = true;
    }

    public function destroyProdi()
    {
        if (!$this->prodiIdToDelete) return;

        $models = [
            'prodi' => Prodi::class,
            'jurusan' => Jurusan::class,
            'fakultas' => Fakultas::class,
        ];

        try {
            $modelClass = $models[$this->prodiForDelete] ?? null;
            
            if ($modelClass) {
                $data = $modelClass::findOrFail($this->prodiIdToDelete);
                $data->delete();
                
                $this->js("Flux.toast('Data {$this->prodiNamaToDelete} berhasil dihapus!')");
            }

            $this->cleanupDeleteState();
            $this->dispatch('refresh-data'); 
            
            if (method_exists($this, 'resetPage')) {
                $this->resetPage();
            }

        } catch (\Exception $e) {
            $this->js("Flux.toast({ variant: 'danger', text: 'Gagal menghapus data!' })");
            $this->showProdiDelete = false;
        }
    }

    private function cleanupDeleteState()
    {
        $this->prodiIdToDelete = null;
        $this->prodiNamaToDelete = null;
        $this->showProdiDelete = false;
        $this->showProdiDelete = false;
    }
}
