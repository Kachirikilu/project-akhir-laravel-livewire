<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Exports\ProdiExport;
use App\Models\ProgramStudi\Departemen;
use App\Models\ProgramStudi\Fakultas;
use Maatwebsite\Excel\Facades\Excel;

trait WithProdiExcel
{
    public function exportProdiExcel()
    {
        $univ = env('UNIVERSITAS');
        $UNIV = strtoupper(env('UNIVERSITAS'));

        $filter = '';
        if ($this->filterPr !== '') {
            $filter = ' '.ucwords($this->filterPr);
        }

        $tag = 'Program Studi'.$filter;
        $TAG = strtoupper($tag);

        $fileName = 'Data_'.$tag.'_'.$univ.'_'.now()->format('Y-m-d').'.xlsx';
        $title = 'DATA '.$TAG.' '.$UNIV;

        if ($this->switchTable == 'fakultas') {
            $queryProdi = $this->inputFkSearch();
            $tag = 'Fakultas';
        } elseif ($this->switchTable == 'departemen') {
            $queryProdi = $this->inputDpSearch();
            $tag = 'Departemen';
        } else {
            $queryProdi = $this->inputPrSearch();
            $this->buttonStrataFilter($queryProdi);
        }

        if ($this->switchTable == 'fakultas') {
            $fileName = 'Data_'.$tag.'_'.$univ.'_'.now()->format('Y-m-d').'.xlsx';
            $title = 'DATA '.$TAG.' '.$UNIV;
        } elseif ($this->switchTable == 'departemen') {
            if ($this->selectedFkId) {
                $fk = Fakultas::find($this->selectedFkId);
                $fileName = 'Data_'.$tag.'_'.$fk->fakultas_fk.'_'.$univ.'_'.now()->format('Y-m-d').'.xlsx';
                $title = 'DATA '.$TAG.' '.strtoupper($fk->fakultas_fk).' '.$UNIV;
            } else {
                $fileName = 'Data_'.$tag.'_'.$univ.'_'.now()->format('Y-m-d').'.xlsx';
                $title = 'DATA '.$TAG.' '.$UNIV;
            }
        } else {
            if ($this->selectedFkId) {
                $fk = Fakultas::find($this->selectedFkId);
                $fileName = 'Data_'.$tag.'_'.$fk->fakultas_fk.'_'.$univ.'_'.now()->format('Y-m-d').'.xlsx';
                $title = 'DATA '.$TAG.' '.strtoupper($fk->fakultas_fk).' '.$UNIV;
            } elseif ($this->selectedDpId) {
                $dp = Departemen::find($this->selectedDpId);
                $fileName = 'Data_'.$tag.'_'.$dp->departemen_dp.'_'.$univ.'_'.now()->format('Y-m-d').'.xlsx';
                $title = 'DATA '.$TAG.' '.strtoupper($dp->departemen_dp).' '.$UNIV;
            }
        }

        return Excel::download(new ProdiExport($queryProdi, $this->switchTable, $title), $fileName);
    }
}
