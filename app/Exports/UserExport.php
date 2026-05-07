<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserExport extends DefaultValueBinder implements FromQuery, ShouldAutoSize, WithColumnFormatting, WithCustomValueBinder, WithHeadings, WithMapping, WithStyles
{
    protected $query;

    protected $switchTable;

    public function __construct($query, $switchTable)
    {
        $this->query = $query;
        $this->switchTable = $switchTable;
    }

    public function query()
    {
        return $this->query;
    }

    // Mengatur Heading 2 Baris (Merge Cell)
    public function headings(): array
    {
        // if ($this->switchTable == 'mahasiswa') {
        //     return [
        //         ['ID', 'Role', 'Nama', 'Email', 'Identitas (ID)', '', 'Angkatan', 'Status', 'Program Studi'],
        //         ['', '', '', '', 'NIM', 'NIK', '', '', '']
        //     ];
        // }

        // Default Heading (untuk Admin/Dosen/Mahasiswa)
        return [
            [
                'ID', 'Role', 'Nama', 'Email',
                'Identitas (ID)', '', '', '', '', // E-I (5 kolom)
                'Angkatan', 'Status', 'Program Studi', 'Kode Kampus',
                'Tempat Lahir', 'Tanggal Lahir', 'Jenis Kelamin', 'Agama', 'No. HP', 'No. Karpeg',
                'Pangkat/Golongan (Admin)', '', '', '', '', // T-X (5 kolom)
                'Pangkat/Golongan (Dosen)', '', '', '', '', // Y-AC (5 kolom)
            ],
            [
                '', '', '', '',
                'NIP', 'NIM', 'NIDN', 'NITK', 'NIK',
                '', '', '', '',
                '', '', '', '', '', '',
                'Pangkat', 'Gol. Awal', 'Gol. Akhir', 'TMT CP/BLU', 'TMT BLU',
                'Pkt. Dosen', 'Gol. Dosen', 'TMT Gol', 'Jabatan', 'TMT Jab',
            ],
        ];
    }

    // Mapping Data (NIP/NIK ditambahkan tanda kutip tunggal agar dibaca string)
    public function map($user): array
    {
        // if ($this->switchTable == 'mahasiswa') {
        //     return [
        //         $user->id,
        //         $user->role,
        //         $user->name,
        //         $user->email,
        //         $user->mahasiswa->nim ?? '',
        //         $user->nik ?? '',
        //         $user->mahasiswa->angkatan ?? '-',
        //         $user->status,
        //         $user->prodi,
        //     ];
        // }

        return [
            $user->id,
            $user->role,
            $user->name,
            $user->email,

            // ID Identitas
            $user->admin->nip ?? $user->dosen->nip ?? '',
            $user->mahasiswa->nim ?? '',
            $user->dosen->nidn ?? '',
            $user->admin->nitk ?? '',
            $user->nik ?? '',
            // ID Identitas

            $user->mahasiswa->angkatan ?? '',
            $user->status,
            $user->prodi,
            $user->admin->kode_wilayah ?? $user->mahasiswa->kode_wilayah ?? '',
            $user->tpt_lahir,
            $user->tgl_lahir,
            $user->gender,
            $user->agama,
            $user->no_hp,
            $user->dosen->no_karpeg ?? '',

            // Hanya Admin (Buatkan jadi 2 Baris, dengan baris pertama "Pangkat/Golongan Amdin")
            $user->admin->pangkat ?? '',
            $user->admin->golongan_awal ?? '',
            $user->admin->golongan_akhir ?? '',
            $user->admin->tmt_cp_blu ?? '',
            $user->admin->tmt_blu ?? '',
            // Hanya Admin

            // Hanya Dosen (Buatkan jadi 2 Baris, dengan baris pertama "Pangkat/Golongan Dosen")
            $user->dosen->pangkat_terakhir ?? '',
            $user->dosen->golongan_terakhir ?? '',
            $user->dosen->tmt_golongan ?? '',
            $user->dosen->jabatan_fungsional ?? '',
            $user->dosen->tmt_jabatan ?? '',
            // Hanya Dosen
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        // Paksa kolom identitas (E sampai I) menjadi string murni
        if (in_array($cell->getColumn(), ['E', 'F', 'G', 'H', 'I'])) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    // Paksa kolom NIK/NIP menjadi format TEXT agar nol di depan tidak hilang
    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Styling Heading (Baris 1 & 2)
        $sheet->mergeCells('A1:A2');
        $sheet->mergeCells('B1:B2');
        $sheet->mergeCells('C1:C2');
        $sheet->mergeCells('D1:D2');

        

        // Merge Identitas (ID) tergantung jumlah kolom identitas
        $sheet->mergeCells('E1:I1');

        $sheet->mergeCells('J1:J2');
        $sheet->mergeCells('K1:K2');
        $sheet->mergeCells('L1:L2');
        $sheet->mergeCells('M1:M2');
        $sheet->mergeCells('N1:N2');
        $sheet->mergeCells('O1:O2');
        $sheet->mergeCells('P1:P2');
        $sheet->mergeCells('Q1:Q2');
        $sheet->mergeCells('R1:R2');
        $sheet->mergeCells('S1:S2');
        $sheet->mergeCells('T1:X1');
        $sheet->mergeCells('Y1:AC1');

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

        $sheet->getStyle('A1:AC2')->applyFromArray($styleArray);

        // Tambah Border ke seluruh data
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle("A1:$highestColumn$highestRow")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        return [];
    }
}
