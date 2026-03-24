<?php

namespace App\Livewire\Admin\MatkulManagement;

use App\Models\Fakultas;
use App\Models\Jurusan;
use App\Models\MataKuliah;
use App\Models\Prodi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

trait WithMatkulModal
{
    public $selected_id;

    public $isEditing = false;

    public $showMKModal = false;

    public $prodi_id_2;

    public $prodi_id_array_2 = [];

    // public $prodi_id_array = [];
    public $prodi_names = [];

    public $prodi_kodes = [];
    // public $prodi_id, $prodi_name_search;
    // public $jurusan_id, $jurusan_name_search;
    // public $fakultas_id, $fakultas_name_search;

    protected $matkuls = [
        'nama_prodi' => 'required|string|max:255|unique:prodis,nama_prodi',
        'jurusan_id' => 'required|exists:jurusans,id',
        'nama_jurusan' => 'required|string|max:255|unique:jurusans,nama_jurusan',
        'fakultas_id' => 'required|exists:fakultas,id',
        'nama_fakultas' => 'required|string|max:255|unique:fakultas,nama_fakultas',
    ]; // Ubah ini jadi validasi punya Mata Kuliah

    public function addMK($mk)
    {
        if ($this->isEditing) {
            $this->resetInput();
        }
        if ($mk !== 'mk-prodi') {
            $this->resetInputProdi();
        }

        $this->resetValidation();
        $this->resetErrorBag();
        $this->isEditing = false;
        $this->mkType = $mk;
        $this->showMKModal = true;

        if ($mk == 'mk-prodi' || $mk == 'mk-universitas') {
            $this->updatedProdiNameSearch($this->prodi_name_search);
        } elseif ($mk == 'mk-jurusan') {
            $this->updatedJurusanNameSearch($this->jurusan_name_search);
        } elseif ($mk == 'mk-fakultas') {
            $this->updatedFakultasNameSearch($this->fakultas_name_search);
        }
    }

    public function editMK($id, $tingkatan)
    {
        $this->selected_id = $id;
        $this->mkType = $tingkatan;
        $this->isEditing = true;

        $this->resetValidation();
        $this->resetErrorBag();

        try {
            // Load relasi mendalam untuk mendapatkan nama Jurusan/Fakultas
            // ... di dalam try editMK ...
            $mk = MataKuliah::with(['prodis.jurusan.fakultas'])->findOrFail($id);

            // SINKRONISASI ARRAY SECARA EKSPLISIT
            $this->prodi_id_array = collect($mk->prodis)->pluck('id')->map(fn ($id) => (string) $id)->toArray();
            $this->prodi_names = collect($mk->prodis)->pluck('nama_prodi')->toArray();
            $this->prodi_kodes = collect($mk->prodis)->map(function ($p) {
                return $p->kode_prodi ?? $p->kode_pr ?? '???';
            })->toArray();

            // Referensi Induk
            $firstProdi = $mk->prodis->first();
            if ($firstProdi) {
                $this->prodi_id = $firstProdi->id;
                $this->prodi_name_search = $firstProdi->nama_prodi;
                $this->jurusan_id = $firstProdi->jurusan_id;
                $this->jurusan_name_search = $firstProdi->jurusan->nama_jurusan ?? '';
                $this->fakultas_id = $firstProdi->jurusan->fakultas_id ?? null;
                $this->fakultas_name_search = $firstProdi->jurusan->fakultas->nama_fakultas ?? '';
            }

            $this->showMKModal = true;

            dd($this->prodi_id_array);

            // Kirim event khusus untuk memaksa Alpine me-refresh data
            $this->dispatch('update-alpine-arrays', [
                'ids' => $this->prodi_id_array,
                'names' => $this->prodi_names,
                'kodes' => $this->prodi_kodes,
            ]);

            // Pemicu Alpine untuk re-render array
            $this->dispatch('refresh-component');
            $this->dispatch('fill-modal-mk', mk: $mk);

        } catch (\Exception $e) {
            $this->dispatch('toast', message: '❌ Data tidak ditemukan!');
        }
    }

    private function inputModalMK($isEditing, $data)
    {
        $tingkatanMap = [
            'mk-prodi' => 0, 'mk-jurusan' => 1, 'mk-fakultas' => 2, 'mk-universitas' => 3,
        ];
        $tingkatan = $tingkatanMap[$this->mkType] ?? 0;
        $targetProdiIds = ($tingkatan === 0) ? [$this->prodi_id] : ($this->prodi_id_array ?: []);

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

                            // Opsional: Jika ingin berhenti di error pertama saja
                            break;
                        }
                    }
                },
            ],
            'sks_kuliah' => 'required|integer|min:1',
            'tipe_sks' => 'required|in:0,1,2,3',
            'is_wajib' => 'required|boolean',
        ];

        // Tambahkan validasi prodi_id untuk tingkatan prodi
        if ($tingkatan === 0) {
            $rules['prodi_id'] = 'required|exists:prodis,id';
        } else {
            $rules['prodi_id_array'] = 'required|array|min:1';
        }

        // Buat validator
        $validator = Validator::make($data, $rules, $this->validationMessages());

        if ($validator->fails()) {
            // MEKANISME PENTING: Pindahkan error array manual ke ErrorBag Livewire
            foreach ($validator->errors()->toArray() as $key => $messages) {
                foreach ($messages as $message) {
                    $this->addError($key, $message);
                }
            }
            // Lempar exception agar eksekusi saveMK terhenti
            $validator->validate();
        }

        return $validator->validated();
    }

    private function normalizeNama($value)
    {
        return ucwords(strtolower(trim($value)));
    }

    private function generateKodePrefix($data, $tingkatan)
    {
        if ($tingkatan === 3) {
            return 'UNI';
        }

        if ($tingkatan === 0) { // Prodi
            return $data['selected_kode_pr'] ?? $data['kode_jr'] ?? $data['kode_fk'] ?? 'UNI';
        } elseif ($tingkatan === 1) { // Jurusan
            return $data['kode_jr'] ?? $data['kode_fk'] ?? 'UNI';
        } elseif ($tingkatan === 2) { // Fakultas
            return $data['kode_fk'] ?? 'UNI';
        }

        return 'UNI';
    }

    public function saveMK($data)
    {
        // Sinkronisasi data penting ke array $data sebelum divalidasi
        $data['prodi_id'] = $this->prodi_id;
        $data['prodi_id_array'] = $this->prodi_id_array;

        $data['is_wajib'] = ($data['is_wajib'] !== '') ? (int) $data['is_wajib'] : 1;
        $data['tipe_sks'] = ! empty($data['tipe_sks']) ? (int) $data['tipe_sks'] : 1;
        $data['sks_kuliah'] = ! empty($data['sks_kuliah']) ? (int) $data['sks_kuliah'] : 1;

        try {
            $validated = $this->inputModalMK(false, $data);

            $tingkatanMap = ['mk-prodi' => 1, 'mk-jurusan' => 2, 'mk-fakultas' => 3, 'mk-universitas' => 4];
            $tingkatan = $tingkatanMap[$this->mkType] ?? 1;
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

        } catch (\Exception $e) {
            $this->dispatch('toast', message: '❌ Gagal: '.$e->getMessage());
        }
    }

    public function updateMK($data)
    {
        // 1. SINKRONISASI DATA SEBELUM VALIDASI (Penting!)
        $data['prodi_id'] = $this->prodi_id;
        $data['prodi_id_array'] = $this->prodi_id_array;

        // Konversi tipe data manual (karena data dari form seringkali berupa string)
        $data['is_wajib'] = ($data['is_wajib'] !== '') ? (int) $data['is_wajib'] : 1;
        $data['tipe_sks'] = ! empty($data['tipe_sks']) ? (int) $data['tipe_sks'] : 1;
        $data['sks_kuliah'] = ! empty($data['sks_kuliah']) ? (int) $data['sks_kuliah'] : 1;

        try {
            // 2. VALIDASI (Sekarang $data sudah punya prodi_id_array)
            $validated = $this->inputModalMK(true, $data);

            DB::transaction(function () use ($validated, $data) {
                $mk = MataKuliah::findOrFail($this->selected_id);

                // 3. UPDATE DATA UTAMA
                $mk->update([
                    'nama_matkul' => $this->normalizeNama($validated['nama_matkul']),
                    'digit_semester' => $validated['digit_semester'],
                    'digit_mk' => $validated['digit_mk'],
                    'semester' => $validated['semester'],
                    'sks_kuliah' => $validated['sks_kuliah'],
                    'tipe_sks' => $validated['tipe_sks'],
                    'is_wajib' => $validated['is_wajib'],
                    'bahan_kajian' => $data['bahan_kajian'] ?? null,
                    'deskripsi' => $data['deskripsi'] ?? null,
                ]);

                // 4. UPDATE KODE MK (Opsional, jika ingin kode berubah saat induk berubah)
                // $tingkatanMap = ['mk-prodi' => 1, 'mk-jurusan' => 2, 'mk-fakultas' => 3, 'mk-universitas' => 4];
                // $mk->update(['kode_mk' => $this->generateKodePrefix($data, $tingkatanMap[$this->mkType])]);

                // 5. SINKRONISASI RELASI PIVOT
                $targetIds = ($this->mkType === 'mk-prodi')
                            ? [$this->prodi_id]
                            : ($this->prodi_id_array ?: []);

                $mk->prodis()->sync(array_filter($targetIds));
            });

            $this->showMKModal = false;
            $this->dispatch('toast', message: '✅ Mata Kuliah berhasil diperbarui!');
            $this->dispatch('refresh-data');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Biarkan Livewire menangani error validasi secara otomatis
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('toast', message: '❌ Gagal memperbarui: '.$e->getMessage());
        }
    }

    public function validationMessages()
    {
        return [
            // Nama Mata Kuliah
            'nama_matkul.required' => 'Nama Mata Kuliah wajib diisi.',
            'nama_matkul.string' => 'Nama Mata Kuliah harus berupa teks.',
            'nama_matkul.max' => 'Nama Mata Kuliah tidak boleh lebih dari 255 karakter.',

            // Semester (Integer)
            'semester.required' => 'Semester wajib diisi.',
            'semester.integer' => 'Semester harus berupa angka.',
            'semester.min' => 'Semester minimal adalah 1.',
            'semester.max' => 'Semester maksimal adalah 8.',

            // Digit Semester & Digit MK (String size 2)
            'digit_semester.required' => 'Digit Semester wajib diisi.',
            'digit_semester.size' => 'Digit Semester harus tepat 2 karakter (contoh: 01).',

            'digit_mk.required' => 'Digit MK wajib diisi.',
            'digit_mk.size' => 'Digit MK harus tepat 2 karakter (contoh: 07).',

            // SKS
            'sks_kuliah.required' => 'SKS Mata Kuliah wajib diisi.',
            'sks_kuliah.integer' => 'SKS harus berupa angka.',
            'sks_kuliah.min' => 'SKS minimal adalah 1.',

            // Tipe SKS & Status Wajib
            'tipe_sks.required' => 'Tipe SKS wajib dipilih.',
            'tipe_sks.in' => 'Tipe SKS yang dipilih tidak valid.',
            'is_wajib.required' => 'Status kewajiban Mata Kuliah wajib ditentukan.',
            'is_wajib.boolean' => 'Format status wajib tidak valid.',
        ];
    }

    public function resetInput()
    {
        $this->selected_id = null;
        $this->prodi_id = null;
        $this->prodi_id_array = [];
        $this->resetErrorBag();
    }

    public function resetInputProdi()
    {
        $this->prodi_id = null;
        $this->prodi_name_search = '';
        $this->resetErrorBag();
    }
}
