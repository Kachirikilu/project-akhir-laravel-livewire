<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MataKuliah extends Model
{
    protected $table = 'mata_kuliahs';
    protected $fillable = [
        'tingkatan_mk', 'kode_mk', 'digit_semester', 'digit_mk', 
        'nama_matkul', 'semester', 'sks_kuliah', 'tipe_sks', 
        'is_wajib', 'bahan_kajian', 'deskripsi'
    ];

    protected $appends = ['matkul', 'sks_tm', 'sks_pr', 'sks_pl', 'sks_sm', 'kode'];

    public function prodis(): BelongsToMany
    {
        return $this->belongsToMany(Prodi::class, 'prodi_pivot_mk', 'mk_id', 'prodi_id');
    }

    protected function matkul(): Attribute {
        return Attribute::get(fn() => $this->nama_matkul);
    }

    protected function sks(): Attribute {
        return Attribute::get(fn() => $this->sks_kuliah);
    }

    // 0: Tatap Muka (TM)
    protected function sksTm(): Attribute {
        return Attribute::get(fn() => $this->tipe_sks == 0 ? $this->sks_kuliah : null);
    }

    // 1: Praktikum (PR)
    protected function sksPr(): Attribute {
        return Attribute::get(fn() => $this->tipe_sks == 1 ? $this->sks_kuliah : null);
    }

    // 2: Praktek Lapangan (PL)
    protected function sksPl(): Attribute {
        return Attribute::get(fn() => $this->tipe_sks == 2 ? $this->sks_kuliah : null);
    }

    // 3: Simulasi (SM)
    protected function sksSm(): Attribute {
        return Attribute::get(fn() => $this->tipe_sks == 3 ? $this->sks_kuliah : null);
    }

    protected function wajib(): Attribute {
        return Attribute::get(fn() => $this->is_wajib);
    }

    /**
     * Logika Pembentukan Kode MK (Contoh: TKE1102)
     */
    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            $prefix = 'UNI'; // Default jika tidak ada relasi atau tingkatan 4

            if ($this->tingkatan_mk == 0) {
                $prefix = strtoupper($this->kode_mk);
            } 
            else {
                // Ambil prodi pertama sebagai acuan prefix kode
                $prodi = $this->prodis->first();

                if ($prodi) {
                    if ($this->tingkatan_mk == 1) { // Tingkat Prodi
                        $prefix = $prodi->kode ?? $prodi->jurusan_rel->fakultas_rel->kode_fk ?? 'UNI'; // Memanggil accessor 'kode' di Model Prodi Anda
                    } 
                    elseif ($this->tingkatan_mk == 2) { // Tingkat Jurusan
                        $prefix = $prodi->jurusan_rel->kode_jr ?? $prodi->jurusan_rel->fakultas_rel->kode_fk ?? 'UNI';
                    } 
                    elseif ($this->tingkatan_mk == 3) { // Tingkat Fakultas
                        $prefix = $prodi->jurusan_rel->fakultas_rel->kode_fk ?? 'UNI';
                    }
                }
            }

            // Gabungkan: PREFIX + DIGIT_SEMESTER + DIGIT_MK
            return $prefix . $this->digit_semester . $this->digit_mk;
        });
    }
}