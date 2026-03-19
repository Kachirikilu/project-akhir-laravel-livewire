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

trait WithUserModal
{
    public $showUserModal = false;
    public $isEditing = false;
    public $roleType;

    public $userId, $email, $password, $name, $nip, $nim, $tahun_angkatan;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'nullable|min:8',
        'name' => 'required|string|max:255',
        'nip' => 'nullable|string|max:50',
        'nim' => 'nullable|string|max:50',
        'tahun_angkatan' => 'nullable|integer',
        'prodi_id' => 'required|exists:prodis,id',
    ];

    // Listener untuk mendeteksi modal dibuka/ditutup
    // protected $listeners = ['loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    // Hook untuk mendeteksi saat showUserModal berubah
    public function updatedShowUserModal($value)
    {
        if ($value === true) {
            $this->loadDraft();
        } elseif ($value === false) {
            $this->saveToDraft();
        }
    }

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
        // $this->resetInput();
        $this->roleType = $role;
        $this->isEditing = false;
        $this->showUserModal = true;
        $this->js("Flux.modal('user-modal').show()");
        $this->updatedProdiNameSearch(''); 
    }

 

    public function editUser($id)
    {
        if (!Auth::user()->admin) {
            $this->dispatch('toast', message: '❌ Hanya admin yang dapat mengedit pengguna.');
            return;
        }

        $this->resetInput();
        $this->isEditing = true;
        $this->showUserModal = true;
        $this->js("Flux.modal('user-modal').show()");

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

        if ($user->admin) {
            $this->roleType = 'admin';
            $this->name = $user->admin->name;
        } elseif ($user->dosen) {
            $this->roleType = 'dosen';
            $this->name = $user->dosen->name;
            $this->nip = $user->dosen->nip;
        } elseif ($user->mahasiswa) {
            $this->roleType = 'mahasiswa';
            $this->name = $user->mahasiswa->name;
            $this->nim = $user->mahasiswa->nim;
            $this->tahun_angkatan = $user->mahasiswa->tahun_angkatan;
        }
    }

    public function resetInput()
    {
        $this->reset([
            'userId', 'email', 'password', 'name', 'nip', 'nim', 'tahun_angkatan', 
            'prodi_id', 'prodi_name_search', 'prodi_results', 
            'roleType'
        ]);
        $this->resetErrorBag(); 
    }

    public function inputModalUser($isEditing) {
        $rules = [
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->userId)],
            'name' => 'required|string|max:255',
            'prodi_id' => 'required|exists:prodis,id', 
        ];

        $rules['password'] = $isEditing ? 'nullable|min:8' : 'required|min:8';

        if ($this->roleType === 'dosen') {
            $rules['nip'] = ['required', Rule::unique('dosens', 'nip')->ignore($this->userId, 'user_id')]; 
        } elseif ($this->roleType === 'mahasiswa') {
            $rules['nim'] = ['required', Rule::unique('mahasiswas', 'nim')->ignore($this->userId, 'user_id')]; 
            $rules['tahun_angkatan'] = 'required|integer|min:1900|max:' . date('Y');
        }

        $this->validate($rules, $this->validationMessages());
    }

    public function saveUser()
    {
        $this->inputModalUser(false);
        
        $user = User::create([
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        if ($this->roleType === 'admin') {
            Admin::create(['user_id' => $user->id, 'name' => $this->name, 'prodi_id' => $this->prodi_id]);
        } elseif ($this->roleType === 'dosen') {
            Dosen::create(['user_id' => $user->id, 'nip' => $this->nip, 'name' => $this->name, 'prodi_id' => $this->prodi_id]);
        } elseif ($this->roleType === 'mahasiswa') {
            Mahasiswa::create([
                'user_id' => $user->id,
                'nim' => $this->nim,
                'tahun_angkatan' => $this->tahun_angkatan,
                'name' => $this->name,
                'prodi_id' => $this->prodi_id,
            ]);
        }

        $this->deleteDraft(); // Hapus draft setelah berhasil menyimpan
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
            $user->admin->update(['name' => $this->name, 'prodi_id' => $this->prodi_id]);
        } elseif ($this->roleType === 'dosen') {
            $user->dosen->update(['name' => $this->name, 'nip' => $this->nip, 'prodi_id' => $this->prodi_id]);
        } elseif ($this->roleType === 'mahasiswa') {
            $user->mahasiswa->update([
                'name' => $this->name,
                'nim' => $this->nim,
                'tahun_angkatan' => $this->tahun_angkatan,
                'prodi_id' => $this->prodi_id,
            ]);
        }

        $this->deleteDraft(); // Hapus draft setelah berhasil update
        $this->showUserModal = false;
        $this->dispatch('toast', message: '✅ Data pengguna berhasil diperbarui.');
        
        if (Auth::id() === $user->id) {
            $this->dispatch('profile-updated');
        }
    }

    public function saveToDraft()
    {
        if ($this->isEditing) {
            return;
        }
        if (!$this->email && !$this->name && !$this->nip && !$this->nim && !$this->tahun_angkatan && !$this->prodi_id) {
            return;
        }

        $draftData = [
            'email' => $this->email,
            'name' => $this->name,
            'nip' => $this->nip,
            'nim' => $this->nim,
            'tahun_angkatan' => $this->tahun_angkatan,
            'prodi_id' => $this->prodi_id,
            'prodi_name_search' => $this->prodi_name_search ?? '',
            'roleType' => $this->roleType,
            'isEditing' => $this->isEditing,
        ];

        $key = 'draft_user_' . auth()->id() . '_' . ($this->roleType ?? 'unknown');
        \Illuminate\Support\Facades\Cache::put($key, $draftData, now()->addHours(24));
    }

    public function loadDraft()
    {
        if ($this->isEditing) {
            return;
        }

        $key = 'draft_user_' . auth()->id() . '_' . ($this->roleType ?? 'unknown');
        if (\Illuminate\Support\Facades\Cache::has($key)) {
            $data = \Illuminate\Support\Facades\Cache::get($key);
            
            $this->email = $data['email'] ?? '';
            $this->name = $data['name'] ?? '';
            $this->nip = $data['nip'] ?? '';
            $this->nim = $data['nim'] ?? '';
            $this->tahun_angkatan = $data['tahun_angkatan'] ?? '';
            $this->prodi_id = $data['prodi_id'] ?? '';
            $this->prodi_name_search = $data['prodi_name_search'] ?? '';
        }
    }

    public function deleteDraft()
    {
        $key = 'draft_user_' . auth()->id() . '_' . ($this->roleType ?? 'unknown');
        \Illuminate\Support\Facades\Cache::forget($key);
    }

    public function validationMessages()
    {
        return [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'prodi_id.required' => 'Program studi wajib dipilih.',
            'prodi_id.exists' => 'Program studi yang dipilih tidak valid.',
            'nip.required' => 'NIP wajib diisi untuk Dosen.',
            'nip.unique' => 'NIP ini sudah terdaftar.',
            'nim.required' => 'NIM wajib diisi untuk Mahasiswa.',
            'nim.unique' => 'NIM ini sudah terdaftar.',
            'tahun_angkatan.required' => 'Tahun masuk wajib diisi.',
            'tahun_angkatan.integer' => 'Tahun masuk harus berupa angka.',
            'tahun_angkatan.min' => 'Tahun masuk tidak valid.',
            'tahun_angkatan.max' => 'Tahun masuk tidak boleh melebihi tahun sekarang.',
        ];
    }
}
