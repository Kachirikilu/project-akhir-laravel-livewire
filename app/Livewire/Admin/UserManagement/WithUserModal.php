<?php

namespace App\Livewire\Admin\UserManagement;

use App\Models\Auth\User;
use App\Models\Auth\Admin;
use App\Models\Auth\Dosen;
use App\Models\Auth\Mahasiswa;
use App\Models\ProgramStudi\Prodi;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

trait WithUserModal
{
    public $showUserModal = false;

    public $isEditing = false;

    public $roleType;

    public $user_id;

    public $prodi_id_2;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'nullable|min:8',
        'name' => 'required|string|max:255',
        'nip' => 'nullable|string|max:20',
        'nitk' => 'nullable|string|max:20',
        'nidn' => 'nullable|string|max:20',
        'nidk' => 'nullable|string|max:20',
        'nim' => 'required|string|max:20',
        'tahun_angkatan' => 'required|integer',
        'prodi_id' => 'required|exists:prodis,id',
    ];

    public function addUser($role)
    {
        $this->resetValidation();
        $this->resetErrorBag();
        $this->isEditing = false;
        $this->roleType = $role;
        $this->showUserModal = true;
        $this->updatedProdiNameSearch($this->prodiNameSearch);
    }

    public function editUser($id)
    {
        if (! Auth::user()->admin) {
            $this->dispatch('toast', message: '❌ Hanya admin yang dapat mengedit pengguna!');

            return;
        }

        $this->resetInput();
        $this->resetValidation();

        $this->resetErrorBag();
        $this->showUserModal = true;
        $this->isEditing = true;

        $user = User::with(['admin', 'dosen', 'mahasiswa'])->findOrFail($id);
        $this->user_id = $user->id;
        $this->prodi_id = $user->admin->prodi_id ?? $user->dosen->prodi_id ?? $user->mahasiswa->prodi_id ?? null;
        $this->prodi_id_2 = $user->admin->prodi_id ?? $user->dosen->prodi_id ?? $user->mahasiswa->prodi_id ?? null;

        if ($this->prodi_id) {
            $prodi = Prodi::find($this->prodi_id);
            $this->prodiNameSearch = $prodi ? $prodi->nama_prodi : '';
        } else {
            $this->prodiNameSearch = '';
        }
        $this->getProdibyUser();
        $this->fetchProdi($this->prodiNameSearch);

        $this->roleType = strtolower($user->role);
    }

    private function inputModalUser($isEditing, $data)
    {
        $rules = [
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->user_id),
            ],
            'password' => $isEditing ? 'nullable|min:8' : 'required|min:8',
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

            $rules['tahun_angkatan'] =
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

        $rules['prodi_id'] = 'required|exists:prodis,id';

        $validator = Validator::make($data, $rules, $this->validationMessages());

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
        return $this->user_id
            ? Rule::unique($table, $column)->ignore($this->user_id, 'user_id')
            : Rule::unique($table, $column);
    }

    public function saveUser($data)
    {
        
        // if (empty($data['prodi_id'])) {
        $data['prodi_id'] = $this->prodi_id;
        // }
        if (empty($data['status'])) {
            $data['status'] = 'Aktif';
        }

        $validated = $this->inputModalUser(false, $data);

        try {
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
                $prodiInput = $validated['prodi_id'];
                $statusInput = $validated['status'];

                if ($this->roleType === 'admin') {
                    Admin::create([
                        'user_id' => $user->id,
                        'name' => $nameInput,
                        'nip' => $identity1Input,
                        'nitk' => $identity2Input,
                        'prodi_id' => $prodiInput,
                        'status' => $statusInput,
                    ]);
                } elseif ($this->roleType === 'dosen') {
                    Dosen::create([
                        'user_id' => $user->id,
                        'name' => $nameInput,
                        'nip' => $identity1Input,
                        'nidn' => $identity2Input,
                        'nidk' => ($validated['nidk'] ?? null) ?: null,
                        'prodi_id' => $prodiInput,
                        'status' => $statusInput,
                    ]);
                } elseif ($this->roleType === 'mahasiswa') {
                    Mahasiswa::create([
                        'user_id' => $user->id,
                        'name' => $nameInput,
                        'nim' => $identity1Input,
                        'tahun_angkatan' => $validated['tahun_angkatan'],
                        'prodi_id' => $prodiInput,
                        'status' => $statusInput,
                    ]);
                }

            });

            $this->resetInput();
            $this->dispatch('toast', message: '✅ Pengguna berhasil ditambahkan!');
            $this->dispatch('refresh-data');
            $this->showUserModal = false;

        } catch (\Exception $e) {
            $this->dispatch('toast', message: '❌ Terjadi kesalahan saat menambahkan data!');
            $this->dispatch('refresh-data');
            $this->showUserModal = false;
        }
    }

    public function updateUser($data)
    {
        if ((empty($data['prodi_id']) && $this->prodi_id !== $this->prodi_id_2) ||
            ($this->prodi_id == $this->prodi_id_2) || ($this->prodi_id !== $this->prodi_id_2)) {
            $data['prodi_id'] = $this->prodi_id;
        } 
        if (empty($data['status'])) {
            $data['status'] = 'Aktif';
        }
        $validated = $this->inputModalUser(true, $data);

        try {
            DB::transaction(function () use ($validated) {

                $user = User::findOrFail($this->user_id);
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
                $prodiInput = $validated['prodi_id'];
                $statusInput = $validated['status'];

                if ($this->roleType === 'admin') {
                    $user->admin->update(
                        [
                            'name' => $nameInput,
                            'nip' => $identity1Input,
                            'nitk' => $identity2Input,
                            'prodi_id' => $prodiInput,
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
                            'prodi_id' => $prodiInput,
                            'status' => $statusInput,
                        ]
                    );
                } elseif ($this->roleType === 'mahasiswa') {
                    $user->mahasiswa->update([
                        'name' => $nameInput,
                        'nim' => $identity1Input,
                        'tahun_angkatan' => $validated['tahun_angkatan'],
                        'prodi_id' => $prodiInput,
                        'status' => $statusInput,
                    ]);
                }
            });

            $this->dispatch('toast', message: '✅ Data pengguna berhasil diperbarui!');
            $this->dispatch('refresh-data');
            $this->showUserModal = false;

            if (Auth::id() === $user->id) {
                $this->dispatch('profile-updated');
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', message: '❌ Terjadi kesalahan saat memperbarui data!');
            $this->dispatch('refresh-data');
            $this->showUserDelete = false;

        }
    }

    public function validationMessages()
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
            'tahun_angkatan.required' => 'Tahun angkatan wajib diisi!',
            'tahun_angkatan.integer' => 'Tahun angkatan harus berupa angka!',
            'tahun_angkatan.min' => 'Tahun angkatan tidak boleh kurang dari tahun 1960!',
            'tahun_angkatan.max' => 'Tahun angkatan tidak boleh melebihi tahun sekarang!',
            'prodi_id.required' => 'Program studi wajib dipilih!',
            'prodi_id.exists' => 'Program studi yang dipilih tidak valid!',
            'excel_file.required' => 'File Excel wajib diunggah!',
            'excel_file.file' => 'File Excel harus berupa file yang valid!',
            'excel_file.mimes' => 'File Excel harus berformat .xlsx, .xls, atau .csv!',
            'status.required' => 'Status pengguna wajib dipilih!',
            'status.in' => 'Status yang dipilih tidak sesuai dengan kategori yang diizinkan!',
        ];
    }

    public function resetInput(
        // $keepProdi = false
        )
    {
        $fields = [
            'user_id',
            // 'email', 'password', 'name', 'nip', 'nitk',
            // 'nidn', 'nidk', 'nim', 'tahun_angkatan',
            'roleType',
        ];

        // if (! $keepProdi) {
        //     $fields = array_merge($fields, ['prodi_id', 'prodiNameSearch', 'prodiResults']);
        // }

        $this->reset($fields);
        $this->resetErrorBag();
    }
}
