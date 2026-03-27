<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Models\ProgramStudi\Fakultas;
use App\Models\ProgramStudi\Jurusan;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

trait WithProdiModal
{
    public $selected_id;

    public $showProdiModal = false;

    public $isEditing = false;

    public $prodiType;

    public $jurusan_id_2;

    public $fakultas_id_2;

    protected $prodis = [
        'nama_prodi' => 'required|string|max:255|unique:prodis,nama_prodi',
        'jurusan_id' => 'required|exists:jurusans,id',
        'nama_jurusan' => 'required|string|max:255|unique:jurusans,nama_jurusan',
        'fakultas_id' => 'required|exists:fakultas,id',
        'nama_fakultas' => 'required|string|max:255|unique:fakultas,nama_fakultas',
    ];

    public function addProdi($prodi)
    {
        $this->resetValidation();
        $this->resetErrorBag();
        $this->isEditing = false;
        $this->prodiType = $prodi;
        $this->showProdiModal = true;
        if ($prodi === 'prodi') {
            $this->updatedJurusanNameSearch($this->jurusanNameSearch);
        } elseif ($prodi === 'jurusan') {
            $this->updatedFakultasNameSearch($this->fakultasNameSearch);
        }
    }

    public function editProdi($id, $type)
    {
        $this->selected_id = $id;
        $this->prodiType = $type;
        $this->isEditing = true;

        $this->resetValidation();
        $this->resetErrorBag();

        $this->jurusan_id = $this->fakultas_id = $this->selected_id = null;

        try {
            if ($type === 'prodi') {
                $prodi = Prodi::with('jurusan_rel')->findOrFail($id);
                $this->selected_id = $prodi->id;
                $this->jurusan_id = $prodi->jurusan_id ?? null;
                $this->jurusan_id_2 = $prodi->jurusan_id ?? null;

                $this->jurusanNameSearch = $prodi->jurusan ?? '';

                if ($this->jurusan_id) {
                    $jurusan = Jurusan::find($this->jurusan_id);
                    $this->jurusanNameSearch = $jurusan ? 'Jurusan '.$jurusan->jurusan : '';
                } else {
                    $this->jurusanNameSearch = '';
                }
                $this->getJurusanbyUser();
                $this->fetchJurusan($this->jurusanNameSearch);

            } elseif ($type === 'jurusan') {
                $jurusan = Jurusan::with('fakultas_rel')->findOrFail($id);
                $this->selected_id = $jurusan->id;

                $this->fakultas_id = $jurusan->fakultas_id;
                $this->fakultas_id_2 = $jurusan->fakultas_id;
                $this->fakultasNameSearch = $jurusan->fakultas_rel->fakultas ?? '';

                if ($this->fakultas_id) {
                    $fakultas = Fakultas::find($this->fakultas_id);
                    $this->fakultasNameSearch = $fakultas ? 'Fakultas '.$fakultas->fakultas : '';
                } else {
                    $this->fakultasNameSearch = '';
                }
                $this->getFakultasbyUser();
                $this->fetchFakultas($this->fakultasNameSearch);

            } elseif ($type === 'fakultas') {
                $fakultas = Fakultas::findOrFail($id);
                $this->selected_id = $fakultas->id;
            }

            $this->showProdiModal = true;

        } catch (\Exception $e) {
            $this->dispatch('toast', message: '❌ Data tidak ditemukan!');
        }
    }

    private function inputModalProdi($isEditing, $data)
    {
        $prodis = [];

        /* ===================== PROGRAM STUDI ===================== */
        if ($this->prodiType === 'prodi') {

            $kodePr = $data['kode_pr'] ?? null;
            if (! empty($kodePr) && ! empty($data['jurusan_id'])) {
                $jurusan = DB::table('jurusans')->find($data['jurusan_id']);
                $kodeJr = $jurusan->kode_jr;

                if (empty($kodeJr) && $jurusan) {
                    $fakultas = DB::table('fakultas')->find($jurusan->fakultas_id);
                    $kodeJr = $fakultas->kode_fk;
                }
                if ($kodePr === $kodeJr) {
                    $data['kode_pr'] = null;
                }
            }

            $prodis = [
                'nama_prodi' => [
                    'required', 'string', 'max:255',
                    $this->uniqueRule('prodis', 'nama_prodi', $isEditing ? $this->selected_id : null),
                ],
                'kode_pr' => [
                    'nullable', 'string', 'min:3', 'max:3',
                    function ($attribute, $value, $fail) use ($data) {
                        if (empty($value)) {
                            return;
                        }

                        $jurusan = DB::table('jurusans')->find($data['jurusan_id']);
                        $fakultasId = $jurusan ? $jurusan->fakultas_id : null;

                        $otherJr = DB::table('jurusans')->where('kode_jr', $value)->where('id', '!=', $data['jurusan_id'])->exists();
                        $otherFk = DB::table('fakultas')->where('kode_fk', $value)->where('id', '!=', $fakultasId)->exists();
                        $otherPr = DB::table('prodis')->where('kode_pr', $value)->where('jurusan_id', '!=', $data['jurusan_id'])->exists();

                        if (empty($data['jurusan_id'])) {
                            $fail('Isi terlebih dahulu Jurusan!');
                        } elseif ($otherJr || $otherFk || $otherPr) {
                            $fail('Kode Program Studi ini sudah digunakan oleh instansi di luar silsilah Anda!');
                        }
                    },
                ],
                'jurusan_id' => ['required', 'exists:jurusans,id'],
                'nama_strata' => [
                    'required',
                    Rule::in(['Sarjana','Magister','Doktor']),
                ],
            ];
        }

        /* ===================== JURUSAN ===================== */
        elseif ($this->prodiType === 'jurusan') {

            $kodeJr = $data['kode_jr'] ?? null;
            if (! empty($kodeJr) && ! empty($data['fakultas_id'])) {
                $fakultas = DB::table('fakultas')->find($data['fakultas_id']);
                $kodeFk = $fakultas->kode_fk ?? null;
                if ($kodeJr === $kodeFk) {
                    $data['kode_jr'] = null;
                }
            }

            $prodis = [
                'nama_jurusan' => [
                    'required', 'string', 'max:255',
                    $this->uniqueRule('jurusans', 'nama_jurusan', $isEditing ? $this->selected_id : null),
                ],
                'kode_jr' => [
                    'nullable', 'string', 'min:3', 'max:3',
                    $this->uniqueRule('jurusans', 'kode_jr', $isEditing ? $this->selected_id : null),

                    function ($attribute, $value, $fail) use ($data) {
                        if (empty($value)) {
                            return;
                        }

                        // 1. Gagal jika dipakai Fakultas lain (bukan induknya)
                        $otherFk = DB::table('fakultas')
                            ->where('kode_fk', $value)
                            ->where('id', '!=', $data['fakultas_id'])
                            ->exists();

                        // 2. Gagal jika dipakai Jurusan lain yang beda Fakultas
                        $otherJr = DB::table('jurusans')
                            ->where('kode_jr', $value)
                            ->where('fakultas_id', '!=', $data['fakultas_id'])
                            ->where('id', '!=', $this->selected_id)
                            ->exists();

                        // 3. Gagal jika dipakai Prodi yang berasal dari Jurusan di luar Fakultas ini
                        $otherPr = DB::table('prodis')
                            ->join('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
                            ->where('prodis.kode_pr', $value)
                            ->where('jurusans.fakultas_id', '!=', $data['fakultas_id'])
                            ->exists();

                        if (empty($data['fakultas_id'])) {
                            $fail('Isi terlebih dahulu Fakultas!');
                        } elseif ($otherFk || $otherJr || $otherPr) {
                            $fail('Kode Jurusan ini sudah digunakan oleh instansi di luar lingkup Fakultas Anda!');
                        }
                    },
                ],
                'fakultas_id' => ['required', 'exists:fakultas,id'],
            ];
        }

        /* ===================== FAKULTAS ===================== */
        elseif ($this->prodiType === 'fakultas') {
            $prodis = [
                'nama_fakultas' => [
                    'required', 'string', 'max:255',
                    $this->uniqueRule('fakultas', 'nama_fakultas', $isEditing ? $this->selected_id : null),
                ],
                'kode_fk' => [
                    'required', 'string', 'min:3', 'max:3',
                    function ($attribute, $value, $fail) {
                        if (empty($value)) {
                            return;
                        }

                        // 1. Cek tabel Fakultas lain (Standard Unique)
                        $otherFk = DB::table('fakultas')->where('kode_fk', $value)->where('id', '!=', $this->selected_id)->exists();
                        // 2. Gagal jika dipakai oleh Jurusan yang bukan milik Fakultas ini
                        $otherJr = DB::table('jurusans')->where('kode_jr', $value)->where('fakultas_id', '!=', $this->selected_id)->exists();
                        // 3. Gagal jika dipakai oleh Prodi yang bukan milik Fakultas ini
                        $otherPr = DB::table('prodis')
                            ->join('jurusans', 'prodis.jurusan_id', '=', 'jurusans.id')
                            ->where('prodis.kode_pr', $value)
                            ->where('jurusans.fakultas_id', '!=', $this->selected_id)
                            ->exists();

                        if ($otherFk || $otherJr || $otherPr) {
                            $fail('Kode Fakultas ini sudah digunakan oleh Jurusan/Prodi dari Fakultas lain!');
                        }
                    },
                ],
            ];
        }

        return Validator::make($data, $prodis, $this->validationMessages())->validate();
    }

    private function uniqueRule(string $table, string $column, $id = null)
    {
        return $id ? Rule::unique($table, $column)->ignore($id) : Rule::unique($table, $column);
    }

    private function normalizeNama($value)
    {
        $value = trim($value);
        $value = strtolower($value);

        return ucwords($value);
    }

    private function prepareData(array $validated)
    {
        if ($this->prodiType === 'prodi') {
            $pattern = '/\b(s1|s2|s3|sarjana|magister|doktor)\b/i';
            $namaBersih = preg_replace($pattern, '', $validated['nama_prodi']);
            $validated['nama_prodi'] = $this->normalizeNama(trim($namaBersih));

        } elseif ($this->prodiType === 'jurusan') {
            $nama = preg_replace('/^jurusan\s+/i', '', trim($validated['nama_jurusan']));
            $validated['nama_jurusan'] = $this->normalizeNama($nama);

        } elseif ($this->prodiType === 'fakultas') {
            $nama = preg_replace('/^fakultas\s+/i', '', trim($validated['nama_fakultas']));
            $validated['nama_fakultas'] = $this->normalizeNama($nama);
        }

        return $validated;
    }

    public function saveProdi($data)
    {
        $data['jurusan_id'] = $this->jurusan_id;
        $data['fakultas_id'] = $this->fakultas_id;

        if (empty($data['nama_strata'])) {
            $data['nama_strata'] = 'Sarjana';
        }

        $validated = $this->inputModalProdi(false, $data);
        $validated = $this->prepareData($validated);

        try {
            DB::transaction(function () use ($validated) {
                if ($this->prodiType === 'prodi') {
                    Prodi::create([
                        'nama_prodi' => $validated['nama_prodi'],
                        'nama_strata' => $validated['nama_strata'],
                        'jurusan_id' => $validated['jurusan_id'],
                        'kode_pr' => $validated['kode_pr'],
                    ]);
                } elseif ($this->prodiType === 'jurusan') {
                    Jurusan::create([
                        'nama_jurusan' => $validated['nama_jurusan'],
                        'fakultas_id' => $validated['fakultas_id'],
                        'kode_jr' => $validated['kode_jr'],
                    ]);
                } elseif ($this->prodiType === 'fakultas') {
                    Fakultas::create([
                        'nama_fakultas' => $validated['nama_fakultas'],
                        'kode_fk' => $validated['kode_fk'],
                    ]);
                }
            });

            $this->resetInput();
            $this->dispatch('toast', message: '✅ Data berhasil ditambahkan!');
            $this->dispatch('refresh-data');
            $this->showProdiModal = false;

        } catch (\Exception $e) {
            $this->dispatch('toast', message: '❌ Terjadi kesalahan saat menambahkan data!');
            $this->dispatch('refresh-data');
            $this->showProdiModal = false;
        }
    }

    public function updateProdi($data)
    {
        if ((empty($data['jurusan_id']) && $this->jurusan_id !== $this->jurusan_id_2) ||
            ($this->jurusan_id == $this->jurusan_id_2) || ($this->jurusan_id !== $this->jurusan_id_2)) {
            $data['jurusan_id'] = $this->jurusan_id;
        }
        if ((empty($data['fakultas_id']) && $this->fakultas_id !== $this->fakultas_id_2) ||
            ($this->fakultas_id == $this->fakultas_id_2) || ($this->fakultas_id !== $this->fakultas_id_2)) {
            $data['fakultas_id'] = $this->fakultas_id;
        }

        if (empty($data['nama_strata'])) {
            $data['nama_strata'] = 'Sarjana';
        }

        $validated = $this->inputModalProdi(true, $data);
        $validated = $this->prepareData($validated);

        try {
            DB::transaction(function () use ($validated) {
                if ($this->prodiType === 'prodi') {
                    Prodi::findOrFail($this->selected_id)->update([
                        'nama_prodi' => $validated['nama_prodi'],
                        'nama_strata' => $validated['nama_strata'],
                        'jurusan_id' => $validated['jurusan_id'],
                        'kode_pr' => $validated['kode_pr'],
                    ]);
                } elseif ($this->prodiType === 'jurusan') {
                    Jurusan::findOrFail($this->selected_id)->update([
                        'nama_jurusan' => $validated['nama_jurusan'],
                        'fakultas_id' => $validated['fakultas_id'],
                        'kode_jr' => $validated['kode_jr'],
                    ]);
                } elseif ($this->prodiType === 'fakultas') {
                    Fakultas::findOrFail($this->selected_id)->update([
                        'nama_fakultas' => $validated['nama_fakultas'],
                        'kode_fk' => $validated['kode_fk'],
                    ]);
                }
            });

            $this->dispatch('toast', message: '✅ Data berhasil diperbarui!');
            $this->dispatch('refresh-data');
            $this->showProdiModal = false;

        } catch (\Exception $e) {
            $this->dispatch('toast', message: '❌ Terjadi kesalahan saat memperbarui data!');
            $this->dispatch('refresh-data');
            $this->showProdiDelete = false;
        }
    }

    public function validationMessages()
    {
        return [
            /* --- Program Studi --- */
            'nama_prodi.required' => 'Nama Program Studi wajib diisi!',
            'nama_prodi.max' => 'Nama Program Studi tidak boleh lebih dari 255 karakter!',
            'nama_prodi.unique' => 'Nama Program Studi sudah ada di database!',
            'nama_strata.required' => 'Nama Strata wajib diisi!',
            'nama_strata.in' => 'Nama Strata yang dipilih tidak sesuai dengan kategori yang diizinkan!',
            'kode_pr.min' => 'Kode Program Studi tidak boleh kurang dari 3 karakter!',
            'kode_pr.max' => 'Kode Program Studi tidak boleh lebih dari 3 karakter!',
            'kode_pr.string' => 'Kode Program Studi harus berupa teks!',
            'kode_pr.unique' => 'Kode Program Studi ini sudah digunakan oleh Program Studi lain!',
            'jurusan_id.required' => 'Jurusan wajib diisi!',
            'jurusan_id.exists' => 'Jurusan yang dipilih tidak valid!',

            /* --- Jurusan --- */
            'nama_jurusan.required' => 'Nama Jurusan wajib diisi!',
            'nama_jurusan.max' => 'Nama Jurusan tidak boleh lebih dari 255 karakter!',
            'nama_jurusan.unique' => 'Nama Jurusan sudah ada di database!',
            'kode_jr.min' => 'Kode Jurusan tidak boleh kurang dari 3 karakter!',
            'kode_jr.max' => 'Kode Jurusan tidak boleh lebih dari 3 karakter!',
            'kode_jr.string' => 'Kode Jurusan harus berupa teks!',
            'kode_jr.unique' => 'Kode Jurusan ini sudah terdaftar di database!',
            'fakultas_id.required' => 'Fakultas wajib diisi!',
            'fakultas_id.exists' => 'Fakultas yang dipilih tidak valid!',

            /* --- Fakultas --- */
            'nama_fakultas.required' => 'Nama Fakultas wajib diisi!',
            'nama_fakultas.max' => 'Nama Fakultas tidak boleh lebih dari 255 karakter!',
            'nama_fakultas.unique' => 'Nama Fakultas sudah ada di database!',
            'kode_fk.required' => 'Kode Fakultas wajib diisi!',
            'kode_fk.min' => 'Kode Fakultas tidak boleh kurang dari 3 karakter!',
            'kode_fk.max' => 'Kode Fakultas tidak boleh lebih dari 3 karakter!',
            'kode_fk.unique' => 'Kode Fakultas sudah terdaftar di database!',
            'kode_fk.string' => 'Kode Fakultas harus berupa teks!',
        ];
    }

    public function resetInput()
    {
        $fields = [
            // 'nama_prodi', 'nama_strata', 'nama_jurusan', 'nama_fakultas',
            'jurusan_id', 'jurusan_id_2', 'fakultas_id', 'fakultas_id_2', 'jurusanNameSearch', 'fakultasNameSearch',
        ];

        // if (! $keepProdi) {
        //     $fields = array_merge($fields, ['prodi_id', 'prodiNameSearch'
        //     // , 'prodiResults'
        //     ]);
        // }

        $this->selected_id = null;
        $this->reset($fields);
        $this->resetErrorBag();
    }
}
