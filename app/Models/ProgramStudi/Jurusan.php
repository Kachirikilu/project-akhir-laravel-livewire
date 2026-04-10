<?php

namespace App\Models\ProgramStudi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Jurusan extends Model
{
    use SoftDeletes;

    protected $fillable = ['fk_id', 'kode_jr', 'nama_jr'];
    protected $appends = ['kode', 'jurusan', 'fakultas'];
    protected $casts = [
        'created_at' => 'date',
        'updated_at' => 'date',
    ];

    public function fk_rel()
    {
        return $this->belongsTo(Fakultas::class, 'fk_id')->withTrashed();
    }

    public function prodis(): HasMany
    {
        return $this->hasMany(Prodi::class, 'jr_id');
    }

    protected function jurusan(): Attribute {
        return Attribute::get(fn() => $this->nama_jr);
    }

    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            if (!empty($this->attributes['kode_jr'])) {
                return $this->attributes['kode_jr'];
            }
            $kodeFakultas = $this->fk_rel?->kode_fk;
            if (!empty($kodeFakultas)) {
                return $kodeFakultas;
            }
            return 'UNI';
        });
    }

    protected function kodeFk(): Attribute
    {
        return Attribute::get(function () {
            $kodeFakultas = $this->fk_rel?->kode_fk;
            if (! empty($kodeFakultas)) {
                return $kodeFakultas;
            }
            return 'UNI';
        });
    }

    // protected function kodeText(): Attribute
    // {
    //     return Attribute::get(function () {
    //         if (!empty($this->attributes['kode_jr'])) {
    //             return $this->attributes['kode_jr'];
    //         }
    //         $kodeFakultas = $this->fk_rel?->kode_fk;
    //         if (!empty($kodeFakultas)) {
    //             return $kodeFakultas;
    //         }
    //         return 'UNI';
    //     });
    // }
    protected function tingkatanProdi(): Attribute
    {
        return Attribute::get(function () {
            if (!empty($this->attributes['kode_jr'])) {
                return 2;
            }
            $kodeFakultas = $this->fk_rel?->kode_fk;
            if (!empty($kodeFakultas)) {
                return 3;
            }
            return 4;
        });
    }

    protected function jurusanJr(): Attribute
    {
        return Attribute::get(fn () => 'Jurusan '.$this->nama_jr);
    }
    protected function fakultas(): Attribute {
        return Attribute::get(fn() => $this->fk_rel?->nama_fk);
    }
    protected function fakultasFk(): Attribute
    {
        return Attribute::get(fn () => 'Fakultas '.$this->fk_rel?->nama_fk);
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

    public function scopeSearchJurusan(Builder $query, $search)
    {
        if (empty(trim($search))) {
            return $query;
        }

        $search = trim($search);
        $searchLower = '%'.strtolower($search).'%';
        $searchTerm = '%'.$search.'%';

        return $query->where(function ($q) use ($search, $searchTerm, $searchLower) {
            // 1. Filter dasar Jurusan
            $q->where('jurusans.nama_jr', 'like', $searchTerm)
                ->orWhere('jurusans.kode_jr', 'like', $searchTerm)
                ->orWhereRaw("CONCAT('Jurusan ', nama_jr) LIKE ?", [$searchTerm]);

            if (is_numeric($search)) {
                $q->orWhere('jurusans.id', 'like', $search);
            }

                $q->orWhere(function($dq) use ($searchLower, $searchTerm) {
                    $dq->whereRaw("DATE_FORMAT(jurusans.created_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                    ->orWhereRaw("DATE_FORMAT(jurusans.created_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
                    ->orWhereRaw("LOWER(DATE_FORMAT(jurusans.created_at, '%a, %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(jurusans.created_at, '%W, %d %M %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(jurusans.created_at, '%a %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(jurusans.created_at, '%W %d %M %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("DATE_FORMAT(jurusans.updated_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                    ->orWhereRaw("DATE_FORMAT(jurusans.updated_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
                    ->orWhereRaw("LOWER(DATE_FORMAT(jurusans.updated_at, '%a, %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(jurusans.updated_at, '%W, %d %M %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(jurusans.updated_at, '%a %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(jurusans.updated_at, '%W %d %M %Y')) LIKE ?", ['%' . $searchLower . '%']);
                });

            // 2. Filter berdasarkan Fakultas (Relasi)
            $q->orWhereHas('fk_rel', function ($sq) use ($searchTerm) {
                $sq->where('nama_fk', 'like', $searchTerm)
                    ->orWhere('kode_fk', 'like', $searchTerm)
                    ->orWhereRaw("CONCAT('Fakultas ', nama_fk) LIKE ?", [$searchTerm]);
            });
        });
    }
}