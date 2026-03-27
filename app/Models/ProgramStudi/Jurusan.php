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

    protected $fillable = ['fakultas_id', 'kode_jr', 'nama_jurusan'];
    protected $appends = ['kode', 'jurusan', 'fakultas'];

    public function fakultas_rel()
    {
        return $this->belongsTo(Fakultas::class, 'fakultas_id')->withTrashed();
    }

    public function prodis(): HasMany
    {
        return $this->hasMany(Prodi::class);
    }

    public function scopeSearchJurusan(Builder $query, $searchTerm)
    {
        $searchTerm = '%' . trim($searchTerm) . '%';

        return $query->where(function ($q) use ($searchTerm) {
            // 1. Filter dasar Jurusan
            $q->where('nama_jurusan', 'like', $searchTerm)
                ->orWhere('kode_jr', 'like', $searchTerm)
                ->orWhere('id', 'like', $searchTerm)
                ->orWhereRaw("CONCAT('Jurusan ', nama_jurusan) LIKE ?", [$searchTerm]);

            // 2. Filter berdasarkan Fakultas (Relasi)
            $q->orWhereHas('fakultas_rel', function ($sq) use ($searchTerm) {
                $sq->where('nama_fakultas', 'like', $searchTerm)
                    ->orWhere('kode_fk', 'like', $searchTerm)
                    ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", [$searchTerm]);
            });
        });
    }

    protected function jurusan(): Attribute {
        return Attribute::get(fn() => $this->nama_jurusan);
    }

    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            if (!empty($this->attributes['kode_jr'])) {
                return $this->attributes['kode_jr'];
            }
            $kodeFakultas = $this->fakultas_rel?->kode_fk;
            if (!empty($kodeFakultas)) {
                return $kodeFakultas;
            }
            return 'UNI';
        });
    }

    protected function kodeText(): Attribute
    {
        return Attribute::get(function () {
            if (!empty($this->attributes['kode_jr'])) {
                return $this->attributes['kode_jr'];
            }
            $kodeFakultas = $this->fakultas_rel?->kode_fk;
            if (!empty($kodeFakultas)) {
                return $kodeFakultas;
            }
            return 'UNI';
        });
    }
    protected function tingkatanProdi(): Attribute
    {
        return Attribute::get(function () {
            if (!empty($this->attributes['kode_jr'])) {
                return 2;
            }
            $kodeFakultas = $this->fakultas_rel?->kode_fk;
            if (!empty($kodeFakultas)) {
                return 3;
            }
            return 4;
        });
    }

    protected function fakultas(): Attribute {
        return Attribute::get(fn() => $this->fakultas_rel?->nama_fakultas);
    }
}