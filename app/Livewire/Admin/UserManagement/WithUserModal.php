<?php

namespace App\Livewire\Admin\UserManagement;

use App\Models\User;
use App\Models\Admin;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

// use Livewire\WithFileUploads;
// use Livewire\Attributes\Validate;

// use Illuminate\Support\LazyCollection;
// use PhpOffice\PhpSpreadsheet\IOFactory;
// use Illuminate\Support\Str;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Validator;


trait WithUserModal
{

    // use WithFileUploads;
    
    public $showUserModal = false;
    public $isEditing = false;
    public $roleType;

    public $userId, $email, $password, $name, $nip, $nitk, $nidn, $nidk, $nim, $tahun_angkatan;

    // public $excelFile;
    // public array $parsedRows = [];
    // public array $rowErrors  = [];

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

    // public function resetModalFields()
    // {
    //     $this->reset([
    //         'userId', 'email', 'password', 'name', 'nip', 'nim', 
    //         'tahun_angkatan', 'prodi_id', 'roleType', 'isEditing', 
    //         'prodi_name_search', 'showUserModal'
    //     ]);
        
    //     $this->resetValidation();
    // }

    public function showAddModal($role)
    {
        if ($this->isEditing) {
            $this->resetInput();
        }

        $this->resetValidation();
        $this->resetErrorBag();
        $this->isEditing = false;
        $this->roleType = $role;
        $this->showUserModal = true;
        $this->js("Flux.modal('user-modal').show()");
        $this->updatedProdiNameSearch($this->prodi_name_search); 
        
    }

    public function editUser($id)
    {
        if (!Auth::user()->admin) {
            $this->dispatch('toast', message: '❌ Hanya admin yang dapat mengedit pengguna.');
            return;
        }

        $this->resetInput();
        $this->resetValidation();

        $this->resetErrorBag();
        $this->showUserModal = true;
        $this->js("Flux.modal('user-modal').show()");
        $this->isEditing = true;

        $user = User::with(['admin', 'dosen', 'mahasiswa'])->findOrFail($id);
        $this->userId = $user->id;
        $this->email = $user->email;
        $this->prodi_id = $user->admin->prodi_id ?? $user->dosen->prodi_id ?? $user->mahasiswa->prodi_id ?? null; 

        if ($this->prodi_id) {
            $prodi = Prodi::find($this->prodi_id);
            $this->prodi_name_search = $prodi ? $prodi->nama_prodi : '';
        } else {
            $this->prodi_name_search = '';
        }
        $this->getProdibyUser();

        $this->name = $user->name;
        $this->roleType = strtolower($user->role);

        if (!$user->mahasiswa) {
            $this->nip = $user->identity;
            if ($user->admin) {
                $this->nitk = $user->identity2;
            } else {
                $this->nidn = $user->identity2;
                $this->nidk = $user->identity3;
            }
        } else {
            $this->nim = $user->identity;
            $this->tahun_angkatan = $user->mahasiswa->tahun_angkatan;
        }
    }

    public function inputModalUser($isEditing)
    {
        $rules = [
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->userId),
            ],
            'name'     => 'required|string|max:255',
            'prodi_id' => 'required|exists:prodis,id',
            'password' => $isEditing ? 'nullable|min:8' : 'required|min:8',
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
                'required|integer|min:1900|max:' . date('Y');
        }

        $this->validate($rules, $this->validationMessages());
    }
    private function uniqueRule(string $table, string $column)
    {
        return $this->userId
            ? Rule::unique($table, $column)->ignore($this->userId, 'user_id')
            : Rule::unique($table, $column);
    }

    public function saveUser()
    {
        $this->inputModalUser(false);
        
        $user = User::create([
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        if ($this->roleType === 'admin') {
            Admin::create([
                'user_id' => $user->id,
                'name' => $this->name,
                'nip' => $this->nip,
                'nitk' => $this->nitk,
                'prodi_id' => $this->prodi_id
            ]);
        } elseif ($this->roleType === 'dosen') {
            Dosen::create([
                'user_id' => $user->id,
                'nip' => $this->nip,
                'nidn' => $this->nidn,
                'nidk' => $this->nidk,
                'name' => $this->name,
                'prodi_id' => $this->prodi_id
            ]);
        } elseif ($this->roleType === 'mahasiswa') {
            Mahasiswa::create([
                'user_id' => $user->id,
                'nim' => $this->nim,
                'tahun_angkatan' => $this->tahun_angkatan,
                'name' => $this->name,
                'prodi_id' => $this->prodi_id,
            ]);
        }

        $this->resetInput();
        $this->showUserModal = false;
        $this->dispatch('toast', message: '✅ Pengguna berhasil ditambahkan.');
    }

    public function updateUser()
    {
        $this->inputModalUser(true);

        $user = User::findOrFail($this->userId);
        $user->update(['email' => $this->email]);

        if ($this->password) {
            $user->update(['password' => Hash::make($this->password)]);
        }

        if ($this->roleType === 'admin') {
            $user->admin->update(
                [
                    'name' => $this->name,
                    'nip' => $this->nip,
                    'nitk' => $this->nitk,
                    'prodi_id' => $this->prodi_id
                ]
            );
        } elseif ($this->roleType === 'dosen') {
            $user->dosen->update(
                [
                    'name' => $this->name,
                    'nip' => $this->nip,
                    'nidn' => $this->nidn,
                    'nidk' => $this->nidk,
                    'prodi_id' => $this->prodi_id
                ]
            );
        } elseif ($this->roleType === 'mahasiswa') {
            $user->mahasiswa->update([
                'name' => $this->name,
                'nim' => $this->nim,
                'tahun_angkatan' => $this->tahun_angkatan,
                'prodi_id' => $this->prodi_id,
            ]);
        }

        $this->showUserModal = false;
        $this->dispatch('toast', message: '✅ Data pengguna berhasil diperbarui.');
        
        if (Auth::id() === $user->id) {
            $this->dispatch('profile-updated');
        }
    }

    public function validationMessages()
    {
        return [
            'email.required' => 'Alamat email wajib diisi!',
            'email.email' => 'Format email tidak valid!',
            'email.unique' => 'Email ini sudah terdaftar di sistem!',
            'name.required' => 'Nama lengkap wajib diisi!',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter!',
            'password.required' => 'Password wajib diisi!',
            'password.min' => 'Password minimal harus 8 karakter!',
            'prodi_id.required' => 'Program studi wajib dipilih!',
            'prodi_id.exists' => 'Program studi yang dipilih tidak valid!',
            'nip.required' => 'NIP wajib diisi untuk Admin dan Dosen!',
            'nip.unique' => 'NIP ini sudah terdaftar!',
            'nitk.unique' => 'NITK ini sudah terdaftar!',
            'nidn.unique' => 'NIDN ini sudah terdaftar!',
            'nidk.unique' => 'NIDK ini sudah terdaftar!',
            'nim.required' => 'NIM wajib diisi untuk Mahasiswa!',
            'nim.unique' => 'NIM ini sudah terdaftar!',
            'tahun_angkatan.required' => 'Tahun masuk wajib diisi!',
            'tahun_angkatan.integer' => 'Tahun masuk harus berupa angka!',
            'tahun_angkatan.min' => 'Tahun masuk tidak valid!',
            'tahun_angkatan.max' => 'Tahun masuk tidak boleh melebihi tahun sekarang!',
            'excel_file.required' => 'File Excel wajib diunggah!',
            'excel_file.file' => 'File Excel harus berupa file yang valid!',
        ];
    }

    public function resetInput($keepProdi = false)
    {
        $fields = [
            'userId', 'email', 'password', 'name', 'nip', 'nitk', 
            'nidn', 'nidk', 'nim', 'tahun_angkatan', 'roleType'
        ];

        if (!$keepProdi) {
            $fields = array_merge($fields, ['prodi_id', 'prodi_name_search', 'prodi_results']);
        }

        $this->reset($fields);
        $this->resetErrorBag(); 
    }
}
