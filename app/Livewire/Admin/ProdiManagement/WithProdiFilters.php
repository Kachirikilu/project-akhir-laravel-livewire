<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Models\ProgramStudi\Prodi;
use Livewire\WithPagination;

trait WithProdiFilters
{
    use WithPagination;

    public $search = '';

    public $filter = '';

    public $sortField = 'kode';

    public $sortDirection = 'asc';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function inputProdiSearch()
    {
        $query = Prodi::query()->with(['jurusan_rel.fakultas_rel']);
        $searchTerm = '%'.$this->search.'%';

        if (! empty($this->search)) {
            $query->where(function ($q) use ($searchTerm) {
                // Prodi
                $q->where('nama_prodi', 'like', $searchTerm)
                    ->orWhere('nama_strata', 'like', $searchTerm);

                if (is_numeric($this->search)) {
                    $q->orWhere('prodis.id', $this->search);
                }
                // Jurusan
                $q->orWhereHas('jurusan_rel', function ($jq) use ($searchTerm) {
                    $jq->where('nama_jurusan', 'like', $searchTerm)
                        ->orWhereRaw("CONCAT('Jurusan ', nama_jurusan) LIKE ?", [$searchTerm]);
                    // if (is_numeric($this->search)) {
                    //     $jq->orWhere('jurusans.id', $this->search);
                    // }
                });
                // Fakultas
                $q->orWhereHas('jurusan_rel.fakultas_rel', function ($fq) use ($searchTerm) {
                    $fq->where('nama_fakultas', 'like', $searchTerm)
                        ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", [$searchTerm]);
                    // if (is_numeric($this->search)) {
                    //     $fq->orWhere('fakultas.id', $this->search);
                    // }
                });

            });
        }

        if (! empty($this->selectedJurusanId)) {
            $query->where('jurusan_id', $this->selectedJurusanId);
        }
        if (! empty($this->selectedFakultasId)) {
            $query->whereHas('jurusan_rel', function ($q) {
                $q->where('fakultas_id', $this->selectedFakultasId);
            });
        }

        $this->sortFieldOrderProdi($query);

        return $query;
    }

    public function filterBy($strata)
    {
        $this->filter = $strata;
        $this->resetPage();
    }

    // public function buttonStrataFilter($query)
    // {
    //     // 1. Terapkan filter relasi Fakultas/Jurusan pada query utama
    //     $query->when($this->selectedFakultasId, function ($q) {
    //         $q->whereHas('jurusan_rel', function ($rel) {
    //             $rel->where('fakultas_id', $this->selectedFakultasId);
    //         });
    //     });

    //     $query->when($this->selectedJurusanId, function ($q) {
    //         $q->where('jurusan_id', $this->selectedJurusanId);
    //     });

    //     // 2. Hitung semua statistik (Total, Sarjana, Magister, Doktor) dalam SATU query
    //     $stats = Prodi::query()
    //         ->when($this->selectedFakultasId, function ($q) {
    //             $q->whereExists(function ($sub) {
    //                 $sub->select(\DB::raw(1))
    //                     ->from('jurusans')
    //                     ->whereColumn('jurusans.id', 'prodis.jurusan_id')
    //                     ->where('fakultas_id', $this->selectedFakultasId);
    //             });
    //         })
    //         ->when($this->selectedJurusanId, function ($q) {
    //             $q->where('jurusan_id', $this->selectedJurusanId);
    //         })
    //         ->selectRaw("
    //             COUNT(*) as total,
    //             SUM(CASE WHEN nama_strata = 'Sarjana' THEN 1 ELSE 0 END) as sarjana,
    //             SUM(CASE WHEN nama_strata = 'Magister' THEN 1 ELSE 0 END) as magister,
    //             SUM(CASE WHEN nama_strata = 'Doktor' THEN 1 ELSE 0 END) as doktor
    //         ")->first();

    //     // 3. Terapkan filter strata untuk tampilan tabel utama
    //     if (in_array($this->filter, ['sarjana', 'magister', 'doktor'])) {
    //         $query->where('nama_strata', ucfirst($this->filter));
    //     }

    //     return [
    //         $stats->total ?? 0,
    //         $stats->sarjana ?? 0,
    //         $stats->magister ?? 0,
    //         $stats->doktor ?? 0
    //     ];
    // }

    public function resetInputFilter()
    {
        $this->reset(['search', 'filter']);
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function sortFieldOrderProdi($query)
    {
        $query->select('prodis.*');

        if ($this->sortField === 'prodi') {
            $query->orderBy('prodis.nama_prodi', $this->sortDirection);
        } elseif ($this->sortField === 'jurusan') {
            $query->leftJoin('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
                ->orderBy('jurusans.nama_jurusan', $this->sortDirection);
        } elseif ($this->sortField === 'fakultas') {
            $query->leftJoin('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
                ->leftJoin('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id')
                ->orderBy('fakultas.nama_fakultas', $this->sortDirection);
        } elseif ($this->sortField === 'strata') {
            $query->orderBy('prodis.nama_strata', $this->sortDirection);
        } elseif ($this->sortField === 'kode') {
            if ($this->switchTable === 'prodi') {
                $query->leftJoin('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
                    ->leftJoin('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id')
                    ->select('prodis.*')
                    ->orderByRaw('COALESCE(prodis.kode_pr, jurusans.kode_jr, fakultas.kode_fk) '.$this->sortDirection);
            } elseif ($this->switchTable === 'jurusan') {
                $query->leftJoin('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
                    ->leftJoin('fakultas', 'jurusans.fakultas_id', '=', 'fakultas.id')
                    ->select('jurusans.*')
                    ->orderByRaw('COALESCE(jurusans.kode_jr, fakultas.kode_fk) '.$this->sortDirection);
            } elseif ($this->switchTable === 'fakultas') {
                $query->orderBy('kode_fk', $this->sortDirection);
            }
        } else {
            $query->orderBy('prodis.id', $this->sortDirection);
        }

        if ($this->filter === 'jurusan') {
            $query->whereHas('jurusan_rel');
        } elseif ($this->filter === 'fakultas') {
            $query->whereHas('jurusan_rel.fakultas');
        }

        return $query;
    }
}
