<?php

namespace App\Models\Akademik;

use App\Models\Auth\Dosen;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RPS extends Model
{
    use SoftDeletes;

    protected $table = 'rps';

    protected $guarded = ['id'];

    protected $appends = ['kode, matkul, tingkatan_mk, akademik, revisi'];

    protected $casts = [
        'tanggal_revisi' => 'date',
    ];

    public function matkul_rel()
    {
        return $this->belongsTo(MataKuliah::class, 'mk_id')->withTrashed();
    }

    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            $kodeMatkul = $this->matkul_rel?->kode;
            $suffixTahun = substr($this->tahun_akademik, -2);

            return $kodeMatkul ? "{$kodeMatkul}-{$suffixTahun}" : null;
        });
    }

    protected function matkul(): Attribute
    {
        return Attribute::get(fn () => $this->matkul_rel?->matkul);
    }

    protected function tingkatanMk(): Attribute
    {
        return Attribute::get(fn () => $this->matkul_rel?->tingkatan_mk);
    }

    protected function akademik(): Attribute
    {
        return Attribute::get(fn () => $this->tahun_akademik);
    }

    protected function rps(): Attribute
    {
        return Attribute::get(fn () => 
            ($this->matkul_rel?->matkul ?? 'Tanpa MK') . ' ' . $this->akademik
        );
    }

    protected function revisi(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->tanggal_revisi) {
                return null;
            }

            return Carbon::parse($this->tanggal_revisi)->translatedFormat('D, d M Y');
        });
    }

    protected function draf(): Attribute
    {
        return Attribute::get(fn () => $this->is_draf);
    }

    protected function drafText(): Attribute
    {
        return Attribute::get(fn () => $this->is_draf == 1 ? 'Draf' : 'Aktif');
    }

    protected function createdDay(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->created_at) {
                return null;
            }

            return Carbon::parse($this->created_at)->translatedFormat('D, d M Y');
        });
    }

    protected function updatedDay(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->updated_at) {
                return null;
            }

            return Carbon::parse($this->updated_at)->translatedFormat('D, d M Y');
        });
    }

    public function cpmks(): BelongsToMany
    {
        return $this->belongsToMany(CPMK::class, 'rps_pivot_cpmk', 'rps_id', 'cpmk_id')
            ->withPivot('sort_order')
            ->orderBy('pivot_sort_order')
            ->withTimestamps();
    }

    public function referensis(): BelongsToMany
    {
        return $this->belongsToMany(Referensi::class, 'rps_pivot_ref', 'rps_id', 'ref_id')
            ->withPivot('sort_order');
    }

    public function dosens(): BelongsToMany
    {
        return $this->belongsToMany(Dosen::class, 'rps_pivot_dosen', 'rps_id', 'dosen_id')
            ->withPivot(['peran', 'is_ketua', 'sort_order'])
            ->orderBy('pivot_sort_order')
            ->withTimestamps();
    }


    public function scopeSearchRPS($query, $search)
    {
        if (empty(trim($search))) {
            return $query;
        }

        $search = trim($search);
        $searchLower = '%'.strtolower($search).'%';
        $searchTerm = '%'.$search.'%';

        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        $mkPart = $search;
        $yearPart = null;

        if (preg_match('/^([A-Za-z]{3}\d{0,4})(\d*)$/i', $searchClean, $matches)) {
            $mkPart = $matches[1];
            $yearPart = (isset($matches[2]) && $matches[2] !== '') ? $matches[2] : null;
        }
        elseif (preg_match('/^(.*?)[-\/\s]+(\d+)?$/', $search, $matches)) {
            $mkPart = trim($matches[1]);
            $yearPart = isset($matches[2]) ? $matches[2] : null;
        }

        if ($yearPart && strlen($yearPart) >= 4) {
            $yearPart = substr($yearPart, -2);
        }

        return $query->where(function ($q) use ($mkPart, $yearPart, $searchLower, $search, $searchTerm) {
            $mkPartClean = preg_replace('/[^A-Za-z0-9]/', '', $mkPart);

            $q->where(function ($group) use ($mkPartClean, $yearPart, $searchTerm, $searchLower) {

                // A. Filter Mata Kuliah
                $group->whereHas('matkul_rel', function ($mq) use ($mkPartClean) {
                    $mq->searchMK($mkPartClean);
                });

                // B. Filter Tahun Akademik (Sekarang 1 digit langsung LIKE)
                if ($yearPart !== null) {
                    $group->where('tahun_akademik', 'like', '%'.$yearPart.'%');
                }

                // C. Filter Tanggal (Revisi, Created, Updated)
                $group->orWhere(function ($dq) use ($searchLower, $searchTerm) {
                    $dq->whereRaw("DATE_FORMAT(rps.tanggal_revisi, '%d/%m/%Y') LIKE ?", [$searchTerm])
                        ->orWhereRaw("DATE_FORMAT(rps.tanggal_revisi, '%Y-%m-%d') LIKE ?", [$searchTerm])
                        ->orWhereRaw("LOWER(DATE_FORMAT(rps.tanggal_revisi, '%a, %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
                        ->orWhereRaw("LOWER(DATE_FORMAT(rps.tanggal_revisi, '%W, %d %M %Y')) LIKE ?", ['%'.$searchLower.'%'])
                        ->orWhereRaw("DATE_FORMAT(rps.created_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                        ->orWhereRaw("DATE_FORMAT(rps.created_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
                        ->orWhereRaw("LOWER(DATE_FORMAT(rps.created_at, '%a, %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
                        ->orWhereRaw("LOWER(DATE_FORMAT(rps.created_at, '%W, %d %M %Y')) LIKE ?", ['%'.$searchLower.'%'])
                        ->orWhereRaw("DATE_FORMAT(rps.updated_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                        ->orWhereRaw("DATE_FORMAT(rps.updated_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
                        ->orWhereRaw("LOWER(DATE_FORMAT(rps.updated_at, '%a, %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
                        ->orWhereRaw("LOWER(DATE_FORMAT(rps.updated_at, '%W, %d %M %Y')) LIKE ?", ['%'.$searchLower.'%']);
                });

                // D. Fallback Umum
                $group->orWhere('tahun_akademik', 'like', $searchTerm);
            });

            // E. Logika Status
            $statusKeywords = [
                'draf' => ['draf', 'draft', 'konsep', 'aseli'],
                'aktif' => ['aktif', 'active', 'publish', 'siap'],
            ];

            if (in_array($searchLower, $statusKeywords['draf'])) {
                $q->orWhere('is_draf', true);
            } elseif (in_array($searchLower, $statusKeywords['aktif'])) {
                $q->orWhere('is_draf', false);
            }

            // F. ID RPS
            if (is_numeric($search)) {
                $q->orWhere('rps.id', 'like', $search);
            }
        });
    }
}
