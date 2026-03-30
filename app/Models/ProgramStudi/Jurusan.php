<?php

namespace App\Models\ProgramStudi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

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

    // protected function kodeText(): Attribute
    // {
    //     return Attribute::get(function () {
    //         if (!empty($this->attributes['kode_jr'])) {
    //             return $this->attributes['kode_jr'];
    //         }
    //         $kodeFakultas = $this->fakultas_rel?->kode_fk;
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
            $q->where('jurusans.nama_jurusan', 'like', $searchTerm)
                ->orWhere('jurusans.kode_jr', 'like', $searchTerm)
                ->orWhereRaw("CONCAT('Jurusan ', nama_jurusan) LIKE ?", [$searchTerm]);

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
            $q->orWhereHas('fakultas_rel', function ($sq) use ($searchTerm) {
                $sq->where('nama_fakultas', 'like', $searchTerm)
                    ->orWhere('kode_fk', 'like', $searchTerm)
                    ->orWhereRaw("CONCAT('Fakultas ', nama_fakultas) LIKE ?", [$searchTerm]);
            });
        });
    }
}