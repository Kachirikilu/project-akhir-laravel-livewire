<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class SubCPMK extends Model
{
    use SoftDeletes;

    protected $table = 'sub_cpmks';
    protected $guarded = ['id'];
    protected $appends = ['kode', 'tugas', 'w_tugas', 'w_mandiri'];

    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            return preg_replace('/([A-Za-z])([0-9])/', '$1-$2', $this->kode_scpmk);
        });
    }

    protected function tugas(): Attribute
    {
        return Attribute::get(fn () => $this->deskripsi_tugas);
    }
    protected function wTugas(): Attribute
    {
        return Attribute::get(fn () => $this->waktu_tugas);
    }
     protected function wMandiri(): Attribute
    {
        return Attribute::get(fn () => $this->waktu_mandiri);
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

    public function cpmks(): BelongsToMany
    {
        return $this->belongsToMany(CPMK::class, 'cpmk_pivot_scpmk', 'scpmk_id', 'cpmk_id')
                    ->withPivot('sort_order')
                    ->withTimestamps();
    }

    public function referensis(): BelongsToMany
    {
        return $this->belongsToMany(Referensi::class, 'scpmk_pivot_ref', 'scpmk_id', 'ref_id')
                    ->withPivot('sort_order');
    }

    
    public function scopeSearchSCPMK($query, $search)
    {
        if (empty(trim($search))) {
            return $query;
        }

        $search = trim($search);
        $searchLower = '%'.strtolower($search).'%';
        $searchTerm = '%'.$search.'%';
        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        return $query->where(function ($q) use ($search, $searchTerm, $searchLower, $searchClean) {
            $q->where('sub_cpmks.kode_scpmk', 'like', $searchTerm)
                    ->orWhere('sub_cpmks.kode_scpmk', 'like', $searchClean)
                    ->orWhere('sub_cpmks.deskripsi', 'like', $searchTerm)
                    ->orWhere('sub_cpmks.materi', 'like', $searchTerm)
                    ->orWhere('sub_cpmks.metodologi', 'like', $searchTerm)
                    ->orWhere('sub_cpmks.indikator', 'like', $searchTerm)
                    ->orWhere('sub_cpmks.deskripsi_tugas', 'like', $searchTerm)
                    ->orWhere('sub_cpmks.waktu_tugas', 'like', $searchTerm)
                    ->orWhere('sub_cpmks.waktu_mandiri', 'like', $searchTerm);

                    $termLower = strtolower(trim($searchTerm, '% ')); 

                    $q->orWhere(function ($enumQ) use ($searchTerm, $termLower) {
                        if (str_contains('ujian tengah semester', $termLower) || str_contains('uts', $termLower)) {
                            $enumQ->orWhere('sub_cpmks.metode', 'UTS');
                        }
                        if (str_contains('ujian akhir semester', $termLower) || str_contains('uas', $termLower)) {
                            $enumQ->orWhere('sub_cpmks.metode', 'UAS');
                        }
                        if ($termLower === 'ujian') {
                            $enumQ->orWhereIn('sub_cpmks.metode', ['UTS', 'UAS']);
                        }
                        $enumQ->orWhere('sub_cpmks.metode', 'like', $searchTerm);
                    });

                if (is_numeric($search)) {
                    $q->orWhere('sub_cpmks.id', 'like', $search);
                }

                $searchConverted = str_replace(',', '.', $searchTerm);
                  $q->orWhere('sub_cpmks.bobot', 'like', '%' . $searchConverted . '%');

                $q->orWhere(function($dq) use ($searchLower, $searchTerm) {
                    $dq->whereRaw("DATE_FORMAT(sub_cpmks.created_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                    ->orWhereRaw("DATE_FORMAT(sub_cpmks.created_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
                    ->orWhereRaw("LOWER(DATE_FORMAT(sub_cpmks.created_at, '%a, %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(sub_cpmks.created_at, '%W, %d %M %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(sub_cpmks.created_at, '%a %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(sub_cpmks.created_at, '%W %d %M %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("DATE_FORMAT(sub_cpmks.updated_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                    ->orWhereRaw("DATE_FORMAT(sub_cpmks.updated_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
                    ->orWhereRaw("LOWER(DATE_FORMAT(sub_cpmks.updated_at, '%a, %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(sub_cpmks.updated_at, '%W, %d %M %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(sub_cpmks.updated_at, '%a %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(sub_cpmks.updated_at, '%W %d %M %Y')) LIKE ?", ['%' . $searchLower . '%']);
                });
                ;
        });
    }
}