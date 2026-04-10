<?php

namespace App\Livewire\Staff\RPSManagement;

use App\Livewire\Global\HasToast;
use App\Models\Akademik\CPMK;
use App\Models\Akademik\RPS;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait WithRPSModal
{
    use HasToast;

    public $selected_id;

    public $isEditing = false;

    public $showRPSModal = false;

    public function addRPS()
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }
        $this->resetInputRPS();

        $this->resetValidation();
        $this->resetErrorBag();
        $this->isEditing = false;
        $this->showRPSModal = true;

        $this->updatedMKNameSearch($this->mkNameSearch);
        $this->updatedCPMKNameSearch($this->cpmkNameSearch);
        $this->updatedCPLNameSearch($this->cplNameSearch);
        $this->updatedRefNameSearch($this->refNameSearch);
        $this->updatedDosenNameSearch($this->refNameSearch);
    }

    public function editRPS($id, $tingkatan)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }
        $this->selected_id = $id;
        $this->mkType = $tingkatan;
        $this->isEditing = true;

        $this->resetInputRPS();
        $this->prodiResults = [];

        $this->resetValidation();
        $this->resetErrorBag();

        try {
            $mk = RPS::with(['prodis', 'cpmks.scpmks', 'cpmks.scpmks.refs', 'cpmks.refs', 'cpmks.cpls'])->findOrFail($id);

            $this->pr_id_array = $mk->prodis->pluck('id')->toArray();
            $this->prodi_name_array = $mk->prodis->pluck('prodi')->toArray();
            $this->prodi_kode_array = $mk->prodis->pluck('kode')->toArray();

            $this->dispatch('refresh-component');

            $firstProdi = $mk->prodis->first();

            if ($firstProdi) {
                $this->jr_id = $firstProdi->jr_id;
                $this->jurusanNameSearch = 'Jurusan '.$firstProdi->jurusan;
                $this->jurusan_kode = $firstProdi->kode;

                $this->fk_id = $firstProdi->fk_id;
                $this->fakultasNameSearch = 'Fakultas '.$firstProdi->fakultas;
                $this->fakultas_kode = $firstProdi->kode;

                if ($tingkatan == 1 || $tingkatan == 4) {
                    $this->pr_id = $firstProdi->id;
                    $this->prodiNameSearch = $firstProdi->prodi;
                    $this->prodi_kode = $firstProdi->kode;
                }
            }

            if ($tingkatan == 1 || $tingkatan == 4) {
                $this->updatedProdiNameSearch($this->prodiNameSearch);
            } elseif ($tingkatan == 2) {
                $this->updatedJurusanNameSearch($this->jurusanNameSearch);
            } elseif ($tingkatan == 3) {
                $this->updatedFakultasNameSearch($this->fakultasNameSearch);
            }

            $this->showRPSModal = true;

            // Fill CPMK data for edit mode
            $this->cpmk_id_array = $mk->cpmks->pluck('id')->toArray();
            $this->cpmk_name_array = $mk->cpmks->pluck('deskripsi')->toArray();
            $this->cpmk_kode_array = $mk->cpmks->pluck('kode')->toArray();

            $allSelected = $mk->cpmks->load(['sub_cpmks', 'scpmks.refs', 'refs', 'cpls']);
            $this->items = $this->mapCPMK($allSelected);

            $this->dispatch('fill-modal-mk', mk: $mk);
            $this->dispatch('refresh-component');

        } catch (\Exception $e) {
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

private function inputModalRPS($isEditing, $data)
{
    // 1. Ambil semua CPL & Ref yang sudah ada di level CPMK yang dipilih
    $cplFromCpmk = [];
    $refFromCpmkScpmk = [];
    $cpmks = [];

    if (! empty($data['cpmk_id_array'])) {
        // Gunakan eager loading untuk efisiensi
        $cpmks = \App\Models\Akademik\CPMK::with(['cpls', 'refs'])->whereIn('id', $data['cpmk_id_array'])->get();
        foreach ($cpmks as $cpmk) {
            $cplFromCpmk = array_merge($cplFromCpmk, $cpmk->cpls?->pluck('id')->toArray() ?? []);
            $refFromCpmkScpmk = array_merge($refFromCpmkScpmk, $cpmk->refs?->pluck('id')->toArray() ?? []);
        }
    }

    // 2. Tambahkan Ref yang berasal dari Sub-CPMK (isi JSON)
    if (! empty($data['cpmk_sub_items_array'])) {
        foreach ($data['cpmk_sub_items_array'] as $group) {
            foreach ($group['scpmk'] ?? [] as $scpmk) {
                if (! empty($scpmk['ref_ids'])) {
                    $refFromCpmkScpmk = array_merge($refFromCpmkScpmk, (array) $scpmk['ref_ids']);
                }
            }
        }
    }

    // --- PROSES PEMBERSIHAN (SINKRONISASI) ---
    // Jika ID sudah ada di CPMK/Sub-CPMK, hapus dari input manual agar tidak duplikat di DB
    if (isset($data['cpl_id_array']) && is_array($data['cpl_id_array'])) {
        $data['cpl_id_array'] = array_values(array_diff(array_unique($data['cpl_id_array']), $cplFromCpmk));
    }

    if (isset($data['ref_id_array']) && is_array($data['ref_id_array'])) {
        $data['ref_id_array'] = array_values(array_diff(array_unique($data['ref_id_array']), $refFromCpmkScpmk));
    }

    // --- RULES VALIDASI ---
    $rules = [
        'deskripsi' => 'required|string|max:1000',
        'mk_id' => 'required|exists:mata_kuliahs,id',
        'tahun_akademik' => [
            'required', 'string', 'regex:/^\d{4}\/\d{4}$/',
            function ($attribute, $value, $fail) use ($data, $isEditing) {
                $query = DB::table('rps')->where('mk_id', $data['mk_id'])->where('tahun_akademik', $value);
                if ($isEditing) {
                    $query->where('id', '!=', $this->selected_id);
                }
                if ($query->exists()) {
                    $fail("RPS untuk Mata Kuliah ini pada tahun akademik $value sudah ada.");
                }
            },
        ],
        'tahun_akademik_1' => 'required|integer|min:1970',
        'tahun_akademik_2' => 'required|integer|min:1971',
        'is_draf' => ['required', 'boolean', function ($attribute, $value, $fail) use ($data) {
            $totalSubCPMK = 0;
            $totalBobot = 0;
            if (! empty($data['cpmk_sub_items_array'])) {
                foreach ($data['cpmk_sub_items_array'] as $group) {
                    foreach ($group['scpmk'] ?? [] as $scpmk) {
                        $totalSubCPMK++;
                        $totalBobot += (float) ($scpmk['bobot'] ?? 0);
                    }
                }
            }
            if ($value == 0) {
                if ($totalSubCPMK < 14) {
                    $fail('Jumlah Sub-CPMK kurang dari 14.');
                }
                $rounded = round($totalBobot, 2);
                if ($rounded < 80 || $rounded > 140) {
                    $fail("Total bobot harus 80% - 140% (Saat ini: $rounded%).");
                }
            }
        }],
        'cpmk_id_array' => 'required|array|min:1',
        'cpl_id_array' => 'nullable|array',
        'ref_id_array' => 'nullable|array',
        'dosen_id_array' => 'required|array|min:1',
        'dosen_items_array' => 'required|array|min:1',
        'dosen_items_array.*.peran' => 'required|in:Koordinator,Pengajar,Asisten',
    ];

    $validator = Validator::make($data, $rules, $this->validationMessagesRPS());

    if ($validator->fails()) {
        // Custom handling untuk error tahun akademik gabungan
        $pesanFormatSama = 'Format Tahun Akademik tidak valid (contoh: 2025/2026)!';
        $isThnEmpty = empty($data['tahun_akademik']) && empty($data['tahun_akademik_1']) && empty($data['tahun_akademik_2']);
        
        foreach ($validator->errors()->toArray() as $key => $messages) {
            if (in_array($key, ['tahun_akademik', 'tahun_akademik_1', 'tahun_akademik_2'])) {
                if (! $this->getErrorBag()->has('tahun_akademik')) {
                    $this->addError('tahun_akademik', $isThnEmpty ? 'Tahun Akademik wajib diisi!' : $pesanFormatSama);
                }
            } else {
                foreach ($messages as $message) {
                    $this->addError($key, $message);
                }
            }
        }
        $validator->validate();
    }

    // --- RETURN DATA ---
    $validated = $validator->validated();
    
    // Gabungkan data yang sudah dibersihkan agar tersedia di function saveRPS
    $validated['cpl_id_array'] = $data['cpl_id_array'] ?? [];
    $validated['ref_id_array'] = $data['ref_id_array'] ?? [];
    $validated['cpmk_id_array'] = array_values(array_unique($data['cpmk_id_array']));
    $validated['dosen_items_array'] = $data['dosen_items_array'] ?? [];
    $validated['cpmk_sub_items_array'] = $data['cpmk_sub_items_array'] ?? [];
    $validated['dosen_id_array'] = $data['dosen_id_array'] ?? [];

    return $validated;
}

    private function normalizeNama($value)
    {
        return ucwords(strtolower(trim($value)));
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

public function saveRPS($data)
{
    if (! $this->AuthCheck('staff')) {
        return;
    }

    // 1. Sinkronisasi awal data dari state Livewire ke parameter
    $data['mk_id'] = $this->mk_id;
    $data['is_draf'] = ($data['is_draf'] !== '') ? (int) $data['is_draf'] : 1;
    $data['cpmk_id_array'] = $this->cpmk_id_array ?? [];
    $data['cpmk_sub_items_array'] = $this->cpmk_sub_items_array ?? [];
    $data['cpl_id_array'] = $this->cpl_id_array ?? [];
    $data['ref_id_array'] = $this->ref_id_array ?? [];
    $data['dosen_id_array'] = $this->dosen_id_array ?? [];
    $data['dosen_items_array'] = $this->dosen_items_array ?? [];

    try {
        // 2. Jalankan validasi dan pembersihan duplikat
        $validated = $this->inputModalRPS(false, $data);

        // 3. Eksekusi Database
DB::transaction(function () use ($validated) {
    // 1. Buat Header RPS
    $rps = RPS::create([
        'deskripsi'      => $validated['deskripsi'],
        'mk_id'          => $validated['mk_id'],
        'tahun_akademik' => $validated['tahun_akademik'],
        'tahun_awal'     => $validated['tahun_akademik_1'],
        'tahun_akhir'    => $validated['tahun_akademik_2'],
        'is_draf'        => $validated['is_draf'],
        'isi_rps_json'   => json_encode($validated['cpmk_sub_items_array']),
    ]);

    // DEBUG: Jika rps ID null, transaksi akan gagal di sini
    if (!$rps->id) {
        throw new \Exception("Gagal mendapatkan ID RPS yang baru dibuat.");
    }

    // 2. Sinkronisasi Dosen
    $dosenSync = [];
    foreach ($validated['dosen_items_array'] as $index => $item) {
        $dosenSync[$item['id']] = [
            'peran'      => $item['peran'] ?: 'Pengajar',
            'is_ketua'   => $item['is_ketua'] ?? false,
            'sort_order' => $index,
        ];
    }
    // Gunakan sync() bukannya attach() untuk keamanan data
    $rps->dosens()->sync($dosenSync);

    // 3. Sinkronisasi CPMK
    if (!empty($validated['cpmk_id_array'])) {
        $rps->cpmks()->sync($validated['cpmk_id_array']);
    }

    // 4. Sinkronisasi CPL (ID Manual)
    if (!empty($validated['cpl_id_array'])) {
        $rps->cpls()->sync($validated['cpl_id_array']);
    }

    // 5. Sinkronisasi Referensi (ID Manual)
    if (!empty($validated['ref_id_array'])) {
        $rps->refs()->sync($validated['ref_id_array']);
    }
});

        // 4. Feedback & Reset
        $namaMK = $this->mk_items['nama'] ?? $this->mk_name;
        $this->toast(message: "RPS $namaMK ({$validated['tahun_akademik']}) berhasil disimpan.");

        $this->resetInputRPS();
        $this->dispatch('refresh-data');
        $this->showRPSModal = false;

    } catch (ValidationException $e) {
        $this->toast(text: 'Validasi Gagal: ' . collect($e->errors())->first()[0], variant: 'danger');
        throw $e;
    } catch (\Exception $e) {
        $this->toast(text: 'Error: ' . $e->getMessage(), variant: 'danger');
        $this->dispatch('refresh-data');
        $this->showRPSModal = false;
    }
}

    public function updateRPS($data)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }
        $data['pr_id'] = $this->pr_id;
        $data['pr_id_array'] = $this->pr_id_array;

        $data['is_wajib'] = ($data['is_wajib'] !== '') ? (int) $data['is_wajib'] : 1;
        $data['tipe_sks'] = ! empty($data['tipe_sks']) ? (int) $data['tipe_sks'] : 1;
        $data['sks_kuliah'] = ! empty($data['sks_kuliah']) ? (int) $data['sks_kuliah'] : 1;

        try {
            $validated = $this->inputModalRPS(true, $data);
            $tingkatan = $this->mkType;
            $kodePrefix = $this->generateKodePrefix($data, $tingkatan);

            DB::transaction(function () use ($validated, $tingkatan, $data) {
                $mk = RPS::findOrFail($this->selected_id);

                // 3. UPDATE DATA UTAMA
                $mk->update([
                    // 'kode_mk' => $kodePrefix,
                    'digit_semester' => $validated['digit_semester'],
                    'digit_mk' => $validated['digit_mk'],
                    'nama_mk' => $this->normalizeNama($validated['nama_mk']),
                    'semester' => $validated['semester'],
                    'sks_kuliah' => $validated['sks_kuliah'],
                    'tipe_sks' => $validated['tipe_sks'],
                    'is_wajib' => $validated['is_wajib'],
                    'bahan_kajian' => $data['bahan_kajian'] ?? null,
                    'deskripsi' => $data['deskripsi'] ?? null,
                ]);

                // 4. LOGIKA TARGET IDs
                $targetIds = ($tingkatan === 1)
                            ? [$this->pr_id]
                            : ($this->pr_id_array ?: []);

                $cleanIds = array_values(array_filter($targetIds));

                // 5. SINKRONISASI RELASI PIVOT DENGAN SORT ORDER
                $syncData = [];
                foreach ($cleanIds as $index => $id) {
                    $syncData[$id] = ['sort_order' => $index];
                }

                $mk->prodis()->sync($syncData);

                // Sync selected CPMKs
                $mk->cpmks()->sync($this->cpmk_id_array ?: []);
            });

            $this->dispatch('refresh-data');
            $this->showRPSModal = false;
            $this->toast(message: 'Mata Kuliah '.$this->normalizeNama($validated['nama_mk']), type: 'update');

        } catch (ValidationException $e) {
            $this->toast(text: $e->getMessage(), variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: $e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data');
            $this->showRPSModal = false;
        }
    }

    private function validationMessagesRPS()
    {
        return [
            // Relasi Mata Kuliah & Prodi
            'mk_id.required' => 'Mata Kuliah asal wajib dipilih!',
            'mk_id.exists' => 'Mata Kuliah yang dipilih tidak valid!',
            'pr_id.required' => 'Program Studi wajib diisi!',
            'pr_id_array.required' => 'Program Studi wajib diisi!',
            'pr_id_array.min' => 'Pilih minimal satu Program Studi!',

            // Tahun Akademik
            'tahun_akademik.required' => 'Tahun Akademik wajib diisi!',
            'tahun_akademik.regex' => 'Format Tahun Akademik tidak valid (contoh: 2025/2026)!',
            'tahun_akademik_1.required' => 'Tahun awal (input kiri) wajib diisi!',
            'tahun_akademik_1.min' => 'Tahun awal minimal adalah 1970!',
            'tahun_akademik_2.required' => 'Tahun akhir (input kanan) wajib diisi!',
            'tahun_akademik_2.min' => 'Tahun akhir minimal adalah 1971!',

            // Deskripsi & Status
            'deskripsi.required' => 'Deskripsi RPS wajib diisi!',
            'deskripsi.max' => 'Deskripsi RPS tidak boleh lebih dari 1000 karakter!',
            'is_draf.required' => 'Status RPS wajib ditentukan!',
            'is_draf.boolean' => 'Format status draf tidak valid!',

            // CPMK & Relasi Data
            'cpmk_id_array.required' => 'Minimal pilih satu CPMK untuk RPS ini!',
            'cpmk_id_array.array' => 'Format data CPMK tidak valid!',
            'cpmk_id_array.min' => 'Minimal harus ada satu CPMK yang dipilih!',

            // Dosen Pengampu
            'dosen_id_array.required' => 'Dosen pengampu wajib dipilih!',
            'dosen_id_array.min' => 'Minimal harus ada satu dosen pengampu!',
            'dosen_items_array.required' => 'Data detail dosen tidak boleh kosong!',
            'dosen_items_array.*.peran.required' => 'Peran dosen (Koordinator/Pengajar/Asisten) wajib dipilih!',
            'dosen_items_array.*.peran.in' => 'Peran dosen hanya boleh: Koordinator, Pengajar, atau Asisten!',
            'dosen_id_array.required' => 'Dosen pengampu wajib diisi!',
            'dosen_items_array.*.peran.required' => 'Peran dosen harus dipilih!',

            // Form Mata Kuliah (Legacy/Template)
            'nama_mk.required' => 'Nama Mata Kuliah wajib diisi!',
            'deskripsi.max' => 'Deskripsi RPS terlalu panjang (Maksimal 1000 karakter)!',
            'semester.required' => 'Semester wajib diisi!',
            'semester.integer' => 'Semester harus berupa angka!',
            'semester.min' => 'Semester minimal adalah 1!',
            'semester.max' => 'Semester maksimal adalah 8!',
            'digit_semester.required' => 'Digit Semester wajib diisi!',
            'digit_semester.size' => 'Digit Semester harus tepat 2 karakter (contoh: 01)!',
            'digit_mk.required' => 'Digit MK wajib diisi!',
            'digit_mk.size' => 'Digit MK harus tepat 2 karakter (contoh: 07)!',
            'sks_kuliah.required' => 'SKS Mata Kuliah wajib diisi!',
            'sks_kuliah.integer' => 'SKS harus berupa angka!',
            'sks_kuliah.min' => 'SKS minimal adalah 1!',
            'tipe_sks.required' => 'Tipe SKS wajib dipilih!',
            'is_wajib.required' => 'Status kewajiban Mata Kuliah wajib ditentukan!',
        ];
    }

    private function resetInputRPS()
    {
        $this->mkNameSearch = '';
        $this->cpmkNameSearch = '';
        $this->cplNameSearch = '';
        $this->refNameSearch = '';
        $this->refNameSearch = '';

        // ambil id untuk simpan ke rps_pivot_cpmk
        $this->cpmk_id_array = [];
        $this->cpmk_items_array = [];
        $this->cpmk_sub_items_array = [];

        // ambil id untuk simpan ke rps_pivot_cpl
        $this->cpl_id_array = [];
        $this->cpl_items_array = [];

        // ambil id untuk simpan ke rps_pivot_ref
        $this->ref_id_array = [];
        $this->ref_items_array = [];

        // ambil id, dosen_items_array.peran, dosen_items_array.is_ketua untuk simpan ke rps_pivot_dosen
        $this->dosen_id_array = [];
        $this->dosen_items_array = [];

        $this->resetErrorBag();
    }
}
