<?php

namespace App\Livewire\Staff\RPSManagement;

use App\Exports\CPLExport;
use App\Exports\CPMKExport;
use App\Exports\DosenExport;
use App\Exports\ReferensiExport;
use App\Exports\RPSExport;
use App\Exports\SubCPMKExport;
use App\Models\Akademik\CPL;
use App\Models\Akademik\CPMK;
use App\Models\Akademik\MataKuliah;
use App\Models\Akademik\RPS;
use App\Models\Akademik\SubCPMK;
use App\Models\Auth\User;
use App\Models\ProgramStudi\Fakultas;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

trait WithOBEExcel
{
    public function exportOBEExcel()
    {
        $univ = env('UNIVERSITAS');
        $UNIV = strtoupper($univ);

        $now = now();
        $sixMonthsAgo = now()->subMonths(6);
        $currentYear = now()->year;
        $threeYearsAgo = now()->subYears(3);
        $fiveYearsAgo = now()->subYears(5);
        $tenYearsAgo = now()->subYears(10);

        $sInput = '';
        $sINPUT = '';

        if (($this->switchTable == 'dosen' && $this->filterStatus == '') || ($this->switchTable == 'rps' && $this->filterRPS == '')) {
            $pr = Auth::user()->prodi;
            $pr_pr = Auth::user()->prodi_pr;
            $sInput .= '_'.$pr;
            $sINPUT .= strtoupper(' '.$pr_pr);
        }

        if ($this->selectedFkId && $this->switchTable == 'dosen') {
            if ($this->filterStatus !== '') {
                $fk = Fakultas::find($this->selectedFkId);
                $sInput .= '_'.$fk->fakultas_fk;
                $sINPUT .= strtoupper(' '.$fk->fakultas_fk);
            }
        } elseif ($this->selectedPrId && ($this->switchTable == 'rps' || $this->switchTable == 'cpmk' || $this->switchTable == 'scpmk' || $this->switchTable == 'cpl' || $this->switchTable == 'ref' || $this->switchTable == 'dosen')) {
            if ($this->filterStatus !== '' || $this->switchTable !== 'dosen') {
                if ($this->switchTable != 'rps' || $this->filterRPS != '' || $this->selectedPrId != Auth::user()->pr_id) {
                    $pr = Prodi::find($this->selectedPrId);
          
                    $sInput .= '_'.$pr->prodi;
                    if ($this->switchTable == 'rps' && $this->filterRPS == '') {
                        $sINPUT .= strtoupper(' & '.$pr->prodi_pr);
                    } else {
                        $sINPUT .= strtoupper(' '.$pr->prodi_pr);
                    }
                }
            }
        }

        if ($this->switchTable == 'rps') {
            if ($this->selectedMKId) {
                $mk = MataKuliah::find($this->selectedMKId);
                $kodeMK = str_replace('-', '', $mk->kode);
                $sInput .= '_Mata Kuliah '.$kodeMK;
                $sINPUT .= strtoupper(' Mata Kuliah '.$mk->kode);
            }
            if ($this->selectedDosenId) {
                $dosen = User::whereHas('dosen', function ($q) {
                    $q->where('dosens.id', $this->selectedDosenId);
                })->first();
                $sInput .= '_Dosen '.$dosen->name;
                $sINPUT .= strtoupper(' Dosen '.$dosen->name);
            }
        }

        if ($this->selectedRPSId && ($this->switchTable == 'cpmk' || $this->switchTable == 'scpmk' || $this->switchTable == 'cpl' || $this->switchTable == 'ref' || $this->switchTable == 'dosen')) {
            $rps = RPS::find($this->selectedRPSId);
            $kodeRPS = str_replace('-', '', $rps->kode);
            $sInput .= '_RPS '.$kodeRPS;
            $sINPUT .= strtoupper(' RPS '.$rps->kode);
        }

        if ($this->selectedCPLId && $this->switchTable == 'cpmk') {
            $cpl = CPL::find($this->selectedCPLId);
            $kodeCPL = str_replace('-', '', $cpl->kode);
            $sInput .= '_CPL '.$kodeCPL;
            $sINPUT .= strtoupper(' CPL '.$cpl->kode);
        }

        if ($this->selectedCPMKId && ($this->switchTable == 'scpmk' || $this->switchTable == 'cpl' || $this->switchTable == 'ref')) {
            $cpmk = CPMK::find($this->selectedCPMKId);
            $kodeCPMK = str_replace('-', '', $cpmk->kode);
            $sInput .= '_CPMK '.$kodeCPMK;
            $sINPUT .= strtoupper(' CPMK '.$cpmk->kode);
        }

        if ($this->selectedSCPMKId && $this->switchTable == 'ref') {
            $scpmk = SubCPMK::find($this->selectedSCPMKId);
            $kodeSCPMK = str_replace('-', '', $scpmk->kode);
            $sInput .= '_SCPMK '.$kodeSCPMK;
            $sINPUT .= strtoupper(' SCPMK '.$scpmk->kode);
        }

        switch ($this->switchTable) {
            case 'rps':
                $queryOBE = $this->inputRPSSearch();
                $tag = 'RPS';
                $TAG = 'RENCANA PEMBELAJARAN SEMESTER';
                $this->buttonRPSFilter($queryOBE, $currentYear, $fiveYearsAgo->year);
                break;
            case 'cpmk':
                $queryOBE = $this->inputCPMKSearch();
                $tag = 'CPMK';
                $TAG = 'CAPAIAN PEMBELAJARAN MATA KULIAH';
                $this->buttonCPMKFilter($queryOBE, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
                break;
            case 'scpmk':
                $queryOBE = $this->inputSCPMKSearch();
                $tag = 'Sub-CPMK';
                $TAG = 'SUB CAPAIAN PEMBELAJARAN MATA KULIAH';
                $this->buttonSCPMKFilter($queryOBE, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
                break;
            case 'cpl':
                $queryOBE = $this->inputCPLSearch();
                $tag = 'CPL';
                $TAG = 'CAPAIAN PEMBELAJARAN LULUSAN';
                $this->buttonCPLFilter($queryOBE, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
                break;
            case 'ref':
                $queryOBE = $this->inputRefSearch();
                $tag = 'Referensi';
                $TAG = strtoupper($tag);
                $this->buttonRefFilter($queryOBE, $now, $sixMonthsAgo, $currentYear, $threeYearsAgo->year, $fiveYearsAgo->year, $tenYearsAgo->year);
                break;
            case 'dosen':
                $queryOBE = $this->inputUserSearch();
                $tag = 'Dosen';
                $TAG = strtoupper($tag);
                $this->buttonUserFilter($queryOBE);
                break;
        }

        $fileName = 'Data_'.$tag.$sInput.'_'.$univ.'_'.now()->format('Y-m-d').'.xlsx';
        $title = 'DATA '.$TAG.$sINPUT.' '.$UNIV;

        switch ($this->switchTable) {
            case 'rps':
                return Excel::download(new RPSExport($queryOBE, $this->switchTable, $title), $fileName);
                break;
            case 'cpmk':
                return Excel::download(new CPMKExport($queryOBE, $this->switchTable, $title), $fileName);
                break;
            case 'scpmk':
                return Excel::download(new SubCPMKExport($queryOBE, $this->switchTable, $title), $fileName);
                break;
            case 'cpl':
                return Excel::download(new CPLExport($queryOBE, $this->switchTable, $title), $fileName);
                break;
            case 'ref':
                return Excel::download(new ReferensiExport($queryOBE, $this->switchTable, $title), $fileName);
                break;
            case 'dosen':
                return Excel::download(new DosenExport($queryOBE, $this->switchTable, $title), $fileName);
                break;
        }
    }
}
