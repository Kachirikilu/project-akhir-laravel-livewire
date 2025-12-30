<?php
// Script untuk fix importExcel method

$filePath = 'app/Livewire/Admin/UserManagement/WithUserModal.php';
$content = file_get_contents($filePath);

$oldMethod = <<<'PHP'
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

        // Convert to array - lebih simple dan reliable
        $allData = $worksheet->toArray();

        if (empty($allData)) {
            throw new \Exception('File Excel kosong');
        }

        // Get headers dari baris pertama
        $headerRow = array_shift($allData);
        $header = collect($headerRow)->map(fn($h) => Str::lower(trim($h ?? '')))->values();

        // Data rows sudah dalam format array
        $collection = collect($allData)
            ->filter(fn($row) => collect($row)->filter(fn($v) => $v !== null && $v !== '')->count() > 0)
            ->values()
            ->toArray();

        $lazyRows = LazyCollection::make(function () use ($collection) {
            foreach ($collection as $row) {
                yield $row;
            }
        });

        $success = 0;
        $errors  = [];
        $rowNumber = 1;

        $lazyRows
            ->chunk(100)
            ->each(function ($rows) use ($header, &$success, &$errors, &$rowNumber) {
                DB::beginTransaction();

                foreach ($rows as $row) {
                    $rowNumber++;

                    try {
                        $data = collect($row)
                            ->map(fn($v) => is_string($v) ? trim($v) : ($v ?? ''))
                            ->combine($header)
                            ->filter(fn($v, $k) => $k !== '') // Remove empty header columns
                            ->toArray();

                        // Debug log
                        \Log::info("Row $rowNumber data:", $data);

                        // Validasi per baris
                        $this->validateExcelRow($data, $rowNumber);

                        // Mapping
                        $this->mapExcelToProperties(collect($data));

                        // Simpan
                        $this->saveUserInternal();

                        $success++;

                    } catch (\Throwable $e) {
                        DB::rollBack();

                        $errors[] = [
                            'row'   => $rowNumber,
                            'email' => $data['email'] ?? '-',
                            'error' => $e->getMessage(),
                        ];

                        \Log::error("Row $rowNumber error:", ['error' => $e->getMessage()]);
                        continue;
                    }
                }

                DB::commit();
            });

        session()->put('import_errors', $errors);

        $this->dispatch(
            'toast',
            message: "✅ Import selesai. Berhasil: $success | Gagal: " . count($errors)
        );
    }
PHP;

$newMethod = <<<'PHP'
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

        // Get headers - normalized
        $headerRow = $allData[0];
        $headers = [];
        foreach ($headerRow as $idx => $headerName) {
            if ($headerName) {
                $headers[$idx] = Str::lower(trim($headerName));
            }
        }

        if (empty($headers)) {
            throw new \Exception('Header tidak ditemukan di baris pertama');
        }

        \Log::info('Excel headers:', $headers);

        // Data rows - mulai dari baris 2 (index 1)
        $dataRows = array_slice($allData, 1);
        
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
PHP;

$content = str_replace($oldMethod, $newMethod, $content);
file_put_contents($filePath, $content);

echo "Method updated successfully!\n";
