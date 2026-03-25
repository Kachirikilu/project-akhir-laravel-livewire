<?php

namespace App\Livewire\Staff\MatkulManagement;

use App\Models\MataKuliah;
use Illuminate\Support\Facades\DB;

trait WithMatkulDelete
{
    public $showMKDelete = false;

    public $mkIdToDelete;

    public $mkNamaToDelete;

    public $mkKodeToDelete;


    public function deleteMK($id)
    {
        $mk = MataKuliah::find($id);

        if (! $mk) {
            $this->js("Flux.toast({ variant: 'danger', text: 'Mata Kuliah tidak ditemukan!' })");
            return;
        }

        $this->mkIdToDelete = $id;
        $this->mkNamaToDelete = $mk->matkul;
        $this->mkKodeToDelete = $mk->kode;
        
        $this->showMKDelete = true;
    }

    public function destroyMK()
    {
       if (! $this->mkIdToDelete) {
            return;
        }

        try {
            DB::transaction(function () {
                $mk = MataKuliah::findOrFail($this->mkIdToDelete);
                $mk->prodis()->detach();
                $mk->delete();
            });

            $this->js("Flux.toast('Mata Kuliah {$this->mkNamaToDelete} berhasil dihapus!')");
            $this->cleanupDeleteState();
            $this->dispatch('refresh-data'); 
            if (method_exists($this, 'resetPage')) {
                $this->resetPage();
            }

        } catch (\Exception $e) {
            $this->js("Flux.toast({ variant: 'danger', text: 'Gagal menghapus: ' . $e->getMessage() })");
            $this->showMKDelete = false;
        }
    }

    private function cleanupDeleteState()
    {
        $this->mkIdToDelete = null;
        $this->mkNamaToDelete = null;
        $this->mkKodeToDelete = null;
        $this->showMKDelete = false;
    }
}
