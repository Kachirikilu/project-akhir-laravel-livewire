<?php

namespace App\Livewire\Staff\MatkulManagement;

use App\Models\Akademik\MataKuliah;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

trait WithMatkulModal
{
    public $selected_id;

    public $isEditing = false;

    public $showMKModal = false;

    // public $prodi_id_2;

    // public $prodi_id_array_2 = [];

    // public $prodi_names = [];

    // public $prodi_kodes = [];

    public function addMK($tingkatan)
    {
        // if ($this->isEditing) {
        //     $this->resetInput();
        // }
        // if ($tingkatan !== 'mk-prodi') {
        $this->resetInput();
        // }

        $this->resetValidation();
        $this->resetErrorBag();
        $this->isEditing = false;
        $this->mkType = $tingkatan;
        $this->showMKModal = true;

        if ($tingkatan == 'mk-prodi' || $tingkatan == 'mk-universitas') {
            $this->updatedProdiNameSearch($this->prodiNameSearch);
        } elseif ($tingkatan == 'mk-jurusan') {
            $this->updatedJurusanNameSearch($this->jurusanNameSearch);
        } elseif ($tingkatan == 'mk-fakultas') {
            $this->updatedFakultasNameSearch($this->fakultasNameSearch);
        }
    }

    public function editMK($id, $tingkatan)
    {
        $this->selected_id = $id;
        $this->mkType = $tingkatan;
        $this->isEditing = true;

        $this->resetInput();
        $this->prodiResults = [];

        $this->resetValidation();
        $this->resetErrorBag();

        try {
            $mk = MataKuliah::with(['prodis'])->findOrFail($id);

            // Sesuaikan dengan $selectedNameArray
            $this->prodi_id_array = $mk->prodis->pluck('id')->toArray();
            $this->prodi_name_array = $mk->prodis->pluck('prodi')->toArray();
            $this->prodi_kode_array = $mk->prodis->pluck('kode')->toArray();

            $this->dispatch('refresh-component');

            // 2. Ambil data Array (untuk Checkbox/Multi-select)
            // $this->prodi_id_array_2 = $this->prodi_id_array;

            // 3. Ambil data Hirarki dari Prodi Pertama sebagai referensi UI
            $firstProdi = $mk->prodis->first();

            if ($firstProdi) {
                $this->jurusan_id = $firstProdi->jurusan_id;
                $this->jurusanNameSearch = 'Jurusan '.$firstProdi->jurusan_rel->jurusan ?? '';
                $this->jurusan_kode = $firstProdi->jurusan_rel->kode ?? 'UNI';

                $this->fakultas_id = $firstProdi->jurusan_rel->fakultas_id ?? null;
                $this->fakultasNameSearch = 'Fakultas '.$firstProdi->jurusan_rel->fakultas_rel->fakultas ?? '';
                $this->fakultas_kode = $firstProdi->jurusan_rel->fakultas_rel->kode ?? 'UNI';

                // dd($tingkatan);
                if ($tingkatan == 'mk-prodi' || $tingkatan == 'mk-universitas') {
                    $this->prodi_id = $firstProdi->id;
                    $this->prodiNameSearch = $firstProdi->prodi;
                    $this->prodi_kode = $firstProdi->kode ?? 'UNI';
                }
            }

            if ($tingkatan == 'mk-prodi' || $tingkatan == 'mk-universitas') {
                $this->updatedProdiNameSearch($this->prodiNameSearch);
            } elseif ($tingkatan == 'mk-jurusan') {
                $this->updatedJurusanNameSearch($this->jurusanNameSearch);
            } elseif ($tingkatan == 'mk-fakultas') {
                $this->updatedFakultasNameSearch($this->fakultasNameSearch);
            }

            // 4. Munculkan Modal
            $this->showMKModal = true;

            $this->dispatch('fill-modal-mk', mk: $mk);
            $this->dispatch('refresh-component');

        } catch (\Exception $e) {
            $this->dispatch('toast', message: '❌ Data tidak ditemukan!');
        }
    }

    private function inputModalMK($isEditing, $data)
    {
        $tingkatanMap = [
            'mk-prodi' => 1, 'mk-jurusan' => 2, 'mk-fakultas' => 3, 'mk-universitas' => 4,
            1 => 1, 2 => 2, 3 => 3, 4 => 4,
        ];
        $tingkatan = $tingkatanMap[$this->mkType] ?? 1;
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

        $validator = Validator::make($data, $rules, $this->validationMessages());

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

    private function generateTingkatanMap($tingkatan) {
        $tingkatanMap = [
            'mk-prodi' => 1, 'mk-jurusan' => 2, 'mk-fakultas' => 3, 'mk-universitas' => 4,
            1 => 1, 2 => 2, 3 => 3, 4 => 4,
        ];
        return $tingkatanMap[$this->mkType] ?? 1;
    }

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
        $data['prodi_id'] = $this->prodi_id;
        $data['prodi_id_array'] = $this->prodi_id_array;

        $data['is_wajib'] = ($data['is_wajib'] !== '') ? (int) $data['is_wajib'] : 1;
        $data['tipe_sks'] = ! empty($data['tipe_sks']) ? (int) $data['tipe_sks'] : 1;
        $data['sks_kuliah'] = ! empty($data['sks_kuliah']) ? (int) $data['sks_kuliah'] : 1;

        // try {
            $validated = $this->inputModalMK(false, $data);
            $tingkatan = $this->generateTingkatanMap($this->mkType);
            $kodePrefix = $this->generateKodePrefix($data, $tingkatan);

            DB::transaction(function () use ($validated, $tingkatan, $kodePrefix, $data) {

                $mk = MataKuliah::create([
                    'tingkatan_mk' => $tingkatan,
                    'kode_mk' => $kodePrefix,
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

            $this->resetInput();
            $this->showMKModal = false;
            $this->dispatch('toast', message: '✅ Mata Kuliah berhasil ditambahkan!');
            $this->dispatch('refresh-data');

        // } catch (\Exception $e) {
        //     $this->dispatch('toast', message: '❌ Gagal: '.$e->getMessage());
        // }
    }

    public function updateMK($data)
    {
        $data['prodi_id'] = $this->prodi_id;
        $data['prodi_id_array'] = $this->prodi_id_array;

        $data['is_wajib'] = ($data['is_wajib'] !== '') ? (int) $data['is_wajib'] : 1;
        $data['tipe_sks'] = ! empty($data['tipe_sks']) ? (int) $data['tipe_sks'] : 1;
        $data['sks_kuliah'] = ! empty($data['sks_kuliah']) ? (int) $data['sks_kuliah'] : 1;

        try {
            $validated = $this->inputModalMK(true, $data);
            $tingkatan = $this->generateTingkatanMap($this->mkType);
            $kodePrefix = $this->generateKodePrefix($data, $tingkatan);

            DB::transaction(function () use ($validated, $tingkatan, $data, $kodePrefix) {
                $mk = MataKuliah::findOrFail($this->selected_id);

                // 3. UPDATE DATA UTAMA
                $mk->update([
                    'kode_mk' => $kodePrefix,
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
                    // Menjadikan index loop sebagai urutan di database
                    $syncData[$id] = ['sort_order' => $index];
                }

                $mk->prodis()->sync($syncData);
            });

            $this->showMKModal = false;
            $this->dispatch('toast', message: '✅ Mata Kuliah berhasil diperbarui!');
            $this->dispatch('refresh-data');

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('toast', message: '❌ Gagal memperbarui: '.$e->getMessage());
        }
    }

    public function validationMessages()
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

    public function resetInput()
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
        $this->prodi_name_array = [];
        $this->prodi_kode_array = [];
        
        $this->resetErrorBag();
    }
}
