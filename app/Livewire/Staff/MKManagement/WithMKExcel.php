<?php

namespace App\Livewire\Staff\MKManagement;

use App\Exports\MKExport;
use App\Models\ProgramStudi\Departemen;
use App\Models\ProgramStudi\Fakultas;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

trait WithMKExcel
{
    public function exportMKExcel()
    {
        $univ = env('UNIVERSITAS');
        $UNIV = strtoupper(env('UNIVERSITAS'));
        $tag = 'Mata Kulliah';
        $TAG = strtoupper($tag);

        $fileName = 'Data_'.$tag.'_'.$univ.'_'.now()->format('Y-m-d').'.xlsx';

        $queryMK = $this->inputMKSearch();
        $this->buttonMKSwitch($queryMK);
        $this->buttonMKFilter($queryMK);

        if ($this->selectedFkId) {
            $fk = Fakultas::find($this->selectedFkId);
            $fileName = 'Data_'.$tag.'_'.$fk->fakultas_fk.'_'.$univ.'_'.now()->format('Y-m-d').'.xlsx';
            $title = 'DATA '.$TAG.' '.strtoupper($fk->fakultas_fk). ' ' . $UNIV;
        } elseif ($this->selectedDpId) {
            $dp = Departemen::find($this->selectedDpId);
            $fileName = 'Data_'.$tag.'_'.$dp->departemen_dp.'_'.$univ.'_'.now()->format('Y-m-d').'.xlsx';
            $title = 'DATA '.$TAG.' '.strtoupper($dp->departemen_dp). ' ' . $UNIV;
        } elseif ($this->selectedPrId && $this->filterMK !== '') {
            $pr = Prodi::find($this->selectedPrId);
            $fileName = 'Data_'.$tag.'_'.$pr->prodi.'_'.$univ.'_'.now()->format('Y-m-d').'.xlsx';
            $title = 'DATA '.$TAG.' PROGRAM STUDI '.strtoupper($pr->prodi). ' ' . $UNIV;
        } elseif ($this->filterMK == '') {
            $pr = Auth::user()->prodi;
            $fileName = 'Data_'.$tag.'_'.$pr.'_'.$univ.'_'.now()->format('Y-m-d').'.xlsx';
            $title = 'DATA '.$TAG.' PROGRAM STUDI '.strtoupper($pr). ' ' . $UNIV;
        } else {
            $title = 'DATA '.$TAG.' '.$UNIV;
        }

        return Excel::download(new MKExport($queryMK, $this->switchTable, $this->filterMK, $this->selectedPrId, $this->selectedDpId, $this->selectedFkId, $title), $fileName);
    }
}
