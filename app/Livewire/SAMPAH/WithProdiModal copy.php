<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Models\Fakultas;
use App\Models\Jurusan;
use App\Models\Prodi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

trait WithProdiModal
{
    public $selected_id;

    public $showProdiModal = false;

    public $isEditing = false;

    public $prodiType;

    public $prodi_id;

    public $jurusan_id;

    public $fakultas_id;

    // public $nama_prodi;

    // public $nama_strata;

    // public $nama_jurusan;

    // public $nama_fakultas;

    public $prodi_name_search;

    // public $prodi_results;

    // public $selectedProdiId;

    // public $selectedJurusanId;

    // public $selectedFakultasId;

    protected $prodis = [
        'prodi_id' => 'required|exists:prodis,id',
        'nama_prodi' => 'required|string|max:255|unique:prodis,nama_prodi',
        'nama_strata' => 'required|string|max:255',
        'jurusan_id' => 'required|exists:jurusans,id',
        'nama_jurusan' => 'required|string|max:255|unique:jurusans,nama_jurusan',
        'fakultas_id' => 'required|exists:fakultas,id',
        'nama_fakultas' => 'required|string|max:255|unique:fakultas,nama_fakultas',
    ];

    public function addProdi($prodi)
    {
        if ($this->isEditing) {
            $this->resetInput();
        }

        $this->resetValidation();
        $this->resetErrorBag();
        $this->isEditing = false;
        $this->prodiType = $prodi;
        $this->showProdiModal = true;
        if ($prodi === 'prodi') {
            $this->updatedJurusanNameSearch($this->jurusan_name_search);
        } elseif ($prodi === 'jurusan') {
            $this->updatedFakultasNameSearch($this->fakultas_name_search);
        }
    }

    public function editProdi($id, $type)
    {
        $this->selected_id = $id;
        $this->prodiType = $type;
        $this->isEditing = true;

        $this->resetValidation();
        $this->resetErrorBag();

        // $this->nama_prodi = $this->nama_jurusan = $this->nama_fakultas
        //  = $this->nama_strata
        // = null;
        $this->jurusan_id = $this->fakultas_id = $this->selected_id = null;

        try {
            if ($type === 'prodi') {
                $prodi = Prodi::with('jurusan_rel')->findOrFail($id);
                $this->selected_id = $prodi->id;
                // $this->nama_prodi = $prodi->nama_prodi;
                // $this->nama_strata = $prodi->nama_strata;
                $this->jurusan_id = $prodi->jurusan_id;
                $this->jurusan_name_search = $prodi->jurusan_rel->nama_jurusan ?? '';

                if ($this->jurusan_id) {
                    $jurusan = Jurusan::find($this->jurusan_id);
                    $this->jurusan_name_search = $jurusan ? 'Jurusan '.$jurusan->nama_jurusan : '';
                } else {
                    $this->jurusan_name_search = '';
                }
                $this->getJurusanbyUser();
                $this->fetchJurusan($this->jurusan_name_search);

            } elseif ($type === 'jurusan') {
                $jurusan = Jurusan::with('fakultas_rel')->findOrFail($id);
                $this->selected_id = $jurusan->id;
                // $this->nama_jurusan = $jurusan->nama_jurusan;
                $this->fakultas_id = $jurusan->fakultas_id;
                $this->fakultas_name_search = $jurusan->fakultas_rel->nama_fakultas ?? '';

                if ($this->fakultas_id) {
                    $fakultas = Fakultas::find($this->fakultas_id);
                    $this->fakultas_name_search = $fakultas ? 'Fakultas '.$fakultas->nama_fakultas : '';
                } else {
                    $this->fakultas_name_search = '';
                }
                $this->getFakultasbyUser();
                $this->fetchFakultas($this->fakultas_name_search);

            } elseif ($type === 'fakultas') {
                $fakultas = Fakultas::findOrFail($id);
                $this->selected_id = $fakultas->id;
                // $this->nama_fakultas = $fakultas->nama_fakultas;
            }

            $this->showProdiModal = true;

        } catch (\Exception $e) {
            $this->dispatch('toast', message: '❌ Data tidak ditemukan!');
        }
    }

    public function inputModalProdi($isEditing, $data)
    {
        $prodis = [];

        /* ===================== PROGRAM STUDI ===================== */
        if ($this->prodiType === 'prodi') {

            $kodePr = $data['kode_pr'] ?? null;
            if (! empty($kodePr) && ! empty($data['jurusan_id'])) {
                $jurusan = DB::table('jurusans')->find($data['jurusan_id']);
                $kodeJr = $jurusan->kode_jr ?? null;
                // Jika tidak ada kode_jr, ambil kode_fk
                if (empty($kodeJr) && $jurusan) {
                    $fakultas = DB::table('fakultas')->find($jurusan->fakultas_id);
                    $kodeJr = $fakultas->kode_fk ?? null;
                }
                // Jika sama → kosongkan kode_pr
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
                    'nullable', 'string', 'max:3',
                    function ($attribute, $value, $fail) use ($data) {
                        if (empty($value)) {
                            return;
                        }

                        $jurusan = DB::table('jurusans')->find($data['jurusan_id']);
                        $fakultasId = $jurusan ? $jurusan->fakultas_id : null;

                        // 1. Gagal jika dipakai Jurusan lain (bukan induknya)
                        $otherJr = DB::table('jurusans')->where('kode_jr', $value)->where('id', '!=', $data['jurusan_id'])->exists();
                        // 2. Gagal jika dipakai Fakultas lain (bukan kakeknya)
                        $otherFk = DB::table('fakultas')->where('kode_fk', $value)->where('id', '!=', $fakultasId)->exists();
                        // 3. Gagal jika dipakai Prodi lain yang beda Jurusan
                        $otherPr = DB::table('prodis')->where('kode_pr', $value)->where('jurusan_id', '!=', $data['jurusan_id'])->exists();

                        if ($otherJr || $otherFk || $otherPr) {
                            $fail('Kode Program Studi ini sudah digunakan oleh instansi di luar silsilah Anda!');
                        }
                    },
                ],
                'jurusan_id' => ['required', 'exists:jurusans,id'],
            ];
        }

        /* ===================== JURUSAN ===================== */
        elseif ($this->prodiType === 'jurusan') {

            $kodeJr = $data['kode_jr'] ?? null;
            if (! empty($kodeJr) && ! empty($data['fakultas_id'])) {
                $fakultas = DB::table('fakultas')->find($data['fakultas_id']);
                $kodeFk = $fakultas->kode_fk ?? null;
                // Jika sama → kosongkan kode_jr
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
                    'nullable', 'string', 'max:3',
                    // Tambahkan ini agar tetap dicek keunikan di tabel jurusans itu sendiri
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
                        // (Sudah tercover uniqueRule di atas sebenarnya, tapi ini untuk proteksi silsilah)
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

                        if ($otherFk || $otherJr || $otherPr) {
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
                    'required', 'string', 'max:3',
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
            $validated['nama_prodi'] = $this->normalizeNama($validated['nama_prodi']);
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
        // dd([
        //     'selected_id' => $this->selected_id,
        //     'isEditing' => $this->isEditing,
        //     'prodiType' => $this->prodiType,
        //     'prodi_id' => $this->prodi_id,
        //     'jurusan_id' => $this->jurusan_id,
        //     'fakultas_id' => $this->fakultas_id,
        //     'prodi_name_search' => $this->prodi_name_search,
        // ]);

        if (empty($data['fakultas_id'])) {
            $data['fakultas_id'] = $this->fakultas_id;
        }
        if (empty($data['jurusan_id'])) {
            $data['jurusan_id'] = $this->jurusan_id;
        }

        $validated = $this->inputModalProdi(false, $data);
        $validated = $this->prepareData($validated);

        try {
            DB::transaction(function () use ($validated) {
                if ($this->prodiType === 'prodi') {
                    Prodi::create([
                        'nama_prodi' => $validated['nama_prodi'],
                        'nama_strata' => ($validated['nama_strata'] ?? '') ?: 'Sarjana',
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
            $this->showProdiModal = false;

            $this->dispatch('prodi-saved');
            $this->dispatch('toast', message: '✅ Data berhasil ditambahkan!');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: '❌ Terjadi kesalahan saat menambahkan data!');
        }
    }

    public function updateProdi($data)
    {
        // dd([
        //     'selected_id' => $this->selected_id,
        //     'isEditing' => $this->isEditing,
        //     'prodiType' => $this->prodiType,
        //     'prodi_id' => $this->prodi_id,
        //     'jurusan_id' => $this->jurusan_id,
        //     'fakultas_id' => $this->fakultas_id,
        // ]);

        if (empty($data['fakultas_id'])) {
            $data['fakultas_id'] = $this->fakultas_id;
        }
        if (empty($data['jurusan_id'])) {
            $data['jurusan_id'] = $this->jurusan_id;
        }
        $validated = $this->inputModalProdi(true, $data);
        $validated = $this->prepareData($validated);

        try {
            DB::transaction(function () use ($validated) {
                if ($this->prodiType === 'prodi') {
                    Prodi::findOrFail($this->selected_id)->update([
                        'nama_prodi' => $validated['nama_prodi'],
                        'nama_strata' => ($validated['nama_strata'] ?? '') ?: 'Sarjana',
                        'jurusan_id' => $validated['jurusan_id'],
                        'kode_pr' => $validated['kode_pr'],
                    ]);
                    $message = 'Program Studi';
                } elseif ($this->prodiType === 'jurusan') {
                    Jurusan::findOrFail($this->selected_id)->update([
                        'nama_jurusan' => $validated['nama_jurusan'],
                        'fakultas_id' => $validated['fakultas_id'],
                        'kode_jr' => $validated['kode_jr'],
                    ]);
                    $message = 'Jurusan';
                } elseif ($this->prodiType === 'fakultas') {
                    Fakultas::findOrFail($this->selected_id)->update([
                        'nama_fakultas' => $validated['nama_fakultas'],
                        'kode_fk' => $validated['kode_fk'],
                    ]);
                    $message = 'Fakultas';
                }
            });

            $this->showProdiModal = false;
            $this->dispatch('toast', message: "✅ Data $message berhasil diperbarui!");

            $this->dispatch('refresh-data');

        } catch (\Exception $e) {
            $this->dispatch('toast', message: '❌ Terjadi kesalahan saat memperbarui data!');
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
            'kode_pr.max' => 'Kode Program Studi tidak boleh lebih dari 3 karakter!',
            'kode_pr.string' => 'Kode Program Studi harus berupa teks!',
            'kode_pr.unique' => 'Kode Program Studi ini sudah digunakan oleh Program Studi lain!',
            'jurusan_id.required' => 'Jurusan wajib diisi!',
            'jurusan_id.exists' => 'Jurusan yang dipilih tidak valid!',

            /* --- Jurusan --- */
            'nama_jurusan.required' => 'Nama Jurusan wajib diisi!',
            'nama_jurusan.max' => 'Nama Jurusan tidak boleh lebih dari 255 karakter!',
            'nama_jurusan.unique' => 'Nama Jurusan sudah ada di database!',
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
            'kode_fk.max' => 'Kode Fakultas tidak boleh lebih dari 3 karakter!',
            'kode_fk.unique' => 'Kode Fakultas sudah terdaftar di database!',
            'kode_fk.string' => 'Kode Fakultas harus berupa teks!',
        ];
    }

    public function resetInput($keepProdi = false)
    {
        $fields = [
            // 'nama_prodi', 'nama_strata', 'nama_jurusan', 'nama_fakultas',
            'jurusan_id', 'fakultas_id', 'jurusan_name_search', 'fakultas_name_search',
        ];

        if (! $keepProdi) {
            $fields = array_merge($fields, ['prodi_id', 'prodi_name_search'
            // , 'prodi_results'
            ]);
        }

        $this->selected_id = null;
        $this->reset($fields);
        $this->resetErrorBag();
    }
}
