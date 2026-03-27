<?php

namespace App\Models\ProgramStudi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jurusan extends Model
{
    use SoftDeletes;

    protected $fillable = ['fakultas_id', 'nama_jurusan', 'kode_jr'];
    protected $appends = ['jurusan', 'kode', 'fakultas'];

    public function fakultas_rel()
    {
        return $this->belongsTo(Fakultas::class, 'fakultas_id')->withTrashed();
    }

    public function prodis(): HasMany
    {
        return $this->hasMany(Prodi::class);
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