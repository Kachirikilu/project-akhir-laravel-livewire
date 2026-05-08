<?php

namespace App\Livewire\Admin\UserManagement;

use App\Exports\UserExport;
use App\Livewire\Global\HasToast;
use App\Models\Auth\Admin;
use App\Models\Auth\Dosen;
use App\Models\Auth\Mahasiswa;
use App\Models\Auth\User;
use App\Models\ProgramStudi\Departemen;
use App\Models\ProgramStudi\Fakultas;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\LazyCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

trait WithUserExcel
{
    use HasToast;
    use WithFileUploads;

    public $excel_file;

    public array $parsedRows = [];

    public array $rowErrors = [];

    public function exportUserExcel()
    {
        $queryUser = $this->inputUserSearch();
        $this->buttonUserFilter($queryUser);

        $queryUser->with(['pendidikans' => fn ($q) => $q->orderByJenjang()]);
        if (! empty($this->switchTable)) {
            $queryUser->whereHas($this->switchTable);
        }

        $univ = env('UNIVERSITAS');
        $UNIV = strtoupper(env('UNIVERSITAS'));

        $filter = '';
        if ($this->filterStatus == 'aktif') {
            $filter = ' '.ucwords($this->filterStatus);
        } elseif ($this->filterStatus == 'non-aktif') {
            $filter = ' Tidak Aktif';
        }

        $tag = ucwords($this->switchTable ?? 'User').$filter;
        $TAG = strtoupper($tag);

        $fileName = 'Data_'.$tag.'_'.$univ.'_'.now()->format('Y-m-d').'.xlsx';
        $title = 'DATA '.$TAG.' '.$UNIV;

        if ($this->filterStatus !== '') {
            if ($this->selectedFkId) {
                $fk = Fakultas::find($this->selectedFkId);
                $fileName = 'Data_'.$tag.'_'.$fk->fakultas_fk.'_'.$univ.'_'.now()->format('Y-m-d').'.xlsx';
                $title = 'DATA '.$TAG.' '.strtoupper($fk->fakultas_fk).' '.$UNIV;
            } elseif ($this->selectedDpId) {
                $dp = Departemen::find($this->selectedDpId);
                $fileName = 'Data_'.$tag.'_'.$dp->departemen_dp.'_'.$univ.'_'.now()->format('Y-m-d').'.xlsx';
                $title = 'DATA '.$TAG.' '.strtoupper($dp->departemen_dp).' '.$UNIV;
            } elseif ($this->selectedPrId) {
                $pr = Prodi::find($this->selectedPrId);
                $fileName = 'Data_'.$tag.'_'.$pr->prodi.'_'.$univ.'_'.now()->format('Y-m-d').'.xlsx';
                $title = 'DATA '.$TAG.' '.strtoupper($pr->prodi).' '.$UNIV;
            }
        } else {
            $pr = Auth::user()->prodi;
            $fileName = 'Data_'.$tag.'_'.$pr.'_'.$univ.'_'.now()->format('Y-m-d').'.xlsx';
            $title = 'DATA '.$TAG.' '.strtoupper($pr).' '.$UNIV;
        }


        return Excel::download(new UserExport($queryUser, $this->switchTable, $title), $fileName);
    }

    public function importUserExcel()
    {
        if (! $this->AuthCheck()) {
            return;
        }
        if ($this->roleType !== 'file') {
            return;
        }

        $this->reset(['parsedRows', 'rowErrors']);

        $this->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        $spreadsheet = IOFactory::load($this->excel_file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();
        $allData = $worksheet->toArray();

        if (empty($allData)) {
            throw new \Exception('File Excel kosong');
        }

        /** ===============================
         *  CARI HEADER
         *  =============================== */
        $headerRowIndex = null;

        foreach ($allData as $i => $row) {
            if (collect($row)->filter(fn ($v) => trim((string) $v) !== '')->count() > 0) {
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
            if (trim((string) $value) !== '') {
                $headers[$idx] = Str::lower(trim($value));
            }
        }

        /** ===============================
         *  PARSE DATA KE TABLE PREVIEW
         *  =============================== */
        $dataRows = array_slice($allData, $headerRowIndex + 1);

        foreach ($dataRows as $excelIndex => $row) {

            if (collect($row)->filter(fn ($v) => trim((string) $v) !== '')->count() === 0) {
                continue;
            }

            $data = [];
            foreach ($headers as $col => $header) {
                $data[$header] = trim((string) ($row[$col] ?? ''));
            }

            $this->parsedRows[] = [
                'email' => $data['email'] ?? '',
                'password' => $data['password'] ?? 'password123',
                'name' => $data['name'] ?? '',
                'nip' => $data['nip'] ?? '',
                'nitk' => $data['nitk'] ?? '',
                'nidn' => $data['nidn'] ?? '',
                'nidk' => $data['nidk'] ?? '',
                'nim' => $data['nim'] ?? '',
                'nik' => $data['nik'] ?? '',
                'angkatan' => $data['tahun angkatan'] ?? '',
                // 'program_id'    => $this->pr_id ?? '',
                'role' => strtolower($data['role'] ?? ''),
            ];
        }

        $this->toast(text: 'File Excel berhasil dimuat. Silakan periksa data!');
    }

    public function updatedExcelFile()
    {
        if ($this->roleType !== 'file') {
            return;
        }

        if (! $this->excel_file) {
            return;
        }

        try {
            $this->importUserExcel();
        } catch (\Throwable $e) {
            $this->toast(text: $e->getMessage(), variant: 'danger');

        }
    }

    public function removeParsedRow($index)
    {
        if (isset($this->parsedRows[$index])) {
            unset($this->parsedRows[$index]);
            $this->parsedRows = array_values($this->parsedRows);
            $this->toast(text: 'Baris dihapus!');
        }
    }

    public function saveAllRows()
    {
        $rules = [
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
            'pr_id' => 'required|exists:prodis,id',
        ];
        $this->validate($rules, $this->validationMessagesUser());

        if (empty($this->parsedRows)) {
            $this->toast(text: 'Tidak ada data untuk disimpan!', variant: 'warning');

            return;
        }

        try {
            $this->processImport();
        } catch (\Throwable $e) {
            $this->dispatch('toast', message: '❌ '.$e->getMessage());
        }
    }

    public function processImport()
    {
        $successCount = 0;
        $this->rowErrors = [];
        $successfulIndices = [];
        $originalRoleType = $this->roleType;

        foreach ($this->parsedRows as $index => $row) {
            try {
                // Set roleType and selected_id_user temporarily for inputModalUser validation
                $this->roleType = $row['role'];
                $this->selected_id_user = null;

                // Prepare data for validation
                $dataToValidate = $row;
                $dataToValidate['pr_id'] = $this->pr_id;
                // Excel doesn't have status usually, default to 'Aktif'
                if (empty($dataToValidate['status'])) {
                    $dataToValidate['status'] = 'Aktif';
                }

                // Reuse the validation logic from WithUserModal
                $validatedData = $this->inputModalUser(false, $dataToValidate);

                // If validation passes, save the user
                $this->saveUserFromExcel($validatedData, $row['role']);

                $successfulIndices[] = $index;
                $successCount++;
            } catch (ValidationException $e) {
                // Store specific field errors for this row
                $this->rowErrors[$index] = $e->errors();
            } catch (\Throwable $e) {
                // Store generic error if it's not a validation exception
                $this->rowErrors[$index] = ['general' => [$e->getMessage()]];
            }
        }

        // Restore roleType
        $this->roleType = $originalRoleType;

        // Remove successful rows from parsedRows
        foreach (array_reverse($successfulIndices) as $idx) {
            unset($this->parsedRows[$idx]);
            // Also remove any old errors for this row if they existed
            unset($this->rowErrors[$idx]);
        }

        // Re-index arrays to keep them clean for the UI
        $this->parsedRows = array_values($this->parsedRows);

        // Re-map errors to new indices
        $newRowErrors = [];
        $i = 0;
        foreach ($this->rowErrors as $oldIdx => $errors) {
            $newRowErrors[$i] = $errors;
            $i++;
        }
        $this->rowErrors = $newRowErrors;

        $failCount = count($this->parsedRows);
        $messageText = "Import selesai | Berhasil: $successCount | Gagal: $failCount";

        if ($failCount === 0) {
            $this->toast(text: $messageText);
            $this->reset('excel_file');
            $this->showUserModal = false;
        } else {
            $this->toast(text: $messageText, variant: 'warning');
        }

        $this->dispatch('refresh-data-user');
    }

    private function saveUserFromExcel($validated, $role)
    {
        DB::transaction(function () use ($validated, $role) {
            $user = User::create([
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            if ($role === 'admin') {
                Admin::create([
                    'user_id' => $user->id,
                    'name' => $validated['name'],
                    'nip' => $validated['nip'],
                    'nitk' => $validated['nitk'] ?? null,
                    'nik' => $validated['nik'],
                    'pr_id' => $validated['pr_id'],
                    'status' => $validated['status'],
                ]);
            } elseif ($role === 'dosen') {
                Dosen::create([
                    'user_id' => $user->id,
                    'name' => $validated['name'],
                    'nip' => $validated['nip'],
                    'nidn' => $validated['nidn'] ?? null,
                    'nidk' => $validated['nidk'] ?? null,
                    'nik' => $validated['nik'],
                    'pr_id' => $validated['pr_id'],
                    'status' => $validated['status'],
                ]);
            } elseif ($role === 'mahasiswa') {
                Mahasiswa::create([
                    'user_id' => $user->id,
                    'name' => $validated['name'],
                    'nim' => $validated['nim'],
                    'nik' => $validated['nik'],
                    'angkatan' => $validated['angkatan'],
                    'pr_id' => $validated['pr_id'],
                    'status' => $validated['status'],
                ]);
            }
        });
    }
}
