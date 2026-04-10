<?php

namespace App\Models\Akademik;

use App\Models\Auth\Dosen;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RPS extends Model
{
    use SoftDeletes;

    protected $table = 'rps';
    protected $guarded = ['id'];
    protected $appends = ['kode', 'mk', 'level_mk', 'revisi_day'];
    protected $casts = [
        'revisi' => 'date',
        'created_at' => 'date',
        'updated_at' => 'date',
    ];

    public function mk_rel(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'mk_id')->withTrashed();
    }

    protected function kodeMk(): Attribute
    {
        return Attribute::get(fn () => $this->mk_rel?->kode);
    }

    protected function kodeBlok(): Attribute
    {
        return Attribute::get(function () {
            $tahunFull = (int) substr($this->akademik, 0, 4);
            $suffixTahun = match (true) {
                $tahunFull >= 3000 => $tahunFull,
                $tahunFull >= 2100 => substr((string) $tahunFull, -3),
                default => substr((string) $tahunFull, -2),
            };

            return $suffixTahun;
        });
    }

    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            $kodeMK = $this->kode_mk;
            if (!$kodeMK || !$this->akademik) {
                return null;
            }
            $suffixTahun = $this->kode_blok;
            return "{$kodeMK}-{$suffixTahun}";
        });
    }


    protected function mk(): Attribute
    {
        return Attribute::get(fn () => $this->mk_rel?->mk);
    }

    protected function levelMk(): Attribute
    {
        return Attribute::get(fn () => $this->mk_rel?->level_mk);
    }

    protected function rps(): Attribute
    {
        return Attribute::get(fn () => 
            ($this->mk_rel?->mk ?? 'Tanpa MK') . ' ' . $this->akademik
        );
    }


    protected function revisiDay(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->revisi) {
                return null;
            }
            return $this->revisi->translatedFormat('D, d M Y');
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
            return $this->created_at->translatedFormat('D, d M Y');
        });
    }

    protected function updatedDay(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->updated_at) {
                return null;
            }
            return $this->updated_at->translatedFormat('D, d M Y');
        });
    }

    public function cpmks(): BelongsToMany
    {
        return $this->belongsToMany(CPMK::class, 'rps_pivot_cpmk', 'rps_id', 'cpmk_id')
            ->withPivot('sort_order')
            ->orderBy('sort_order')
            ->withTimestamps();
    }

    public function cpls(): BelongsToMany
    {
        return $this->belongsToMany(CPL::class, 'rps_pivot_cpl', 'rps_id', 'cpl_id')
                    ->withPivot('sort_order')
                    ->orderBy('sort_order');
    }


    public function refs(): BelongsToMany
    {
        return $this->belongsToMany(Referensi::class, 'rps_pivot_ref', 'rps_id', 'ref_id')
            ->withPivot('sort_order')
            ->orderBy('sort_order');
    }

    public function dosens(): BelongsToMany
    {
        return $this->belongsToMany(Dosen::class, 'rps_pivot_dosen', 'rps_id', 'dosen_id')
            ->withPivot(['peran', 'is_ketua', 'sort_order'])
            ->orderBy('sort_order')
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
                $group->whereHas('mk_rel', function ($mq) use ($mkPartClean) {
                    $mq->searchMK($mkPartClean);
                });

                // B. Filter Tahun Akademik (Sekarang 1 digit langsung LIKE)
                if ($yearPart !== null) {
                    $group->where('akademik', 'like', '%'.$yearPart.'%');
                }

                // C. Filter Tanggal (Revisi, Created, Updated)
                $group->orWhere(function ($dq) use ($searchLower, $searchTerm) {
                    $dq->whereRaw("DATE_FORMAT(rps.revisi, '%d/%m/%Y') LIKE ?", [$searchTerm])
                        ->orWhereRaw("DATE_FORMAT(rps.revisi, '%Y-%m-%d') LIKE ?", [$searchTerm])
                        ->orWhereRaw("LOWER(DATE_FORMAT(rps.revisi, '%a, %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
                        ->orWhereRaw("LOWER(DATE_FORMAT(rps.revisi, '%W, %d %M %Y')) LIKE ?", ['%'.$searchLower.'%'])
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
                $group->orWhere('akademik', 'like', $searchTerm);
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
