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
            $querySesi->searchKelasSesi($search);
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
        $this->reset(['search', 'filterSesi']);
        $this->resetPage();
    }

    public function sortFieldOrderSesi($querySesi)
    {
        $querySesi->select('kelas_sesi.*')
            ->withCount('kehadirans')
            ->leftJoin('kelas_sesi_overrides', 'kelas_sesi.id', '=', 'kelas_sesi_overrides.sesi_id')
            ->leftJoin('kelas_jadwals', 'kelas_sesi.kj_id', '=', 'kelas_jadwals.id');

        return match ($this->sortField) {
            'pertemuan_ke'        => $querySesi->orderBy('kelas_sesi.pertemuan_ke', $this->sortDirection),
            'hari_pelaksanaan'    => $querySesi->orderByRaw("WEEKDAY(kelas_sesi.tanggal) " . $this->sortDirection),
            'tanggal_pelaksanaan' => $querySesi->orderBy('kelas_sesi.tanggal', $this->sortDirection),
            'jam_pelaksanaan'     => $querySesi->orderByRaw("COALESCE(kelas_sesi_overrides.jam_mulai, kelas_jadwals.jam_mulai) " . $this->sortDirection),
            'jumlah_kehadiran'    => $querySesi
                ->withCount('kehadirans')
                ->orderBy('kehadirans_count', $this->sortDirection),
            'created_at'          => $querySesi->orderBy('kelas_sesi.created_at', $this->sortDirection),
            'updated_at'          => $querySesi->orderBy('kelas_sesi.updated_at', $this->sortDirection),
            default               => $querySesi->orderBy('kelas_sesi.id', $this->sortDirection),
        };
    }
}
