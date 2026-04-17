<flux:modal name="rps-detail-modal" wire:model="detailRPSModal" x-data
    flyout
    class="md:w-[90vw] max-w-6xl h-[98vh] !bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">


        @php
            $data = $detailRPSData ?? [];
        @endphp

        {{-- ================= HEADER ================= --}}
        <table style="size: A4 landscape; margin: 1.5cm 1cm;  font-family: "Times New Roman", serif; font-size: 11px; " class="w-full border border-black mb-4">
            <tr>
                <td class="w-[12%] border border-black p-2 text-center">
                    <div class="h-[80px] flex items-center justify-center border border-dashed text-[10px]">
                        LOGO
                    </div>
                </td>
                <td class="w-[88%] border border-black text-center font-bold text-[16px] leading-tight py-2">
                    <div>UNIVERSITAS SRIWIJAYA</div>
                    <div>Fakultas {{ $data['fakultas'] ?? '' }}</div>
                    <div>Jurusan {{ $data['jurusan'] ?? '' }}</div>
                    <div>Program Studi {{ $data['prodi'] ?? '' }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="border border-black text-center font-bold text-[16px] py-2">
                    RENCANA PEMBELAJARAN SEMESTER
                </td>
            </tr>
        </table>

        {{-- ================= IDENTITAS ================= --}}
        <div class="font-bold mb-2">A. IDENTITAS MATA KULIAH</div>

        <table class="w-full border border-black text-center">
            <tr class="font-bold">
                <td class="border p-2">Nama</td>
                <td class="border p-2">Kode</td>
                <td class="border p-2">Bahan Kajian</td>
                <td class="border p-2">SKS</td>
                <td class="border p-2">Semester</td>
                <td class="border p-2">Revisi</td>
            </tr>
            <tr>
                <td class="border p-2">{{ $data['mk'] ?? '' }}</td>
                <td class="border p-2">{{ $data['kode_mk'] ?? '' }}</td>
                <td class="border p-2">{{ $data['bahanKajian'] ?? '' }}</td>
                <td class="border p-2">{{ $data['sks'] ?? '' }} / {{ $data['sksPraktikum'] ?? '' }}</td>
                <td class="border p-2">{{ $data['semester'] ?? '' }}</td>
                <td class="border p-2">{{ $data['revisi'] ?? '' }}</td>
            </tr>

            <tr>
                <td class="border p-2 font-bold">Deskripsi</td>
                <td colspan="5" class="border p-2 text-left">{{ $data['deskripsi'] ?? '' }}</td>
            </tr>

            <tr>
                <td class="border p-2 font-bold">CPL</td>
                <td colspan="5" class="border p-2 text-left whitespace-pre-line">{{ $data['cpl'] ?? '' }}</td>
            </tr>

            <tr>
                <td class="border p-2 font-bold">CPMK</td>
                <td colspan="5" class="border p-2 text-left whitespace-pre-line">{{ $data['listCpmkWithDesc'] ?? '' }}
                </td>
            </tr>

            <tr>
                <td class="border p-2 font-bold">Tim Pengajar</td>
                <td colspan="3" class="border p-2 text-left whitespace-pre-line">{{ $data['timPengajar'] ?? '' }}</td>
                <td class="border p-2 font-bold">Ketua</td>
                <td class="border p-2">{{ $data['ketuaTimPengajar'] ?? '' }}</td>
            </tr>

            <tr>
                <td class="border p-2 font-bold">Instruktur</td>
                <td colspan="5" class="border p-2 whitespace-pre-line">{{ $data['instruktur'] ?? '' }}</td>
            </tr>
        </table>

        {{-- ================= PAGE BREAK ================= --}}
        <div style="page-break-before: always;"></div>

        {{-- ================= PROGRAM PEMBELAJARAN ================= --}}
        <div class="font-bold mb-2">B. PROGRAM PEMBELAJARAN</div>

        <table class="w-full border border-black text-[10px]">
            <thead class="font-bold text-center">
                <tr>
                    <td class="border p-1">CPMK</td>
                    <td class="border p-1">Sub-CPMK</td>
                    <td class="border p-1">Materi</td>
                    <td class="border p-1">Referensi</td>
                    <td class="border p-1">Metode</td>
                    <td class="border p-1">Tugas</td>
                    <td class="border p-1">Indikator</td>
                    <td class="border p-1">Bobot</td>
                    <td class="border p-1">Dosen</td>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['programPembelajaran'] ?? [] as $row)
                    <tr>
                        <td class="border p-1 text-center">{{ $row['cpmk'] }}</td>
                        <td class="border p-1 text-left">{{ $row['sub_cpmk'] }}</td>
                        <td class="border p-1 text-left">{{ $row['materi'] }}</td>
                        <td class="border p-1 text-left whitespace-pre-line">{{ $row['referensi'] }}</td>
                        <td class="border p-1 text-left">{{ $row['metodologi'] }}</td>
                        <td class="border p-1 text-left">{{ $row['tugas'] }}</td>
                        <td class="border p-1 text-left">{{ $row['indikator'] }}</td>
                        <td class="border p-1 text-center">{{ $row['bobot'] }}</td>
                        <td class="border p-1 text-center whitespace-pre-line">{{ $row['dosen'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="border p-4 text-center">Tidak ada data program pembelajaran.</td>
                    </tr>
                @endforelse

                <tr class="font-bold">
                    <td colspan="9" class="border p-2 text-left">
                        Beban belajar: {{ $data['totalSks'] ?? 0 }} SKS
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- ================= PAGE BREAK ================= --}}
        <div style="page-break-before: always;"></div>

        {{-- ================= REFERENSI ================= --}}
        <div class="font-bold mb-2">Referensi</div>

        <ol class="list-decimal ml-5">
            @foreach ($data['referensi'] ?? [] as $ref)
                <li class="mb-2 text-justify">{{ $ref }}</li>
            @endforeach
        </ol>

        {{-- ================= NILAI ================= --}}
        <div class="mt-6">
            <div class="font-bold mb-2">Skala Penilaian</div>

            <table class="w-1/2 text-center">
                <tr class="font-bold">
                    <td>Nilai</td>
                    <td>Range</td>
                    <td>Predikat</td>
                </tr>
                <tr>
                    <td>A</td>
                    <td>86-100</td>
                    <td>Sangat Baik</td>
                </tr>
                <tr>
                    <td>B</td>
                    <td>71-85</td>
                    <td>Baik</td>
                </tr>
                <tr>
                    <td>C</td>
                    <td>56-70</td>
                    <td>Cukup</td>
                </tr>
                <tr>
                    <td>D</td>
                    <td>41-55</td>
                    <td>Kurang</td>
                </tr>
                <tr>
                    <td>E</td>
                    <td>0-40</td>
                    <td>Sangat Kurang</td>
                </tr>
            </table>
        </div>



</flux:modal>
