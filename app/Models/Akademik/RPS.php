<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

use App\Models\Auth\Dosen;

class Rps extends Model
{
    use SoftDeletes;

    protected $table = 'rps';
    protected $guarded = ['id'];
    protected $appends = ['kode, matkul, tingkatan_mk, akademik, revisi'];


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


    protected function revisi(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->tanggal_revisi) {
                return null;
            }

            return Carbon::parse($this->tanggal_revisi)->translatedFormat('D, d M Y');
        });
    }

    public function cpmks(): BelongsToMany
    {
        return $this->belongsToMany(Cpmk::class, 'rps_pivot_cpmk', 'rps_id', 'cpmk_id')
                    ->withPivot('sort_order')
                    ->orderBy('pivot_sort_order')
                    ->withTimestamps();
    }

    public function referensis(): BelongsToMany
    {
        return $this->belongsToMany(Referensi::class, 'rps_pivot_referensi', 'rps_id', 'ref_id')
                    ->withPivot(['kategori', 'sort_order']);
    }

    public function dosens(): BelongsToMany
    {
        return $this->belongsToMany(Dosen::class, 'rps_pivot_dosen', 'rps_id', 'dosen_id')
                    ->withPivot(['peran', 'is_ketua', 'sort_order'])
                    ->withTimestamps();
    }

    public function scopeSearchRPS($query, $search)
    {
        if (empty(trim($search))) return $query;

        $search = trim($search);
        $searchLower = strtolower($search);
        $searchTerm = '%' . $search . '%';
        
        // 1. Logika Pemecah (MK & Tahun Akademik)
        $mkPart = $search;
        $yearPart = null;

        // 2. Cek jika ada pemisah jelas (- atau /)
        if (preg_match('/^(.*?)[-\/](\d+)$/', $search, $matches)) {
            $mkPart = $matches[1];
            $yearPart = $matches[2];
        } elseif (preg_match('/^(.+)(\d{2})$/', $search, $matches)) {
            $mkPart = $matches[1];
            $yearPart = $matches[2];
        }

        return $query->where(function ($q) use ($mkPart, $yearPart, $searchLower, $search, $searchTerm) {
            
            $q->where(function($group) use ($mkPart, $yearPart, $search, $searchTerm, $searchLower) {
                
                // A. Filter Mata Kuliah
                $group->whereHas('matkul_rel', function ($mq) use ($mkPart, $search) {
                    $mq->searchMK($mkPart)->orWhere('nama_matkul', 'like', '%' . $search . '%');
                });

                // B. Filter Tahun Akademik (Jika ada part tahun di input)
                if ($yearPart) {
                    $group->where('tahun_akademik', 'like', '%' . $yearPart . '%');
                }

                // C. Filter TAHUN REVISI (Format Tanggal Lengkap)
                $group->orWhere(function($dq) use ($searchLower, $searchTerm) {
                    $dq->whereRaw("DATE_FORMAT(tanggal_revisi, '%d/%m/%Y') LIKE ?", [$searchTerm])
                    ->orWhereRaw("DATE_FORMAT(tanggal_revisi, '%Y-%m-%d') LIKE ?", [$searchTerm])
                    ->orWhereRaw("LOWER(DATE_FORMAT(tanggal_revisi, '%a, %d %b %Y')) LIKE ?", ['%' . $searchLower . '%']) // Sat, 28 Mar 2026
                    ->orWhereRaw("LOWER(DATE_FORMAT(tanggal_revisi, '%W, %d %M %Y')) LIKE ?", ['%' . $searchLower . '%']) // Saturday, 28 March 2026
                    ->orWhereRaw("LOWER(DATE_FORMAT(tanggal_revisi, '%a %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(tanggal_revisi, '%W %d %M %Y')) LIKE ?", ['%' . $searchLower . '%']);
                });

                // D. Fallback Tahun Akademik Umum
                $group->orWhere('tahun_akademik', 'like', $searchTerm);
            });

            // E. Logika Status
            $statusKeywords = [
                'draf' => ['draf', 'draft', 'konsep', 'aseli'],
                'aktif' => ['aktif', 'active', 'publish', 'siap']
            ];

            if (in_array($searchLower, $statusKeywords['draf'])) {
                $q->orWhere('is_draf', true);
            } elseif (in_array($searchLower, $statusKeywords['aktif'])) {
                $q->orWhere('is_draf', false);
            }
        });
    }
}
