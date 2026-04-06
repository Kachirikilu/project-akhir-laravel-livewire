<?php

namespace App\Livewire\Staff\MatkulManagement;

use App\Livewire\Global\HasToast;
use App\Models\Akademik\MataKuliah;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait WithMatkulModal
{
    use HasToast;

    public $selected_id;

    public $isEditing = false;

    public $showMKModal = false;

    public function addMK($tingkatan)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }
        $this->resetInputMK();

        $this->resetValidation();
        $this->resetErrorBag();
        $this->isEditing = false;
        $this->mkType = $tingkatan;
        $this->showMKModal = true;

        if ($tingkatan == 1 || $tingkatan == 4) {
            $this->updatedProdiNameSearch($this->prodiNameSearch);
        } elseif ($tingkatan == 2) {
            $this->updatedJurusanNameSearch($this->jurusanNameSearch);
        } elseif ($tingkatan == 3) {
            $this->updatedFakultasNameSearch($this->fakultasNameSearch);
        }
    }

    public function editMK($id, $tingkatan)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }
        $this->selected_id = $id;
        $this->mkType = $tingkatan;
        $this->isEditing = true;

        $this->resetInputMK();
        $this->prodiResults = [];

        $this->resetValidation();
        $this->resetErrorBag();

        try {
            $mk = MataKuliah::with(['prodis'])->findOrFail($id);

            $this->prodi_id_array = $mk->prodis->pluck('id')->toArray();
            foreach ($mk->prodis as $prodi) {
                $this->prodi_items_array[] = [
                    'kode' => $prodi->kode,
                    'name' => $prodi->prodi,
                    'name2' => $prodi->jurusanJr,
                    'name3' => $prodi->fakultasFk,
                ];
            }

            // $this->dispatch('refresh-component');

            $firstProdi = $mk->prodis->first();

            if ($firstProdi) {
                $this->jurusan_id = $firstProdi->jurusan_id;
                $this->jurusanNameSearch = $firstProdi->jurusanJr;
                $this->jurusan_kode = $firstProdi->kode;

                $this->fakultas_id = $firstProdi->fakultas_id;
                $this->fakultasNameSearch = $firstProdi->fakultasFk;
                $this->fakultas_kode = $firstProdi->kode;

                if ($tingkatan == 1 || $tingkatan == 4) {
                    $this->prodi_id = $firstProdi->id;
                }
                if ($tingkatan == 1) {
                    $this->prodiNameSearch = $firstProdi->prodi;
                    $this->prodi_items = [
                        'kode' => $firstProdi->kode,
                        'name' => $firstProdi->prodi,
                        'name2' => $firstProdi->jurusanJr,
                        'name3' => $firstProdi->fakultasFk,
                    ];
                    $this->fetchProdi($this->prodiNameSearch);
                }
            }

            

            if ($tingkatan == 4) {
                $this->updatedProdiNameSearch($this->prodiNameSearch);
            } elseif ($tingkatan == 2) {
                $this->updatedJurusanNameSearch($this->jurusanNameSearch);
                $this->fetchJurusan();
            } elseif ($tingkatan == 3) {
                $this->updatedFakultasNameSearch($this->fakultasNameSearch);
                $this->fetchFakultas();
            }

            $this->showMKModal = true;

            $this->dispatch('fill-modal-mk', mk: $mk);
            $this->dispatch('refresh-component');

        } catch (\Exception $e) {
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    private function inputModalMK($isEditing, $data)
    {
        // $tingkatanMap = [
        //     'mk-prodi' => 1, 'mk-jurusan' => 2, 'mk-fakultas' => 3, 'mk-universitas' => 4,
        //     1 => 1, 2 => 2, 3 => 3, 4 => 4,
        // ];
        $tingkatan = $this->mkType ?? 1;
        $targetProdiIds = ($tingkatan === 1) ? [$this->prodi_id] : ($this->prodi_id_array ?: []);

        $rules = [
            'nama_matkul' => 'required|string|max:255',
            'semester' => 'required|integer|min:1|max:8',
            'digit_semester' => 'required|string|size:2',
            'digit_mk' => [
                'required', 'string', 'size:2',
                function ($attribute, $value, $fail) use ($targetProdiIds, $isEditing) {
                    if (empty($value) || empty($targetProdiIds)) {
                        return;
                    }

                    foreach ($targetProdiIds as $index => $pId) {
                        if (empty($pId)) {
                            continue;
                        }

                        $query = DB::table('mata_kuliahs')
                            ->join('prodi_pivot_mk', 'mata_kuliahs.id', '=', 'prodi_pivot_mk.mk_id')
                            ->where('prodi_pivot_mk.prodi_id', $pId)
                            ->where('mata_kuliahs.digit_mk', $value);

                        if ($isEditing) {
                            $query->where('mata_kuliahs.id', '!=', $this->selected_id);
                        }

                        if ($query->exists()) {
                            $namaProdi = DB::table('prodis')->where('id', $pId)->value('nama_prodi') ?? "Prodi ID: $pId";
                            $fail("Digit MK '$value' sudah terpakai di Program Studi: ***$namaProdi***.");
                            break;
                        }
                    }
                },
            ],
            'sks_kuliah' => 'required|integer|min:1',
            'tipe_sks' => 'required|in:1,2,3,4',
            'is_wajib' => 'required|boolean',
        ];

        if ($tingkatan === 1) {
            $rules['prodi_id'] = 'required|exists:prodis,id';
        } else {
            $rules['prodi_id_array'] = 'required|array|min:1';
        }

        $validator = Validator::make($data, $rules, $this->validationMessagesMK());

        if ($validator->fails()) {
            foreach ($validator->errors()->toArray() as $key => $messages) {
                foreach ($messages as $message) {
                    $this->addError($key, $message);
                }
            }
            $validator->validate();
        }

        return $validator->validated();
    }

    private function normalizeNama($value)
    {
        return ucwords(strtolower(trim($value)));
    }

    // private function generateTingkatanMap($tingkatan) {
    //     $tingkatanMap = [
    //         'mk-prodi' => 1, 'mk-jurusan' => 2, 'mk-fakultas' => 3, 'mk-universitas' => 4,
    //         1 => 1, 2 => 2, 3 => 3, 4 => 4,
    //     ];
    //     return $tingkatanMap[$this->mkType] ?? 1;
    // }

    private function generateKodePrefix($data, $tingkatan)
    {
        if ($tingkatan === 1) { // Prodi
            return $this->prodi_kode ?? $this->jurusan_kode ?? $this->fakultas_kode ?? 'UNI';
        } elseif ($tingkatan === 2) { // Jurusan
            return $this->jurusan_kode ?? $this->fakultas_kode ?? 'UNI';
        } elseif ($tingkatan === 3) { // Fakultas
            return $this->fakultas_kode ?? 'UNI';
        } elseif ($tingkatan === 4) {
            return 'UNI';
        }
    }

    public function saveMK($data)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }
        $data['prodi_id'] = $this->prodi_id;
        $data['prodi_id_array'] = $this->prodi_id_array;

        $data['is_wajib'] = ($data['is_wajib'] !== '') ? (int) $data['is_wajib'] : 1;
        $data['tipe_sks'] = ! empty($data['tipe_sks']) ? (int) $data['tipe_sks'] : 1;
        $data['sks_kuliah'] = ! empty($data['sks_kuliah']) ? (int) $data['sks_kuliah'] : 1;

        try {
            $validated = $this->inputModalMK(false, $data);
            $tingkatan = $this->mkType;
            $kodePrefix = $this->generateKodePrefix($data, $tingkatan);

            DB::transaction(function () use ($validated, $tingkatan, $data) {

                $mk = MataKuliah::create([
                    'tingkatan_mk' => $tingkatan,
                    // 'kode_mk' => $kodePrefix,
                    'digit_semester' => $validated['digit_semester'],
                    'digit_mk' => $validated['digit_mk'],
                    'nama_matkul' => $this->normalizeNama($validated['nama_matkul']),
                    'semester' => $validated['semester'],
                    'sks_kuliah' => $validated['sks_kuliah'],
                    'tipe_sks' => $validated['tipe_sks'],
                    'is_wajib' => $validated['is_wajib'],
                    'bahan_kajian' => $data['bahan_kajian'] ?? null,
                    'deskripsi' => $data['deskripsi'] ?? null,
                ]);

                $targetIds = ($tingkatan === 1) ? [$this->prodi_id] : ($this->prodi_id_array ?: []);
                $targetIds = array_filter($targetIds);
                if (! empty($targetIds)) {
                    $mk->prodis()->attach($targetIds);
                }
            });

            $this->resetInputMK();
            $this->dispatch('refresh-data');
            $this->showMKModal = false;
            $this->toast(message: 'Mata Kuliah '.$this->normalizeNama($validated['nama_matkul']));

        } catch (ValidationException $e) {
            $this->toast(text: $e->getMessage(), variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: $e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data');
            $this->showMKModal = false;
        }
    }

    public function updateMK($data)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }
        $data['prodi_id'] = $this->prodi_id;
        $data['prodi_id_array'] = $this->prodi_id_array;

        $data['is_wajib'] = ($data['is_wajib'] !== '') ? (int) $data['is_wajib'] : 1;
        $data['tipe_sks'] = ! empty($data['tipe_sks']) ? (int) $data['tipe_sks'] : 1;
        $data['sks_kuliah'] = ! empty($data['sks_kuliah']) ? (int) $data['sks_kuliah'] : 1;

        try {
            $validated = $this->inputModalMK(true, $data);
            $tingkatan = $this->mkType;
            $kodePrefix = $this->generateKodePrefix($data, $tingkatan);

            DB::transaction(function () use ($validated, $tingkatan, $data) {
                $mk = MataKuliah::findOrFail($this->selected_id);

                // 3. UPDATE DATA UTAMA
                $mk->update([
                    // 'kode_mk' => $kodePrefix,
                    'digit_semester' => $validated['digit_semester'],
                    'digit_mk' => $validated['digit_mk'],
                    'nama_matkul' => $this->normalizeNama($validated['nama_matkul']),
                    'semester' => $validated['semester'],
                    'sks_kuliah' => $validated['sks_kuliah'],
                    'tipe_sks' => $validated['tipe_sks'],
                    'is_wajib' => $validated['is_wajib'],
                    'bahan_kajian' => $data['bahan_kajian'] ?? null,
                    'deskripsi' => $data['deskripsi'] ?? null,
                ]);

                // 4. LOGIKA TARGET IDs
                $targetIds = ($tingkatan === 1)
                            ? [$this->prodi_id]
                            : ($this->prodi_id_array ?: []);

                $cleanIds = array_values(array_filter($targetIds));

                // 5. SINKRONISASI RELASI PIVOT DENGAN SORT ORDER
                $syncData = [];
                foreach ($cleanIds as $index => $id) {
                    $syncData[$id] = ['sort_order' => $index];
                }

                $mk->prodis()->sync($syncData);
            });

            $this->dispatch('refresh-data');
            $this->showMKModal = false;
            $this->toast(message: 'Mata Kuliah '.$this->normalizeNama($validated['nama_matkul']), type: 'update');

        } catch (ValidationException $e) {
            $this->toast(text: $e->getMessage(), variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: $e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data');
            $this->showMKModal = false;
        }
    }

    private function validationMessagesMK()
    {
        return [
            'prodi_id.required' => 'Program Studi wajib diisi!',

            'prodi_id_array.required' => 'Program Studi wajib diisi!',
            'prodi_id_array.array' => 'Program Studi dalam bentuk Array!',
            'prodi_id_array.min' => 'Program Studi minimal berisi satu data!',

            // Nama Mata Kuliah
            'nama_matkul.required' => 'Nama Mata Kuliah wajib diisi!',
            'nama_matkul.string' => 'Nama Mata Kuliah harus berupa teks!',
            'nama_matkul.max' => 'Nama Mata Kuliah tidak boleh lebih dari 255 karakter!',

            // Semester (Integer)
            'semester.required' => 'Semester wajib diisi!',
            'semester.integer' => 'Semester harus berupa angka!',
            'semester.min' => 'Semester minimal adalah 1!',
            'semester.max' => 'Semester maksimal adalah 8!',

            // Digit Semester & Digit MK (String size 2)
            'digit_semester.required' => 'Digit Semester wajib diisi!',
            'digit_semester.size' => 'Digit Semester harus tepat 2 karakter (contoh: 01)!',

            'digit_mk.required' => 'Digit MK wajib diisi!',
            'digit_mk.size' => 'Digit MK harus tepat 2 karakter (contoh: 07)!',

            // SKS
            'sks_kuliah.required' => 'SKS Mata Kuliah wajib diisi!',
            'sks_kuliah.integer' => 'SKS harus berupa angka!',
            'sks_kuliah.min' => 'SKS minimal adalah 1!',

            // Tipe SKS & Status Wajib
            'tipe_sks.required' => 'Tipe SKS wajib dipilih!',
            'tipe_sks.in' => 'Tipe SKS yang dipilih tidak valid!',
            'is_wajib.required' => 'Status kewajiban Mata Kuliah wajib ditentukan!',
            'is_wajib.boolean' => 'Format status wajib tidak valid!',
        ];
    }

    private function resetInputMK()
    {
        $this->prodiNameSearch = '';
        $this->jurusanNameSearch = '';
        $this->fakultasNameSearch = '';

        $this->prodi_id = null;
        $this->jurusan_id = null;
        $this->fakultas_id = null;

        $this->prodi_name = null;
        $this->jurusan_name = null;
        $this->fakultas_name = null;

        $this->prodi_id_array = [];
        $this->prodi_items_array = [];

        $this->resetErrorBag();
    }
}
