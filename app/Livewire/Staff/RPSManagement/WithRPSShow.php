<?php

namespace App\Livewire\Staff\RPSManagement;

use App\Models\Akademik\RPS;

trait WithRPSShow
{
    private function formatRPSDetailForShow(RPS $rps): array
    {
        $mk = $rps->mk_rel;
        $prodi = $mk?->prodis->first();

        $timPengajar = $rps->dosens->pluck('name')->filter()->implode("\n");
        $ketua = optional($rps->dosens->first(function ($d) {
            return (bool) ($d->pivot->is_ketua ?? false);
        }))->name ?: $rps->dosens->first()?->name;
        $instruktur = $rps->dosens->filter(function ($d) {
            return strtolower(trim((string) ($d->pivot->peran ?? ''))) === 'pengajar';
        })->pluck('name')->filter()->implode("\n");

        return [
            'fakultas' => $prodi?->jr_rel?->fk_rel?->nama_fk ?? '-',
            'jurusan' => $prodi?->jr_rel?->nama_jr ?? '-',
            'prodi' => $prodi?->nama_pr ?? '-',

            'mk' => $rps->mk,
            'kode_mk' => $rps->kode,
            'bahanKajian' => $mk?->bahan_kajian ?? '-',
            'sks' => $rps->sks ?? 0,
            'sksPraktikum' => $mk?->sks_pr ?? 0,
            'semester' => $mk?->semester ?? '-',
            'revisi' => $rps->revisi_day ?? '-',

            'deskripsi' => $rps->deskripsi ?? '-',
            'cpl' => $rps->cpls->map(function ($c, $idx) {
                $label = trim(($c->kode ?? '').' '.($c->deskripsi ?? ''));

                return ($idx + 1).'. '.trim($label);
            })->implode("\n"),
            'listCpmkWithDesc' => $rps->cpmks->map(function ($c, $idx) {
                return ($idx + 1).'. '.trim(($c->kode ?? '').': '.($c->deskripsi_cpl ?? ''));
            })->implode("\n"),

            'timPengajar' => $timPengajar,
            'ketuaTimPengajar' => $ketua ?? '-',
            'instruktur' => $instruktur ?: '-',
            'totalSks' => $rps->sks ?? 0,

            'programPembelajaran' => $this->buildProgramPembelajaranRows($rps),
            'referensi' => $rps->refs->map(function ($ref) {
                return trim(($ref->penulis_tahun ?? '').' '.($ref->judul ?? '').' '.($ref->penerbit ?? ''));
            })->filter()->values()->all(),
        ];
    }

    private function buildProgramPembelajaranRows(RPS $rps): array
    {
        $rows = [];
        foreach ($rps->cpmks as $cpmk) {
            foreach ($cpmk->scpmks as $scpmk) {
                $rows[] = [
                    'cpmk' => $cpmk->kode,
                    'sub_cpmk' => $scpmk->kode,
                    'materi' => $scpmk->materi,
                    'referensi' => $this->formatScpmkReferensi($scpmk),
                    'metodologi' => $scpmk->metodologi,
                    'tugas' => $scpmk->tugas,
                    'indikator' => $scpmk->indikator,
                    'bobot' => $this->formatBobot($scpmk->bobot),
                    'dosen' => $this->getDosenNamesForSubCpmk($rps, $scpmk),
                    'metode' => $scpmk->metode,
                    'is_placeholder' => false,
                ];
            }
        }

        $hasUTS = collect($rows)->contains(function ($row) {
            return strtoupper(trim((string) ($row['metode'] ?? ''))) === 'UTS';
        });
        $hasUAS = collect($rows)->contains(function ($row) {
            return in_array(strtoupper(trim((string) ($row['metode'] ?? ''))), ['UAS', 'LAPORAN AKHIR', 'HASIL PROJEK', 'HASIL PROYEK'], true);
        });

        $finalRows = [];
        $pointer = 0;

        for ($meeting = 1; $meeting <= 16; $meeting++) {
            if (! $hasUTS && $meeting === 8) {
                $finalRows[] = $this->createPlaceholderMeetingRow('UTS', $rps->bobot_uts, $rps);

                continue;
            }

            if (! $hasUAS && $meeting === 16) {
                $finalRows[] = $this->createPlaceholderMeetingRow('UAS', $rps->bobot_uas, $rps);

                continue;
            }

            if (! isset($rows[$pointer])) {
                continue;
            }

            $row = $rows[$pointer];
            $row['meeting'] = $meeting;
            $finalRows[] = $row;
            $pointer++;
        }

        return $finalRows;
    }

    private function createPlaceholderMeetingRow(string $type, $weight, RPS $rps): array
    {
        return [
            'cpmk' => '',
            'sub_cpmk' => strtoupper($type),
            'materi' => '',
            'referensi' => '',
            'metodologi' => '',
            'tugas' => '',
            'indikator' => '',
            'bobot' => $this->formatBobot($weight),
            'dosen' => $rps->dosens->pluck('name')->filter()->implode("\n"),
            'metode' => $type,
            'is_placeholder' => true,
        ];
    }

    private function formatScpmkReferensi($scpmk): string
    {
        return collect($scpmk->refs ?? [])->map(function ($ref) {
            return trim(($ref->penulis_tahun ?? '').' '.($ref->judul ?? '').' '.($ref->penerbit ?? ''));
        })->filter()->implode("\n");
    }

    private function getDosenNamesForSubCpmk(RPS $rps, $scpmk): string
    {
        $assigned = collect($scpmk->dosens ?? [])->filter(function ($d) use ($rps) {
            return (int) ($d->pivot->rps_id ?? 0) === $rps->id;
        })->pluck('name')->filter();

        if ($assigned->isNotEmpty()) {
            return $assigned->implode("\n");
        }

        return $rps->dosens->pluck('name')->filter()->implode("\n");
    }

    private function formatBobot($value): string
    {
        if ($value === null || $value === '') {
            return '0%';
        }

        return rtrim(rtrim(number_format((float) $value, 2, ',', '.'), '0'), ',').'%';
    }
}
