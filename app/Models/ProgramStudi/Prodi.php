<?php

namespace App\Models\ProgramStudi;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

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

    protected function fakultas(): Attribute
    {
        return Attribute::get(fn () => $this->jurusan_rel?->fakultas_rel?->nama_fakultas);
    }
    protected function fakultasId(): Attribute
    {
        return Attribute::get(fn () => $this->jurusan_rel?->fakultas_rel?->id);
    }



    public function scopeSearchProdi(Builder $query, $searchTerm)
    {
        $searchTerm = '%' . trim($searchTerm) . '%';

        return $query->where(function ($q) use ($searchTerm) {
            // 1. Filter dasar Prodi (Nama, Kode Prodi, ID)
            $q->where('nama_prodi', 'like', $searchTerm)
                ->orWhere('kode_pr', 'like', $searchTerm)
                ->orWhere('prodis.id', 'like', $searchTerm);

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
