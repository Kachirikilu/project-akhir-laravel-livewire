<?php

namespace App\Livewire\Staff\KelasManagement\JadwalManagement\SesiManagement;

use App\Livewire\Global\HasSortir;
use App\Models\Kelas\KelasSesi;
use Livewire\WithPagination;

trait WithSesiFilters
{
    use HasSortir;
    use WithPagination;

    public $search = '';

    public $filterSesi = '';

    public $searchBobotSesi = '';

    public function inputSesiSearch($idJadwal)
    {
        $querySesi = KelasSesi::where('kj_id', $idJadwal)
            ->with(['jadwal_rel', 'jadwal_rel.kelas_rel']);
        $search = $this->search;

        if (! empty($search)) {
            // $querySesi->searchJadwalSesi($search);
        }

        $this->sortFieldOrderSesi($querySesi);

        return $querySesi;
    }

    public function filterBySesi($kelas)
    {
        $this->filterSesi = $kelas;
        $this->resetPage();
    }

    public function resetInputFilter()
    {
        $this->reset(['search', 'filterJadwal']);
        $this->resetPage();
    }

    public function sortFieldOrderSesi($querySesi)
    {
        $querySesi->select('kelas_sesi.*');

        return match ($this->sortField) {
            // 'kode' => $this->applyJadwalKodeSort($querySesi),
            // 'kode' => $querySesi->orderByRaw("CONCAT(kelas_jadwals.label_kelas, kelas_jadwals.kode_wilayah, kelas_jadwals.tanggal_mulai) ".$this->sortDirection),
            // 'kelas' => $querySesi->orderBy('kelas_jadwals.nama_kelas', $this->sortDirection),
            // 'label_kelas' => $querySesi->orderByRaw("CONCAT(kelas_jadwals.label_kelas, kelas_jadwals.kode_wilayah) ".$this->sortDirection),
            // 'hari_pelaksanaan' => $querySesi->orderBy('kelas_jadwals.hari_pelaksanaan', $this->sortDirection),
            // 'jam_pelaksanaan' => $querySesi->orderBy('kelas_jadwals.jam_mulai', $this->sortDirection),
            // 'kapasitas' => $querySesi
            //     ->withCount('mahasiswas')
            //     ->orderBy('mahasiswas_count', $this->sortDirection),
            // 'tanggal_pelaksanaan' => $querySesi->orderBy('kelas_jadwals.tanggal_mulai', $this->sortDirection),
            // 'created_at' => $querySesi->orderBy('kelas_jadwals.created_at', $this->sortDirection),
            // 'updated_at' => $querySesi->orderBy('kelas_jadwals.updated_at', $this->sortDirection),
            default => $querySesi->orderBy('kelas_sesi.id', $this->sortDirection),
        };
    }
}
