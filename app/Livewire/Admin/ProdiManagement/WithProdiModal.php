<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Models\ProgramStudi\Fakultas;
use App\Models\ProgramStudi\Jurusan;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use App\Livewire\Global\HasToast;

trait WithProdiModal
{
    use HasToast;
    
    public $selected_id_pr;

    public $showProdiModal = false;

    public $isEditingPr = false;

    public $prodiType;

    public $jr_id_2;

    public $fk_id_2;

    protected $prodis = [
        'nama_pr' => 'required|string|max:255|unique:prodis,nama_pr',
        'jr_id' => 'required|exists:jurusans,id',
        'nama_jr' => 'required|string|max:255|unique:jurusans,nama_jr',
        'fk_id' => 'required|exists:fakultas,id',
        'nama_fk' => 'required|string|max:255|unique:fakultas,nama_fk',
    ];

    public function addProdi($prodi)
    {
        if (! $this->AuthCheck()) {
            return; 
        }

        $this->resetValidation();
        $this->resetErrorBag();
        $this->isEditingPr = false;
        $this->prodiType = $prodi;
        $this->showProdiModal = true;
        if ($prodi === 'prodi') {
            $this->updatedJrNameSearch($this->jrNameSearch);
        } elseif ($prodi === 'jurusan') {
            $this->updatedFkNameSearch($this->fkNameSearch);
        }
    }

    public function editProdi($id, $type)
    {
        if (! $this->AuthCheck()) {
            return; 
        }

        $this->selected_id_pr = $id;
        $this->prodiType = $type;
        $this->isEditingPr = true;

        $this->resetValidation();
        $this->resetErrorBag();

        $this->jr_id = $this->fk_id = $this->selected_id_pr = null;

        try {
            if ($type === 'prodi') {
                $prodi = Prodi::with('jr_rel')->findOrFail($id);
                $this->selected_id_pr = $prodi->id;
                $this->jr_id = $prodi->jr_id ?? null;
                $this->jr_id_2 = $prodi->jr_id ?? null;

                $this->jrNameSearch = $prodi->jurusan ?? '';

                if ($this->jr_id) {
                    $jurusan = Jurusan::find($this->jr_id);
                    $this->jrNameSearch = $jurusan ? $jurusan->jurusanJr : '';
                } else {
                    $this->jrNameSearch = '';
                }
                $this->getJrbyUser();
                $this->fetchJr($this->jrNameSearch);

            } elseif ($type === 'jurusan') {
                $jurusan = Jurusan::with('fk_rel')->findOrFail($id);
                $this->selected_id_pr = $jurusan->id;

                $this->fk_id = $jurusan->fk_id;
                $this->fk_id_2 = $jurusan->fk_id;
                $this->fkNameSearch = $jurusan->fakultas ?? '';

                if ($this->fk_id) {
                    $fakultas = Fakultas::find($this->fk_id);
                    $this->fkNameSearch = $fakultas ? $fakultas->fakultasFk : '';
                } else {
                    $this->fkNameSearch = '';
                }
                $this->getFkbyUser();
                $this->fetchFk($this->fkNameSearch);

            } elseif ($type === 'fakultas') {
                $fakultas = Fakultas::findOrFail($id);
                $this->selected_id_pr = $fakultas->id;
            }

            $this->showProdiModal = true;

        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Mengambil Data: '.$e->getMessage(), variant: 'danger');
        }
    }

    private function inputModalProdi($isEditingPr, $data)
    {
        $prodis = [];

        /* ===================== PROGRAM STUDI ===================== */
        if ($this->prodiType === 'prodi') {

            $kodePr = $data['kode_pr'] ?? null;
            if (! empty($kodePr) && ! empty($data['jr_id'])) {
                $jurusan = DB::table('jurusans')->find($data['jr_id']);
                $kodeJr = $jurusan->kode_jr;

                if (empty($kodeJr) && $jurusan) {
                    $fakultas = DB::table('fakultas')->find($jurusan->fk_id);
                    $kodeJr = $fakultas->kode_fk;
                }
                if ($kodePr === $kodeJr) {
                    $data['kode_pr'] = null;
                }
            }

            $prodis = [
                'nama_pr' => [
                    'required', 'string', 'max:255',
                    $this->uniqueRule('prodis', 'nama_pr', $isEditingPr ? $this->selected_id_pr : null),
                ],
                'kode_pr' => [
                    'nullable', 'string', 'min:3', 'max:3',
                    function ($attribute, $value, $fail) use ($data) {
                        if (empty($value)) {
                            return;
                        }

                        $jurusan = DB::table('jurusans')->find($data['jr_id']);
                        $fakultasId = $jurusan ? $jurusan->fk_id : null;

                        $otherJr = DB::table('jurusans')->where('kode_jr', $value)->where('id', '!=', $data['jr_id'])->exists();
                        $otherFk = DB::table('fakultas')->where('kode_fk', $value)->where('id', '!=', $fakultasId)->exists();
                        $otherPr = DB::table('prodis')->where('kode_pr', $value)->where('jr_id', '!=', $data['jr_id'])->exists();

                        if (empty($data['jr_id'])) {
                            $fail('Isi terlebih dahulu Jurusan!');
                        } elseif ($otherJr || $otherFk || $otherPr) {
                            $fail('Kode Program Studi ini sudah digunakan oleh instansi di luar silsilah Anda!');
                        }
                    },
                ],
                'jr_id' => ['required', 'exists:jurusans,id'],
                'strata' => [
                    'required',
                    Rule::in(['Sarjana','Magister','Doktor']),
                ],
            ];
        }

        /* ===================== JURUSAN ===================== */
        elseif ($this->prodiType === 'jurusan') {

            $kodeJr = $data['kode_jr'] ?? null;
            if (! empty($kodeJr) && ! empty($data['fk_id'])) {
                $fakultas = DB::table('fakultas')->find($data['fk_id']);
                $kodeFk = $fakultas->kode_fk ?? null;
                if ($kodeJr === $kodeFk) {
                    $data['kode_jr'] = null;
                }
            }

            $prodis = [
                'nama_jr' => [
                    'required', 'string', 'max:255',
                    $this->uniqueRule('jurusans', 'nama_jr', $isEditingPr ? $this->selected_id_pr : null),
                ],
                'kode_jr' => [
                    'nullable', 'string', 'min:3', 'max:3',
                    $this->uniqueRule('jurusans', 'kode_jr', $isEditingPr ? $this->selected_id_pr : null),

                    function ($attribute, $value, $fail) use ($data) {
                        if (empty($value)) {
                            return;
                        }

                        // 1. Gagal jika dipakai Fakultas lain (bukan induknya)
                        $otherFk = DB::table('fakultas')
                            ->where('kode_fk', $value)
                            ->where('id', '!=', $data['fk_id'])
                            ->exists();

                        // 2. Gagal jika dipakai Jurusan lain yang beda Fakultas
                        $otherJr = DB::table('jurusans')
                            ->where('kode_jr', $value)
                            ->where('fk_id', '!=', $data['fk_id'])
                            ->where('id', '!=', $this->selected_id_pr)
                            ->exists();

                        // 3. Gagal jika dipakai Prodi yang berasal dari Jurusan di luar Fakultas ini
                        $otherPr = DB::table('prodis')
                            ->join('jurusans', 'prodis.jr_id', '=', 'jurusans.id')
                            ->where('prodis.kode_pr', $value)
                            ->where('jurusans.fk_id', '!=', $data['fk_id'])
                            ->exists();

                        if (empty($data['fk_id'])) {
                            $fail('Isi terlebih dahulu Fakultas!');
                        } elseif ($otherFk || $otherJr || $otherPr) {
                            $fail('Kode Jurusan ini sudah digunakan oleh instansi di luar lingkup Fakultas Anda!');
                        }
                    },
                ],
                'fk_id' => ['required', 'exists:fakultas,id'],
            ];
        }

        /* ===================== FAKULTAS ===================== */
        elseif ($this->prodiType === 'fakultas') {
            $prodis = [
                'nama_fk' => [
                    'required', 'string', 'max:255',
                    $this->uniqueRule('fakultas', 'nama_fk', $isEditingPr ? $this->selected_id_pr : null),
                ],
                'kode_fk' => [
                    'required', 'string', 'min:3', 'max:3',
                    function ($attribute, $value, $fail) {
                        if (empty($value)) {
                            return;
                        }

                        // 1. Cek tabel Fakultas lain (Standard Unique)
                        $otherFk = DB::table('fakultas')->where('kode_fk', $value)->where('id', '!=', $this->selected_id_pr)->exists();
                        // 2. Gagal jika dipakai oleh Jurusan yang bukan milik Fakultas ini
                        $otherJr = DB::table('jurusans')->where('kode_jr', $value)->where('fk_id', '!=', $this->selected_id_pr)->exists();
                        // 3. Gagal jika dipakai oleh Prodi yang bukan milik Fakultas ini
                        $otherPr = DB::table('prodis')
                            ->join('jurusans', 'prodis.jr_id', '=', 'jurusans.id')
                            ->where('prodis.kode_pr', $value)
                            ->where('jurusans.fk_id', '!=', $this->selected_id_pr)
                            ->exists();

                        if ($otherFk || $otherJr || $otherPr) {
                            $fail('Kode Fakultas ini sudah digunakan oleh Jurusan/Prodi dari Fakultas lain!');
                        }
                    },
                ],
            ];
        }

        return Validator::make($data, $prodis, $this->validationMessagesProdi())->validate();
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
            $namaBersih = preg_replace($pattern, '', $validated['nama_pr']);
            $validated['nama_pr'] = $this->normalizeNama(trim($namaBersih));

        } elseif ($this->prodiType === 'jurusan') {
            $nama = preg_replace('/^jurusan\s+/i', '', trim($validated['nama_jr']));
            $validated['nama_jr'] = $this->normalizeNama($nama);

        } elseif ($this->prodiType === 'fakultas') {
            $nama = preg_replace('/^fakultas\s+/i', '', trim($validated['nama_fk']));
            $validated['nama_fk'] = $this->normalizeNama($nama);
        }

        return $validated;
    }

    private function formatStrata(string $strata): string
    {
        return match ($strata) {
            'Sarjana'  => 'S1',
            'Magister' => 'S2',
            'Doktor'   => 'S3',
            default    => $strata,
        };
    }

    public function saveProdi($data)
    {
        if (! $this->AuthCheck()) {
            return; 
        }

        $data['jr_id'] = $this->jr_id;
        $data['fk_id'] = $this->fk_id;

        if (empty($data['strata'])) {
            $data['strata'] = 'Sarjana';
        }

        try {

            $validated = $this->inputModalProdi(false, $data);
            $validated = $this->prepareData($validated);
            $message = '';

            DB::transaction(function () use ($validated, $message) {
                if ($this->prodiType === 'prodi') {
                    $strata = $this->formatStrata($validated['strata']);
                    $message = "Program Studi " .  $strata . ' ' . $validated['nama_pr'];
                    Prodi::create([
                        'nama_pr' => $validated['nama_pr'],
                        'strata' => $validated['strata'],
                        'jr_id' => $validated['jr_id'],
                        'kode_pr' => $validated['kode_pr'],
                    ]);
                } elseif ($this->prodiType === 'jurusan') {
                    $message = "Jurusan " . $validated['nama_jr'];
                    Jurusan::create([
                        'nama_jr' => $validated['nama_jr'],
                        'fk_id' => $validated['fk_id'],
                        'kode_jr' => $validated['kode_jr'],
                    ]);
                } elseif ($this->prodiType === 'fakultas') {
                    $message = "Fakultas " . $validated['nama_fk'];
                    Fakultas::create([
                        'nama_fk' => $validated['nama_fk'],
                        'kode_fk' => $validated['kode_fk'],
                    ]);
                }
            });

            $this->resetInputProdi();            
            $this->dispatch('refresh-data-pr');
            $this->showProdiModal = false;
            $this->toast(message: $message);

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Menambahkan: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data-pr');
            $this->showProdiModal = false;
        }
    }

    public function updateProdi($data)
    {
        if (! $this->AuthCheck()) {
            return; 
        }
        if ((empty($data['jr_id']) && $this->jr_id !== $this->jr_id_2) ||
            ($this->jr_id == $this->jr_id_2) || ($this->jr_id !== $this->jr_id_2)) {
            $data['jr_id'] = $this->jr_id;
        }
        if ((empty($data['fk_id']) && $this->fk_id !== $this->fk_id_2) ||
            ($this->fk_id == $this->fk_id_2) || ($this->fk_id !== $this->fk_id_2)) {
            $data['fk_id'] = $this->fk_id;
        }

        if (empty($data['strata'])) {
            $data['strata'] = 'Sarjana';
        }

        try {
            $validated = $this->inputModalProdi(true, $data);
            $validated = $this->prepareData($validated);
            $message = '';

            DB::transaction(function () use ($validated, &$message) {
                if ($this->prodiType === 'prodi') {
                    $strata = $this->formatStrata($validated['strata']);
                    $message = "Program Studi " .  $strata . ' ' . $validated['nama_pr'];
                    Prodi::findOrFail($this->selected_id_pr)->update([
                        'nama_pr' => $validated['nama_pr'],
                        'strata' => $validated['strata'],
                        'jr_id' => $validated['jr_id'],
                        'kode_pr' => $validated['kode_pr'],
                    ]);
                } elseif ($this->prodiType === 'jurusan') {
                    $message = "Jurusan " . $validated['nama_jr'];
                    Jurusan::findOrFail($this->selected_id_pr)->update([
                        'nama_jr' => $validated['nama_jr'],
                        'fk_id' => $validated['fk_id'],
                        'kode_jr' => $validated['kode_jr'],
                    ]);
                } elseif ($this->prodiType === 'fakultas') {
                    $message = "Fakultas " . $validated['nama_fk'];
                    Fakultas::findOrFail($this->selected_id_pr)->update([
                        'nama_fk' => $validated['nama_fk'],
                        'kode_fk' => $validated['kode_fk'],
                    ]);
                }
            });

            $this->resetInputProdi();            
            $this->dispatch('refresh-data-pr');
            $this->showProdiModal = false;
            $this->toast(message: $message, type: 'update');

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Memperbarui: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data-pr');
            $this->showProdiDelete = false;
        }
    }

    private function validationMessagesProdi()
    {
        return [
            /* --- Program Studi --- */
            'nama_pr.required' => 'Nama Program Studi wajib diisi!',
            'nama_pr.max' => 'Nama Program Studi tidak boleh lebih dari 255 karakter!',
            'nama_pr.unique' => 'Nama Program Studi sudah ada di database!',
            'strata.required' => 'Nama Strata wajib diisi!',
            'strata.in' => 'Nama Strata yang dipilih tidak sesuai dengan kategori yang diizinkan!',
            'kode_pr.min' => 'Kode Program Studi tidak boleh kurang dari 3 karakter!',
            'kode_pr.max' => 'Kode Program Studi tidak boleh lebih dari 3 karakter!',
            'kode_pr.string' => 'Kode Program Studi harus berupa teks!',
            'kode_pr.unique' => 'Kode Program Studi ini sudah digunakan oleh Program Studi lain!',
            'jr_id.required' => 'Jurusan wajib diisi!',
            'jr_id.exists' => 'Jurusan yang dipilih tidak valid!',

            /* --- Jurusan --- */
            'nama_jr.required' => 'Nama Jurusan wajib diisi!',
            'nama_jr.max' => 'Nama Jurusan tidak boleh lebih dari 255 karakter!',
            'nama_jr.unique' => 'Nama Jurusan sudah ada di database!',
            'kode_jr.min' => 'Kode Jurusan tidak boleh kurang dari 3 karakter!',
            'kode_jr.max' => 'Kode Jurusan tidak boleh lebih dari 3 karakter!',
            'kode_jr.string' => 'Kode Jurusan harus berupa teks!',
            'kode_jr.unique' => 'Kode Jurusan ini sudah terdaftar di database!',
            'fk_id.required' => 'Fakultas wajib diisi!',
            'fk_id.exists' => 'Fakultas yang dipilih tidak valid!',

            /* --- Fakultas --- */
            'nama_fk.required' => 'Nama Fakultas wajib diisi!',
            'nama_fk.max' => 'Nama Fakultas tidak boleh lebih dari 255 karakter!',
            'nama_fk.unique' => 'Nama Fakultas sudah ada di database!',
            'kode_fk.required' => 'Kode Fakultas wajib diisi!',
            'kode_fk.min' => 'Kode Fakultas tidak boleh kurang dari 3 karakter!',
            'kode_fk.max' => 'Kode Fakultas tidak boleh lebih dari 3 karakter!',
            'kode_fk.unique' => 'Kode Fakultas sudah terdaftar di database!',
            'kode_fk.string' => 'Kode Fakultas harus berupa teks!',
        ];
    }

    private function resetInputProdi()
    {
        $fields = [
            // 'nama_pr', 'strata', 'nama_jr', 'nama_fk',
            'jr_id', 'jr_id_2', 'fk_id', 'fk_id_2', 'jrNameSearch', 'fkNameSearch',
        ];

        // if (! $keepProdi) {
        //     $fields = array_merge($fields, ['pr_id', 'prNameSearch'
        //     // , 'prResults'
        //     ]);
        // }

        $this->selected_id_pr = null;
        $this->reset($fields);
        $this->resetErrorBag();
    }
}
