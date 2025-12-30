<?php

return [
    'exports' => [
        'csv' => [
            'delimiter' => ',',
        ],
        'properties' => [
            'creator' => 'Laravel Excel',
        ],
    ],

    'imports' => [
        'readOnly' => true,
        'heading' => 'slugged',
    ],

    'extension_detector' => [
        'xlsx' => \Maatwebsite\Excel\Files\LocalTemporaryFile::class,
        'csv' => \Maatwebsite\Excel\Files\LocalTemporaryFile::class,
        'tsv' => \Maatwebsite\Excel\Files\LocalTemporaryFile::class,
    ],

    'temporary_files' => [
        'local_path' => 'storage/framework/cache',
        'remote_disk' => null,
        'remote_path' => 'temporary-excel-files',
        'force_deletion' => false,
    ],
];
