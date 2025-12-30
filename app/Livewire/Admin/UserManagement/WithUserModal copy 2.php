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

use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;

use Illuminate\Support\LazyCollection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


trait WithUserModal
{

    use WithFileUploads;
    
    public $showUserModal = false;
    public $isEditing = false;
    public $roleType;

    public $userId, $email, $password, $name, $nip, $nitk, $nidn, $nidk, $nim, $tahun_angkatan;

    public $excelFile;
    public array $parsedRows = [];
    public array $rowErrors  = [];

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



    public function importExcel()
    {
        if ($this->roleType !== 'file') {
            return;
        }

        $this->validate([
            'excelFile' => 'required|file|mimes:xlsx,xls|max:10240',
        ], [
            'excelFile.required' => 'File Excel wajib diupload.',
            'excelFile.file' => 'File harus berupa file yang valid.',
            'excelFile.mimes' => 'File harus berformat .xlsx atau .xls',
            'excelFile.max' => 'Ukuran file tidak boleh lebih dari 10MB.',
        ]);

        if (!$this->excelFile) {
            throw new \Exception('File tidak dapat dibaca');
        }

        // Load spreadsheet
        $spreadsheet = IOFactory::load($this->excelFile->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();

        // Convert to array
        $allData = $worksheet->toArray();

        if (empty($allData)) {
            throw new \Exception('File Excel kosong');
        }

        // Cari baris pertama yang berisi sesuatu (untuk header)
        $firstNonEmptyIndex = null;
        foreach ($allData as $idx => $row) {
            if (collect($row)->filter(fn($v) => $v !== null && trim((string) $v) !== '')->count() > 0) {
                $firstNonEmptyIndex = $idx;
                break;
            }
        }

        // Jika tidak ada baris berisi data => Excel benar-benar kosong
        if ($firstNonEmptyIndex === null) {
            throw new \Exception('File Excel kosong');
        }

        // Gunakan baris pertama non-kosong sebagai header
        $headerRow = $allData[$firstNonEmptyIndex];
        $headers = [];
        foreach ($headerRow as $idx => $headerName) {
            if ($headerName !== null && trim((string) $headerName) !== '') {
                $headers[$idx] = Str::lower(trim($headerName));
            }
        }

        if (empty($headers)) {
            throw new \Exception('Header tidak ditemukan');
        }

        \Log::info('Excel headers (found at row ' . ($firstNonEmptyIndex + 1) . '):', $headers);

        // Data rows dimulai setelah baris header
        $dataRows = array_slice($allData, $firstNonEmptyIndex + 1);
        
        $success = 0;
        $errors  = [];
        $rowNumber = 1;

        foreach ($dataRows as $excelRowIndex => $rowData) {
            $rowNumber = $excelRowIndex + 2; // Baris Excel dimulai dari 1, data dari baris 2

            // Skip empty rows
            if (collect($rowData)->filter(fn($v) => $v !== null && $v !== '')->count() === 0) {
                continue;
            }

            try {
                // Build data dengan header keys
                $data = [];
                foreach ($headers as $colIndex => $headerName) {
                    $data[$headerName] = isset($rowData[$colIndex]) ? trim($rowData[$colIndex]) : '';
                }

                \Log::info("Row $rowNumber data:", $data);

                // Validate
                $this->validateExcelRow($data, $rowNumber);

                // Map to properties
                $this->mapExcelToProperties(collect($data));

                // Save
                $this->saveUserInternal();

                $success++;

            } catch (\Throwable $e) {
                $errors[] = [
                    'row'   => $rowNumber,
                    'email' => $data['email'] ?? '-',
                    'error' => $e->getMessage(),
                ];

                \Log::error("Row $rowNumber error:", ['error' => $e->getMessage(), 'data' => $data ?? []]);
                continue;
            }
        }

        session()->put('import_errors', $errors);

        $this->dispatch(
            'toast',
            message: "✅ Import selesai. Berhasil: $success | Gagal: " . count($errors)
        );
    }

    private function validateExcelRow(array $data, int $rowNumber)
    {
        $rules = [
            'email' => ['required', 'email', 'unique:users,email'],
            'name'  => ['required', 'string'],
            'role'  => ['required', 'in:admin,dosen,mahasiswa'],
        ];

        if ($data['role'] === 'admin') {
            $rules['nip']  = ['required', 'unique:admins,nip', 'unique:dosens,nip', 'unique:mahasiswas,nim'];
            $rules['nitk'] = ['nullable', 'unique:admins,nitk'];
        }

        if ($data['role'] === 'dosen') {
            $rules['nip']  = ['required', 'unique:dosens,nip', 'unique:admins,nip', 'unique:mahasiswas,nim'];
            $rules['nidn'] = ['nullable', 'unique:dosens,nidn'];
            $rules['nidk'] = ['nullable', 'unique:dosens,nidk'];
        }

        if ($data['role'] === 'mahasiswa') {
            $rules['nim'] = ['required', 'unique:mahasiswas,nim', 'unique:admins,nip', 'unique:dosens,nip'];
            $rules['tahun angkatan'] = ['required', 'integer', 'min:1900', 'max:' . date('Y')];
        }

        $validator = Validator::make($data, $rules, [], [
            'email' => 'Email',
            'nip' => 'NIP',
            'nitk' => 'NITK',
            'nidn' => 'NIDN',
            'nidk' => 'NIDK',
            'nim' => 'NIM',
            'tahun angkatan' => 'Tahun Angkatan',
        ]);

        if ($validator->fails()) {
            throw new \Exception(
                collect($validator->errors()->messages())
                    ->map(fn($v, $k) => "$k: " . implode(', ', $v))
                    ->implode(' | ')
            );
        }
    }

    private function mapExcelToProperties($data)
    {
        $this->email = $data['email'] ?? null;
        $this->password = $data['password'] ?? 'password123';
        $this->name = $data['name'] ?? null;

        $this->nip  = $data['nip'] ?? null;
        $this->nitk = $data['nitk'] ?? null;
        $this->nidn = $data['nidn'] ?? null;
        $this->nidk = $data['nidk'] ?? null;
        $this->nim  = $data['nim'] ?? null;

        $this->tahun_angkatan = $data['tahun angkatan'] ?? null;
        $this->roleType = strtolower(trim($data['role'] ?? ''));

        // Lookup program studi by nama_prodi
        $prodiName = $data['program studi'] ?? null;
        if ($prodiName) {
            $this->prodi_id = \App\Models\Prodi::where('nama_prodi', $prodiName)->value('id');
            
            if (!$this->prodi_id) {
                throw new \Exception("Program studi '$prodiName' tidak ditemukan di database");
            }
        } else {
            throw new \Exception('Program studi wajib diisi');
        }
    }

    private function saveUserInternal()
    {
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
        }
        elseif ($this->roleType === 'dosen') {
            Dosen::create([
                'user_id' => $user->id,
                'nip' => $this->nip,
                'nidn' => $this->nidn,
                'nidk' => $this->nidk,
                'name' => $this->name,
                'prodi_id' => $this->prodi_id
            ]);
        }
        elseif ($this->roleType === 'mahasiswa') {
            Mahasiswa::create([
                'user_id' => $user->id,
                'nim' => $this->nim,
                'tahun_angkatan' => $this->tahun_angkatan,
                'name' => $this->name,
                'prodi_id' => $this->prodi_id,
            ]);
        }

        $this->resetInput();
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
            // 'nip.required' => 'NIP wajib diisi untuk Admin dan Dosen.',
            'nip.unique' => 'NIP ini sudah terdaftar.',
            // 'nitk.required' => 'NIDK wajib diisi untuk Admin.',
            'nitk.unique' => 'NIDK ini sudah terdaftar.',
            // 'nidn.required' => 'NIDN wajib diisi untuk Dosen.',
            'nidn.unique' => 'NIDN ini sudah terdaftar.',
            // 'nidK.required' => 'NIDK wajib diisi untuk Dosen.',
            'nidK.unique' => 'NIDK ini sudah terdaftar.',
            'nim.required' => 'NIM wajib diisi untuk Mahasiswa.',
            'nim.unique' => 'NIM ini sudah terdaftar.',
            'tahun_angkatan.required' => 'Tahun masuk wajib diisi.',
            'tahun_angkatan.integer' => 'Tahun masuk harus berupa angka.',
            'tahun_angkatan.min' => 'Tahun masuk tidak valid.',
            'tahun_angkatan.max' => 'Tahun masuk tidak boleh melebihi tahun sekarang.',
        ];
    }

    public function resetInput()
    {
        $this->reset([
            'userId', 'email', 'password', 'name', 'nip', 'nitk', 'nidn', 'nidk', 'nim', 'tahun_angkatan', 
            'prodi_id', 'prodi_name_search', 'prodi_results', 
            'roleType'
        ]);
        $this->resetErrorBag(); 
    }
}
