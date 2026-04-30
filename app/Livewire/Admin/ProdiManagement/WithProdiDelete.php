<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Livewire\Global\HasToast;
use App\Models\ProgramStudi\Fakultas;
use App\Models\ProgramStudi\Departemen;
use App\Models\ProgramStudi\Prodi;

trait WithProdiDelete
{
    use HasToast;

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
            'departemen' => Departemen::class,
            'fakultas' => Fakultas::class,
        ];
    }

    public function deleteProdi($id, $type, $isTrashed = false)
    {
        if (! $this->AuthCheck()) {
            return; 
        }
        $modelClass = $this->getModels()[$type] ?? null;

        $data = $isTrashed
            ? $modelClass::withTrashed()->find($id)
            : $modelClass::find($id);

        if (! $data) {
            $this->toast(type: 'unfound', variant: 'warning');
            return;
        }

        $this->prodiIdToDelete = $id;
        $this->prodiNamaToDelete = $this->getFormattedName($data);
        $this->prodiForDelete = $type;
        $this->isPermanentDelete = $isTrashed;
        $this->showProdiDelete = true;
    }

    private function getFormattedName($data): string
    {
        if (isset($data->strata) && isset($data->nama_pr)) {
            $strata = match ($data->strata) {
                'Sarjana' => 'S1',
                'Magister' => 'S2',
                'Doktor' => 'S3',
                default => $data->strata,
            };

            return 'Program Studi '.$strata.' '.$data->nama_pr;
        }
        if (isset($data->nama_dp)) {
            return 'Departemen '.$data->nama_dp;
        }
        if (isset($data->nama_fk)) {
            return 'Fakultas '.$data->nama_fk;
        }

        return null;
    }

    public function destroyProdi()
    {
        if (! $this->AuthCheck()) {
            return; 
        }
        if (! $this->prodiIdToDelete) {
            return;
        }

        $type = 'delete';

        try {
            $modelClass = $this->getModels()[$this->prodiForDelete] ?? null;
            $data = $modelClass::withTrashed()->findOrFail($this->prodiIdToDelete);

            $this->toast(variant: 'warning');

            if ($this->isPermanentDelete) {
                $type = 'permanent';
                $data->forceDelete();
            } else {
                $data->delete();
            }

            $this->dispatch('refresh-data-pr');
            $this->showProdiDelete = false;
            $this->toast(message: $this->prodiNamaToDelete, type: $type);
            $this->cleanupDeleteStateProdi();

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-pr');
            $this->showProdiDelete = false;
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    public function restoreProdi($id, $type)
    {
        if (! $this->AuthCheck()) {
            return; 
        }
        try {
            $modelClass = $this->getModels()[$type] ?? null;
            $prodi = $modelClass::withTrashed()->findOrFail($id);
            $message = $this->getFormattedName($prodi);
            $prodi->restore();

            $this->dispatch('refresh-data-pr');
            $this->showProdiDelete = false;
            $this->toast(message: $message, type: 'recycle');

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-pr');
            $this->showProdiDelete = false;
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    private function cleanupDeleteStateProdi()
    {
        $this->prodiIdToDelete = null;
        $this->prodiNamaToDelete = null;
        $this->showProdiDelete = false;
    }
}
