<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class CPMK extends Model
{
    use SoftDeletes;

    protected $table = 'cpmks';
    protected $guarded = ['id'];
    protected $appends = ['kode'];


    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            return preg_replace('/([A-Za-z])([0-9])/', '$1-$2', $this->kode_cpmk);
        });
    }

    protected function createdDay(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->created_at) {
                return null;
            }

            return Carbon::parse($this->created_at)->translatedFormat('D, d M Y');
        });
    }
    protected function updatedDay(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->updated_at) {
                return null;
            }

            return Carbon::parse($this->updated_at)->translatedFormat('D, d M Y');
        });
    }

    public function rps(): BelongsToMany
    {
        return $this->belongsToMany(RPS::class, 'rps_pivot_cpmk', 'cpmk_id', 'rps_id')
                    ->withTimestamps();
    }

    public function sub_cpmks(): BelongsToMany
    {
        return $this->belongsToMany(SubCPMK::class, 'cpmk_pivot_scpmk', 'cpmk_id', 'scpmk_id')
                    ->withPivot('sort_order')
                    ->withTimestamps();
    }

    public function referensis(): BelongsToMany
    {
        return $this->belongsToMany(Referensi::class, 'cpmk_pivot_ref', 'cpmk_id', 'ref_id')
                    ->withPivot('sort_order');
    }

    public function cpls(): BelongsToMany
    {
        return $this->belongsToMany(Cpl::class, 'cpmk_pivot_cpl', 'cpmk_id', 'cpl_id')
                    ->withPivot('sort_order');
        ;
    }

    public function scopeSearchCPMK($query, $search)
    {
        if (empty(trim($search))) {
            return $query;
        }

        $search = trim($search);
        $searchTerm = '%'.$search.'%';
        $searchLower = '%'.strtolower($search).'%';
        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        return $query->where(function ($q) use ($search, $searchTerm, $searchLower, $searchClean) {
            $q->where('cpmks.kode_cpmk', 'like', $searchTerm)
                    ->orWhere('cpmks.kode_cpmk', 'like', $searchClean)
                    ->orWhere('cpmks.deskripsi', 'like', $searchTerm);

                if (is_numeric($search)) {
                    $q->orWhere('cpmks.id', 'like', $search);
                }

                $q->orWhere(function($dq) use ($searchLower, $searchTerm) {
                    $dq->whereRaw("DATE_FORMAT(cpmks.created_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                    ->orWhereRaw("DATE_FORMAT(cpmks.created_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
                    ->orWhereRaw("LOWER(DATE_FORMAT(cpmks.created_at, '%a, %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(cpmks.created_at, '%W, %d %M %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(cpmks.created_at, '%a %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(cpmks.created_at, '%W %d %M %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("DATE_FORMAT(cpmks.updated_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                    ->orWhereRaw("DATE_FORMAT(cpmks.updated_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
                    ->orWhereRaw("LOWER(DATE_FORMAT(cpmks.updated_at, '%a, %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(cpmks.updated_at, '%W, %d %M %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(cpmks.updated_at, '%a %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(cpmks.updated_at, '%W %d %M %Y')) LIKE ?", ['%' . $searchLower . '%']);
                });
                ;
        });
    }
}