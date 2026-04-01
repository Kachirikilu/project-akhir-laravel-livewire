<?php

namespace App\Livewire\Staff\MatkulManagement;

use App\Models\Akademik\MataKuliah;
use Illuminate\Support\Facades\DB;
use App\Livewire\Global\HasToast;

trait WithMatkulDelete
{
    use HasToast;
    public $showMKDelete = false;
    public $mkIdToDelete;
    public $mkNamaToDelete;
    public $mkKodeToDelete;
    public $isPermanentDelete = false;

    /**
     * DELETE (SOFT & FORCE DELETE GABUNGAN)
     */
    public function deleteMK($id, $isTrashed = false)
    {
        if (! $this->AuthCheck('staff')) {
            return; 
        }
        $mk = $isTrashed ? MataKuliah::withTrashed()->find($id) : MataKuliah::find($id);

        if (!$mk) {
            $this->toast(message: 'Mata Kuliah', type: 'unfound', variant: 'warning');
            return;
        }

        $this->mkIdToDelete = $id;
        $this->mkNamaToDelete = $mk->matkul;
        $this->mkKodeToDelete = $mk->kode;
        $this->isPermanentDelete = $isTrashed;
        
        $this->showMKDelete = true;
    }

    /**
     * PROSES EKSEKUSI PENGHAPUSAN
     */
    public function destroyMK()
    {
        if (! $this->AuthCheck('staff')) {
            return; 
        }
       if (!$this->mkIdToDelete) return;

        $type = 'delete';

        try {
            DB::transaction(function () {
                $mk = MataKuliah::withTrashed()->findOrFail($this->mkIdToDelete);

                if ($this->isPermanentDelete) {
                    $mk->prodis()->detach();
                    $mk->forceDelete();
                } else {
                    $mk->delete();
                }
            });

            if ($this->isPermanentDelete) {
                $type = 'permanent';
            }

            $this->toast(message: 'Mata Kuliah ' . $this->mkNamaToDelete, type: $type);

            $this->cleanupDeleteStateMK();
            $this->dispatch('refresh-data'); 
            
            if (method_exists($this, 'resetPage')) {
                $this->resetPage();
            }

        } catch (\Exception $e) {
            $this->dispatch('refresh-data');
            $this->showMKDelete = false;
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    /**
     * RESTORE MATA KULIAH
     */
    public function restoreMK($id)
    {
        if (! $this->AuthCheck('staff')) {
            return; 
        }
        try {
            $mk = MataKuliah::withTrashed()->findOrFail($id);
            $mk->restore();

            $this->dispatch('refresh-data');
            $this->toast(message: 'Mata Kuliah '. $mk->matkul, type: 'recycle', isAkun: true);

        } catch (\Exception $e) {
            $this->dispatch('refresh-data');
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    private function cleanupDeleteStateMK()
    {
        $this->mkIdToDelete = null;
        $this->mkNamaToDelete = null;
        $this->mkKodeToDelete = null;
        $this->isPermanentDelete = false;
        $this->showMKDelete = false;
    }
}