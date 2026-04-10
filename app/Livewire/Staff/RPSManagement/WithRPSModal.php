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

    public function editRPS($id)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $this->resetInputRPS();
        $this->resetValidation();
        $this->resetErrorBag();

        $this->selected_id = $id;
        $this->isEditing = true;

        try {
            // 1. Load data RPS dengan relasi yang sangat lengkap
            $rps = RPS::with([
                'mk_rel',
                'dosens',
                'cpmks.scpmks.refs', // Penting untuk mapping Sub-CPMK
                'cpmks.refs',        // Referensi Utama CPMK
                'cpmks.cpls',        // CPL dari CPMK
                'cpls',              // CPL manual di tingkat RPS
                'refs',              // Referensi manual di tingkat RPS
            ])->findOrFail($id);

            $this->mkNameSearch = $rps->mk_rel?->mk;
            $this->mk_id = $rps->mk_id;
            $this->mk_items = $this->itemsMK($rps->mk_rel);

            // 2. Fill Data Dosen
            $this->dosen_id_array = $rps->dosens->pluck('id')->toArray();
            $this->dosen_items_array = $rps->dosens->map(function ($d) {
                return $this->itemsDosen($d);
            })->toArray();

            // 3. MAPPING CPMK (MENGGUNAKAN FUNGSI mapCPMK ANDA)
            $this->cpmk_id_array = $rps->cpmks->pluck('id')->toArray();
            $this->cpmk_items_array = $rps->cpmks->map(function ($c) {
                return $this->itemsCPMK($c);
            })->toArray();
            $this->cpmk_sub_items_array = $this->mapCPMK($rps->cpmks);

            // $totalSubCPMK = 0;
            // foreach ($this->cpmk_sub_items_array as $group) {
            //     $totalSubCPMK += count($group['scpmk'] ?? []);
            // }
            // $this->is_draf = ($totalSubCPMK < 14) ? 1 : (int) $rps->is_draf;

            // 2. Fill Data CPL & Referensi Tambahan (Manual)
            $this->cpl_id_array = $rps->cpls->pluck('id')->toArray();
            $this->cpl_items_array = $rps->cpls->map(function ($c) {
                return $this->itemsCPL($c);
            })->toArray();

            $this->ref_id_array = $rps->refs->pluck('id')->toArray();
            $this->ref_items_array = $rps->refs->map(function ($r) {
                return $this->itemsRef($r);
            })->toArray();

            $this->fetchMK($this->mkNameSearch);
            $this->updatedCPMKNameSearch($this->cpmkNameSearch);
            $this->updatedCPLNameSearch($this->cplNameSearch);
            $this->updatedRefNameSearch($this->refNameSearch);
            $this->updatedDosenNameSearch($this->refNameSearch);

            $this->showRPSModal = true;

            // Dispatch ke AlpineJS
            $this->dispatch('fill-modal-rps', rps: $rps);
            $this->dispatch('refresh-component');

        } catch (\Exception $e) {
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    private function inputModalRPS($isEditing, $data)
    {
        // 1. Ambil data dari CPMK terpilih
        $cplFromCpmk = [];
        $refFromCpmkScpmk = [];

        if (! empty($data['cpmk_id_array'])) {
            $cpmks = CPMK::with(['cpls', 'refs'])->whereIn('id', $data['cpmk_id_array'])->get();
            foreach ($cpmks as $cpmk) {
                $cplFromCpmk = array_merge($cplFromCpmk, $cpmk->cpls?->pluck('id')->toArray() ?? []);
                $refFromCpmkScpmk = array_merge($refFromCpmkScpmk, $cpmk->refs?->pluck('id')->toArray() ?? []);
            }
        }

        // 2. Tambahkan Ref dari Sub-CPMK JSON
        if (! empty($data['cpmk_sub_items_array'])) {
            foreach ($data['cpmk_sub_items_array'] as $group) {
                foreach ($group['scpmk'] ?? [] as $scpmk) {
                    if (! empty($scpmk['ref_ids'])) {
                        $refFromCpmkScpmk = array_merge($refFromCpmkScpmk, (array) $scpmk['ref_ids']);
                    }
                }
            }
        }

        // --- PROSES PEMBERSIHAN ---
        $cleanCpl = [];
        if (isset($data['cpl_id_array']) && is_array($data['cpl_id_array'])) {
            $cleanCpl = array_values(array_diff(array_unique($data['cpl_id_array']), $cplFromCpmk));
        }

        $cleanRef = [];
        if (isset($data['ref_id_array']) && is_array($data['ref_id_array'])) {
            $cleanRef = array_values(array_diff(array_unique($data['ref_id_array']), $refFromCpmkScpmk));
        }

        // --- RULES VALIDASI ---
        $rules = [
            'deskripsi' => 'required|string|max:1000',
            'mk_id' => 'required|exists:mata_kuliahs,id',
            'akademik' => [
                'required', 'string', 'regex:/^\d{4}\/\d{4}$/',
                function ($attribute, $value, $fail) use ($data, $isEditing) {
                    $query = DB::table('rps')->where('mk_id', $data['mk_id'])->where('akademik', $value);
                    if ($isEditing) {
                        $query->where('id', '!=', $this->selected_id);
                    }
                    if ($query->exists()) {
                        $fail("RPS untuk Mata Kuliah ini pada tahun akademik $value sudah ada.");
                    }
                },
            ],
            'akademik_1' => 'required|integer|min:1970',
            'akademik_2' => 'required|integer|min:1971',
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
            'dosen_items_array' => [
                'required',
                'array',
                'min:1',
                function ($attribute, $value, $fail) {
                    // Cek apakah ada minimal satu dosen yang 'is_ketua' bernilai true
                    $hasKetua = collect($value)->contains(function ($item) {
                        // Pastikan mengecek nilai boolean atau truthy dari is_ketua
                        return isset($item['is_ketua']) && ($item['is_ketua'] === true || $item['is_ketua'] === 1 || $item['is_ketua'] === 'true');
                    });

                    if (! $hasKetua) {
                        $fail('Harus ada minimal satu dosen yang dipilih sebagai Ketua Tim.');
                    }
                },
            ],
            'dosen_items_array.*.peran' => 'required|in:Koordinator,Pengajar,Asisten',
        ];

        $validator = Validator::make($data, $rules, $this->validationMessagesRPS());

        if ($validator->fails()) {
            $pesanFormatSama = 'Format Tahun Akademik tidak valid (contoh: 2025/2026)!';
            $isThnEmpty = empty($data['akademik']) && empty($data['akademik_1']) && empty($data['akademik_2']);

            foreach ($validator->errors()->toArray() as $key => $messages) {
                if (in_array($key, ['akademik', 'akademik_1', 'akademik_2'])) {
                    if (! $this->getErrorBag()->has('akademik')) {
                        $this->addError('akademik', $isThnEmpty ? 'Tahun Akademik wajib diisi!' : $pesanFormatSama);
                    }
                } else {
                    foreach ($messages as $message) {
                        $this->addError($key, $message);
                    }
                }
            }
            $validator->validate();
        }

        $validated = $validator->validated();

        $validated['cpl_id_array'] = $cleanCpl;
        $validated['ref_id_array'] = $cleanRef;
        $validated['cpmk_id_array'] = array_values(array_unique($data['cpmk_id_array'] ?? []));
        $validated['dosen_items_array'] = $data['dosen_items_array'] ?? [];
        $validated['cpmk_sub_items_array'] = $data['cpmk_sub_items_array'] ?? [];
        $validated['dosen_id_array'] = $data['dosen_id_array'] ?? [];

        return $validated;
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
                $rps = RPS::create([
                    'deskripsi' => $validated['deskripsi'],
                    'mk_id' => $validated['mk_id'],
                    'akademik' => $validated['akademik'],
                    'tahun_awal' => $validated['akademik_1'],
                    'tahun_akhir' => $validated['akademik_2'],
                    'is_draf' => $validated['is_draf'],
                    // 'isi_rps_json' => json_encode($validated['cpmk_sub_items_array'] ?? []),
                ]);

                $rps->refresh();

                // 1. Sync Dosen
                if (! empty($validated['dosen_id_array'])) {
                    $syncDosen = [];
                    foreach ($validated['dosen_id_array'] as $index => $id) {
                        $detail = collect($validated['dosen_items_array'])->firstWhere('id', $id);
                        $syncDosen[(int) $id] = [
                            'peran' => $detail['peran'] ?? 'Pengajar',
                            'is_ketua' => (bool) ($detail['is_ketua'] ?? false),
                            'sort_order' => $index,
                        ];
                    }
                    $rps->dosens()->sync($syncDosen);
                }

                // 3. Mapping CPMK
                if (! empty($validated['cpmk_id_array'])) {
                    $cpmkSync = [];
                    foreach ($validated['cpmk_id_array'] as $index => $id) {
                        if (! empty($id)) {
                            $cpmkSync[(int) $id] = [
                                'sort_order' => $index,
                            ];
                        }
                    }
                    $rps->cpmks()->sync($cpmkSync);
                }

                // 4. Mapping CPL (ID Baru/Manual)
                if (! empty($validated['cpl_id_array'])) {
                    $cplSync = [];
                    foreach ($validated['cpl_id_array'] as $index => $id) {
                        if (! empty($id)) {
                            $cplSync[(int) $id] = [
                                'sort_order' => $index,
                            ];
                        }
                    }
                    $rps->cpls()->sync($cplSync);
                }

                // 5. Mapping Referensi (ID Baru/Manual)
                if (! empty($validated['ref_id_array'])) {
                    $refSync = [];
                    foreach ($validated['ref_id_array'] as $index => $id) {
                        if (! empty($id)) {
                            $refSync[(int) $id] = [
                                'sort_order' => $index,
                            ];
                        }
                    }
                    $rps->refs()->sync($refSync);
                }
            });
            // 4. Feedback & Reset
            $kodeMK = $this->mk_items['kode'];
            $kodeRPS = $data['digit_akademik'];
            $namaMK = $this->mk_items['name'];
            $this->toast(message: "RPS $kodeMK-$kodeRPS $namaMK ({$validated['akademik']})");
            $this->resetInputRPS();
            $this->dispatch('refresh-data');
            $this->showRPSModal = false;

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Error: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data');
            $this->showRPSModal = false;
        }
    }

    public function updateRPS($data)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        // Sinkronkan state Livewire ke array data sebelum validasi
        $data['mk_id'] = $this->mk_id;
        $data['cpmk_id_array'] = $this->cpmk_id_array ?? [];
        $data['dosen_id_array'] = $this->dosen_id_array ?? [];
        $data['dosen_items_array'] = $this->dosen_items_array ?? [];
        $data['cpmk_sub_items_array'] = $this->cpmk_sub_items_array ?? [];
        $data['cpl_id_array'] = $this->cpl_id_array ?? [];
        $data['ref_id_array'] = $this->ref_id_array ?? [];

        try {
            $validated = $this->inputModalRPS(true, $data);

            DB::transaction(function () use ($validated) {
                $rps = RPS::findOrFail($this->selected_id);

                // 1. Update Data Utama
                $rps->update([
                    'deskripsi' => $validated['deskripsi'],
                    'mk_id' => $validated['mk_id'],
                    'akademik' => $validated['akademik'],
                    'tahun_awal' => $validated['akademik_1'],
                    'tahun_akhir' => $validated['akademik_2'],
                    'is_draf' => $validated['is_draf'],
                    'isi_rps_json' => json_encode($validated['cpmk_sub_items_array']),
                ]);

                // 2. Sync Dosen dengan Pivot Data
                $syncDosen = [];
                foreach ($validated['dosen_id_array'] as $index => $id) {
                    $detail = collect($validated['dosen_items_array'])->firstWhere('id', $id);
                    $syncDosen[(int) $id] = [
                        'peran' => $detail['peran'] ?? 'Pengajar',
                        'is_ketua' => (bool) ($detail['is_ketua'] ?? false),
                        'sort_order' => $index,
                    ];
                }
                $rps->dosens()->sync($syncDosen);

                // 3. Sync CPMK
                $syncCpmk = [];
                foreach ($validated['cpmk_id_array'] as $index => $id) {
                    $syncCpmk[(int) $id] = ['sort_order' => $index];
                }
                $rps->cpmks()->sync($syncCpmk);

                // 4. Sync CPL (Manual/Tambahan)
                $syncCpl = [];
                foreach ($validated['cpl_id_array'] as $index => $id) {
                    $syncCpl[(int) $id] = ['sort_order' => $index];
                }
                $rps->cpls()->sync($syncCpl);

                // 5. Sync Referensi (Manual/Tambahan)
                $syncRef = [];
                foreach ($validated['ref_id_array'] as $index => $id) {
                    $syncRef[(int) $id] = ['sort_order' => $index];
                }
                $rps->refs()->sync($syncRef);
            });

            $this->toast(message: 'RPS Berhasil diperbarui', type: 'update');
            $this->showRPSModal = false;
            $this->dispatch('refresh-data');

        } catch (\Exception $e) {
            $this->toast(text: 'Gagal memperbarui: '.$e->getMessage(), variant: 'danger');
        }
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
            'akademik.required' => 'Tahun Akademik wajib diisi!',
            'akademik.regex' => 'Format Tahun Akademik tidak valid (contoh: 2025/2026)!',
            'akademik_1.required' => 'Tahun awal (input kiri) wajib diisi!',
            'akademik_1.min' => 'Tahun awal minimal adalah 1970!',
            'akademik_2.required' => 'Tahun akhir (input kanan) wajib diisi!',
            'akademik_2.min' => 'Tahun akhir minimal adalah 1971!',

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
