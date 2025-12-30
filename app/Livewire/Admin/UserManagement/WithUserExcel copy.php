<?php

namespace App\Livewire\Admin\UserManagement;

use App\Models\User;
use App\Models\Admin;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use Illuminate\Support\Facades\Hash;

use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;

// use Illuminate\Support\LazyCollection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

trait WithUserExcel
{

    use WithFileUploads;

    public $excelFile;
    public array $parsedRows = [];
    public array $rowErrors  = [];

    public function importExcel()
    {
        if ($this->roleType !== 'file') {
            return;
        }

        $this->reset(['parsedRows', 'rowErrors']);

        $this->validate([
            'excelFile' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        $spreadsheet = IOFactory::load($this->excelFile->getRealPath());
        $worksheet   = $spreadsheet->getActiveSheet();
        $allData     = $worksheet->toArray();

        if (empty($allData)) {
            throw new \Exception('File Excel kosong');
        }

        /** ===============================
         *  CARI HEADER
         *  =============================== */
        $headerRowIndex = null;

        foreach ($allData as $i => $row) {
            if (collect($row)->filter(fn ($v) => trim((string)$v) !== '')->count() > 0) {
                $headerRowIndex = $i;
                break;
            }
        }

        if ($headerRowIndex === null) {
            throw new \Exception('Header tidak ditemukan');
        }

        $rawHeader = $allData[$headerRowIndex];

        $headers = [];
        foreach ($rawHeader as $idx => $value) {
            if (trim((string)$value) !== '') {
                $headers[$idx] = Str::lower(trim($value));
            }
        }

        /** ===============================
         *  PARSE DATA KE TABLE PREVIEW
         *  =============================== */
        $dataRows = array_slice($allData, $headerRowIndex + 1);

        foreach ($dataRows as $excelIndex => $row) {

            if (collect($row)->filter(fn ($v) => trim((string)$v) !== '')->count() === 0) {
                continue;
            }

            $data = [];
            foreach ($headers as $col => $header) {
                $data[$header] = trim((string)($row[$col] ?? ''));
            }

            $this->parsedRows[] = [
                'email'            => $data['email'] ?? '',
                'password'         => $data['password'] ?? 'password123',
                'name'             => $data['name'] ?? '',
                'nip'              => $data['nip'] ?? '',
                'nitk'             => $data['nitk'] ?? '',
                'nidn'             => $data['nidn'] ?? '',
                'nidk'             => $data['nidk'] ?? '',
                'nim'              => $data['nim'] ?? '',
                'tahun_angkatan'   => $data['tahun angkatan'] ?? '',
                'program_studi'    => $data['program studi'] ?? '',
                'role'             => strtolower($data['role'] ?? ''),
            ];
        }

        $this->dispatch(
            'toast',
            message: '📄 File Excel berhasil dimuat. Silakan periksa data.'
        );
    }

    public function updatedExcelFile()
    {
        if ($this->roleType !== 'file') {
            return;
        }

        if (!$this->excelFile) {
            return;
        }

        try {
            $this->importExcel();
        } catch (\Throwable $e) {
            $this->dispatch('toast', message: '❌ ' . $e->getMessage());
        }
    }

    public function removeParsedRow($index)
    {
        if (isset($this->parsedRows[$index])) {
            unset($this->parsedRows[$index]);
            $this->parsedRows = array_values($this->parsedRows);
            $this->dispatch('toast', message: '🗑️ Baris dihapus.');
        }
    }

    public function saveAllRows()
    {
        if (empty($this->parsedRows)) {
            $this->dispatch('toast', message: '⚠️ Tidak ada data untuk disimpan.');
            return;
        }

        try {
            $this->processImport();
            $this->reset(['parsedRows', 'rowErrors', 'excelFile']);
            $this->showUserModal = false;
        } catch (\Throwable $e) {
            $this->dispatch('toast', message: '❌ ' . $e->getMessage());
        }
    }

    public function processImport()
    {
        $success = 0;
        $this->rowErrors = [];

        foreach ($this->parsedRows as $index => $row) {
            try {
                $this->validateExcelRow($row, $index + 2);
                $this->mapExcelToProperties(collect($row));
                $this->saveUserInternal();

                $success++;
            } catch (\Throwable $e) {
                $this->rowErrors[$index] = $e->getMessage();
            }
        }

        $this->dispatch(
            'toast',
            message: "✅ Import selesai | Berhasil: $success | Gagal: " . count($this->rowErrors)
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
            $rules['tahun_angkatan'] = ['required', 'integer', 'min:1900', 'max:' . date('Y')];
        }

        $validator = Validator::make($data, $rules, [], [
            'email' => 'Email',
            'nip' => 'NIP',
            'nitk' => 'NITK',
            'nidn' => 'NIDN',
            'nidk' => 'NIDK',
            'nim' => 'NIM',
            'tahun_angkatan' => 'Tahun Angkatan',
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

        $this->tahun_angkatan = $data['tahun_angkatan'] ?? null;
        $this->roleType = strtolower(trim($data['role'] ?? ''));

        $prodiName = $data['program_studi'] ?? null;
        if ($prodiName) {
            $this->prodi_id = Prodi::where('nama_prodi', $prodiName)->value('id');
            
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

}
