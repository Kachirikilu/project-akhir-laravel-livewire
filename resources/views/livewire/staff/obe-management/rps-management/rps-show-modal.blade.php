<flux:modal name="rps-detail-modal" wire:model="detailRPSModal" x-data flyout
    class="md:w-[95vw] max-w-7xl h-[98vh] !bg-white !p-8 overflow-y-auto">

    <style>
        .rps-table {
            font-family: "Times New Roman", Times, serif;
            color: black !important;
        }

        .rps-table td,
        .rps-table th {
            border: 1px solid black !important;
            padding: 8px;
        }

        /* Indentasi Paragraf agar nomor sejajar */
        .list-indent {
            padding-left: 20px;
            text-indent: -20px;
            text-align: justify;
        }
    </style>

    @php
        $data = $detailRPSData ?? [];
    @endphp

    <div class="rps-table bg-white p-4">
        {{-- ================= HEADER ================= --}}
        <table class="w-full mb-4">
            <tr>
                <td class="w-[12%] text-center">
                    <div class="flex items-center justify-center">
                        {{-- Ganti dengan asset() jika logo ada di public --}}
                        <img src="https://upload.wikimedia.org/wikipedia/commons/3/3a/Logo_Universitas_Sriwijaya.png"
                            class="h-20 object-contain">
                    </div>
                </td>
                <td class="w-[88%] text-center font-bold text-lg leading-tight uppercase">
                    <div>UNIVERSITAS SRIWIJAYA</div>
                    <div>Fakultas {{ $data['fakultas'] ?? '' }}</div>
                    <div>Jurusan {{ $data['jurusan'] ?? '' }}</div>
                    <div>Program Studi {{ $data['programStudi'] ?? '' }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-center font-bold text-lg py-2 uppercase">
                    RENCANA PEMBELAJARAN SEMESTER
                </td>
            </tr>
        </table>

        {{-- ================= IDENTITAS ================= --}}
        <div class="font-bold mb-2">A. IDENTITAS MATA KULIAH</div>
        <table class="w-full mb-6 text-sm">
            <tr class="font-bold text-center bg-gray-50">
                <td class="w-1/6">Nama Mata Kuliah</td>
                <td class="w-1/6">Kode</td>
                <td class="w-1/6">Bahan Kajian</td>
                <td class="w-1/6">SKS (K/P)</td>
                <td class="w-1/6">Semester</td>
                <td class="w-1/6">Tanggal Revisi</td>
            </tr>
            <tr class="text-center">
                <td>{{ $data['namaMataKuliah'] ?? '' }}</td>
                <td>{{ $data['kodeMataKuliah'] ?? '' }}</td>
                <td>{{ $data['bahanKajian'] ?? '' }}</td>
                <td>{{ $data['sksKuliah'] ?? '0' }} / {{ $data['sksPraktikum'] ?? '0' }}</td>
                <td>{{ $data['semester'] ?? '' }}</td>
                <td>{{ $data['tanggalRevisi'] ?? '' }}</td>
                <td>{{ $data['bobot_uts'] ?? '' }}</td>
                <td>{{ $data['bobot_uas'] ?? '' }}</td>

            </tr>
            <tr>
                <td class="font-bold bg-gray-50">Deskripsi</td>
                <td colspan="5" class="text-justify leading-relaxed">
                    {{ $data['deskripsiMataKuliah'] ?? '' }}
                </td>
            </tr>
            <tr>
                <td class="font-bold bg-gray-50 align-top">CPL</td>
                <td colspan="5">
                    @foreach (explode("\n", $data['cpl'] ?? '') as $line)
                        @if (trim($line))
                            <div class="list-indent mb-1">{{ $line }}</div>
                        @endif
                    @endforeach
                </td>
            </tr>
            <tr>
                <td class="font-bold bg-gray-50 align-top">CPMK</td>
                <td colspan="5">
                    @foreach (explode("\n", $data['listCpmkWithDesc'] ?? '') as $line)
                        @if (trim($line))
                            <div class="list-indent mb-1">{{ $line }}</div>
                        @endif
                    @endforeach
                </td>
            </tr>
            <tr>
                <td class="font-bold bg-gray-50">Tim Pengajar</td>
                <td colspan="2" class="whitespace-pre-line">{{ $data['timPengajar'] ?? '' }}</td>
                <td class="font-bold bg-gray-50 text-center">Ketua / Instruktur</td>
                <td colspan="2">
                    <div class="font-bold text-blue-800">Ketua: {{ $data['ketuaTimPengajar'] ?? '-' }}</div>
                    <div class="text-xs mt-1 border-t pt-1">Instruktur: <br> {{ $data['instruktur'] ?? '-' }}</div>
                </td>
            </tr>
        </table>

        {{-- ================= PROGRAM PEMBELAJARAN ================= --}}
        <div class="font-bold mb-2">B. PROGRAM PEMBELAJARAN</div>
        <table class="w-full text-[10px] leading-tight">
            <thead class="bg-gray-50 font-bold text-center">
                <tr>
                    <td class="p-1">CPMK</td>
                    <td class="p-1 w-[15%]">Sub-CPMK</td>
                    <td class="p-1 w-[15%]">Materi</td>
                    <td class="p-1">Metodologi & Waktu</td>
                    <td class="p-1">Metode</td>
                    <td class="p-1 w-[15%]">Tugas & Asesmen</td>
                    <td class="p-1">Indikator</td>
                    <td class="p-1">Bobot</td>
                    <td class="p-1">Dosen</td>
                </tr>
            </thead>
            <tbody class="border-t border-black">
                @php
                    $programData = collect($data['programPembelajaran'] ?? []);
                    $uasKeywords = ['UAS', 'LAPORAN AKHIR', 'HASIL PROYEK', 'HASIL PROJEK'];

                    // 1. Deteksi apakah ada UTS/UAS di data
                    $hasUTS = $programData->contains(function ($row) {
                        return str_contains(strtoupper($row['sub_cpmk'] ?? ''), 'UTS') ||
                            strtoupper(trim($row['metode'] ?? '')) === 'UTS';
                    });

                    $hasUAS = $programData->contains(function ($row) use ($uasKeywords) {
                        $sub = strtoupper($row['sub_cpmk'] ?? '');
                        $metode = strtoupper(trim($row['metode'] ?? ''));
                        return collect($uasKeywords)->contains(fn($kw) => str_contains($sub, $kw)) ||
                            in_array($metode, $uasKeywords);
                    });

                    // 2. Helper Bobot
                    if (!function_exists('getDisplayBobot')) {
                        function getDisplayBobot($row, $rpsData, $forceType = null)
                        {
                            $raw = $row['bobot'] ?? '0%';
                            $isEmpty = in_array(trim($raw), ['0%', '0,0%', '0,00%', '0', '']);

                            if ($isEmpty) {
                                $sub = strtoupper($row['sub_cpmk'] ?? '');
                                $metode = strtoupper(trim($row['metode'] ?? ''));
                                $isUTS = $forceType === 'UTS' || str_contains($sub, 'UTS') || $metode === 'UTS';

                                if ($isUTS) {
                                    return ($rpsData['bobot_uts'] ?? 0) . '%';
                                }
                                return ($rpsData['bobot_uas'] ?? 0) . '%';
                            }
                            return $raw;
                        }
                    }

                    // 3. Bangun baris final
                    $finalRows = collect();

                    if (!$hasUTS && !$hasUAS) {
                        // SkenARIO STANDAR: Tidak ada UTS/UAS di database, sisipkan di 8 & 16
                        foreach ($programData as $index => $row) {
                            if ($index === 7) {
                                $finalRows->push(['type' => 'AUTO_UTS']);
                            }
                            $finalRows->push(array_merge($row, ['type' => 'NORMAL']));
                        }
                        if ($finalRows->count() < 16) {
                            $finalRows->push(['type' => 'AUTO_UAS']);
                        }
                    } else {
                        // SKENARIO CUSTOM: Ikuti urutan database sepenuhnya
                        foreach ($programData as $row) {
                            $sub = strtoupper($row['sub_cpmk'] ?? '');
                            $metode = strtoupper(trim($row['metode'] ?? ''));
                            $isUTS = str_contains($sub, 'UTS') || $metode === 'UTS';
                            $isUAS =
                                collect($uasKeywords)->contains(fn($kw) => str_contains($sub, $kw)) ||
                                in_array($metode, $uasKeywords);

                            $type = $isUTS ? 'EXPLICIT_UTS' : ($isUAS ? 'EXPLICIT_UAS' : 'NORMAL');
                            $finalRows->push(array_merge($row, ['type' => $type]));
                        }
                    }
                @endphp

                @foreach ($finalRows as $row)
                    @if ($row['type'] === 'AUTO_UTS')
                        <tr class="bg-gray-50 font-bold italic text-center">
                            <td class="p-2 border border-black">-</td>
                            <td class="p-2 border border-black text-left">UJIAN TENGAH SEMESTER (UTS)</td>
                            <td class="p-2 border border-black">-</td>
                            <td class="p-2 border border-black">-</td>
                            <td class="p-2 border border-black">Evaluasi Tengah Semester</td>
                            <td class="p-2 border border-black">-</td>
                            <td class="p-2 border border-black">Ketepatan Jawaban</td>
                            <td class="p-2 border border-black text-blue-600">{{ ($data['bobot_uts'] ?? 0) . '%' }}
                            </td>
                            <td class="p-2 border border-black">Tim</td>
                        </tr>
                    @elseif ($row['type'] === 'AUTO_UAS')
                        <tr class="bg-gray-50 font-bold italic text-center">
                            <td class="p-2 border border-black">-</td>
                            <td class="p-2 border border-black text-left">UJIAN AKHIR SEMESTER (UAS)</td>
                            <td class="p-2 border border-black">-</td>
                            <td class="p-2 border border-black">-</td>
                            <td class="p-2 border border-black">Evaluasi Akhir Semester</td>
                            <td class="p-2 border border-black">-</td>
                            <td class="p-2 border border-black">Ketepatan Jawaban</td>
                            <td class="p-2 border border-black text-blue-600">{{ ($data['bobot_uas'] ?? 0) . '%' }}
                            </td>
                            <td class="p-2 border border-black">Tim</td>
                        </tr>
                    @else
                        @php
                            $isExam = in_array($row['type'], ['EXPLICIT_UTS', 'EXPLICIT_UAS']);
                            $examType = str_contains($row['type'], 'UTS') ? 'UTS' : ($isExam ? 'UAS' : null);
                        @endphp
                        <tr class="{{ $isExam ? 'bg-gray-50 font-semibold italic' : '' }}">
                            <td class="p-2 border border-black text-center font-bold">{{ $row['cpmk'] ?? '' }}</td>
                            <td class="p-2 border border-black text-left">{{ $row['sub_cpmk'] ?? '-' }}</td>
                            <td class="p-2 border border-black text-left">{{ $row['materi'] ?? '-' }}</td>
                            <td class="p-2 border border-black text-left">{{ $row['metodologi'] ?? '-' }}</td>
                            <td class="p-2 border border-black text-left">{{ $row['metode'] ?? '-' }}</td>
                            <td class="p-2 border border-black text-left">{{ $row['tugas'] ?? '-' }}</td>
                            <td class="p-2 border border-black text-left">{{ $row['indikator'] ?? '-' }}</td>
                            <td class="p-2 border border-black text-center text-blue-600 font-bold">
                                {{ getDisplayBobot($row, $data, $examType) }}
                            </td>
                            <td class="p-2 border border-black text-center">{{ $row['dosen'] ?? '-' }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 text-sm font-bold border border-black p-2">
            Beban belajar mahasiswa selama satu semester: {{ $data['totalSks'] ?? '0' }} SKS
        </div>
    </div>
</flux:modal>
