<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CPMK extends Model
{
    use SoftDeletes;

    protected $table = 'cpmks';

    protected $guarded = ['id'];

    protected $appends = ['kode'];

    protected $casts = [
        'created_at' => 'date',
        'updated_at' => 'date',
    ];

    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            return preg_replace('/([A-Za-z])([0-9])/', '$1-$2', $this->kode_cpmk);
        });
    }

    protected function countScpmk(): Attribute
    {
        return Attribute::get(function () {
            return $this->scpmks->count();
        });
    }

    protected function totalBobot(): Attribute
    {
        return Attribute::get(function () {
            return $this->scpmks->sum('bobot') ?? 0;
        });
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

    public function rps(): BelongsToMany
    {
        return $this->belongsToMany(RPS::class, 'rps_pivot_cpmk', 'cpmk_id', 'rps_id')
            ->withTimestamps();
    }

    public function scpmks(): BelongsToMany
    {
        return $this->belongsToMany(SubCPMK::class, 'cpmk_pivot_scpmk', 'cpmk_id', 'scpmk_id')
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    public function refs(): BelongsToMany
    {
        return $this->belongsToMany(Referensi::class, 'cpmk_pivot_ref', 'cpmk_id', 'ref_id')
            ->withPivot('sort_order');
    }

    public function cpls(): BelongsToMany
    {
        return $this->belongsToMany(CPL::class, 'cpmk_pivot_cpl', 'cpmk_id', 'cpl_id')
            ->withPivot('sort_order');
    }

    public function scopeSearchCPMK($query, $search)
    {
        if (empty(trim($search))) {
            return $query;
        }

        $search = trim($search);
        $searchTerm = '%'.$search.'%';
        $searchLower = strtolower($search);
        $searchLikeLower = '%'.$searchLower.'%';
        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        return $query->where(function ($q) use ($search, $searchTerm, $searchLikeLower, $searchClean) {

            // --- 1. PENCARIAN TEKS DASAR ---
            $q->where('cpmks.kode_cpmk', 'like', $searchTerm)
                ->orWhere('cpmks.kode_cpmk', 'like', $searchClean)
                ->orWhere('cpmks.deskripsi', 'like', $searchTerm);

            if (is_numeric($search)) {
                $q->orWhere('cpmks.id', 'like', $search);
            }

            // --- 2. PENCARIAN JUMLAH SUB-CPMK (Langsung dari CPMK) ---
            if (preg_match('/(\d+)\s*(pert|scpm|sub-?c)/i', $search, $matches)) {
                $number = $matches[1];
                $q->orWhereExists(function ($sq) use ($number) {
                    $sq->select(DB::raw(1))
                        ->from('cpmk_pivot_scpmk')
                        ->whereColumn('cpmk_pivot_scpmk.cpmk_id', 'cpmks.id')
                        ->groupBy('cpmk_pivot_scpmk.cpmk_id')
                        ->havingRaw('COUNT(*) = ?', [$number]);
                });
            }

            // --- 3. PENCARIAN TOTAL BOBOT (Langsung dari CPMK) ---
            if (preg_match('/(\d+)\s*(%|pers|bob|tot)/i', $search, $matches)) {
                $weight = $matches[1];
                $q->orWhereExists(function ($sq) use ($weight) {
                    $sq->select(DB::raw(1))
                        ->from('cpmk_pivot_scpmk')
                        ->join('sub_cpmks', 'cpmk_pivot_scpmk.scpmk_id', '=', 'sub_cpmks.id')
                        ->whereColumn('cpmk_pivot_scpmk.cpmk_id', 'cpmks.id')
                        ->groupBy('cpmk_pivot_scpmk.cpmk_id')
                        ->havingRaw('SUM(sub_cpmks.bobot) = ?', [$weight]);
                });
            }

            // --- 4. FILTER TANGGAL ---
            $q->orWhere(function ($dq) use ($searchLikeLower, $searchTerm) {
                $dq->whereRaw("DATE_FORMAT(cpmks.created_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                    ->orWhereRaw("DATE_FORMAT(cpmks.created_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
                    ->orWhereRaw("LOWER(DATE_FORMAT(cpmks.created_at, '%a, %d %b %Y')) LIKE ?", [$searchLikeLower])
                    ->orWhereRaw("LOWER(DATE_FORMAT(cpmks.created_at, '%W, %d %M %Y')) LIKE ?", [$searchLikeLower])
                    ->orWhereRaw("DATE_FORMAT(cpmks.updated_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                    ->orWhereRaw("LOWER(DATE_FORMAT(cpmks.updated_at, '%a, %d %b %Y')) LIKE ?", [$searchLikeLower]);
            });
        });
    }
}
