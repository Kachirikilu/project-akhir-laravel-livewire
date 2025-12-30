<?php

namespace App\Livewire\Admin\UserManagement\Concerns;

use App\Models\Prodi;
use Illuminate\Support\Facades\Auth;

trait WithProdiAutocomplete
{
    public $prodi_id;
    public $prodi_name_search = '';
    public $prodi_results = [];

    public function updatedProdiNameSearch($value)
    {
        if (strlen($value) < 1) {
            $this->prodi_results = [];
            return;
        }

        $this->prodi_results = Prodi::where('nama_prodi', 'like', "%{$value}%")
            ->limit(5)
            ->get(['id', 'nama_prodi', 'fakultas'])
            ->toArray();
    }

    public function selectProdi($id, $name)
    {
        $this->prodi_id = $id;
        $this->prodi_name_search = $name;
        $this->prodi_results = [];
    }
}
