<?php

namespace App\Livewire\Admin\UserManagement;

use App\Livewire\Global\HasErrorCount;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithDosenSearchFilters;
use App\Models\Auth\Admin;
use App\Models\Auth\Dosen;
use App\Models\Auth\Mahasiswa;
use App\Models\Auth\User;
use App\Models\Akademik\RPS;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

trait WithUserModal
{
    use HasErrorCount;
    use HasToast;
    use WithDosenSearchFilters;

    public $showUserModal = false;

    public $isEditingUser = false;

    public $roleType;

    public $selected_id_user;

    public $pr_id_2;

    public $dosen_rps_items_list = [];

    public $dosen_rps_modal_page = 3;

    public $dosen_rps_id;

    protected $dosen_rps_modal_paginator;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'nullable|min:8',
        'name' => 'required|string|max:255',
        'nip' => 'nullable|string|max:20',
        'nitk' => 'nullable|string|max:20',
        'nidn' => 'nullable|string|max:20',
        'nidk' => 'nullable|string|max:20',
        'nim' => 'required|string|max:20',
        'angkatan' => 'required|integer',
        'pr_id' => 'required|exists:prodis,id',
    ];

    public function addUser($role)
    {
        if (! $this->AuthCheck()) {
            return;
        }
        $this->resetValidation();
        $this->resetErrorBag();
        $this->isEditingUser = false;
        $this->roleType = $role;
        $this->showUserModal = true;
        $this->updatedPrNameSearch($this->prNameSearch);
    }

    public function editUser($id, $withRPS = false)
    {
        if (! $this->AuthCheck()) {
            return;
        }

        $this->resetInputUser();
        $this->resetValidation();

        $this->resetErrorBag();
        $this->showUserModal = true;
        $this->isEditingUser = true;

        try {
            $user = User::with(['admin', 'dosen', 'mahasiswa'])->findOrFail($id);
            $this->selected_id_user = $user->id;
            $this->pr_id = $user->pr_id;
            $this->pr_id_2 = $user->pr_id;
            $this->prNameSearch = $user->prodi;

            $this->getPrbyUser();
            $this->fetchPr($this->prNameSearch);

            if ($user->dosen && $withRPS) {
                $this->dosen_rps_id = $user->dosen->id;
                $this->resetPage('dosen_rps_modal_page');
                $this->loadDosenRPSPagination();
            }

            $this->roleType = strtolower($user->role);
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Mengambil Data: '.$e->getMessage(), variant: 'danger');
        }
    }

    private function loadDosenRPSPagination()
    {
        if (empty($this->dosen_rps_id)) {
            return;
        }

        $dosen = Dosen::find($this->dosen_rps_id);

        if (! $dosen) {
            return;
        }

        $rps = RPS::whereHas('dosens', function ($query) use ($dosen) {
            $query->where('dosens.id', $dosen->id);
        })->paginate($this->dosen_rps_modal_page, ['*'], 'dosen_rps_modal_page');

        $this->dosen_rps_items_list = $this->mapRPS($rps);
        $this->dosen_rps_modal_paginator = $rps;
    }

    public function updatedDosenRPSModalPage($page)
    {
        $this->loadDosenRPSPagination();
    }

    private function inputModalUser($isEditingUser, $data)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $rules = [
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->selected_id_user),
            ],
            'password' => $isEditingUser ? 'nullable|min:8' : 'required|min:8',
            'name' => 'required|string|max:255',
        ];

        /* ===================== ADMIN ===================== */
        if ($this->roleType === 'admin') {

            $rules['nip'] = [
                'required',
                $this->uniqueRule('admins', 'nip'),
                Rule::unique('admins', 'nitk'),
                Rule::unique('dosens', 'nip'),
                Rule::unique('dosens', 'nidn'),
                Rule::unique('dosens', 'nidk'),
                Rule::unique('mahasiswas', 'nim'),
            ];

            $rules['nitk'] = [
                'nullable',
                $this->uniqueRule('admins', 'nitk'),
                Rule::unique('admins', 'nip'),
                Rule::unique('dosens', 'nip'),
                Rule::unique('dosens', 'nidn'),
                Rule::unique('dosens', 'nidk'),
                Rule::unique('mahasiswas', 'nim'),
            ];

            $rules['status'] = [
                'required',
                Rule::in([
                    'Aktif',                  // Hijau (Produktif)
                    'Tugas Belajar',          // Kuning (Transisi/Sementara)
                    'Mutasi',                 // Kuning (Transisi/Sementara)
                    'Cuti Luar Tanggungan',   // Kuning (Transisi/Sementara)
                    'Resign',                 // Orange (Keluar Prosedural)
                    'Pensiun',                // Orange (Keluar Prosedural)
                    'Diberhentikan',          // Merah (Masalah/Sanksi)
                    'Meninggal Dunia',         // Merah (Permanen)
                ]),
            ];
        }

        /* ===================== DOSEN ===================== */
        elseif ($this->roleType === 'dosen') {

            $rules['nip'] = [
                'required',
                $this->uniqueRule('dosens', 'nip'),
                Rule::unique('dosens', 'nidn'),
                Rule::unique('dosens', 'nidk'),
                Rule::unique('admins', 'nip'),
                Rule::unique('admins', 'nitk'),
                Rule::unique('mahasiswas', 'nim'),
            ];

            $rules['nidn'] = [
                'nullable',
                $this->uniqueRule('dosens', 'nidn'),
                Rule::unique('dosens', 'nip'),
                Rule::unique('dosens', 'nidk'),
                Rule::unique('admins', 'nip'),
                Rule::unique('admins', 'nitk'),
                Rule::unique('mahasiswas', 'nim'),
            ];

            $rules['nidk'] = [
                'nullable',
                $this->uniqueRule('dosens', 'nidk'),
                Rule::unique('dosens', 'nip'),
                Rule::unique('dosens', 'nidn'),
                Rule::unique('admins', 'nip'),
                Rule::unique('admins', 'nitk'),
                Rule::unique('mahasiswas', 'nim'),
            ];

            $rules['status'] = [
                'required',
                Rule::in([
                    'Aktif',                  // Hijau (Produktif)
                    'Tugas Belajar',          // Kuning (Transisi/Studi)
                    'Izin Belajar',           // Kuning (Transisi/Studi)
                    'Cuti Sabatika',          // Kuning (Transisi/Riset)
                    'Alih Tugas',             // Orange (Perubahan Jabatan)
                    'Resign',                 // Orange (Keluar Prosedural)
                    'Pensiun',                // Orange (Keluar Prosedural)
                    'Diberhentikan',          // Merah (Masalah/Sanksi)
                    'Meninggal Dunia',         // Merah (Permanen)
                ]),
            ];
        }

        /* ===================== MAHASISWA ===================== */
        elseif ($this->roleType === 'mahasiswa') {

            $rules['nim'] = [
                'required',
                $this->uniqueRule('mahasiswas', 'nim'),
                Rule::unique('admins', 'nip'),
                Rule::unique('admins', 'nitk'),
                Rule::unique('dosens', 'nip'),
                Rule::unique('dosens', 'nidn'),
                Rule::unique('dosens', 'nidk'),
            ];

            $rules['angkatan'] =
                'required|integer|min:1960|max:'.date('Y');

            $rules['status'] = [
                'required',
                Rule::in([
                    'Aktif',                  // Hijau (Aktif Kuliah)
                    'Lulus',                  // Biru (Output Positif)
                    'Cuti',                   // Kuning (Jeda Resmi)
                    'Pindah',                 // Kuning (Transisi Keluar)
                    'Non-Aktif',              // Orange (Masalah Administrasi)
                    'Mengundurkan Diri',      // Orange (Keluar Prosedural)
                    'Drop Out',               // Merah (Masalah Akademik/Sanksi)
                    'Hilang',                 // Merah (Tanpa Kabar/Ghaib)
                    'Meninggal Dunia',         // Merah (Permanen)
                ]),
            ];
        }

        $rules['pr_id'] = 'required|exists:prodis,id';

        $validator = Validator::make($data, $rules, $this->validationMessagesUser());

        $validator->after(function ($validator) use ($data) {

            if ($this->roleType === 'admin') {

                if (! empty($data['nip']) && ! empty($data['nitk']) && $data['nip'] === $data['nitk']) {

                    $validator->errors()->add(
                        'nitk',
                        'NITK tidak boleh memiliki nilai yang sama dengan NIP!'
                    );

                }

            } elseif ($this->roleType === 'dosen') {

                if (! empty($data['nip']) && ! empty($data['nidn']) && $data['nip'] === $data['nidn']) {

                    $validator->errors()->add(
                        'nidn',
                        'NIDN tidak boleh memiliki nilai yang sama dengan NIP dan NIDK!'
                    );

                }

                if (! empty($data['nip']) && ! empty($data['nidk']) && $data['nip'] === $data['nidk']) {

                    $validator->errors()->add(
                        'nidk',
                        'NIDK tidak boleh memiliki nilai yang sama dengan NIP dan NIDN!'
                    );

                }

                if (! empty($data['nidn']) && ! empty($data['nidk']) && $data['nidn'] === $data['nidk']) {

                    $validator->errors()->add(
                        'nidk',
                        'NIDK tidak boleh memiliki nilai yang sama dengan NIP dan NIDN!'
                    );

                }

            }

        });

        return $validator->validate();
    }

    private function uniqueRule(string $table, string $column)
    {
        return $this->selected_id_user
            ? Rule::unique($table, $column)->ignore($this->selected_id_user, 'user_id')
            : Rule::unique($table, $column);
    }

    public function saveUser($data)
    {
        if (! $this->AuthCheck()) {
            return;
        }
        // if (empty($data['pr_id'])) {
        $data['pr_id'] = $this->pr_id;
        // }
        if (empty($data['status'])) {
            $data['status'] = 'Aktif';
        }

        try {
            $validated = $this->inputModalUser(false, $data);

            DB::transaction(function () use ($validated) {

                $user = User::create([
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                ]);

                $nameInput = $validated['name'];
                if ($this->roleType !== 'mahasiswa') {
                    $identity1Input = $validated['nip'];
                    if ($this->roleType == 'admin') {
                        $identity2Input = ($validated['nitk'] ?? null) ?: null;
                    } else {
                        $identity2Input = ($validated['nidn'] ?? null) ?: null;
                    }
                } else {
                    $identity1Input = $validated['nim'];
                }
                $prodiInput = $validated['pr_id'];
                $statusInput = $validated['status'];

                $dosen = null;

                if ($this->roleType === 'admin') {
                    Admin::create([
                        'user_id' => $user->id,
                        'name' => $nameInput,
                        'nip' => $identity1Input,
                        'nitk' => $identity2Input,
                        'pr_id' => $prodiInput,
                        'status' => $statusInput,
                    ]);
                } elseif ($this->roleType === 'dosen') {
                    $dosen = Dosen::create([
                        'user_id' => $user->id,
                        'name' => $nameInput,
                        'nip' => $identity1Input,
                        'nidn' => $identity2Input,
                        'nidk' => ($validated['nidk'] ?? null) ?: null,
                        'pr_id' => $prodiInput,
                        'status' => $statusInput,
                    ]);
                } elseif ($this->roleType === 'mahasiswa') {
                    Mahasiswa::create([
                        'user_id' => $user->id,
                        'name' => $nameInput,
                        'nim' => $identity1Input,
                        'angkatan' => $validated['angkatan'],
                        'pr_id' => $prodiInput,
                        'status' => $statusInput,
                    ]);
                }

                if (!empty($this->showRPSModal) && $dosen) {
                    if (! isset($this->dosen_id_array) || ! is_array($this->dosen_id_array)) {
                        $this->dosen_id_array = [];
                    }
                    if (! isset($this->dosen_items_array) || ! is_array($this->dosen_items_array)) {
                        $this->dosen_items_array = [];
                    }
                    if (! in_array($dosen->id, $this->dosen_id_array)) {
                        $this->dosen_id_array[] = $dosen->id;
                        $this->dosen_items_array[] = $this->itemsDosen($dosen);
                    }

                    $isKetua = collect($this->dosen_items_array)
                        ->contains(fn ($item) => $item['is_ketua'] === true);
                    if (! $isKetua && count($this->dosen_items_array) > 0) {
                        $lastIndex = array_key_last($this->dosen_items_array);
                        $this->dosen_items_array[$lastIndex]['is_ketua'] = true;
                        $this->dosen_items_array[$lastIndex]['peran'] = 'Koordinator';
                    }
                }

            });

            $this->resetInputUser();
            $this->dispatch('refresh-data-user');
            $this->showUserModal = false;
            $this->toast(message: ucfirst($this->roleType), isAkun: true);

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Menambahkan: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data-user');
            $this->showUserModal = false;
        }
    }

    public function updateUser($data)
    {
        if (! $this->AuthCheck()) {
            return;
        }
        if ((empty($data['pr_id']) && $this->pr_id !== $this->pr_id_2) ||
            ($this->pr_id == $this->pr_id_2) || ($this->pr_id !== $this->pr_id_2)) {
            $data['pr_id'] = $this->pr_id;
        }
        if (empty($data['status'])) {
            $data['status'] = 'Aktif';
        }

        try {
            $validated = $this->inputModalUser(true, $data);

            DB::transaction(function () use ($validated) {

                $user = User::findOrFail($this->selected_id_user);
                $user->update(['email' => $validated['email']]);

                if ($validated['password']) {
                    $user->update(['password' => Hash::make($validated['password'])]);
                }

                $nameInput = $validated['name'];
                if ($this->roleType !== 'mahasiswa') {
                    $identity1Input = $validated['nip'];
                    if ($this->roleType == 'admin') {
                        $identity2Input = ($validated['nitk'] ?? null) ?: null;
                    } else {
                        $identity2Input = ($validated['nidn'] ?? null) ?: null;
                    }
                } else {
                    $identity1Input = $validated['nim'];
                }
                $prodiInput = $validated['pr_id'];
                $statusInput = $validated['status'];

                if ($this->roleType === 'admin') {
                    $user->admin->update(
                        [
                            'name' => $nameInput,
                            'nip' => $identity1Input,
                            'nitk' => $identity2Input,
                            'pr_id' => $prodiInput,
                            'status' => $statusInput,
                        ]
                    );
                } elseif ($this->roleType === 'dosen') {
                    $user->dosen->update(
                        [
                            'name' => $nameInput,
                            'nip' => $identity1Input,
                            'nidn' => $identity2Input,
                            'nidk' => ($validated['nidk'] ?? null) ?: null,
                            'pr_id' => $prodiInput,
                            'status' => $statusInput,
                        ]
                    );
                } elseif ($this->roleType === 'mahasiswa') {
                    $user->mahasiswa->update([
                        'name' => $nameInput,
                        'nim' => $identity1Input,
                        'angkatan' => $validated['angkatan'],
                        'pr_id' => $prodiInput,
                        'status' => $statusInput,
                    ]);
                }
            });

            $this->dispatch('refresh-data-user');
            $this->showUserModal = false;
            $this->toast(message: ucfirst($this->roleType), type: 'update', isAkun: true);

            if (Auth::id() === $this->selected_id_user) {
                $this->dispatch('profile-updated');
            }

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Memperbarui: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data-user');
            $this->showUserDelete = false;
        }
    }

    public function validationMessagesUser()
    {
        return [
            'email.required' => 'Alamat email wajib diisi!',
            'email.email' => 'Format email tidak valid!',
            'email.unique' => 'Email ini sudah terdaftar di sistem!',
            'password.required' => 'Password wajib diisi!',
            'password.min' => 'Password minimal harus 8 karakter!',
            'name.required' => 'Nama lengkap wajib diisi!',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter!',
            'nip.required' => 'NIP wajib diisi untuk Admin dan Dosen!',
            'nip.unique' => 'NIP ini sudah terdaftar!',
            'nitk.unique' => 'NITK ini sudah terdaftar!',
            'nidn.unique' => 'NIDN ini sudah terdaftar!',
            'nidk.unique' => 'NIDK ini sudah terdaftar!',
            'nim.required' => 'NIM wajib diisi untuk Mahasiswa!',
            'nim.unique' => 'NIM ini sudah terdaftar!',
            'angkatan.required' => 'Tahun angkatan wajib diisi!',
            'angkatan.integer' => 'Tahun angkatan harus berupa angka!',
            'angkatan.min' => 'Tahun angkatan tidak boleh kurang dari tahun 1960!',
            'angkatan.max' => 'Tahun angkatan tidak boleh melebihi tahun sekarang!',
            'pr_id.required' => 'Program studi wajib dipilih!',
            'pr_id.exists' => 'Program studi yang dipilih tidak valid!',
            'excel_file.required' => 'File Excel wajib diunggah!',
            'excel_file.file' => 'File Excel harus berupa file yang valid!',
            'excel_file.mimes' => 'File Excel harus berformat .xlsx, .xls, atau .csv!',
            'status.required' => 'Status pengguna wajib dipilih!',
            'status.in' => 'Status yang dipilih tidak sesuai dengan kategori yang diizinkan!',
        ];
    }

    public function getUserErrorSections()
    {
        return [
            1 => $this->getErrorCount([
                'email',
                'password',
            ]),
            2 => $this->getErrorCount([
                'name',
                'nip',
                'nitk',
                'nidn',
                'nidk',
                'nim',
                'angkatan',
                'pr_id',
                'status',
            ]),
            3 => $this->getErrorCount([]),
        ];
    }

    public function resetInputUser(
        // $keepProdi = false
    ) {
        $fields = [
            'selected_id_user',
            // 'email', 'password', 'name', 'nip', 'nitk',
            // 'nidn', 'nidk', 'nim', 'angkatan',
            'roleType',
        ];

        // if (! $keepProdi) {
        //     $fields = array_merge($fields, ['pr_id', 'prNameSearch', 'prResults']);
        // }

        $this->reset($fields);
        $this->resetErrorBag();
    }
}
