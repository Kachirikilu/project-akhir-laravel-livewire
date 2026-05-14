<?php

namespace App\Models\Kelas;

use App\Models\Auth\Mahasiswa;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class KelasJadwal extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function kelas_rel(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function sesis(): HasMany
    {
        return $this->hasMany(SesiKelas::class, 'kj_id')->orderBy('pertemuan_ke');
    }

    public function mahasiswas(): BelongsToMany
    {
        return $this->belongsToMany(Mahasiswa::class, 'mahasiswa_kelas', 'kj_id', 'mahasiswa_id')
            ->withTimestamps();
    }

    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            return preg_replace('/([A-Za-z])([0-9])/', '$1-$2', $this->kode_jadwal);
        });
    }

    protected function labelFull(): Attribute
    {
        return Attribute::get(fn () => $this->label_kelas.' '.$this->kode_wilayah);
    }

    protected function hari(): Attribute
    {
        return Attribute::get(fn () => $this->hari_pelaksanaan);
    }

    protected function tanggalPelaksanaan(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->tanggal_mulai) {
                return '-';
            }

            $mulai = Carbon::parse($this->tanggal_mulai)->format('d/m/Y');
            $akhir = $this->tanggal_berakhir
                ? Carbon::parse($this->tanggal_berakhir)->format('d/m/Y')
                : 'Selesai';

            return "{$mulai} - {$akhir}";
        });
    }

    protected function jamPelaksanaan(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->jam_mulai) {
                return '-';
            }

            $mulai = Carbon::parse($this->jam_mulai)->format('H:i');
            $akhir = $this->jam_berakhir
                ? Carbon::parse($this->jam_berakhir)->format('H:i')
                : '';

            return $akhir ? "{$mulai} - {$akhir}" : $mulai;
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

    public function scopeSearchKelasJadwal($query, $search)
    {
        $searchTerm = '%'.$search.'%';
        $searchLower = strtolower($search);

        return $query->where(function ($q) use ($searchLower, $search, $searchTerm) {
            $q->where('kelas_jadwals.kode_jadwal', 'like', $searchTerm)
                ->orWhere('kelas_jadwals.label_kelas', 'like', $searchTerm)
                ->orWhere('kelas_jadwals.kode_wilayah', 'like', $searchTerm)
                ->orWhereRaw("CONCAT(kelas_jadwals.label_kelas, ' ', kelas_jadwals.kode_wilayah) LIKE ?", [$searchTerm])

                ->orWhere('kelas_jadwals.hari_pelaksanaan', 'like', $searchTerm)
                ->orWhere('kelas_jadwals.jam_mulai', 'like', $searchTerm)
                ->orWhere('kelas_jadwals.jam_berakhir', 'like', $searchTerm)
                ->orWhere('kelas_jadwals.kapasitas', 'like', $search)
                ->orWhere(function ($dq) use ($searchLower, $searchTerm) {
                    $dq->whereRaw("DATE_FORMAT(kelas_jadwals.tanggal_mulai, '%d/%m/%Y') LIKE ?", [$searchTerm])
                        ->orWhereRaw("DATE_FORMAT(kelas_jadwals.tanggal_mulai, '%Y-%m-%d') LIKE ?", [$searchTerm])
                        ->orWhereRaw("DATE_FORMAT(kelas_jadwals.tanggal_berakhir, '%d/%m/%Y') LIKE ?", [$searchTerm])
                        ->orWhereRaw("DATE_FORMAT(kelas_jadwals.tanggal_berakhir, '%Y-%m-%d') LIKE ?", [$searchTerm])
                        ->orWhereRaw("DATE_FORMAT(kelas_jadwals.created_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                        ->orWhereRaw("DATE_FORMAT(kelas_jadwals.created_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
                        ->orWhereRaw("LOWER(DATE_FORMAT(kelas_jadwals.created_at, '%a, %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
                        ->orWhereRaw("LOWER(DATE_FORMAT(kelas_jadwals.created_at, '%W, %d %M %Y')) LIKE ?", ['%'.$searchLower.'%'])
                        ->orWhereRaw("DATE_FORMAT(kelas_jadwals.updated_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                        ->orWhereRaw("DATE_FORMAT(kelas_jadwals.updated_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
                        ->orWhereRaw("LOWER(DATE_FORMAT(kelas_jadwals.updated_at, '%a, %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
                        ->orWhereRaw("LOWER(DATE_FORMAT(kelas_jadwals.updated_at, '%W, %d %M %Y')) LIKE ?", ['%'.$searchLower.'%']);
                });

            if (is_numeric($search)) {
                $q->orWhere('kelas_jadwals.id', 'like', $search);
            }
        });
    }
}
