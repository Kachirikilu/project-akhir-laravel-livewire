<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProdiExport extends DefaultValueBinder implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, WithEvents
{
    protected $queryProdi;

    protected $switchTable;

    protected $title;

    public function __construct($queryProdi, $switchTable, $title)
    {
        $this->queryProdi = $queryProdi;
        $this->switchTable = $switchTable;
        $this->title = $title;
    }

    public function query()
    {
        return $this->queryProdi;
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function headings(): array
    {
        if ($this->switchTable == 'fakultas') {
            return [
                [
                    'ID', 'Kode FK', 'Fakultas',
                ],
            ];
        } elseif ($this->switchTable == 'departemen') {
            return [
                [
                    'ID', 'Kode DP', 'Departemen',
                    'Fakultas', '',
                ],
                [
                    '', '', '',
                    'Kode FK', 'Nama Fakultas',
                ],
            ];
        } else {
            return [
                [
                    'ID', 'Kode PR', 'Program Studi',
                    'Departemen', '',
                    'Fakultas', '',
                ],
                [
                    '', '', '',
                    'Kode DP', 'Nama Departemen',
                    'Kode FK', 'Nama Fakultas',
                ],
            ];
        }
    }

    public function map($pr): array
    {
        if ($this->switchTable == 'fakultas') {
            return [
                $pr->id ?? '', // A
                $pr->kode ?? '', // B
                $pr->fakultas_fk ?? '', // C
            ];
        } elseif ($this->switchTable == 'departemen') {
            return [
                $pr->id ?? '', // A
                $pr->kode ?? '', // B
                $pr->departemen ?? '', // C
                $pr->kode_fk ?? '', // D
                $pr->fakultas_fk ?? '', // E
            ];
        } else {
            return [
                $pr->id ?? '', // A
                $pr->kode ?? '', // B
                $pr->prodi ?? '', // C
                $pr->kode_dp ?? '', // D
                $pr->departemen_dp ?? '', // E
                $pr->kode_fk ?? '', // F
                $pr->fakultas_fk ?? '', // G
            ];
        }
    }

    public function styles(Worksheet $sheet)
    {
        $styleArray = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '075985'],
            ],
        ];

        if ($this->switchTable !== 'fakultas') {
            $verticalMerges = ['A', 'B', 'C'];
            foreach ($verticalMerges as $col) {
                $sheet->mergeCells("{$col}4:{$col}5");
            }
            $sheet->mergeCells('D4:E4');
            if ($this->switchTable !== 'departemen') {
                $sheet->mergeCells('F4:G4');
                $alignmentMerges = ['A', 'B', 'D', 'F'];
            } else {
                $alignmentMerges = ['A', 'B', 'D'];
            }
        } else {
            $alignmentMerges = ['A', 'B'];
        }

        $highestRow = $sheet->getHighestRow();
        foreach ($alignmentMerges as $c) {
            $sheet->getStyle($c . '4:' . $c . $highestRow)
                ->getAlignment()->applyFromArray([
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ]);
        }

        if ($this->switchTable == 'fakultas') {
            $sheet->getStyle('A4:C4')->applyFromArray($styleArray);
        } elseif ($this->switchTable == 'departemen') {
            $sheet->getStyle('A4:E5')->applyFromArray($styleArray);
        } else {
            $sheet->getStyle('A4:G5')->applyFromArray($styleArray);
        }

        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle("A4:$highestColumn$highestRow")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                if ($this->switchTable == 'fakultas') {
                    $highestColumn = 'C';
                } elseif ($this->switchTable == 'departemen') {
                    $highestColumn = 'E';
                } else {
                    $highestColumn = 'G';
                }

                $title = $this->title;

                $sheet->mergeCells("A2:{$highestColumn}2");
                $sheet->setCellValue('A2', $title);

                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getRowDimension(2)->setRowHeight(30);
            },
        ];
    }
}
