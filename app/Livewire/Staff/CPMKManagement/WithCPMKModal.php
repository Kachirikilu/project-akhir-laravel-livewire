<?php

namespace App\Livewire\Staff\CPMKManagement;

use App\Livewire\Global\HasToast;
use App\Models\Akademik\CPMK;
use App\Models\Akademik\SubCPMK;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\WithPagination;

trait WithCPMKModal
{
    use HasToast;

    use WithPagination;


    public $selected_id_cpmk;

    public $isEditingCPMK = false;

    public $showEditCPMK = false;

    public $showCPMKModal = false;

    public $mk_id_2;

    public $rps_items_list = [];

    public $rps_modal_page = 5;

    public $cpmk_rps_id;

    protected $rps_modal_paginator;

    public function addCPMK()
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        if ($this->showEditCPMK == true) {
            $this->resetInputCPMK();
        }

        $this->resetValidation();
        $this->resetErrorBag();
        $this->isEditingCPMK = false;
        $this->showCPMKModal = true;
        $this->showEditCPMK = false;

        $this->updatedSCPMKNameSearch($this->scpmkNameSearch);
        $this->updatedCPLNameSearch($this->cplNameSearch);
        $this->updatedRefNameSearch($this->refNameSearch);
    }

    public function editCPMK($id)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $this->resetInputCPMK();
        $this->resetValidation();
        $this->resetErrorBag();

        $this->selected_id_cpmk = $id;
        $this->isEditingCPMK = true;
        $this->showEditCPMK = true;

        try {
            // 1. Load data CPMK dengan relasi yang sangat lengkap
            $cpmk = CPMK::with([
                'scpmks',
                'scpmks.refs',
                'cpls',
                'refs',
                'rps',
            ])->findOrFail($id);

            // 3. MAPPING CPMK (MENGGUNAKAN FUNGSI mapCPMK ANDA)
            $this->scpmk_id_array = $cpmk->scpmks->pluck('id')->toArray();
            $this->scpmk_items_array = $cpmk->scpmks->map(function ($c) {
                return $this->itemsSCPMK($c);
            })->toArray();

            $this->scpmk_sub_items_array = $cpmk->scpmks->map(function ($s) {
                return ['scpmk' => [collect($this->mapSCPMK(collect([$s])))->first()]];
            })->toArray();

            $this->cpl_id_array = $cpmk->cpls->pluck('id')->toArray();
            $this->cpl_items_array = $cpmk->cpls->map(function ($c) {
                return $this->itemsCPL($c);
            })->toArray();

            $this->ref_id_array = $cpmk->refs->pluck('id')->toArray();
            $this->ref_items_array = $cpmk->refs->map(function ($r) {
                return $this->itemsRef($r);
            })->toArray();

            $this->updatedSCPMKNameSearch($this->cpmkNameSearch);
            $this->updatedCPLNameSearch($this->cplNameSearch);
            $this->updatedRefNameSearch($this->refNameSearch);

            $this->showCPMKModal = true;

            $this->cpmk_rps_id = $cpmk->id;
            $this->rps_modal_page = 5;
            $this->resetPage('rps_modal_page');
            $this->loadCPMKRPSPagination();

            $this->dispatch('fill-modal-cpmk', cpmk: $cpmk);
            $this->dispatch('refresh-component');

        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Mengambil Data: '.$e->getMessage(), variant: 'danger');
        }
    }

    private function loadCPMKRPSPagination()
    {
        if (empty($this->cpmk_rps_id)) {
            return;
        }

        $cpmk = CPMK::find($this->cpmk_rps_id);

        if (! $cpmk) {
            return;
        }

        $rps = $cpmk->rps()->paginate(5, ['*'], 'rps_modal_page');
        $this->rps_items_list = $this->mapRPS($rps);
        $this->rps_modal_paginator = $rps;
    }

    public function updatedRpsModalPage($page)
    {
        $this->loadCPMKRPSPagination();
    }

    private function inputModalCPMK($isEditingCPMK, $data)
    {
        // 1. Ambil data Referensi yang melekat pada SCPMK terpilih saja
        $refFromScpmk = [];

        if (! empty($data['scpmk_id_array'])) {
            $scpmks = SubCPMK::with('refs')->whereIn('id', $data['scpmk_id_array'])->get();
            foreach ($scpmks as $scpmk) {
                $refFromScpmk = array_merge($refFromScpmk, $scpmk->refs?->pluck('id')->toArray() ?? []);
            }
        }

        // --- PROSES PEMBERSIHAN REFERENSI ---
        $cleanRef = [];
        if (isset($data['ref_id_array']) && is_array($data['ref_id_array'])) {
            $cleanRef = array_values(array_diff(array_unique($data['ref_id_array']), $refFromScpmk));
        }

        $rules = [
            'kode_cpmk_1' => 'required|alpha|max:10',
            'kode_cpmk_2' => 'required|numeric|min:1',
            'kode_cpmk' => [
                'required',
                'alpha_num',
                'max:20',
                function ($attribute, $value, $fail) use ($isEditingCPMK) {
                    $query = DB::table('cpmks')
                        ->where('kode_cpmk', $value);

                    if ($isEditingCPMK) {
                        $query->where('id', '!=', $this->selected_id_cpmk);
                    }

                    if ($query->exists()) {
                        $fail("Kode CPMK '$value' sudah digunakan di Mata Kuliah ini!");
                    }
                },
            ],
            'deskripsi' => 'required|string|max:1000',
            'scpmk_id_array' => 'required|array|min:1',
            'cpl_id_array' => 'required|array|min:1',
        ];

        $validator = Validator::make($data, $rules, $this->validationMessagesCPMK());

        if ($validator->fails()) {
            $errors = $validator->errors();
            if (empty($data['kode_cpmk_1']) && empty($data['kode_cpmk_2'])) {
                $this->addError('kode_cpmk', 'Kode CPMK wajib diisi!');
            } elseif ($errors->has('kode_cpmk_1') || $errors->has('kode_cpmk_2')) {
                $combinedMessage = $errors->first('kode_cpmk_1') ?: $errors->first('kode_cpmk_2');
                $this->addError('kode_cpmk', $combinedMessage);
            }
            foreach ($errors->toArray() as $key => $messages) {
                if (! in_array($key, ['kode_cpmk_1', 'kode_cpmk_2', 'kode_cpmk'])) {
                    foreach ($messages as $message) {
                        $this->addError($key, $message);
                    }
                }
                if ($key === 'kode_cpmk' && ! $this->getErrorBag()->has('kode_cpmk')) {
                    $this->addError('kode_cpmk', $messages[0]);
                }
            }
            throw ValidationException::withMessages($this->getErrorBag()->messages());
        }

        $validated = $validator->validated();

        $validated['ref_id_array'] = $cleanRef;
        $validated['scpmk_id_array'] = array_values(array_unique($data['scpmk_id_array'] ?? []));
        $validated['cpl_id_array'] = array_values(array_unique($data['cpl_id_array'] ?? []));

        return $validated;
    }

    public function saveCPMK($data)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $data['scpmk_id_array'] = $this->scpmk_id_array ?? [];
        $data['cpl_id_array'] = $this->cpl_id_array ?? [];
        $data['ref_id_array'] = $this->ref_id_array ?? [];

        try {
            // 1. Jalankan validasi & pembersihan
            $validated = $this->inputModalCPMK(false, $data);
            // 2. Eksekusi Database
            DB::transaction(function () use ($validated) {
                $cpmk = CPMK::create([
                    'kode_cpmk' => strtoupper($validated['kode_cpmk']),
                    'deskripsi' => $validated['deskripsi'],
                ]);

                // Sync Sub-CPMK (SCPMK)
                if (! empty($validated['scpmk_id_array'])) {
                    $syncData = [];
                    foreach ($validated['scpmk_id_array'] as $index => $id) {
                        $syncData[(int) $id] = ['sort_order' => $index];
                    }
                    $cpmk->scpmks()->sync($syncData);
                }

                // Sync CPL
                if (! empty($validated['cpl_id_array'])) {
                    $syncData = [];
                    foreach ($validated['cpl_id_array'] as $index => $id) {
                        $syncData[(int) $id] = ['sort_order' => $index];
                    }
                    $cpmk->cpls()->sync($syncData);
                }

                // Sync Referensi (yang sudah difilter/clean)
                if (! empty($validated['ref_id_array'])) {
                    $syncData = [];
                    foreach ($validated['ref_id_array'] as $index => $id) {
                        $syncData[(int) $id] = ['sort_order' => $index];
                    }
                    $cpmk->refs()->sync($syncData);
                }
            });

            $this->toast(message: "CPMK {$validated['kode_cpmk']} berhasil disimpan!");
            $this->resetInputCPMK();
            $this->dispatch('refresh-data-cpmk');
            $this->showCPMKModal = false;

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal', variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal: '.$e->getMessage(), variant: 'danger');
        }
    }

    public function updateCPMK($data)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $data['scpmk_id_array'] = $this->scpmk_id_array ?? [];
        $data['cpl_id_array'] = $this->cpl_id_array ?? [];
        $data['ref_id_array'] = $this->ref_id_array ?? [];

        try {
            $validated = $this->inputModalCPMK(true, $data);

            DB::transaction(function () use ($validated) {
                $cpmk = CPMK::findOrFail($this->selected_id_cpmk);

                // 1. Update Data Utama CPMK
                $cpmk->update([
                    'kode_cpmk' => strtoupper($validated['kode_cpmk']),
                    'deskripsi' => $validated['deskripsi'],
                ]);

                // 2. Update Tanggal Revisi pada RPS Terkait
                if ($cpmk->rps()->exists()) {
                    $cpmk->rps()
                        ->where('is_draf', 0)
                        ->update(['revisi' => now()]);
                }

                // 3. Sync Sub-CPMK (SCPMK) ke Pivot
                $syncScpmk = [];
                foreach ($validated['scpmk_id_array'] as $index => $id) {
                    $syncScpmk[(int) $id] = ['sort_order' => $index];
                }
                // Pastikan nama relasi di model CPMK adalah scpmks()
                $cpmk->scpmks()->sync($syncScpmk);

                // 4. Sync CPL (Manual/Tambahan)
                $syncCpl = [];
                foreach ($validated['cpl_id_array'] as $index => $id) {
                    $syncCpl[(int) $id] = ['sort_order' => $index];
                }
                $cpmk->cpls()->sync($syncCpl);

                // 5. Sync Referensi (Manual/Tambahan)
                $syncRef = [];
                foreach ($validated['ref_id_array'] as $index => $id) {
                    $syncRef[(int) $id] = ['sort_order' => $index];
                }
                $cpmk->refs()->sync($syncRef);
            });

            $this->toast(message: 'CPMK Berhasil diperbarui', type: 'update');
            $this->showCPMKModal = false;
            $this->dispatch('refresh-data-cpmk');

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal memperbarui: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data-cpmk');
            $this->showCPMKModal = false;
        }
    }

    private function validationMessagesCPMK()
    {
        return [

            'kode_cpmk_1.required' => 'Kode awalan (input kiri) wajib diisi!',
            'kode_cpmk_1.alpha' => 'Kode awalan harus berupa huruf!',
            'kode_cpmk_1.max' => 'Kode awalan terlalu panjang!',

            // Kode CPMK Bagian 2 (Angka - Kanan)
            'kode_cpmk_2.required' => 'Nomor kode (input kanan) wajib diisi!',
            'kode_cpmk_2.numeric' => 'Nomor kode harus berupa angka!',
            'kode_cpmk_2.min' => 'Nomor kode minimal adalah 1!',

            // Pesan General untuk Hasil Gabungan
            'kode_cpmk.required' => 'Kode CPMK lengkap wajib terbentuk!',
            'kode_cpmk.alpha_num' => 'Gabungan kode harus alfanumerik!',
            'kode_cpmk.required' => 'Kode CPMK wajib diisi!',
            'kode_cpmk.alpha_num' => 'Kode CPMK hanya boleh berisi huruf dan angka!',
            'kode_cpmk.max' => 'Kode CPMK maksimal 20 karakter!',

            // Deskripsi & Status
            'deskripsi.required' => 'Deskripsi CPMK wajib diisi!',
            'deskripsi.max' => 'Deskripsi CPMK terlalu panjang (Maksimal 1000 karakter)!',
            'is_draf.required' => 'Status CPMK wajib ditentukan!',
            'is_draf.boolean' => 'Format status draf tidak valid!',

            // CPMK & Relasi Data
            'scpmk_id_array.required' => 'Minimal pilih satu Sub-CPMK untuk CPMK ini!',
            'scpmk_id_array.array' => 'Format data Sub-CPMK tidak valid!',
            'scpmk_id_array.min' => 'Minimal harus ada satu Sub-CPMK yang dipilih!',

            'cpl_id_array.required' => 'Minimal pilih satu CPL untuk CPMK ini!',
            'cpl_id_array.array' => 'Format data CPL tidak valid!',
            'cpl_id_array.min' => 'Minimal harus ada satu CPL yang dipilih!',

            'ref_id_array.array' => 'Format data Referensi tidak valid!',

            // Form Mata Kuliah (Legacy/Template)
            'nama_mk.required' => 'Nama Mata Kuliah wajib diisi!',
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

    private function resetInputCPMK()
    {
        $this->scpmkNameSearch = '';
        $this->cplNameSearch = '';
        $this->refNameSearch = '';

        $this->scpmk_id_array = [];
        $this->scpmk_items_array = [];
        $this->scpmk_sub_items_array = [];

        $this->cpl_id_array = [];
        $this->cpl_items_array = [];

        $this->ref_id_array = [];
        $this->ref_items_array = [];

        $this->dosen_id_array = [];
        $this->dosen_items_array = [];

        $this->resetErrorBag();
    }
}
