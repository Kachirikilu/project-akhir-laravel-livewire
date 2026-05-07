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
        'xlsx'     => 'Xlsx',
        'xlsm'     => 'Xlsx',
        'xltx'     => 'Xlsx',
        'xltm'     => 'Xlsx',
        'xls'      => 'Xls',
        'xlt'      => 'Xls',
        'ods'      => 'Ods',
        'ots'      => 'Ods',
        'slk'      => 'Slk',
        'xml'      => 'Xml',
        'gnumeric' => 'Gnumeric',
        'htm'      => 'Html',
        'html'     => 'Html',
        'csv'      => 'Csv',
        'tsv'      => 'Csv',
        'pdf'      => 'Dompdf',
    ],

    // 'temporary_files' => [
    //     'local_path' => 'storage/framework/cache',
    //     'remote_disk' => null,
    //     'remote_path' => 'temporary-excel-files',
    //     'force_deletion' => false,
    // ],
    'temporary_files' => [
        'local_path'          => storage_path('framework/laravel-excel'),
        'remote_disk'         => null,
        'remote_path'         => null,
        'force_deletion'      => true,
    ],
];
