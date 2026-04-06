<?php

namespace App\Models\ProgramStudi;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Prodi extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'jurusan_id',
        'kode_pr',
        'nama_prodi',
        'nama_strata',
    ];

    protected $appends = ['kode', 'prodi', 'strata', 'jurusan', 'fakultas'];

    public function jurusan_rel()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id')->withTrashed();
    }

    public function mata_kuliahs()
    {
        return $this->belongsToMany(
            MataKuliah::class,
            'prodi_pivot_mk',
            'prodi_id',
            'mk_id'
        )->withTimestamps();
    }

    protected function prodi(): Attribute
    {
        return Attribute::get(function () {
            if ($this->nama_strata == 'Sarjana') {
                return 'S1 ' . $this->nama_prodi;
            }
            if ($this->nama_strata == 'Magister') {
                return 'S2 ' . $this->nama_prodi;
            }
            if ($this->nama_strata == 'Doktor') {
                return 'S3 ' . $this->nama_prodi;
            }
            return $this->nama_prodi;
        });
    }

    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            if (! empty($this->attributes['kode_pr'])) {
                return $this->attributes['kode_pr'];
            }
            $kodeJurusan = $this->jurusan_rel?->kode_jr;
            if (! empty($kodeJurusan)) {
                return $kodeJurusan;
            }
            $kodeFakultas = $this->jurusan_rel?->fakultas_rel?->kode_fk;
            if (! empty($kodeFakultas)) {
                return $kodeFakultas;
            }
            return 'UNI';
        });
    }

    protected function kodeJr(): Attribute
    {
        return Attribute::get(function () {
            $kodeJurusan = $this->jurusan_rel?->kode_jr;
            if (! empty($kodeJurusan)) {
                return $kodeJurusan;
            }
            $kodeFakultas = $this->jurusan_rel?->fakultas_rel?->kode_fk;
            if (! empty($kodeFakultas)) {
                return $kodeFakultas;
            }
            return 'UNI';
        });
    }

    protected function kodeFk(): Attribute
    {
        return Attribute::get(function () {
            $kodeFakultas = $this->jurusan_rel?->fakultas_rel?->kode_fk;
            if (! empty($kodeFakultas)) {
                return $kodeFakultas;
            }
            return 'UNI';
        });
    }

    // protected function kodeText(): Attribute
    // {
    //     return Attribute::get(function () {
    //         if (! empty($this->attributes['kode_pr'])) {
    //             return $this->attributes['kode_pr'];
    //         }
    //         $kodeJurusan = $this->jurusan_rel?->kode_jr;
    //         if (! empty($kodeJurusan)) {
    //             return $kodeJurusan;
    //         }
    //         $kodeFakultas = $this->jurusan_rel?->fakultas_rel?->kode_fk;
    //         if (! empty($kodeFakultas)) {
    //             return $kodeFakultas;
    //         }
    //         return 'UNI';
    //     });
    // }
    protected function tingkatanProdi(): Attribute
    {
        return Attribute::get(function () {
            if (! empty($this->attributes['kode_pr'])) {
                return 1;
            }
            $kodeJurusan = $this->jurusan_rel?->kode_jr;
            if (! empty($kodeJurusan)) {
                return 2;
            }
            $kodeFakultas = $this->jurusan_rel?->fakultas_rel?->kode_fk;
            if (! empty($kodeFakultas)) {
                return 3;
            }
            return 4;
        });
    }

    protected function strata(): Attribute
    {
        return Attribute::get(fn () => $this->nama_strata);
    }

    protected function jurusan(): Attribute
    {
        return Attribute::get(fn () => $this->jurusan_rel?->nama_jurusan);
    }
    protected function jurusanJr(): Attribute
    {
        return Attribute::get(fn () => 'Jurusan '.$this->jurusan_rel?->nama_jurusan);
    }

    protected function fakultas(): Attribute
    {
        return Attribute::get(fn () => $this->jurusan_rel?->fakultas_rel?->nama_fakultas);
    }
    protected function fakultasFk(): Attribute
    {
        return Attribute::get(fn () => 'Fakultas '.$this->jurusan_rel?->fakultas_rel?->nama_fakultas);
    }
    protected function fakultasId(): Attribute
    {
        return Attribute::get(fn () => $this->jurusan_rel?->fakultas_rel?->id);
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

    public function scopeSearchProdi(Builder $query, $search)
    {
        if (empty(trim($search))) {
            return $query;
        }

        $search = trim($search);
        $searchLower = '%'.strtolower($search).'%';
        $searchTerm = '%'.$search.'%';

        return $query->where(function ($q) use ($search, $searchTerm, $searchLower) {
            // 1. Filter dasar Prodi (Nama, Kode Prodi, ID)
            $q->where('prodis.nama_prodi', 'like', $searchTerm)
                ->orWhere('prodis.kode_pr', 'like', $searchTerm);
            
            if (is_numeric($search)) {
                $q->orWhere('prodis.id', 'like', $search);
            } 

            // 2. Filter Pintar Strata (S1, S2, S3 / Sarjana, Magister, Doktor)
            $q->orWhereRaw("
                CONCAT(
                    CASE 
                        WHEN nama_strata = 'Sarjana' THEN 'S1' 
                        WHEN nama_strata = 'Magister' THEN 'S2' 
                        WHEN nama_strata = 'Doktor' THEN 'S3' 
                        ELSE nama_strata 
                    END, 
                    ' ', 
                    nama_prodi
                ) LIKE ?", [$searchTerm])
            ->orWhereRaw("CONCAT(nama_strata, ' ', nama_prodi) LIKE ?", [$searchTerm]);

                $q->orWhere(function($dq) use ($searchLower, $searchTerm) {
                    $dq->whereRaw("DATE_FORMAT(prodis.created_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                    ->orWhereRaw("DATE_FORMAT(prodis.created_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
                    ->orWhereRaw("LOWER(DATE_FORMAT(prodis.created_at, '%a, %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(prodis.created_at, '%W, %d %M %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(prodis.created_at, '%a %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(prodis.created_at, '%W %d %M %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("DATE_FORMAT(prodis.updated_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                    ->orWhereRaw("DATE_FORMAT(prodis.updated_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
                    ->orWhereRaw("LOWER(DATE_FORMAT(prodis.updated_at, '%a, %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(prodis.updated_at, '%W, %d %M %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(prodis.updated_at, '%a %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(prodis.updated_at, '%W %d %M %Y')) LIKE ?", ['%' . $searchLower . '%']);
                });

            // 3. Filter Relasi ke Jurusan (Termasuk kode_jr)
            $q->orWhereHas('jurusan_rel', function ($j) use ($searchTerm) {
                $j->where('nama_jurusan', 'like', $searchTerm)
                    ->orWhere('kode_jr', 'like', $searchTerm)
                    ->orWhereRaw("CONCAT('Jurusan ', nama_jurusan) LIKE ?", [$searchTerm])

                    // 4. Filter Relasi ke Fakultas (Termasuk kode_fk)
                    ->orWhereHas('fakultas_rel', function ($f) use ($searchTerm) {
                        $f->where('nama_fakultas', 'like', $searchTerm)
                            ->orWhere('kode_fk', 'like', $searchTerm)
                            ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", [$searchTerm]);
                    });
            });
        });
    }
}
