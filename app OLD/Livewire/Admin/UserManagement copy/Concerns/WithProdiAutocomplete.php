<?php

namespace App\Livewire\Admin\UserManagement\Concerns;

use App\Models\Prodi;
use Illuminate\Support\Facades\Auth;

trait WithProdiAutocomplete
{
    public $prodi_id;
    public $prodiNameSearch = '';
    public $prodiResults = [];

    public function updatedProdiNameSearch($value)
    {
        if (strlen($value) < 1) {
            $this->prodiResults = [];
            return;
        }

        $this->prodiResults = Prodi::where('nama_prodi', 'like', "%{$value}%")
            ->limit(5)
            ->get(['id', 'nama_prodi', 'fakultas'])
            ->toArray();
    }

    public function selectProdi($id, $name)
    {
        $this->prodi_id = $id;
        $this->prodiNameSearch = $name;
        $this->prodiResults = [];
    }
}
