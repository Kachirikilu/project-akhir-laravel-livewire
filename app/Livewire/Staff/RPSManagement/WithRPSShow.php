<?php

namespace App\Livewire\Staff\RPSManagement;

use App\Models\Akademik\RPS;
use App\Models\Akademik\SubCPMK;

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

            'nama_mk' => $mk->nama_mk,
            'kode_mk' => $rps->kode_mk,
            'bahan_kajian' => $mk?->bahan_kajian ?? '-',
            'sks' => $mk->sks_tm ?? $mk->sks_pl ?? $mk->sks_sm ?? 0,
            'sks_pr' => $mk?->sks_pr ?? 0,
            'semester' => $mk?->semester ?? '-',
            'revisi' => $rps->revisi_day ?? '-',

            'bobot_uts' => $rps->bobot_uts ?? '-',
            'bobot_uas' => $rps->bobot_uas ?? '-',

            'deskripsi' => $rps->deskripsi_rps ?? '-',
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

        $utsFields = SubCPMK::UTS_FIELDS;
        $uasFields = SubCPMK::UAS_FIELDS;

        $hasUTS = collect($rows)->contains(function ($row) {
            return SubCPMK::isUTS($row['metode'] ?? '', $row['sub_cpmk'] ?? '');
        });

        $hasUAS = collect($rows)->contains(function ($row) {
            return SubCPMK::isUAS($row['metode'] ?? '', $row['sub_cpmk'] ?? '');
        });

        // Kondisi 1: Kedua ada (UTS dan UAS) → kembalikan apa adanya
        if ($hasUTS && $hasUAS) {
            return $rows;
        }

        // Kondisi 2: Tidak ada salah satunya (hanya UTS atau hanya UAS) → tambah yang kurang
        if ($hasUTS && !$hasUAS) {
            $rows[] = $this->createPlaceholderMeetingRow('UAS', $rps->bobot_uas, $rps);
            return $rows;
        }

        if (!$hasUTS && $hasUAS) {
            $rows[] = $this->createPlaceholderMeetingRow('UTS', $rps->bobot_uts, $rps);
            return $rows;
        }

        // Kondisi 3: Tidak ada keduanya → bikin placeholder UTS dan UAS dengan struktur 1-16
        $finalRows = [];
        $pointer = 0;
        for ($meeting = 1; $meeting <= 16; $meeting++) {
            if ($meeting === 8) {
                $finalRows[] = $this->createPlaceholderMeetingRow('UTS', $rps->bobot_uts, $rps);
                continue;
            }
            if ($meeting === 16) {
                $finalRows[] = $this->createPlaceholderMeetingRow('UAS', $rps->bobot_uas, $rps);
                continue;
            }
            if (isset($rows[$pointer])) {
                $finalRows[] = $rows[$pointer++];
            }
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
        // Jika null atau kosong, beri default 0%
        if ($value === null || $value === '' || $value === 0 || $value === '0') {
            return '0%';
        }

        // Jika sudah ada tanda %, bersihkan dulu untuk diformat ulang atau langsung kembalikan
        $cleanValue = str_replace('%', '', (string) $value);
        $cleanValue = str_replace(',', '.', $cleanValue); // Pastikan format desimal titik untuk float

        if (! is_numeric($cleanValue)) {
            return '0%';
        }

        // Format angka: 15.00 -> 15, 15.50 -> 15,5
        $formatted = rtrim(rtrim(number_format((float) $cleanValue, 2, ',', '.'), '0'), ',');

        return $formatted.'%';
    }
}
