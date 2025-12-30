<?php
$filePath = 'app/Livewire/Admin/UserManagement/WithUserModal.php';
$content = file_get_contents($filePath);

$oldImports = <<<'PHP'
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;

use Illuminate\Support\LazyCollection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
PHP;

$newImports = <<<'PHP'
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;

use Illuminate\Support\LazyCollection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
PHP;

$content = str_replace($oldImports, $newImports, $content);
file_put_contents($filePath, $content);

echo "Validator import added!\n";
